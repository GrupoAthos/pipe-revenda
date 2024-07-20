<?php
include 'config/db.php';

$query_etapas = "SELECT * FROM etapa_pipe ORDER BY ordem";
$result_etapas = $conn->query($query_etapas);

$options = '';
while ($etapa = $result_etapas->fetch_assoc()) {
    $options .= '<option value="' . $etapa['id'] . '">' . strtoupper($etapa['nome']) . '</option>';
}

echo $options;

$conn->close();
?>
