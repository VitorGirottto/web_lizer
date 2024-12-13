<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Serviço</title>
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

if (isset($_GET['id'])) {
    $id_servico = $_GET['id'];

    $stmt = $conn->prepare("SELECT id, valor FROM servicos WHERE id = :id");
    $stmt->bindParam(':id', $id_servico, PDO::PARAM_INT);
    $stmt->execute();
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servico) {
        echo "<p>Erro: Serviço não encontrado.</p>";
        exit;
    }
} else {
    echo "<p>Erro: ID de serviço não fornecido.</p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST['valor'];

    $sql = "UPDATE servicos SET valor = :valor WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':id', $id_servico, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Serviço atualizado com sucesso!';
    } else {
        echo "<p>Erro ao atualizar serviço.</p>";
    }
}
?>

<h2>Editar Serviço</h2>

<a href="servico.php" class="button">Voltar para Lista de Serviços</a>

<form action="" method="post">
    <label for="valor">Valor:</label>
    <input type="number" name="valor" step="0.01" value="<?php echo htmlspecialchars($servico['valor']); ?>" required>

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
