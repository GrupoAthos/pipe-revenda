<?php
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function verifysenha($senha, $hashed_senha) {
    return password_verify($senha, $hashed_senha);
}
?>
