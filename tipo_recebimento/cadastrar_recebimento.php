<?php
session_start();
require '../principal/autenticar.php';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Tipo de Recebimento</title>
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
    $descricao = $_POST['descricao'];  

    $sql = "INSERT INTO tipo_recebimento (descricao) VALUES (:descricao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':descricao', $descricao);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Tipo de Recebimento cadastrado com sucesso!';
    } else {
        echo "<p>Erro ao cadastrar tipo de recebimento.</p>";
    }
}

?>

<h2>Cadastrar Tipo de Recebimento</h2>

<a href="tipo_recebimento.php" class="button">Voltar para Lista de Tipos de Recebimento</a>

<form action="" method="post">
    <label for="descricao">Descrição:</label>
    <input type="text" name="descricao" required>

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
