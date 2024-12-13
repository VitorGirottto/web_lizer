<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cliente</title>
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

$stmt_grupos = $conn->query("SELECT id, nome FROM grupo_familiar");
$grupos_familiares = $stmt_grupos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $data_nascimento = $_POST['data_nascimento'];
    $grupo_familiar_id = $_POST['grupo_familiar_id'] ?: NULL; 

    if ($grupo_familiar_id) {
        $stmt_check_grupo = $conn->prepare("SELECT COUNT(*) FROM grupo_familiar WHERE id = :grupo_familiar_id");
        $stmt_check_grupo->bindParam(':grupo_familiar_id', $grupo_familiar_id);
        $stmt_check_grupo->execute();
        $grupo_existente = $stmt_check_grupo->fetchColumn();
        
        if (!$grupo_existente) {
            echo "<p>Erro: O grupo familiar selecionado não existe.</p>";
            exit;
        }
    }

    $sql = "INSERT INTO clientes (nome, telefone, email, endereco, data_nascimento, grupo_familiar_id)
            VALUES (:nome, :telefone, :email, :endereco, :data_nascimento, :grupo_familiar_id)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':data_nascimento', $data_nascimento);
    $stmt->bindParam(':grupo_familiar_id', $grupo_familiar_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Cliente cadastrado com sucesso!';
    } else {
        echo "<p>Erro ao cadastrar cliente.</p>";
    }
}
?>

<h2>Cadastrar Cliente</h2>

<a href="clientes.php" class="button">Voltar para Lista de Clientes</a>

<form action="" method="post">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" required>

    <label for="telefone">Telefone:</label>
    <input type="tel" name="telefone">

    <label for="email">E-mail:</label>
    <input type="email" name="email">

    <label for="endereco">Endereço:</label>
    <textarea name="endereco"></textarea>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" name="data_nascimento">

    <label for="grupo_familiar_id">Grupo Familiar:</label>
    <select name="grupo_familiar_id">
        <option value="">Selecione um grupo familiar (opcional)</option>
        <?php foreach ($grupos_familiares as $grupo): ?>
            <option value="<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nome']); ?></option>
        <?php endforeach; ?>
    </select>

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
