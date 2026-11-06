<?php
require "conexao.php";

$erro = "";
$editar = null;

// EXCLUIR
if (isset($_GET["excluir"])) {
    $id = (int)$_GET["excluir"];

    try {
        $pdo->prepare("DELETE FROM pets WHERE id=?")->execute([$id]);

        header("Location: pets.php?msg=excluido");
        exit;

    } catch (PDOException $e) {
        $erro = "Não é possível excluir: este pet possui atendimentos.";
    }
}

// SALVAR OU ATUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = (int)($_POST["id"] ?? 0);
    $tutor_id = (int)($_POST["tutor_id"] ?? 0);
    $nome = trim($_POST["nome"] ?? "");
    $especie = trim($_POST["especie"] ?? "");
    $raca = trim($_POST["raca"] ?? "");
    $idade = $_POST["idade"] === "" ? null : (int)$_POST["idade"];

    if ($tutor_id <= 0 || $nome === "" || $especie === "") {
        $erro = "Preencha tutor, nome e espécie.";

    } else {

        if ($id > 0) {
            $sql = "UPDATE pets SET tutor_id=?, nome=?, especie=?, raca=?, idade=?
                    WHERE id=?";

            $pdo->prepare($sql)->execute([
                $tutor_id, $nome, $especie, $raca, $idade, $id
            ]);

        } else {
            $sql = "INSERT INTO pets (tutor_id, nome, especie, raca, idade)
                    VALUES (?, ?, ?, ?, ?)";

            $pdo->prepare($sql)->execute([
                $tutor_id, $nome, $especie, $raca, $idade
            ]);
        }

        header("Location: pets.php?msg=salvo");
        exit;
    }
}

// EDITAR
if (isset($_GET["editar"])) {
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE id=?");
    $stmt->execute([(int)$_GET["editar"]]);
    $editar = $stmt->fetch();
}

$tutores = $pdo->query("SELECT * FROM tutores ORDER BY nome")->fetchAll();

$sql = "SELECT pets.*, tutores.nome AS tutor_nome
        FROM pets
        INNER JOIN tutores ON tutores.id = pets.tutor_id
        ORDER BY pets.nome";

$pets = $pdo->query($sql)->fetchAll();

include "topo.php";
?>

<h2>Cadastro de pets</h2>

<?php if ($erro !== ""): ?>
    <div class="erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (isset($_GET["msg"])): ?>
    <div class="msg">Operação realizada com sucesso.</div>
<?php endif; ?>

<?php if (!$tutores): ?>

    <div class="erro">Cadastre um tutor primeiro.</div>

<?php else: ?>

<form method="post" action="pets.php">

    <h3><?= $editar ? "Editar pet" : "Novo pet" ?></h3>

    <input type="hidden" name="id" value="<?= $editar["id"] ?? 0 ?>">

    <label>Tutor *</label>
    <select name="tutor_id" required>
        <option value="">Selecione</option>

        <?php foreach ($tutores as $t): ?>
            <option value="<?= $t["id"] ?>"
                <?= ($editar["tutor_id"] ?? "") == $t["id"] ? "selected" : "" ?>>
                <?= htmlspecialchars($t["nome"]) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <label>Nome do pet *</label>
    <input type="text" name="nome" required
        value="<?= htmlspecialchars($editar["nome"] ?? "") ?>">

    <label>Espécie *</label>
    <select name="especie" required>
        <option value="">Selecione</option>

        <?php foreach (["Cachorro", "Gato", "Ave", "Outro"] as $opcao): ?>
            <option value="<?= $opcao ?>"
                <?= ($editar["especie"] ?? "") == $opcao ? "selected" : "" ?>>
                <?= $opcao ?>
            </option>
        <?php endforeach; ?>

    </select>

    <label>Raça</label>
    <input type="text" name="raca"
        value="<?= htmlspecialchars($editar["raca"] ?? "") ?>">

    <label>Idade</label>
    <input type="number" name="idade" min="0"
        value="<?= htmlspecialchars((string)($editar["idade"] ?? "")) ?>">

    <button type="submit">Salvar pet</button>

    <a class="btn btn-secundario" href="pets.php">Limpar</a>

</form>

<?php endif; ?>

<h3>Pets cadastrados</h3>

<table>
    <tr>
        <th>Nome</th>
        <th>Espécie</th>
        <th>Raça</th>
        <th>Idade</th>
        <th>Tutor</th>
        <th>Ações</th>
    </tr>

    <?php if (!$pets): ?>
        <tr>
            <td colspan="6">Nenhum pet cadastrado.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($pets as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p["nome"]) ?></td>
            <td><?= htmlspecialchars($p["especie"]) ?></td>
            <td><?= htmlspecialchars($p["raca"] ?? "") ?></td>
            <td><?= htmlspecialchars((string)($p["idade"] ?? "")) ?></td>
            <td><?= htmlspecialchars($p["tutor_nome"]) ?></td>

            <td class="acoes">
                <a class="btn" href="pets.php?editar=<?= $p["id"] ?>">
                    Editar
                </a>

                <a class="btn btn-excluir"
                   onclick="return confirm('Excluir pet?')"
                   href="pets.php?excluir=<?= $p["id"] ?>">
                    Excluir
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php include "rodape.php"; ?>