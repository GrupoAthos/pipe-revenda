<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $licenca = $_POST['licenca'];
    $quantidade_maquinas = $_POST['quantidade_maquinas'];
    $quantidade_celulares = $_POST['quantidade_celulares'];
    $modulo_apresentado = $_POST['modulo_apresentado'];
    $inicio_vigor_mensalidade = $_POST['inicio_vigor_mensalidade'];
    $mensalidade = $_POST['mensalidade'];
    $pontualidade = $_POST['pontualidade'];
    $mensalidade_final = $_POST['mensalidade_final'];
    $id_vendedor = $_SESSION['idfuncionario'];
    $id_etapa = 1; // Etapa inicial
    $datacadastro = date('Y-m-d'); // Data atual

    // Consulta para inserir a proposta
    $stmt = $conn->prepare("INSERT INTO propostas (id_cliente, licenca, quantidade_maquinas, quantidade_celulares, modulo_apresentado, inicio_vigor_mensalidade, mensalidade, pontualidade, mensalidade_final, id_vendedor, id_etapa, datacadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiddsssddiis", $id_cliente, $licenca, $quantidade_maquinas, $quantidade_celulares, $modulo_apresentado, $inicio_vigor_mensalidade, $mensalidade, $pontualidade, $mensalidade_final, $id_vendedor, $id_etapa, $datacadastro);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao inserir a proposta: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }

    $conn->close();
}

// Consulta para obter a lista de clientes do vendedor logado
$id_vendedor = $_SESSION['idfuncionario'];
$query_clientes = "SELECT id, UPPER(nome) as nome FROM cliente_pipe WHERE id_vendedor = ? ORDER BY nome";
$stmt_clientes = $conn->prepare($query_clientes);
$stmt_clientes->bind_param("i", $id_vendedor);
$stmt_clientes->execute();
$result_clientes = $stmt_clientes->get_result();
$stmt_clientes->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Proposta</title>
    <?php include ('includes/header.php');?>
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
                            <h1 class="m-0">Adicionar Proposta</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Nova Proposta</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="id_cliente">Cliente</label>
                                    <select class="form-control" id="id_cliente" name="id_cliente" required>
                                        <?php while ($row = $result_clientes->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nome']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="licenca">Licença</label>
                                    <input type="number" step="0.01" class="form-control" id="licenca" name="licenca" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade_maquinas">Quantidade de Máquinas</label>
                                    <input type="number" class="form-control" id="quantidade_maquinas" name="quantidade_maquinas" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade_celulares">Quantidade de Celulares</label>
                                    <input type="number" class="form-control" id="quantidade_celulares" name="quantidade_celulares" required>
                                </div>
                                <div class="form-group">
                                    <label for="modulo_apresentado">Módulo Apresentado</label>
                                    <select class="form-control" id="modulo_apresentado" name="modulo_apresentado" required>
                                        <option value="GERENCIAL">GERENCIAL</option>
                                        <option value="EXPERT">EXPERT</option>
                                        <option value="STARTER">STARTER</option>
                                        <option value="FULL">FULL</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="inicio_vigor_mensalidade">Início Vigor Mensalidade</label>
                                    <input type="date" class="form-control" id="inicio_vigor_mensalidade" name="inicio_vigor_mensalidade">
                                </div>
                                <div class="form-group">
                                    <label for="mensalidade">Mensalidade</label>
                                    <input type="number" step="0.01" class="form-control" id="mensalidade" name="mensalidade" required>
                                </div>
                                <div class="form-group">
                                    <label for="pontualidade">Pontualidade</label>
                                    <input type="number" step="0.01" class="form-control" id="pontualidade" name="pontualidade" required>
                                </div>
                                <div class="form-group">
                                    <label for="mensalidade_final">Mensalidade Final</label>
                                    <input type="number" step="0.01" class="form-control" id="mensalidade_final" name="mensalidade_final" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                            </form>
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

    <!-- jQuery -->
    <script src="https://adminlte.io/themes/v3/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://adminlte.io/themes/v3/dist/js/adminlte.min.js"></script>

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
