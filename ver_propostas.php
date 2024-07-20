<?php
include 'config/db.php';

$id_cliente = $_GET['id'] ?? 0;

$query = "SELECT id, descricao, datacadastro, licenca, mensalidade_final 
          FROM propostas 
          WHERE id_cliente = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['descricao']) . '</td>
                <td>' . date('d/m/Y', strtotime($row['datacadastro'])) . '</td>
                <td>' . htmlspecialchars($row['licenca']) . '</td>
                <td>' . htmlspecialchars($row['mensalidade_final']) . '</td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="5" class="text-center">Nenhuma proposta encontrada para este cliente.</td></tr>';
}

$stmt->close();
$conn->close();
?>
