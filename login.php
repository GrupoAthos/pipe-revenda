<?php
session_start();
require 'config/db.php';
require 'includes/functions.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = sanitizeInput($_POST['nome']);
    $senha = sanitizeInput($_POST['senha']);

    $sql = "SELECT idfuncionario, senha,nomecompleto FROM funcionario WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_senha,$nomecompleto);
        $stmt->fetch();

        if (verifysenha($senha, $hashed_senha)) {
            $_SESSION['idfuncionario'] = $id;
            $_SESSION['nomecompleto'] = $nomecompleto; // Adicionando nome completo à sessão
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Senha inválida.";
        }
    } else {
        $error = "Nenhum usuário encontrado com esse nome.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-image: url('https://static.vecteezy.com/ti/vetor-gratis/p3/3161225-fundo-branco-e-azul-abstrato-vetor.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            overflow: hidden;
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
        }
        .login-logo {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Pipe</b>Vendas
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Digite login e senha para entrar.</p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="nome" class="form-control" placeholder="nome" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="senha" class="form-control" placeholder="senha" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Logar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://adminlte.io/themes/v3/plugins/jquery/jquery.min.js"></script>
<script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://adminlte.io/themes/v3/dist/js/adminlte.min.js"></script>
</body>
</html>