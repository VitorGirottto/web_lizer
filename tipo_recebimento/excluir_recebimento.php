<?php
session_start();
require '../principal/autenticar.php';

require '../banco/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM tipo_recebimento WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Tipo de Recebimento excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir o Tipo de Recebimento.";
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro: " . $e->getMessage();
    }

    header("Location: tipo_recebimento.php");
    exit();
} else {
    $_SESSION['mensagem_erro'] = "ID não encontrado.";
    header("Location: tipo_recebimento.php");
    exit();
}
?>
