<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$idfuncionario = $_SESSION['idfuncionario'];

// Consulta para obter clientes cadastrados
$query = "SELECT c.id, c.nome AS nome_cliente, r.nomerevenda, c.cidade, 
                 (SELECT COUNT(*) FROM propostas p WHERE p.id_cliente = c.id) AS total_propostas
          FROM cliente_pipe c
          JOIN revenda r ON c.idrevenda = r.idrevenda
          WHERE c.id_vendedor = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idfuncionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes Cadastrados</title>
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
                            <h1 class="m-0">Clientes Cadastrados</h1>
                        </div>
                        <div class="col-sm-6">
                            <a href="cadastrar_cliente.php" class="btn btn-success float-right"><i class="fas fa-user-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Clientes</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Revenda</th>
                                            <th>Cidade</th>
                                            <th>Total de Propostas</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(strtoupper($row['nome_cliente'])); ?></td>
                                                <td><?php echo htmlspecialchars(strtoupper($row['nomerevenda'])); ?></td>
                                                <td><?php echo htmlspecialchars(strtoupper($row['cidade'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['total_propostas']); ?></td>
                                                <td>
                                                    <a href="editar_cliente.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalPropostas" data-id="<?php echo $row['id']; ?>"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Modal para visualizar propostas -->
        <div class="modal fade" id="modalPropostas" tabindex="-1" role="dialog" aria-labelledby="modalPropostasLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPropostasLabel">Propostas do Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Data Cadastro</th>
                                        <th>Licença</th>
                                        <th>Mensalidade Final</th>
                                    </tr>
                                </thead>
                                <tbody id="propostasCliente">
                                    <!-- Propostas serão carregadas via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

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
        $('#modalPropostas').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idCliente = button.data('id');
            var modal = $(this);

            $.ajax({
                url: 'ver_propostas.php',
                method: 'GET',
                data: { id: idCliente },
                success: function (data) {
                    modal.find('#propostasCliente').html(data);
                },
                error: function () {
                    modal.find('#propostasCliente').html('<tr><td colspan="5" class="text-danger">Erro ao carregar propostas.</td></tr>');
                }
            });
        });
    </script>
</body>
</html>
