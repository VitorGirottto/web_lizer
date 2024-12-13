<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendar Serviço</title>
    <script>
        function atualizarValor() {
            const selectServico = document.getElementById("servico_id");
            const inputValor = document.getElementById("valor");

            const valor = selectServico.options[selectServico.selectedIndex].getAttribute("data-valor");

            inputValor.value = valor ? valor : '';
        }

        function mostrarMensagemSucesso() {
            const mensagem = document.getElementById("mensagem-sucesso");
            mensagem.style.display = "block"; 

            setTimeout(function() {
                mensagem.style.display = "none";
            }, 7000);
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
    $cliente_id = $_POST['cliente_id'];
    $data_hora = $_POST['data_hora'];
    $servico_id = $_POST['servico_id'];
    $valor = $_POST['valor'];
    $observacao = $_POST['observacao'];

    $status_id = 1; 

    $sql = "INSERT INTO agendamentos (cliente_id, servico_id, data_hora, valor, status_id, observacao)
            VALUES (:cliente_id, :servico_id, :data_hora, :valor, :status_id, :observacao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cliente_id', $cliente_id);
    $stmt->bindParam(':servico_id', $servico_id);
    $stmt->bindParam(':data_hora', $data_hora);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':status_id', $status_id); 
    $stmt->bindParam(':observacao', $observacao);

    if ($stmt->execute()) {
        $mensagem_sucesso = 'Agendamento realizado com sucesso!';
    } else {
        echo "<p>Erro ao agendar o serviço.</p>";
    }
}

?>

<h2>Agendar Serviço</h2>

<a href="agenda.php" class="button">Voltar para Lista de Agendamentos</a>

<form action="" method="post">
    <label for="cliente_id">Cliente:</label>
    <select name="cliente_id" required>
        <?php
        $stmt = $conn->query("SELECT id, nome FROM clientes");
        while ($cliente = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$cliente['id']}'>{$cliente['nome']}</option>";
        }
        ?>
    </select>

    <label for="data_hora">Data e Hora:</label>
    <input type="datetime-local" name="data_hora" required>

    <label for="servico_id">Serviço:</label>
    <select name="servico_id" id="servico_id" onchange="atualizarValor()" required>
        <option value="">Selecione um serviço</option>
        <?php
        $stmt = $conn->query("SELECT id, valor, observacao FROM servicos");
        while ($servico = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$servico['id']}' data-valor='{$servico['valor']}'>{$servico['observacao']}</option>";
        }
        ?>
    </select>

    <label for="valor">Valor do Serviço:</label>
    <input type="text" name="valor" id="valor">

    <label for="observacao">Observações:</label>
    <textarea name="observacao"></textarea>

    <button type="submit" class="button">Agendar</button>
</form>

<?php if ($mensagem_sucesso): ?>
    <div id="mensagem-sucesso">
        <?php echo $mensagem_sucesso; ?>
    </div>
    <script>mostrarMensagemSucesso();</script>
<?php endif; ?>

</body>
</html>
