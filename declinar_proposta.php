<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_proposta = $_POST['id_proposta'];
    $justificativa = $_POST['justificativa'];

    // Primeiro, obtenha a etapa atual da proposta
    $stmt = $conn->prepare("SELECT e.nome AS etapa_atual FROM propostas p JOIN etapa_pipe e ON p.id_etapa = e.id WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_proposta);
        $stmt->execute();
        $stmt->bind_result($etapa_atual);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de obtenção da etapa atual: " . $conn->error;
        exit();
    }

    // Atualize a etapa da proposta
    $stmt = $conn->prepare("UPDATE propostas SET id_etapa = (SELECT id FROM etapa_pipe WHERE nome = 'declinou') WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_proposta);

        if ($stmt->execute()) {
            // Insira o histórico da proposta
            $stmt = $conn->prepare("INSERT INTO proposta_historico (id_proposta, observacao, etapa_atual, proxima_etapa, data) VALUES (?, ?, ?, 'declinou', NOW())");
            if ($stmt) {
                $stmt->bind_param("iss", $id_proposta, $justificativa, $etapa_atual);

                if ($stmt->execute()) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Erro ao salvar a justificativa: " . $stmt->error;
                }
            } else {
                echo "Erro ao preparar a consulta para histórico: " . $conn->error;
            }
        } else {
            echo "Erro ao declinar a proposta: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de atualização da etapa: " . $conn->error;
    }

    $conn->close();
} else {
    $id_proposta = $_GET['id'];
    $stmt = $conn->prepare("SELECT c.nome AS nome_cliente, p.descricao FROM propostas p JOIN cliente_pipe c ON p.id_cliente = c.id WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_proposta);
        $stmt->execute();
        $stmt->bind_result($nome_cliente, $descricao);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de detalhes da proposta: " . $conn->error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Declinar Proposta</title>
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
                            <h1 class="m-0">Declinar Proposta</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-danger text-white">Detalhes da Proposta</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($nome_cliente); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <textarea class="form-control" disabled><?php echo htmlspecialchars($descricao); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="justificativa">Justificativa</label>
                                    <textarea class="form-control" id="justificativa" name="justificativa" required></textarea>
                                </div>
                                <input type="hidden" name="id_proposta" value="<?php echo $id_proposta; ?>">
                                <button type="submit" class="btn btn-danger">Declinar</button>
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
