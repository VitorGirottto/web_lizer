<?php
require '../banco/conexao.php';

session_start();
require '../principal/autenticar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $select_sql = "SELECT ag.id, c.nome AS cliente, s.observacao AS servico, ag.data_hora, ag.valor, ag.observacao AS observacao, ag.status_id, st.descricao AS status
                   FROM agendamentos ag
                   JOIN clientes c ON ag.cliente_id = c.id
                   JOIN servicos s ON ag.servico_id = s.id
                   JOIN status st ON ag.status_id = st.id
                   WHERE ag.id = :id";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bindValue(':id', $id);
    $select_stmt->execute();

    $agendamento = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        echo "Agendamento não encontrado!";
        exit;
    }

    if ($agendamento['status_id'] == 4 || $agendamento['status_id'] == 5) {
        header("Location: agenda.php?erro=status_nao_editavel");
        exit;
    }

} else {
    echo "ID do agendamento não informado!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_hora = $_POST['data_hora'];
    $valor = $_POST['valor'];
    $observacao = $_POST['observacao'];

    try {
        $update_sql = "UPDATE agendamentos SET data_hora = :data_hora, valor = :valor, observacao = :observacao WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindValue(':data_hora', $data_hora);
        $update_stmt->bindValue(':valor', $valor);
        $update_stmt->bindValue(':observacao', $observacao);
        $update_stmt->bindValue(':id', $id);
        $update_stmt->execute();

        echo "Agendamento atualizado com sucesso!";
        header("Location: agenda.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar o agendamento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <style>
    </style>
</head>
<body>

<h2>Editar Agendamento</h2>

<a href="agenda.php" class="button">Voltar para Lista de Agendamentos</a>

<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'status_nao_editavel') {
    echo "<p style='color: red;'>Você não pode editar um agendamento com status 4 ou 5.</p>";
}
?>

<form method="POST">
    <label for="cliente">Cliente:</label>
    <input type="text" name="cliente" id="cliente" value="<?php echo $agendamento['cliente']; ?>" disabled>

    <label for="servico">Serviço:</label>
    <input type="text" name="servico" id="servico" value="<?php echo $agendamento['servico']; ?>" disabled>

    <label for="data_hora">Data e Hora:</label>
    <input type="datetime-local" name="data_hora" id="data_hora" value="<?php echo date('Y-m-d\TH:i', strtotime($agendamento['data_hora'])); ?>">

    <label for="valor">Valor:</label>
    <input type="text" name="valor" id="valor" value="<?php echo $agendamento['valor']; ?>">

    <label for="observacao">Observação:</label>
    <textarea name="observacao" id="observacao"><?php echo $agendamento['observacao']; ?></textarea>

    <label for="status">Status:</label>
    <input type="text" id="status" value="<?php echo $agendamento['status']; ?>" disabled>

    <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">

    <button type="submit" class="btn-atualizar">Atualizar</button>
</form>

</body>
</html>
