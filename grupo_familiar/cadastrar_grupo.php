<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Grupo Familiar</title>
    <script>
        function mostrarMensagemSucesso() {
            const mensagem = document.getElementById("mensagem-sucesso");
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

<?php

require '../banco/conexao.php';

$mensagem_sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO grupo_familiar (nome, descricao) VALUES (:nome, :descricao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Grupo Familiar cadastrado com sucesso!';
    } else {
        echo "<p>Erro ao cadastrar grupo familiar.</p>";
    }
}
?>

<h2>Cadastrar Grupo Familiar</h2>

<a href="grupo_familiar.php" class="button">Voltar para Lista de Grupos Familiares</a>

<form action="" method="post">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" required>

    <label for="descricao">Descrição:</label>
    <textarea name="descricao" required></textarea>

    <button type="submit">Cadastrar</button>
</form>

<?php if ($mensagem_sucesso): ?>
    <div id="mensagem-sucesso">
        <?php echo $mensagem_sucesso; ?>
    </div>
    <script>mostrarMensagemSucesso();</script>
<?php endif; ?>

</body>
</html>
