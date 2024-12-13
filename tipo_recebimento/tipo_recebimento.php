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
    <title>Tipos de Recebimento</title>
    <style>
    </style>
</head>
<body>

<?php

require '../banco/conexao.php';

$stmt_recebimentos = $conn->query("SELECT id, descricao FROM tipo_recebimento");
$tipos_recebimento = $stmt_recebimentos->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Tipos de Recebimento Cadastrados</h2>

<div class="button-container">
    <a href="cadastrar_recebimento.php" class="button">Cadastrar Novo Tipo de Recebimento</a>
    <a href="../principal/principal.php" class="button">Voltar</a>
</div>

<div class="barra-pesquisa">
    <input type="text" id="pesquisa" onkeyup="filtrarTiposRecebimento()" placeholder="Pesquisar por nome...">
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tipos_recebimento as $tipo): ?>
            <tr>
                <td><?php echo $tipo['id']; ?></td>
                <td><?php echo htmlspecialchars($tipo['descricao']); ?></td>
                <td class="acoes">
                    <a href="editar_recebimento.php?id=<?php echo $tipo['id']; ?>" class="editar">Alterar</a>
                    <a href="excluir_recebimento.php?id=<?php echo $tipo['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este tipo de recebimento?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function filtrarTiposRecebimento() {
        const pesquisa = document.getElementById("pesquisa").value.toLowerCase();
        const linhas = document.querySelectorAll("tbody tr");

        linhas.forEach(function(linha) {
            const descricao = linha.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (descricao.includes(pesquisa)) {
                linha.style.display = "";
            } else {
                linha.style.display = "none";
            }
        });
    }
</script>

</body>
</html>
