<?php
require '../banco/conexao.php';
session_start();
require '../principal/autenticar.php';

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username && $password) {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insert_sql = "INSERT INTO usuarios (username, password) VALUES (:username, :password)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindValue(':username', $username);
            $insert_stmt->bindValue(':password', $passwordHash);

            if ($insert_stmt->execute()) {
                $_SESSION['mensagem_sucesso'] = 'Usuário criado com sucesso!';
                header("Location: login.php"); 
                exit;
            } else {
                $mensagem_erro = 'Erro ao criar o usuário.';
            }
        } catch (PDOException $e) {
            $mensagem_erro = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
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
    <title>Criar Usuário</title>
    <script>
        function mostrarMensagemSucesso() {
            const mensagem = document.getElementById("mensagem-sucesso");
            mensagem.style.display = "block";
            setTimeout(function() {
                mensagem.style.display = "none";
            }, 5000);
        }

        function mostrarMensagemErro() {
            const mensagem = document.getElementById("erro-message");
            mensagem.style.display = "block";
            setTimeout(function() {
                mensagem.style.display = "none";
            }, 5000);
        }
    </script>
    <style>
    </style>
</head>
<body>

<h2>Criar Usuário</h2>

<a href="login.php" class="button">Voltar para Login</a>

<?php
if ($mensagem_sucesso) {
    echo "<div id='mensagem-sucesso'>" . $mensagem_sucesso . "</div>";
    echo "<script>mostrarMensagemSucesso();</script>";
}

if ($mensagem_erro) {
    echo "<div id='erro-message'>" . $mensagem_erro . "</div>";
    echo "<script>mostrarMensagemErro();</script>";
}
?>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Senha:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Criar</button>
</form>

</body>
</html>
