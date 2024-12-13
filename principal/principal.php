<?php
session_start();
require '../principal/autenticar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Sistema de Salão de Beleza</title>
    <style>
    </style>
</head>
<body>

<?php

require '../banco/conexao.php';

$hoje = date('Y-m-d');
$sql = "SELECT a.id, c.nome AS cliente, s.observacao AS servico, a.data_hora, a.valor
        FROM agendamentos a
        JOIN clientes c ON a.cliente_id = c.id
        JOIN servicos s ON a.servico_id = s.id
        WHERE DATE(a.data_hora) = :hoje
        ORDER BY a.data_hora";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':hoje', $hoje);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-left">
    <a href="../agenda/agenda.php" class="button">
        <span>Ver Agendamentos</span>
    </a>
    <a href="../cliente/clientes.php" class="button">
        <span>Clientes</span>
    </a>
    <a href="../grupo_familiar/grupo_familiar.php" class="button">
        <span>Grupo Familiar</span>
    </a>
    <a href="../servico/servico.php" class="button">
        <span>Serviços</span>
    </a>
    <a href="../recebimento/recebimento.php" class="button">
        <span>Recebimentos</span>
    </a>
    <a href="../tipo_recebimento/tipo_recebimento.php" class="button">
        <span>Tipo de Recebimento</span>
    </a>
    <a href="../login/login.php" class="button">
        <span>Usuários</span>
    <a href="logout.php" class="button">
        <span>Logout</span>
    </a>
</div>

<div class="content-right">
    <h2>Agendamentos do Dia</h2>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Data e Hora</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($agendamentos) > 0): ?>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($agendamento['cliente']); ?></td>
                        <td><?php echo htmlspecialchars($agendamento['servico']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($agendamento['data_hora'])); ?></td>
                        <td>R$ <?php echo number_format($agendamento['valor'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhum agendamento para hoje</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
