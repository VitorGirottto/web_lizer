<?php
require '../banco/conexao.php';

session_start();
require '../principal/autenticar.php';

if (isset($_GET['erro']) && $_GET['erro'] == 'status_nao_editavel') {
    echo "<div id='erro-message'>Você não pode editar um agendamento recebido.</div>";
}

if (isset($_GET['erro']) && $_GET['erro'] == 'status_nao_excluivel') {
    echo "<div id='erro-message'>Você não pode excluir um agendamento recebido.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id = $_POST['status'];
    $id = $_POST['id'];

    try {
               $select_sql = "SELECT status_id FROM agendamentos WHERE id = :id";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bindValue(':id', $id);
        $select_stmt->execute();
        $agendamento = $select_stmt->fetch(PDO::FETCH_ASSOC);

        if (($agendamento['status_id'] == 4 || $agendamento['status_id'] == 5) && ($status_id != 4 && $status_id != 5)) {
            $delete_sql = "DELETE FROM recebimentos WHERE agendamento_id = :agendamento_id";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bindValue(':agendamento_id', $id);
            $delete_stmt->execute();
        } 
        else if (($agendamento['status_id'] == 4 && $status_id == 5) || ($agendamento['status_id'] == 5 && $status_id == 4)) {
            $delete_sql = "DELETE FROM recebimentos WHERE agendamento_id = :agendamento_id";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bindValue(':agendamento_id', $id);
            $delete_stmt->execute();
        }

        if ($status_id == 'Selecione o Status' || !is_numeric($status_id)) {
            $status_id = 0; 
        }

        $update_sql = "UPDATE agendamentos SET status_id = :status_id WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindValue(':status_id', $status_id);
        $update_stmt->bindValue(':id', $id);
        $update_stmt->execute();

        if ($status_id == 4 || $status_id == 5) {
            header("Location: recebimentos.php?id=" . $id); 
            exit;  
        } else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (PDOException $e) {
        echo "Erro ao atualizar o status: " . $e->getMessage();
    }
}

require '../banco/conexao.php';

$cliente_nome = isset($_GET['cliente_nome']) ? $_GET['cliente_nome'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$agendamento_id = isset($_GET['agendamento_id']) ? $_GET['agendamento_id'] : ''; 

$sql = "SELECT ag.id, c.nome AS cliente, s.observacao AS servico, ag.data_hora, ag.valor, ag.observacao AS observacao, ag.status_id
        FROM agendamentos ag
        JOIN clientes c ON ag.cliente_id = c.id
        JOIN servicos s ON ag.servico_id = s.id
        WHERE 1=1";

if (!empty($cliente_nome)) {
    $sql .= " AND c.nome LIKE :cliente_nome";
}
if (!empty($data_inicio)) {
    $sql .= " AND ag.data_hora >= :data_inicio";
}
if (!empty($data_fim)) {
    $sql .= " AND ag.data_hora <= :data_fim";
}
if (!empty($agendamento_id)) {
    $sql .= " AND ag.id = :agendamento_id"; 
}

$sql .= " ORDER BY ag.data_hora ASC";

$stmt = $conn->prepare($sql);

if (!empty($cliente_nome)) {
    $stmt->bindValue(':cliente_nome', '%' . $cliente_nome . '%');
}
if (!empty($data_inicio)) {
    $stmt->bindValue(':data_inicio', $data_inicio . ' 00:00:00');
}
if (!empty($data_fim)) {
    $stmt->bindValue(':data_fim', $data_fim . ' 23:59:59');
}
if (!empty($agendamento_id)) {
    $stmt->bindValue(':agendamento_id', $agendamento_id);
}

$stmt->execute();

$status_query = "SELECT id, descricao FROM status";
$status_stmt = $conn->prepare($status_query);
$status_stmt->execute();
$statuses = $status_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Agendamentos</title>
    <style>
    </style>
        
</head>
<body>

<h2>Lista de Agendamentos</h2>

<a href="agendar.php" class="button">Agendar Novo Serviço</a>
<a href="../principal/principal.php" class="button">Voltar</a>
<form method="GET">
    <label for="cliente_nome">Nome do Cliente:</label>
    <input type="text" name="cliente_nome" id="cliente_nome">
    
    <label for="data_inicio">Data Início:</label>
    <input type="date" name="data_inicio" id="data_inicio">
    
    <label for="data_fim">Data Fim:</label>
    <input type="date" name="data_fim" id="data_fim">
    
    <label for="agendamento_id">ID do Agendamento:</label>
    <input type="number" name="agendamento_id" id="agendamento_id">
    
    <button type="submit" class="button">Pesquisar</button>
</form>

<?php
$current_date = '';

while ($agendamento = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $agendamento_date = substr($agendamento['data_hora'], 0, 10); 

    if ($agendamento_date != $current_date) {
        if ($current_date != '') {
            echo "</table><br>"; 
        }
        $current_date = $agendamento_date;
        
        echo "<h3>Agendamentos para o dia: " . date("d/m/Y", strtotime($current_date)) . "</h3>";
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Data e Hora</th>
                    <th>Valor</th>
                    <th>Observação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>";
    }

    $is_disabled = ($agendamento['status_id'] == 4 || $agendamento['status_id'] == 5) ? 'disabled' : '';
    
    echo "<tr>
            <td>{$agendamento['id']}</td>
            <td>{$agendamento['cliente']}</td>
            <td>{$agendamento['servico']}</td>
            <td>{$agendamento['data_hora']}</td>
            <td>{$agendamento['valor']}</td>
            <td>{$agendamento['observacao']}</td>
            <td>
                <form action='' method='POST'>
                    <select name='status'>";
                        foreach ($statuses as $status) {
                            $selected = ($agendamento['status_id'] == $status['id']) ? 'selected' : '';
                            echo "<option value='{$status['id']}' $selected>{$status['descricao']}</option>";
                        }
    echo "      </select>
                    <input type='hidden' name='id' value='{$agendamento['id']}'>
                    <button type='submit' class='btn-atualizar'>Atualizar</button>
                </form>
            </td>
            <td>
                <a href='editar_agendamento.php?id={$agendamento['id']}' class='btn-editar' $is_disabled>Editar</a>
                <a href='excluir_agendamento.php?id={$agendamento['id']}' class='btn-excluir' onclick='return confirm(\"Tem certeza que deseja excluir este agendamento?\")' $is_disabled>Excluir</a>
            </td>
        </tr>";
}
?>

</body>
</html>
