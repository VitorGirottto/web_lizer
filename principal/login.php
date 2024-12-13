<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: principal.php"); 
    exit;
}

$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../banco/conexao.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['username'];
            header("Location: principal.php"); 
            exit;
        } else {
            $mensagem_erro = 'Usuário ou senha inválidos.';
        }
    } else {
        $mensagem_erro = 'Preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
    </style>
</head>
<body>


<?php
if ($mensagem_erro) {
    echo "<div class='erro-message'>" . $mensagem_erro . "</div>";
}
?>

<form action="" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Senha:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Entrar</button>
</form>

</body>
</html>
