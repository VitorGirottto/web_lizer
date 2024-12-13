<?php
require '../banco/conexao.php';
session_start();
require '../principal/autenticar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $usuario['password'];

        $update_stmt = $conn->prepare("UPDATE usuarios SET username = :username, password = :password WHERE id = :id");
        $update_stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':id' => $id
        ]);

        $_SESSION['mensagem_sucesso'] = "Usuário atualizado com sucesso!";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
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

<h2>Editar Usuário</h2>

<a href="login.php" class="button">Voltar para Lista de Usuários</a>

<?php
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<div id='mensagem-sucesso'>" . $_SESSION['mensagem_sucesso'] . "</div>";
    echo "<script>mostrarMensagemSucesso();</script>";
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    echo "<div id='erro-message'>" . $_SESSION['mensagem_erro'] . "</div>";
    echo "<script>mostrarMensagemErro();</script>";
    unset($_SESSION['mensagem_erro']);
}
?>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>

    <label for="password">Nova Senha:</label>
    <input type="password" name="password" id="password">

    <button type="submit">Atualizar</button>
</form>

</body>
</html>
