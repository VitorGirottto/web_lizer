<?php
session_start();
require '../principal/autenticar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Clientes</title>
    <script>
        function filtrarClientes() {
            const pesquisa = document.getElementById("pesquisa").value.toLowerCase();
            const linhas = document.querySelectorAll(".linha-cliente");

            linhas.forEach(function(linha) {
                const nomeCliente = linha.querySelector(".nome-cliente").textContent.toLowerCase();
                if (nomeCliente.includes(pesquisa)) {
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

    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $excluir_id);

    if ($stmt->execute()) {
        echo "<p>Cliente excluído com sucesso!</p>";
        header("Location: clientes.php");
    } else {
        echo "<p>Erro ao excluir cliente.</p>";
    }
}

$stmt = $conn->query("SELECT id, nome, telefone, email, endereco, data_nascimento FROM clientes ORDER BY nome ASC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Lista de Clientes</h2>

<div class="button-container">
    <a href="cadastro_cliente.php" class="button">Cadastrar Novo Cliente</a>
    <a href="../principal/principal.php" class="button">Voltar</a>
</div>

<div class="barra-pesquisa">
    <input type="text" id="pesquisa" onkeyup="filtrarClientes()" placeholder="Pesquisar por nome...">
</div>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Endereço</th>
            <th>Data de Nascimento</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr class="linha-cliente">
                <td class="nome-cliente"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                <td><?php echo htmlspecialchars($cliente['endereco']); ?></td>
                <td><?php echo htmlspecialchars($cliente['data_nascimento']); ?></td>
                <td class="acoes">
                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="editar">Editar</a>
                    <a href="?excluir_id=<?php echo $cliente['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
