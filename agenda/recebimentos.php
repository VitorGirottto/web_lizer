<?php
require '../banco/conexao.php';

session_start();
require '../principal/autenticar.php';

$agendamento = null;
$tipos_recebimento = [];

// Recupera os tipos de recebimento
try {
    $sql = "SELECT id, descricao FROM tipo_recebimento";
    $stmt = $conn->query($sql);
    $tipos_recebimento = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar tipos de recebimento: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erro de segurança. Solicitação inválida.');
    }

    $valor = $_POST['valor'];
    $agendamento_id = $_POST['agendamento_id'];
    $tipo_id = $_POST['tipo_id'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $cliente_id = $_POST['cliente_id'];

    if (!is_numeric($valor)) {
        echo "O valor informado não é válido.";
        exit;
    }

    try {
        $insert_sql = "INSERT INTO recebimentos (valor, tipo_id, descricao, data, agendamento_id, cliente_id) 
                       VALUES (:valor, :tipo_id, :descricao, :data, :agendamento_id, :cliente_id)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindValue(':valor', $valor);
        $insert_stmt->bindValue(':tipo_id', $tipo_id);
        $insert_stmt->bindValue(':descricao', $descricao);
        $insert_stmt->bindValue(':data', $data);
        $insert_stmt->bindValue(':agendamento_id', $agendamento_id);
        $insert_stmt->bindValue(':cliente_id', $cliente_id);

        $insert_stmt->execute();

        if ($insert_stmt->rowCount() > 0) {
            echo '<div class="success-message">Pagamento registrado com sucesso!</div>';
        } else {
            echo '<div class="error-message">Erro ao registrar o pagamento.</div>';
        }

        header("Location: agenda.php");
        exit;

    } catch (PDOException $e) {
        echo "Erro ao registrar o pagamento: " . $e->getMessage();
        exit;
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['id'])) {
    $agendamento_id = $_GET['id'];

    try {
        $sql = "SELECT ag.id, c.id AS cliente_id, c.nome AS cliente, s.observacao AS servico, ag.valor 
                FROM agendamentos ag
                JOIN clientes c ON ag.cliente_id = c.id
                JOIN servicos s ON ag.servico_id = s.id
                WHERE ag.id = :agendamento_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':agendamento_id', $agendamento_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<p>Agendamento não encontrado.</p>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Erro ao buscar o agendamento: " . $e->getMessage();
        exit;
    }
} else {
    echo "<p>Agendamento não especificado.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recebimento</title>
    <style>
        /* Adicionar seu CSS aqui */
    </style>
</head>
<body>

<h2>Recebimento do Serviço</h2>

<?php if ($agendamento): ?>
<div class="form-container">
    <form action="recebimentos.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <h3>Detalhes do Agendamento</h3>
        
        <label for="cliente">Cliente:</label>
        <input type="text" name="cliente" id="cliente" value="<?php echo htmlspecialchars($agendamento['cliente']); ?>" disabled>
        
        <label for="servico">Serviço:</label>
        <input type="text" name="servico" id="servico" value="<?php echo htmlspecialchars($agendamento['servico']); ?>" disabled>
        
        <label for="valor">Valor:</label>
        <input type="number" name="valor" id="valor" value="<?php echo htmlspecialchars($agendamento['valor']); ?>" required step="0.01">
        
        <label for="tipo_id">Tipo de Pagamento:</label>
        <select name="tipo_id" id="tipo_id" required>
            <?php foreach ($tipos_recebimento as $tipo): ?>
                <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['descricao']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao" rows="3"></textarea>

        <label for="data">Data do Pagamento:</label>
        <input type="date" name="data" id="data" required>
        
        <input type="hidden" name="agendamento_id" value="<?php echo $agendamento['id']; ?>">
        <input type="hidden" name="cliente_id" value="<?php echo $agendamento['cliente_id']; ?>">

        <button type="submit" class="btn-atualizar">Registrar Recebimento</button>
    </form>
</div>
<?php endif; ?>

</body>
</html>
