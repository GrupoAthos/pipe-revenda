<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$idfuncionario = $_SESSION['idfuncionario'];

// Consulta para obter clientes com previsao_entrega vencida
$query = "SELECT p.id, r.nomerevenda, c.nome AS nome_cliente, p.previsao_entrega, p.licenca, p.modulo_apresentado, p.mensalidade_final, p.id_etapa, e.nome AS etapa_atual, p.datacadastro
          FROM propostas p 
          JOIN cliente_pipe c ON p.id_cliente = c.id 
          JOIN etapa_pipe e ON p.id_etapa = e.id 
          JOIN revenda r ON c.idrevenda = r.idrevenda
          WHERE p.id_vendedor = ? AND not e.id in  (5,6) AND previsao_entrega < NOW()";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idfuncionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notificações de Previsão de Entrega</title>
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
                            <h1 class="m-0">Notificações de Previsão de Entrega</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Previsão Pendente</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Revenda</th>
                                            <th>Licença</th>
                                            <th>Módulo</th>
                                            <th>Mensalidade Final</th>
                                            <th>Previsão de Entrega</th>
                                            <th>Etapa Atual</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(strtoupper($row['nome_cliente'])); ?></td>
                                                <td><?php echo htmlspecialchars(strtoupper($row['nomerevenda'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['licenca']); ?></td>
                                                <td><?php echo htmlspecialchars($row['modulo_apresentado']); ?></td>
                                                <td><?php echo htmlspecialchars($row['mensalidade_final']); ?></td>
                                                <td>
                                                    <?php if (is_null($row['previsao_entrega'])): ?>
                                                        <span style="color: red;">Atenção</span>
                                                    <?php else: ?>
                                                        <?php echo date('d/m/Y', strtotime($row['previsao_entrega'])); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['etapa_atual']); ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPrevisao" data-id="<?php echo $row['id']; ?>" data-previsao="<?php echo $row['previsao_entrega']; ?>">Atualizar Previsão</button>
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

        <!-- Modal para Atualizar Previsão de Entrega -->
        <div class="modal fade" id="modalPrevisao" tabindex="-1" role="dialog" aria-labelledby="modalPrevisaoLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPrevisaoLabel">Atualizar Previsão de Entrega</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="atualizar_previsao.php">
                        <div class="modal-body">
                            <input type="text" name="id" id="modalIdProposta">
                            <div class="form-group">
                                <label for="previsao_entrega">Nova Previsão de Entrega</label>
                                <input type="date" class="form-control" id="previsao_entrega" name="previsao_entrega" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
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
        $('#modalPrevisao').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var previsao = button.data('previsao');
            var modal = $(this);
            modal.find('#modalIdProposta').val(id);
            modal.find('#previsao_entrega').val(previsao);
        });
    </script>
</body>
</html>
