<?php
session_start();
require '../principal/autenticar.php';

if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<div id='mensagem-sucesso'>" . $_SESSION['mensagem_sucesso'] . "</div>";
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    echo "<div id='erro-message'>" . $_SESSION['mensagem_erro'] . "</div>";
    unset($_SESSION['mensagem_erro']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Grupos Familiares</title>
    <style>
    </style>
</head>
<body>

<?php

require '../banco/conexao.php';

$stmt_grupos = $conn->query("SELECT id, nome, descricao FROM grupo_familiar");
$grupos_familiares = $stmt_grupos->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Grupos Familiares Cadastrados</h2>

<div class="button-container">
    <a href="cadastrar_grupo.php" class="button">Cadastrar Novo Grupo Familiar</a>
    <a href="../principal/principal.php" class="button">Voltar</a>
</div>

<div class="barra-pesquisa">
    <input type="text" id="pesquisa" onkeyup="filtrarClientes()" placeholder="Pesquisar por nome...">
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grupos_familiares as $grupo): ?>
            <tr>
                <td><?php echo $grupo['id']; ?></td>
                <td><?php echo htmlspecialchars($grupo['nome']); ?></td>
                <td><?php echo htmlspecialchars($grupo['descricao']); ?></td>
                <td class="acoes">
                    <a href="editar_grupo.php?id=<?php echo $grupo['id']; ?>" class="editar">Alterar</a>
                    <a href="excluir_grupo.php?id=<?php echo $grupo['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este grupo?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function filtrarClientes() {
        const pesquisa = document.getElementById("pesquisa").value.toLowerCase();
        const linhas = document.querySelectorAll("tbody tr");

        linhas.forEach(function(linha) {
            const nomeCliente = linha.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (nomeCliente.includes(pesquisa)) {
                linha.style.display = "";
            } else {
                linha.style.display = "none";
            }
        });
    }
</script>

</body>
</html>
