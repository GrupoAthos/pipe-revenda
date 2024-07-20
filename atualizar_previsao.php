<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $previsao_entrega = $_POST['previsao_entrega'];

    // Atualizar a data de previsao_entrega na tabela proposta_historico
    $stmt = $conn->prepare("UPDATE propostas SET previsao_entrega = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $previsao_entrega, $id);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao atualizar previsão de entrega: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de atualização: " . $conn->error;
    }

    $conn->close();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
