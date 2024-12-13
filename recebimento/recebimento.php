<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Recebimentos</title>
    <script>
        function filtrarRecebimentos() {
            const pesquisaCliente = document.getElementById("pesquisa-cliente").value.toLowerCase();
            const dataInicio = document.getElementById("data-inicio").value;
            const dataFim = document.getElementById("data-fim").value;
            const linhas = document.querySelectorAll(".linha-recebimento");

            let total = 0;

            linhas.forEach(function(linha) {
                const clienteNome = linha.querySelector(".cliente-nome").textContent.toLowerCase();
                const dataRecebimento = linha.querySelector(".data-recebimento").textContent;
                const valorRecebimento = parseFloat(linha.querySelector(".valor-recebimento").textContent.replace("R$", "").replace(",", "."));

                let mostrar = true;

                if (pesquisaCliente && !clienteNome.includes(pesquisaCliente)) {
                    mostrar = false;
                }

                if (dataInicio && dataRecebimento < dataInicio) {
                    mostrar = false;
                }
                if (dataFim && dataRecebimento > dataFim) {
                    mostrar = false;
                }

                if (mostrar) {
                    linha.style.display = "";
                    total += valorRecebimento;
                } else {
                    linha.style.display = "none";
                }
            });

            document.getElementById("valor-total").textContent = "R$ " + total.toFixed(2).replace(".", ",");
        }

        window.onload = function() {
            filtrarRecebimentos();
        }
    </script>
    <style>
    </style>
</head>
<body>

<?php

require '../banco/conexao.php';

$stmt_clientes = $conn->query("SELECT id, nome FROM clientes");
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT r.id, r.valor, r.data, r.descricao, r.agendamento_id, c.nome AS cliente_nome
        FROM recebimentos r
        JOIN clientes c ON r.cliente_id = c.id
        ORDER BY r.data DESC";
$stmt = $conn->query($sql);
$recebimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Lista de Recebimentos</h2>

<a href="javascript:history.back()" class="button">Voltar</a>

<div class="filtro-cliente">
    <input type="text" id="pesquisa-cliente" onkeyup="filtrarRecebimentos()" placeholder="Pesquisar por cliente...">
</div>

<div class="filtro-periodo">
    <input type="date" id="data-inicio" onchange="filtrarRecebimentos()">
    <input type="date" id="data-fim" onchange="filtrarRecebimentos()">
</div>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Data</th>
            <th>ID do Agendamento</th> 
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recebimentos as $recebimento): ?>
            <tr class="linha-recebimento">
                <td class="cliente-nome"><?php echo htmlspecialchars($recebimento['cliente_nome']); ?></td>
                <td><?php echo htmlspecialchars($recebimento['descricao']); ?></td>
                <td class="valor-recebimento">R$ <?php echo number_format($recebimento['valor'], 2, ',', '.'); ?></td>
                <td class="data-recebimento"><?php echo htmlspecialchars($recebimento['data']); ?></td>
                <td class="agendamento-id"><?php echo htmlspecialchars($recebimento['agendamento_id']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="valor-total">R$ 0,00</div>

</body>
</html>
