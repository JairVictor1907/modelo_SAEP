<?php
require "conexao.php";

$totalTutores = $pdo->query("SELECT COUNT(*) FROM tutores")->fetchColumn();

$totalPets = $pdo->query("SELECT COUNT(*) FROM pets")->fetchColumn();

$totalAtendimentos = $pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();

$sql = "SELECT COALESCE(SUM(valor), 0) FROM atendimentos
        WHERE status = 'Realizado'";

$faturamento = $pdo->query($sql)->fetchColumn();

include "topo.php";
?>

<h2>Painel inicial</h2>

<p>Resumo das informações do Pet Shop.</p>

<div class="resumo">

    <div class="card">
        <h3>Tutores</h3>
        <div class="numero"><?= $totalTutores ?></div>
    </div>

    <div class="card">
        <h3>Pets</h3>
        <div class="numero"><?= $totalPets ?></div>
    </div>

    <div class="card">
        <h3>Atendimentos</h3>
        <div class="numero"><?= $totalAtendimentos ?></div>
    </div>

    <div class="card">
        <h3>Faturamento</h3>
        <div class="numero">
            R$ <?= number_format($faturamento, 2, ',', '.') ?>
        </div>
    </div>

</div>

<p>
    <a class="btn" href="tutores.php">Cadastrar tutor</a>
    <a class="btn" href="pets.php">Cadastrar pet</a>
    <a class="btn" href="atendimentos.php">Cadastrar atendimento</a>
</p>

<?php include "rodape.php"; ?>