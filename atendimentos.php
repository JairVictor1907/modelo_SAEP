<?php
require "conexao.php";

$erro = "";
$editar = null;

// EXCLUIR
if (isset($_GET["excluir"])) {
    $id = (int)$_GET["excluir"];

    $pdo->prepare("DELETE FROM atendimentos WHERE id=?")->execute([$id]);

    header("Location: atendimentos.php?msg=excluido");
    exit;
}

// SALVAR OU ATUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = (int)($_POST["id"] ?? 0);
    $tutor_id = (int)($_POST["tutor_id"] ?? 0);
    $pet_id = (int)($_POST["pet_id"] ?? 0);
    $data = $_POST["data_atendimento"] ?? "";
    $servico = trim($_POST["servico"] ?? "");
    $status = $_POST["status"] ?? "Agendado";
    $valor = $_POST["valor"] ?? 0;

    if ($tutor_id <= 0 || $pet_id <= 0 || $data === "" || $servico === "") {
        $erro = "Preencha todos os campos obrigatórios.";

    } else {

        if ($id > 0) {
            $sql = "UPDATE atendimentos SET tutor_id=?, pet_id=?,
                    data_atendimento=?, servico=?, status=?, valor=?
                    WHERE id=?";

            $pdo->prepare($sql)->execute([
                $tutor_id, $pet_id, $data, $servico, $status, $valor, $id
            ]);

        } else {
            $sql = "INSERT INTO atendimentos
                    (tutor_id, pet_id, data_atendimento, servico, status, valor)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $pdo->prepare($sql)->execute([
                $tutor_id, $pet_id, $data, $servico, $status, $valor
            ]);
        }

        header("Location: atendimentos.php?msg=salvo");
        exit;
    }
}

// EDITAR
if (isset($_GET["editar"])) {
    $stmt = $pdo->prepare("SELECT * FROM atendimentos WHERE id=?");
    $stmt->execute([(int)$_GET["editar"]]);
    $editar = $stmt->fetch();
}

// BUSCAR TUTORES E PETS
$tutores = $pdo->query("SELECT * FROM tutores ORDER BY nome")->fetchAll();
$pets = $pdo->query("SELECT * FROM pets ORDER BY nome")->fetchAll();

// LISTAR ATENDIMENTOS
$sql = "SELECT atendimentos.*, tutores.nome AS tutor_nome,
        pets.nome AS pet_nome
        FROM atendimentos
        INNER JOIN tutores ON tutores.id = atendimentos.tutor_id
        INNER JOIN pets ON pets.id = atendimentos.pet_id
        ORDER BY atendimentos.data_atendimento DESC";

$atendimentos = $pdo->query($sql)->fetchAll();

include "topo.php";
?>

<h2>Cadastro de atendimentos</h2>

<?php if ($erro !== ""): ?>
    <div class="erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (isset($_GET["msg"])): ?>
    <div class="msg">Operação realizada com sucesso.</div>
<?php endif; ?>

<?php if (!$tutores || !$pets): ?>

    <div class="erro">Cadastre tutores e pets primeiro.</div>

<?php else: ?>

<form method="post" action="atendimentos.php">

    <h3><?= $editar ? "Editar atendimento" : "Novo atendimento" ?></h3>

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

    <label>Pet *</label>
    <select name="pet_id" required>
        <option value="">Selecione</option>

        <?php foreach ($pets as $p): ?>
            <option value="<?= $p["id"] ?>"
                <?= ($editar["pet_id"] ?? "") == $p["id"] ? "selected" : "" ?>>
                <?= htmlspecialchars($p["nome"]) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <label>Data do atendimento *</label>
    <input type="date" name="data_atendimento" required
        value="<?= $editar["data_atendimento"] ?? "" ?>">

    <label>Serviço *</label>
    <input type="text" name="servico" required
        placeholder="Ex: Banho e tosa"
        value="<?= htmlspecialchars($editar["servico"] ?? "") ?>">

    <label>Status</label>
    <select name="status">
        <?php foreach (["Agendado", "Realizado", "Cancelado"] as $s): ?>
            <option value="<?= $s ?>"
                <?= ($editar["status"] ?? "Agendado") == $s ? "selected" : "" ?>>
                <?= $s ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Valor</label>
    <input type="number" name="valor" step="0.01" min="0"
        value="<?= $editar["valor"] ?? "0" ?>">

    <button type="submit">Salvar atendimento</button>

    <a class="btn btn-secundario" href="atendimentos.php">Limpar</a>

</form>

<?php endif; ?>

<h3>Atendimentos cadastrados</h3>

<table>
    <tr>
        <th>Pet</th>
        <th>Tutor</th>
        <th>Data</th>
        <th>Serviço</th>
        <th>Status</th>
        <th>Valor</th>
        <th>Ações</th>
    </tr>

    <?php if (!$atendimentos): ?>
        <tr>
            <td colspan="7">Nenhum atendimento cadastrado.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($atendimentos as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a["pet_nome"]) ?></td>
            <td><?= htmlspecialchars($a["tutor_nome"]) ?></td>
            <td><?= date("d/m/Y", strtotime($a["data_atendimento"])) ?></td>
            <td><?= htmlspecialchars($a["servico"]) ?></td>
            <td><?= htmlspecialchars($a["status"]) ?></td>
            <td>R$ <?= number_format($a["valor"], 2, ",", ".") ?></td>

            <td class="acoes">
                <a class="btn" href="atendimentos.php?editar=<?= $a["id"] ?>">
                    Editar
                </a>

                <a class="btn btn-excluir"
                   onclick="return confirm('Excluir atendimento?')"
                   href="atendimentos.php?excluir=<?= $a["id"] ?>">
                    Excluir
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php include "rodape.php"; ?>