<?php
header('Content-Type: application/json');

// Configuração para WAMP
$host = 'localhost';
$dbname = 'colegio_nilo_brandao';
$username = 'root';
$password = ''; // Normalmente vazio no WAMP

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query('SELECT * FROM cursos');
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($cursos);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>