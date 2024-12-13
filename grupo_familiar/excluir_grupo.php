<?php
session_start();
require '../principal/autenticar.php';
require '../banco/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM grupo_familiar WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Grupo familiar excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir o grupo familiar.";
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao excluir: " . $e->getMessage();
    }
    header("Location: grupo_familiar.php");
    exit;
} else {
    header("Location: grupo_familiar.php");
    exit;
}
