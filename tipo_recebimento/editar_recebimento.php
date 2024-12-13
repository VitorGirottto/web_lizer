<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Tipo de Recebimento</title>
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
$id = $_GET['id'];

$sql = "SELECT * FROM tipo_recebimento WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$tipo_recebimento = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = $_POST['descricao'];  

    $sql = "UPDATE tipo_recebimento SET descricao = :descricao WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Tipo de Recebimento atualizado com sucesso!';
    } else {
        echo "<p>Erro ao atualizar tipo de recebimento.</p>";
    }
}

?>

<h2>Editar Tipo de Recebimento</h2>

<a href="tipo_recebimento.php" class="button">Voltar para Lista de Tipos de Recebimento</a>

<form action="" method="post">
    <label for="descricao">Descrição:</label>
    <input type="text" name="descricao" value="<?php echo htmlspecialchars($tipo_recebimento['descricao']); ?>" required>

    <button type="submit">Atualizar</button>
</form>

<?php if ($mensagem_sucesso): ?>
    <div id="mensagem-sucesso">
        <?php echo $mensagem_sucesso; ?>
    </div>
    <script>mostrarMensagemSucesso();</script>
<?php endif; ?>

</body>
</html>
