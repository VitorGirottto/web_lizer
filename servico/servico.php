<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Serviços</title>
    <script>
        function filtrarServicos() {
            const pesquisa = document.getElementById("pesquisa").value.toLowerCase();
            const linhas = document.querySelectorAll(".linha-servico");

            linhas.forEach(function(linha) {
                const observacaoServico = linha.querySelector(".observacao-servico").textContent.toLowerCase();
                if (observacaoServico.includes(pesquisa)) {
                    linha.style.display = "";
                } else {
                    linha.style.display = "none";
                }
            });
        }
    </script>
    <style>
    </style>
</head>
<body>

<?php

require '../banco/conexao.php';

if (isset($_GET['excluir_id'])) {
    $excluir_id = $_GET['excluir_id'];

    $sql = "DELETE FROM servicos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $excluir_id);

    if ($stmt->execute()) {
        echo "<p>Serviço excluído com sucesso!</p>";
        header("Location: servico.php");
    } else {
        echo "<p>Erro ao excluir serviço.</p>";
    }
}

$stmt = $conn->query("SELECT id, valor, observacao FROM servicos ORDER BY id ASC");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Lista de Serviços</h2>

<div class="button-container">
    <a href="cadastrar_servico.php" class="button">Cadastrar Novo Serviço</a>
    <a href="../principal/principal.php" class="button">Voltar</a>
</div>

<div class="barra-pesquisa">
    <input type="text" id="pesquisa" onkeyup="filtrarServicos()" placeholder="Pesquisar por observação...">
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Valor</th>
            <th>Observação</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($servicos as $servico): ?>
            <tr class="linha-servico">
                <td><?php echo htmlspecialchars($servico['id']); ?></td>
                <td><?php echo number_format($servico['valor'], 2, ',', '.'); ?></td>
                <td class="observacao-servico"><?php echo htmlspecialchars($servico['observacao']); ?></td>
                <td class="acoes">
                    <a href="editar_servico.php?id=<?php echo $servico['id']; ?>" class="editar">Editar</a>
                    <a href="?excluir_id=<?php echo $servico['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este serviço?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
