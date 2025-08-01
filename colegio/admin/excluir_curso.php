<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}

// Verifica se o ID do curso foi fornecido
if (!isset($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do curso não fornecido.";
    header('Location: cursos.php');
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($id === false) {
    $_SESSION['mensagem'] = "ID do curso inválido.";
    header('Location: cursos.php');
    exit;
}

// Verifica e corrige o caminho do config.php
$configPath = __DIR__ . '/../php/config.php';
if (!file_exists($configPath)) {
    die("Erro: Arquivo de configuração não encontrado em: " . $configPath);
}
require_once $configPath;

try {
    $conn = getDBConnection();
    
    // Verifica se o curso existe antes de excluir
    $stmt = $conn->prepare('SELECT id FROM cursos WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['mensagem'] = "Curso não encontrado.";
        header('Location: cursos.php');
        exit;
    }
    
    // Exclui o curso
    $stmt = $conn->prepare('DELETE FROM cursos WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Curso excluído com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir o curso.";
    }
    
} catch(PDOException $e) {
    error_log("Erro no excluir_curso.php: " . $e->getMessage());
    $_SESSION['mensagem'] = "Erro ao conectar com o banco de dados. Por favor, tente novamente.";
}

header('Location: cursos.php');
exit;
?>