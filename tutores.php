<?php
require "conexao.php";

$erro = "";
$editar = null;

// EXCLUIR
if (isset($_GET["excluir"])) {
    $id = (int)$_GET["excluir"];

    try {
        $pdo->prepare("DELETE FROM tutores WHERE id = ?")->execute([$id]);

        header("Location: tutores.php?msg=excluido");
        exit;

    } catch (PDOException $e) {
        $erro = "Não é possível excluir: tutor possui pets ou atendimentos.";
    }
}

// SALVAR OU ATUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = (int)($_POST["id"] ?? 0);
    $nome = trim($_POST["nome"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($nome === "" || $telefone === "") {
        $erro = "Preencha nome e telefone.";

    } else {

        if ($id > 0) {
            $sql = "UPDATE tutores SET nome=?, telefone=?, email=? WHERE id=?";
            $pdo->prepare($sql)->execute([$nome, $telefone, $email, $id]);

        } else {
            $sql = "INSERT INTO tutores (nome, telefone, email) VALUES (?, ?, ?)";
            $pdo->prepare($sql)->execute([$nome, $telefone, $email]);
        }

        header("Location: tutores.php?msg=salvo");
        exit;
    }
}

// EDITAR
if (isset($_GET["editar"])) {
    $stmt = $pdo->prepare("SELECT * FROM tutores WHERE id=?");
    $stmt->execute([(int)$_GET["editar"]]);
    $editar = $stmt->fetch();
}

$tutores = $pdo->query("SELECT * FROM tutores ORDER BY nome")->fetchAll();

include "topo.php";
?>

<h2>Cadastro de tutores</h2>

<?php if ($erro !== ""): ?>
    <div class="erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (isset($_GET["msg"])): ?>
    <div class="msg">Operação realizada com sucesso.</div>
<?php endif; ?>

<form method="post" action="tutores.php">

    <h3><?= $editar ? "Editar tutor" : "Novo tutor" ?></h3>

    <input type="hidden" name="id"
        value="<?= $editar["id"] ?? 0 ?>">

    <label>Nome *</label>
    <input type="text" name="nome" required
        value="<?= htmlspecialchars($editar["nome"] ?? "") ?>">

    <label>Telefone *</label>
    <input type="text" name="telefone" required
        value="<?= htmlspecialchars($editar["telefone"] ?? "") ?>">

    <label>E-mail</label>
    <input type="email" name="email"
        value="<?= htmlspecialchars($editar["email"] ?? "") ?>">

    <button type="submit">Salvar tutor</button>

    <a class="btn btn-secundario" href="tutores.php">Limpar</a>

</form>

<h3>Tutores cadastrados</h3>

<table>
    <tr>
        <th>Nome</th>
        <th>Telefone</th>
        <th>E-mail</th>
        <th>Ações</th>
    </tr>

    <?php if (!$tutores): ?>
        <tr>
            <td colspan="4">Nenhum tutor cadastrado.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($tutores as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t["nome"]) ?></td>
            <td><?= htmlspecialchars($t["telefone"]) ?></td>
            <td><?= htmlspecialchars($t["email"] ?? "") ?></td>

            <td class="acoes">
                <a class="btn" href="tutores.php?editar=<?= $t["id"] ?>">
                    Editar
                </a>

                <a class="btn btn-excluir"
                   onclick="return confirm('Excluir tutor?')"
                   href="tutores.php?excluir=<?= $t["id"] ?>">
                    Excluir
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php include "rodape.php"; ?>