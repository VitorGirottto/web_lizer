<?php
require '../banco/conexao.php';

session_start();
require '../principal/autenticar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $select_sql = "SELECT id, nome, descricao FROM grupo_familiar WHERE id = :id";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bindValue(':id', $id);
    $select_stmt->execute();

    $grupo = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        echo "Grupo não encontrado!";
        exit;
    }
} else {
    echo "ID do grupo não informado!";
    exit;
}

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    try {
        $update_sql = "UPDATE grupo_familiar SET nome = :nome, descricao = :descricao WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindValue(':nome', $nome);
        $update_stmt->bindValue(':descricao', $descricao);
        $update_stmt->bindValue(':id', $id);

        if ($update_stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = 'Grupo Familiar atualizado com sucesso!';
            header("Location: grupo_familiar.php");
            exit;
        } else {
            $mensagem_erro = 'Erro ao atualizar o grupo familiar.';
        }
    } catch (PDOException $e) {
        $mensagem_erro = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Grupo Familiar</title>
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

<h2>Editar Grupo Familiar</h2>

<a href="grupo_familiar.php" class="button">Voltar para Lista de Grupos Familiares</a>

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
    <label for="nome">Nome:</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($grupo['nome']); ?>" required>

    <label for="descricao">Descrição:</label>
    <textarea name="descricao" required><?php echo htmlspecialchars($grupo['descricao']); ?></textarea>

    <button type="submit" class="button">Atualizar</button>
</form>

</body>
</html>
