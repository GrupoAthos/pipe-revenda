<?php
session_start();
include 'db.php';

if (!isset($_SESSION['idfuncionario'])) {
    header("Location: login.php");
    exit();
}

$id_proposta = $_GET['id'];

// Consulta para obter o histórico da proposta
$query = "SELECT campo, valor_antigo, valor_novo, data_alteracao FROM historico_propostas WHERE id_proposta = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_proposta);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Histórico da Proposta</title>
    <?php include ('includes/header.php');?>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Histórico da Proposta</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Valor Antigo</th>
                    <th>Valor Novo</th>
                    <th>Data de Alteração</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['campo']); ?></td>
                        <td><?php echo htmlspecialchars($row['valor_antigo']); ?></td>
                        <td><?php echo htmlspecialchars($row['valor_novo']); ?></td>
                        <td><?php echo htmlspecialchars($row['data_alteracao']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
</body>
</html>
