<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_proposta = $_POST['id_proposta'];
    $proxima_etapa = $_POST['proxima_etapa'];
    $observacao = $_POST['observacao'];
    $previsao_entrega = $_POST['previsao_entrega'];

    // Primeiro, obtenha a etapa atual da proposta
    $stmt = $conn->prepare("SELECT e.nome AS etapa_atual, e.ordem AS ordem_atual FROM propostas p JOIN etapa_pipe e ON p.id_etapa = e.id WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_proposta);
        $stmt->execute();
        $stmt->bind_result($etapa_atual, $ordem_atual);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de obtenção da etapa atual: " . $conn->error;
        exit();
    }

    // Atualize a etapa da proposta e a previsão de entrega
    $stmt = $conn->prepare("UPDATE propostas SET id_etapa = (SELECT id FROM etapa_pipe WHERE nome = ?), previsao_entrega = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $proxima_etapa, $previsao_entrega, $id_proposta);

        if ($stmt->execute()) {
            // Insira o histórico da proposta
            $stmt = $conn->prepare("INSERT INTO proposta_historico (id_proposta, observacao, etapa_atual, proxima_etapa, data, previsao_entrega) VALUES (?, ?, ?, ?, NOW(), ?)");
            if ($stmt) {
                $stmt->bind_param("issss", $id_proposta, $observacao, $etapa_atual, $proxima_etapa, $previsao_entrega);

                if ($stmt->execute()) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Erro ao salvar a alteração de etapa: " . $stmt->error;
                }
            } else {
                echo "Erro ao preparar a consulta para histórico: " . $conn->error;
            }
        } else {
            echo "Erro ao alterar a etapa: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de atualização da etapa: " . $conn->error;
    }

    $conn->close();
} else {
    $id_proposta = $_GET['id'];
    $stmt = $conn->prepare("SELECT c.nome AS nome_cliente, p.descricao, p.id_etapa FROM propostas p JOIN cliente_pipe c ON p.id_cliente = c.id WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_proposta);
        $stmt->execute();
        $stmt->bind_result($nome_cliente, $descricao, $id_etapa_atual);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de detalhes da proposta: " . $conn->error;
        exit();
    }

    // Consulta para obter a próxima etapa, excluindo a etapa atual e "Declinou"
    $query_etapas = "SELECT * FROM etapa_pipe WHERE id != ? AND nome != 'Declinou' AND ordem > (SELECT ordem FROM etapa_pipe WHERE id = ?) ORDER BY ordem LIMIT 1";
    $stmt_etapas = $conn->prepare($query_etapas);
    if ($stmt_etapas) {
        $stmt_etapas->bind_param("ii", $id_etapa_atual, $id_etapa_atual);
        $stmt_etapas->execute();
        $result_etapas = $stmt_etapas->get_result();
    } else {
        echo "Erro ao preparar a consulta de etapas: " . $conn->error;
        exit();
    }

    // Calcular a data de previsão de entrega (data atual + 7 dias)
    $previsao_entrega = date('Y-m-d', strtotime('+7 days'));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alterar Etapa</title>
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
                            <h1 class="m-0">Alterar Etapa</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-primary text-white">Detalhes da Proposta</div>
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
                                    <label for="proxima_etapa">Próxima Etapa</label>
                                    <select class="form-control" id="proxima_etapa" name="proxima_etapa" required>
                                        <?php while ($etapa = $result_etapas->fetch_assoc()): ?>
                                            <option value="<?php echo $etapa['nome']; ?>"><?php echo strtoupper($etapa['nome']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="previsao_entrega">Previsão de Entrega</label>
                                    <input type="date" class="form-control" id="previsao_entrega" name="previsao_entrega" value="<?php echo $previsao_entrega; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="observacao">Observação</label>
                                    <textarea class="form-control" id="observacao" name="observacao" required></textarea>
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
                            $stmt_hist = $conn->prepare("SELECT observacao, data FROM proposta_historico WHERE id_proposta = ? ORDER BY data DESC");
                            if ($stmt_hist) {
                                $stmt_hist->bind_param("i", $id_proposta);
                                $stmt_hist->execute();
                                $result_hist = $stmt_hist->get_result();

                                if ($result_hist->num_rows > 0) {
                                    echo '<table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Observação</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                    while ($row_hist = $result_hist->fetch_assoc()) {
                                        $data_brasil = date('d/m/Y', strtotime($row_hist['data']));
                                        echo '<tr>
                                                <td>' . htmlspecialchars($row_hist['observacao']) . '</td>
                                                <td>' . htmlspecialchars($data_brasil) . '</td>
                                              </tr>';
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<p>Nenhuma alteração registrada.</p>';
                                }
                                $stmt_hist->close();
                            } else {
                                echo "Erro ao preparar a consulta de histórico: " . $conn->error;
                            }
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
</body>
</html>
