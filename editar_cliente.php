<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$idfuncionario = $_SESSION['idfuncionario'];
$id_cliente = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nome = $_POST['nome'];
    $cidade = $_POST['cidade'];
    $idrevenda = $_POST['idrevenda'];

    $stmt = $conn->prepare("UPDATE cliente_pipe SET nome = ?, cidade = ?, idrevenda = ? WHERE id = ?");
    $stmt->bind_param("ssii", $nome, $cidade, $idrevenda, $id_cliente);

    if ($stmt->execute()) {
        header("Location: cliente.php");
        exit();
    } else {
        echo "Erro ao atualizar cliente: " . $stmt->error;
    }

    $stmt->close();
}

$stmt = $conn->prepare("SELECT UPPER(nome) as nome, cidade, idrevenda FROM cliente_pipe WHERE id = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$stmt->bind_result($nome, $cidade, $idrevenda);
$stmt->fetch();
$stmt->close();

// Consulta para obter as revendas
$query_revendas = "SELECT idrevenda, nomerevenda FROM revenda";
$result_revendas = $conn->query($query_revendas);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'includes/navbar.php'; ?>
        <?php include 'menu.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Editar Cliente</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Informações do Cliente</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $nome; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo $cidade; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="idrevenda">Revenda</label>
                                    <select class="form-control" id="idrevenda" name="idrevenda" required>
                                        <?php while ($revenda = $result_revendas->fetch_assoc()): ?>
                                            <option value="<?php echo $revenda['idrevenda']; ?>" <?php echo ($idrevenda == $revenda['idrevenda']) ? 'selected' : ''; ?>>
                                                <?php echo $revenda['nomerevenda']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <input type="hidden" name="id_cliente" value="<?php echo $id_cliente; ?>">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="cliente.php" class="btn btn-secondary">Cancelar</a>
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>
