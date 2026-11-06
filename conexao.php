<?php
$host = "localhost";
$porta = "3307";
$banco = "petshop";
$usuario = "root";
$senha = "admin";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados.");
}
?>