<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Serviço</title>
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
    $valor = $_POST['valor'];
    $observacao = $_POST['observacao'];

    $sql = "INSERT INTO servicos (valor, observacao) VALUES (:valor, :observacao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':observacao', $observacao);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Serviço cadastrado com sucesso!';
    } else {
        echo "<p>Erro ao cadastrar serviço.</p>";
    }
}
?>

<h2>Cadastrar Serviço</h2>

<a href="servico.php" class="button">Voltar para Lista de Serviços</a>

<form action="" method="post">
    <label for="valor">Valor:</label>
    <input type="number" name="valor" step="0.01" required>

    <label for="observacao">Observação:</label>
    <textarea name="observacao" required></textarea>

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
