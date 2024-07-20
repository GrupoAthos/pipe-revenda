<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$idfuncionario = $_SESSION['idfuncionario'];
$id_proposta = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_proposta = $_POST['id_proposta'];
    $licenca = $_POST['licenca'];
    $quantidade_maquinas = $_POST['quantidade_maquinas'];
    $quantidade_celulares = $_POST['quantidade_celulares'];
    $modulo_apresentado = $_POST['modulo_apresentado'];
    $inicio_vigor_mensalidade = $_POST['inicio_vigor_mensalidade'];
    $mensalidade = $_POST['mensalidade'];
    $pontualidade = $_POST['pontualidade'];
    $mensalidade_final = $_POST['mensalidade_final'];

    // Obter valores antigos
    $stmt_old = $conn->prepare("SELECT licenca, quantidade_maquinas, quantidade_celulares, modulo_apresentado, inicio_vigor_mensalidade, mensalidade, pontualidade, mensalidade_final FROM propostas WHERE id = ?");
    $stmt_old->bind_param("i", $id_proposta);
    $stmt_old->execute();
    $stmt_old->bind_result($old_licenca, $old_quantidade_maquinas, $old_quantidade_celulares, $old_modulo_apresentado, $old_inicio_vigor_mensalidade, $old_mensalidade, $old_pontualidade, $old_mensalidade_final);
    $stmt_old->fetch();
    $stmt_old->close();

    // Atualizar proposta
    $stmt = $conn->prepare("UPDATE propostas SET licenca = ?, quantidade_maquinas = ?, quantidade_celulares = ?, modulo_apresentado = ?, inicio_vigor_mensalidade = ?, mensalidade = ?, pontualidade = ?, mensalidade_final = ? WHERE id = ?");
    $stmt->bind_param("iidssssdi", $licenca, $quantidade_maquinas, $quantidade_celulares, $modulo_apresentado, $inicio_vigor_mensalidade, $mensalidade, $pontualidade, $mensalidade_final, $id_proposta);
    if ($stmt->execute()) {
        // Inserir no histórico se houver alterações
        $campos = ['licenca', 'quantidade_maquinas', 'quantidade_celulares', 'modulo_apresentado', 'inicio_vigor_mensalidade', 'mensalidade', 'pontualidade', 'mensalidade_final'];
        $valores_antigos = [$old_licenca, $old_quantidade_maquinas, $old_quantidade_celulares, $old_modulo_apresentado, $old_inicio_vigor_mensalidade, $old_mensalidade, $old_pontualidade, $old_mensalidade_final];
        $valores_novos = [$licenca, $quantidade_maquinas, $quantidade_celulares, $modulo_apresentado, $inicio_vigor_mensalidade, $mensalidade, $pontualidade, $mensalidade_final];

        for ($i = 0; $i < count($campos); $i++) {
            if ($valores_antigos[$i] != $valores_novos[$i]) {
                $stmt_hist = $conn->prepare("INSERT INTO proposta_historico_negociacao (id_proposta, campo_alterado, valor_antigo, valor_novo) VALUES (?, ?, ?, ?)");
                $stmt_hist->bind_param("isss", $id_proposta, $campos[$i], $valores_antigos[$i], $valores_novos[$i]);
                $stmt_hist->execute();
                $stmt_hist->close();
            }
        }
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erro ao atualizar proposta: " . $stmt->error;
    }
    $stmt->close();
}

// Obter proposta atual
$stmt = $conn->prepare("SELECT licenca, quantidade_maquinas, quantidade_celulares, modulo_apresentado, inicio_vigor_mensalidade, mensalidade, pontualidade, mensalidade_final FROM propostas WHERE id = ?");
$stmt->bind_param("i", $id_proposta);
$stmt->execute();
$stmt->bind_result($licenca, $quantidade_maquinas, $quantidade_celulares, $modulo_apresentado, $inicio_vigor_mensalidade, $mensalidade, $pontualidade, $mensalidade_final);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Proposta</title>
    <?php include ('includes/header.php'); ?>
</head>
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
                            <h1 class="m-0">Editar Proposta</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Informações da Proposta</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="licenca">Licença</label>
                                    <input type="number" step="0.01" class="form-control" id="licenca" name="licenca" value="<?php echo $licenca; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade_maquinas">Quantidade de Máquinas</label>
                                    <input type="number" class="form-control" id="quantidade_maquinas" name="quantidade_maquinas" value="<?php echo $quantidade_maquinas; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade_celulares">Quantidade de Celulares</label>
                                    <input type="number" class="form-control" id="quantidade_celulares" name="quantidade_celulares" value="<?php echo $quantidade_celulares; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="modulo_apresentado">Módulo Apresentado</label>
                                    <select class="form-control" id="modulo_apresentado" name="modulo_apresentado" required>
                                        <option value="GERENCIAL" <?php echo ($modulo_apresentado == 'GERENCIAL') ? 'selected' : ''; ?>>GERENCIAL</option>
                                        <option value="EXPERT" <?php echo ($modulo_apresentado == 'EXPERT') ? 'selected' : ''; ?>>EXPERT</option>
                                        <option value="STARTER" <?php echo ($modulo_apresentado == 'STARTER') ? 'selected' : ''; ?>>STARTER</option>
                                        <option value="FULL" <?php echo ($modulo_apresentado == 'FULL') ? 'selected' : ''; ?>>FULL</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="inicio_vigor_mensalidade">Início Vigor Mensalidade</label>
                                    <input type="date" class="form-control" id="inicio_vigor_mensalidade" name="inicio_vigor_mensalidade" value="<?php echo $inicio_vigor_mensalidade; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="mensalidade">Mensalidade</label>
                                    <input type="number" step="0.01" class="form-control" id="mensalidade" name="mensalidade" value="<?php echo $mensalidade; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="pontualidade">Pontualidade</label>
                                    <input type="number" step="0.01" class="form-control" id="pontualidade" name="pontualidade" value="<?php echo $pontualidade; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="mensalidade_final">Mensalidade Final</label>
                                    <input type="number" step="0.01" class="form-control" id="mensalidade_final" name="mensalidade_final" value="<?php echo $mensalidade_final; ?>" readonly>
                                </div>
                                <input type="hidden" name="id_proposta" value="<?php echo $id_proposta; ?>">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                            </form>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-header bg-info text-white">Histórico de Alterações</div>
                        <div class="card-body">
                            <?php
                            // Consulta para obter o histórico de alterações
                            $stmt_hist = $conn->prepare("SELECT campo_alterado, valor_antigo, valor_novo, data_alteracao FROM proposta_historico_negociacao WHERE id_proposta = ? ORDER BY data_alteracao DESC");
                            $stmt_hist->bind_param("i", $id_proposta);
                            $stmt_hist->execute();
                            $result_hist = $stmt_hist->get_result();

                            if ($result_hist->num_rows > 0) {
                                echo '<table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Campo Alterado</th>
                                                <th>Valor Antigo</th>
                                                <th>Valor Novo</th>
                                                <th>Data Alteração</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                while ($row_hist = $result_hist->fetch_assoc()) {
                                    echo '<tr>
                                            <td>' . htmlspecialchars($row_hist['campo_alterado']) . '</td>
                                            <td>' . htmlspecialchars($row_hist['valor_antigo']) . '</td>
                                            <td>' . htmlspecialchars($row_hist['valor_novo']) . '</td>
                                            <td>' . htmlspecialchars($row_hist['data_alteracao']) . '</td>
                                          </tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<p>Nenhuma alteração registrada.</p>';
                            }
                            $stmt_hist->close();
                            ?>
                        </div>
                    </div>
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

    <script>
        $(document).ready(function() {
            $('#mensalidade, #pontualidade').on('input', function() {
                var mensalidade = parseFloat($('#mensalidade').val()) || 0;
                var pontualidade = parseFloat($('#pontualidade').val()) || 0;
                var mensalidade_final = mensalidade - pontualidade;
                $('#mensalidade_final').val(mensalidade_final.toFixed(2));
            });
        });
    </script>
</body>
</html>
