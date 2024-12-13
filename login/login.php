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

require '../banco/conexao.php';

$stmt_usuarios = $conn->query("SELECT id, username FROM usuarios");
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <style>
    </style>
</head>
<body>

<h2>Usuários Cadastrados</h2>

<div class="button-container">
    <a href="criar_usuario.php" class="button">Cadastrar Novo Usuário</a>
    <a href="../principal/principal.php" class="button">Voltar</a>
</div>

<div class="barra-pesquisa">
    <input type="text" id="pesquisa" onkeyup="filtrarUsuarios()" placeholder="Pesquisar por nome...">
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                <td class="acoes">
                    <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="editar">Alterar</a>
                    <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function filtrarUsuarios() {
        const pesquisa = document.getElementById("pesquisa").value.toLowerCase();
        const linhas = document.querySelectorAll("tbody tr");

        linhas.forEach(function(linha) {
            const nomeUsuario = linha.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (nomeUsuario.includes(pesquisa)) {
                linha.style.display = "";
            } else {
                linha.style.display = "none";
            }
        });
    }
</script>

</body>
</html>
