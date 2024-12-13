<?php
require '../banco/conexao.php';
require '../principal/autenticar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $select_sql = "SELECT status_id FROM agendamentos WHERE id = :id";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bindValue(':id', $id);
        $select_stmt->execute();
        $agendamento = $select_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agendamento) {
            echo "Agendamento não encontrado!";
            exit;
        }

        if ($agendamento['status_id'] == 4 || $agendamento['status_id'] == 5) {
            header("Location: agenda.php?erro=status_nao_excluivel");
            exit;
        }

        $conn->beginTransaction();

        $delete_recebimento_sql = "DELETE FROM recebimentos WHERE agendamento_id = :agendamento_id";
        $delete_recebimento_stmt = $conn->prepare($delete_recebimento_sql);
        $delete_recebimento_stmt->bindValue(':agendamento_id', $id);
        $delete_recebimento_stmt->execute();

        $delete_agendamento_sql = "DELETE FROM agendamentos WHERE id = :id";
        $delete_agendamento_stmt = $conn->prepare($delete_agendamento_sql);
        $delete_agendamento_stmt->bindValue(':id', $id);
        $delete_agendamento_stmt->execute();

        $conn->commit();

        header("Location: agenda.php");
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Erro ao excluir o agendamento: " . $e->getMessage();
    }
} else {
    echo "ID do agendamento não fornecido!";
}
?>
