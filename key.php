<?php
include 'config/db.php';

// Dados do novo usuário
$nome = 'rodolfo';
$nomecompleto = 'Rodolfo Completo'; // Caso queira definir um nome completo
$email = 'rodolfo@example.com'; // Caso queira definir um email
$senha = '98825';
$nivel_acesso = 'user'; // Defina o nível de acesso conforme necessário

// Criptografar a senha
$hashed_senha = password_hash($senha, PASSWORD_DEFAULT);

// Preparar e executar a consulta para inserir o usuário
$stmt = $conn->prepare("INSERT INTO funcionario (nome, nomecompleto, email, senha, nivel_acesso) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sssss", $nome, $nomecompleto, $email, $hashed_senha, $nivel_acesso);

    if ($stmt->execute()) {
        echo "Usuário criado com sucesso.";
    } else {
        echo "Erro ao criar usuário: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Erro ao preparar a consulta: " . $conn->error;
}

$conn->close();
?>
