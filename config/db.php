<?php
$servername = "srv1186.hstgr.io";
$username = "u453529070_taubate";
$password = "[9Txlqaye";
$database = "u453529070_taubate";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Define o charset para evitar problemas de segurança e compatibilidade
?>
