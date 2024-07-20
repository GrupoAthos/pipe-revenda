<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cidade = $_POST['cidade'];
    $idrevenda = $_POST['idrevenda'];
    $telefone = $_POST['telefone'];
    $observacao = $_POST['observacao'];
    $id_vendedor = $_SESSION['idfuncionario'];
    $datacadastro = date('Y-m-d'); // Data atual

    // Inserir cliente
    $stmt = $conn->prepare("INSERT INTO cliente_pipe (nome, idrevenda, cidade,telefone, observacao, id_vendedor, datacadastro) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sississ", $nome, $idrevenda, $cidade ,$telefone, $observacao, $id_vendedor, $datacadastro);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao cadastrar cliente: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }

    $conn->close();
}

// Consulta para obter a lista de revendas
$query_revendas = "SELECT idrevenda, nomerevenda FROM revenda ORDER BY nomerevenda";
$result_revendas = $conn->query($query_revendas);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastrar Cliente</title>
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
                            <h1 class="m-0">Cadastrar Cliente</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Novo Cliente</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                <div class="form-group">
                                    <label for="idrevenda">Revenda</label>
                                    <select class="form-control" id="idrevenda" name="idrevenda" required>
                                        <?php while ($row = $result_revendas->fetch_assoc()): ?>
                                            <option value="<?php echo $row['idrevenda']; ?>"><?php echo htmlspecialchars($row['nomerevenda']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade">
                                </div>
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" required>
                                </div>
                                <div class="form-group">
                                    <label for="observacao">Observação</label>
                                    <textarea class="form-control" id="observacao" name="observacao"></textarea>
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
</body>
</html>
