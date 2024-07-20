<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$idfuncionario = $_SESSION['idfuncionario'];
$nomecompleto = $_SESSION['nomecompleto'] ?? '';

$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-01');
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-t');
$filtro_etapa = isset($_GET['etapa']) ? $_GET['etapa'] : '';
$filtro_todas = isset($_GET['filtrar_todas']) ? true : false;

// Consulta para obter propostas do funcionário logado
$query = "SELECT p.id, r.nomerevenda, c.nome AS nome_cliente, p.previsao_entrega, p.licenca, p.modulo_apresentado, p.mensalidade_final, p.id_etapa, e.nome AS nome_etapa, p.datacadastro
          FROM propostas p 
          JOIN cliente_pipe c ON p.id_cliente = c.id 
          JOIN etapa_pipe e ON p.id_etapa = e.id 
          JOIN revenda r ON c.idrevenda = r.idrevenda
          WHERE p.id_vendedor = ?";

if ($filtro_todas) {
    // Se estiver filtrando todas, apenas removemos o filtro de data
    $query .= "";
} else {
    if ($filtro_data_inicio) {
        $query .= " AND p.datacadastro >= '$filtro_data_inicio'";
    }
    if ($filtro_data_fim) {
        $query .= " AND p.datacadastro <= '$filtro_data_fim'";
    }
}
if ($filtro_etapa) {
    $query .= " AND p.id_etapa = ?";
}

$stmt = $conn->prepare($query);

if ($filtro_etapa) {
    $stmt->bind_param("ii", $idfuncionario, $filtro_etapa);
} else {
    $stmt->bind_param("i", $idfuncionario);
}

$stmt->execute();
$result = $stmt->get_result();

// Organizando propostas por etapas e calculando totais
$propostas_por_etapa = [];
$total_propostas = 0;
$total_licenca = 0;
$total_mensalidade_final = 0;
$total_licenca_contratos = 0;

while ($row = $result->fetch_assoc()) {
    $propostas_por_etapa[$row['nome_etapa']][] = $row;
    if ($row['nome_etapa'] != 'DECLINOU') {
        $total_propostas++;
        $total_licenca += $row['licenca'];
        $total_mensalidade_final += $row['mensalidade_final'];
    }
    if ($row['nome_etapa'] == 'CONTRATO LANÇADO') {
        $total_licenca_contratos += $row['licenca'];
    }
}

// Consulta para obter as etapas
$query_etapas = "SELECT * FROM etapa_pipe ORDER BY ordem";
$result_etapas = $conn->query($query_etapas);

// Função para renderizar tabelas de propostas
function renderizar_tabela_propostas($propostas) {
    if (empty($propostas)) {
        return '<div class="card-body">Nenhuma proposta</div>';
    }

    $html = '<table class="table table-striped">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i></th>
                        <th><i class="fas fa-store"></i></th>
                        <th><i class="fas fa-file-alt"></i></th>
                        <th><i class="fas fa-dollar-sign"></i></th>
                        <th><i class="fas fa-calendar-alt"></i></th>
                        <th><i class="fas fa-clock"></th>
                        <th><i class="fas fa-cogs"></i></th>
                        <th><i class="fas fa-tools"></i></th>
                    </tr>
                </thead>
                <tbody>';
    foreach ($propostas as $proposta) {
        $row_class = (strtotime($proposta['previsao_entrega']) <= time()) ? ' class="table-danger"' : '';

        $html .= '<tr' . $row_class . '>
                    <td>' . htmlspecialchars(strtoupper($proposta['nome_cliente'])) . '</td>
                    <td>' . htmlspecialchars(strtoupper($proposta['nomerevenda'])) . '</td>
                    <td>' . htmlspecialchars($proposta['licenca']) . '</td>
                    <td>' . htmlspecialchars($proposta['mensalidade_final']) . '</td>
                    <td>' . htmlspecialchars($proposta['modulo_apresentado']) . '</td>
                    <td>' . date('d/m/Y', strtotime($proposta['datacadastro'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($proposta['previsao_entrega'])) . '</td>
                    <td>';
        if ($proposta['id_etapa'] == 5) {
            $html .= '<a href="atender_proposta.php?id=' . $proposta['id'] . '" class="btn btn-info btn-sm"><i class="fas fa-headset"></i></a>';
        } else {
            $html .= '<a href="editar_proposta.php?id=' . $proposta['id'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                      <a href="atender_proposta.php?id=' . $proposta['id'] . '" class="btn btn-info btn-sm"><i class="fas fa-headset"></i></a>
                      <a href="alterar_etapa.php?id=' . $proposta['id'] . '" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i></a>
                      <a href="declinar_proposta.php?id=' . $proposta['id'] . '" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i></a>';
        }
        $html .= '</td></tr>';
    }
    $html .= '</tbody>
            </table>';

    return $html;
}

// Consultas SQL independentes para os cards
$sql_total_propostas = "SELECT COUNT(*) AS total FROM propostas p
                        JOIN etapa_pipe e ON p.id_etapa = e.id
                        WHERE p.id_vendedor = ? AND e.nome != 'Declinou'";
$stmt_total_propostas = $conn->prepare($sql_total_propostas);
$stmt_total_propostas->bind_param("i", $idfuncionario);
$stmt_total_propostas->execute();
$result_total_propostas = $stmt_total_propostas->get_result();
$total_propostas = $result_total_propostas->fetch_assoc()['total'];

$sql_total_licenca = "SELECT SUM(p.licenca) AS total FROM propostas p
                      JOIN etapa_pipe e ON p.id_etapa = e.id
                      WHERE p.id_vendedor = ? AND e.nome != 'Declinou'";
$stmt_total_licenca = $conn->prepare($sql_total_licenca);
$stmt_total_licenca->bind_param("i", $idfuncionario);
$stmt_total_licenca->execute();
$result_total_licenca = $stmt_total_licenca->get_result();
$total_licenca = $result_total_licenca->fetch_assoc()['total'];

$sql_total_mensalidade_final = "SELECT SUM(p.mensalidade_final) AS total FROM propostas p
                                JOIN etapa_pipe e ON p.id_etapa = e.id
                                WHERE p.id_vendedor = ? AND e.nome != 'Declinou'";
$stmt_total_mensalidade_final = $conn->prepare($sql_total_mensalidade_final);
$stmt_total_mensalidade_final->bind_param("i", $idfuncionario);
$stmt_total_mensalidade_final->execute();
$result_total_mensalidade_final = $stmt_total_mensalidade_final->get_result();
$total_mensalidade_final = $result_total_mensalidade_final->fetch_assoc()['total'];

$sql_total_licenca_contratos = "SELECT SUM(p.licenca) AS total FROM propostas p
                                JOIN etapa_pipe e ON p.id_etapa = e.id
                                WHERE p.id_vendedor = ? AND e.nome = 'CONTRATO LANÇADO'";
$stmt_total_licenca_contratos = $conn->prepare($sql_total_licenca_contratos);
$stmt_total_licenca_contratos->bind_param("i", $idfuncionario);
$stmt_total_licenca_contratos->execute();
$result_total_licenca_contratos = $stmt_total_licenca_contratos->get_result();
$total_licenca_contratos = $result_total_licenca_contratos->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<?php include 'includes/header.php'; ?>
<body class="hold-transition sidebar-mini">
    
    <div class="wrapper">
        
        <!-- Navbar -->
        <?php include 'includes/navbar.php';?>
        <?php include 'menu.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <h1 class="bemvindo">Bem vindo, <?php echo strtoupper($nomecompleto); ?></h1>

                    <!-- Totals Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-dark">
                                <div class="inner">
                                    <h3><?php echo $total_propostas; ?></h3>
                                    <p>Total de Propostas</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?php echo number_format($total_licenca, 2); ?></h3>
                                    <p>Total de Licenças</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo number_format($total_mensalidade_final, 2); ?></h3>
                                    <p>Total Mensalidade Final</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo number_format($total_licenca_contratos, 2); ?></h3>
                                    <p>Licença Contrato Lançado</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">FILTRAR PROPOSTAS</div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="data_inicio">DATA INÍCIO</label>
                                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo $filtro_data_inicio; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="data_fim">DATA FIM</label>
                                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo $filtro_data_fim; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="etapa">ETAPA</label>
                                        <select class="form-control" id="etapa" name="etapa">
                                            <option value="">TODAS</option>
                                            <?php
                                            while ($etapa = $result_etapas->fetch_assoc()) {
                                                echo '<option value="' . $etapa['id'] . '" ' . ($filtro_etapa == $etapa['id'] ? 'selected' : '') . '>' . strtoupper($etapa['nome']) . '</option>';
                                            }
                                            $result_etapas->data_seek(0); // Reset result set pointer for later use
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 align-self-end">
                                        <button type="submit" class="btn btn-primary">FILTRAR</button>
                                        <a href="dashboard.php?filtrar_todas=1" class="btn btn-secondary">FILTRAR TODAS</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <h2 class="mt-5 text-primary">PROPOSTAS</h2>
                    <?php while ($etapa = $result_etapas->fetch_assoc()): ?>
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-info text-white"><?php echo strtoupper($etapa['nome']); ?></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php echo renderizar_tabela_propostas($propostas_por_etapa[$etapa['nome']] ?? []); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                Anything you want
            </div>
            <strong>&copy; 2023 <a href="#">Company</a>. All rights reserved.</strong>
        </footer>
    </div>
    <!-- ./wrapper -->

    <?php include 'includes/footer.php'; ?>
    
    <!-- Floating Button and Menu -->
    <button class="floating-button" onclick="toggleMenu()">
        <i class="fas fa-plus"></i>
    </button>
    <div class="floating-menu" id="floatingMenu">
        <a href="cadastrar_cliente.php">
            <i class="fas fa-user-plus"></i> + Cliente
        </a>
        <a href="adicionar_proposta.php">
            <i class="fas fa-file-contract"></i> + Contrato
        </a>
        <button onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i> Voltar ao Topo
        </button>
    </div>
    <style>
        .floating-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .floating-menu {
            position: fixed;
            bottom: 80px;
            left: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
        }
        .floating-menu a, .floating-menu button {
            padding: 10px 20px;
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            border: none;
            background: none;
            cursor: pointer;
        }
        .floating-menu a:hover, .floating-menu button:hover {
            background-color: #f0f0f0;
        }
        .floating-menu a i, .floating-menu button i {
            margin-right: 10px;
        }
    </style>
    <script>
        function toggleMenu() {
            var menu = document.getElementById('floatingMenu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'flex' : 'none';
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('click', function(event) {
            var menu = document.getElementById('floatingMenu');
            var isClickInside = menu.contains(event.target) || event.target.matches('.floating-button');
            if (!isClickInside) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>
