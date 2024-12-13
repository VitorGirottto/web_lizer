<?php
require '../banco/conexao.php';
session_start();
require '../principal/autenticar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['mensagem_sucesso'] = "Usuário excluído com sucesso!";
    header("Location: login.php");
    exit;
} else {
    $_SESSION['mensagem_erro'] = "Usuário não encontrado.";
    header("Location: login.php");
    exit;
}
