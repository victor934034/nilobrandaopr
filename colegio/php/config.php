<?php
// config.php

function getDBConnection() {
    try {
        $host = 'localhost';
        $dbname = 'colegio_nilo_brandao';
        $user = 'root';
        $pass = '';

        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Inicializa a conexão globalmente (opcional)
$conn = getDBConnection();


// Debug: verifique se a conexão existe
if (!isset($conn)) {
    die("Erro: Conexão com o banco de dados não foi estabelecida");
}

// Debug: verifique o slug recebido
error_log("Slug recebido: " . ($_GET['slug'] ?? 'Nenhum slug recebido'));

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'colegio_nilo_brandao');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sua senha aqui se existir

