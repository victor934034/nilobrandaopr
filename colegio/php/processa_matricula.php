<?php
// Inicie a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_error'] = 'Método de requisição inválido';
    header("Location: matricula.php");
    exit;
}

// Função para sanitizar strings
function sanitizeString($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Validação dos campos obrigatórios
$camposObrigatorios = ['nome', 'email', 'telefone', 'curso'];
foreach ($camposObrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        $_SESSION['form_error'] = "O campo " . ucfirst($campo) . " é obrigatório";
        header("Location: matricula.php");
        exit;
    }
}

// Sanitização e validação dos dados
$nome = sanitizeString($_POST['nome']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$telefone = sanitizeString($_POST['telefone']);
$curso = sanitizeString($_POST['curso']);
$mensagem = isset($_POST['mensagem']) ? sanitizeString($_POST['mensagem']) : '';
$data = date('Y-m-d H:i:s');

// Validação adicional do email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error'] = "E-mail inválido";
    header("Location: matricula.php");
    exit;
}

// Validação do curso (prevenção contra valores não permitidos)
$cursosPermitidos = ['ensino-fundamental-ii', 'ensino-medio', 'ensino-tecnico'];
if (!in_array($curso, $cursosPermitidos)) {
    $_SESSION['form_error'] = "Curso selecionado é inválido";
    header("Location: matricula.php");
    exit;
}

try {
    $conn = getDBConnection();
    
    // Verifica se a conexão foi estabelecida
    if (!$conn) {
        throw new Exception("Erro na conexão com o banco de dados");
    }
    
    // Prepara e executa a query
    $stmt = $conn->prepare("INSERT INTO matriculas 
                          (nome, email, telefone, curso, mensagem, data_criacao) 
                          VALUES (:nome, :email, :telefone, :curso, :mensagem, :data)");
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':curso', $curso);
    $stmt->bindParam(':mensagem', $mensagem);
    $stmt->bindParam(':data', $data);
    
    if ($stmt->execute()) {
        $_SESSION['form_success'] = "Matrícula enviada com sucesso!";
        header("Location: matricula.php");
        exit;
    } else {
        throw new Exception("Erro ao executar a query");
    }
    
} catch(PDOException $e) {
    error_log("Erro PDO: " . $e->getMessage());
    $_SESSION['form_error'] = "Erro ao processar sua solicitação. Tente novamente mais tarde.";
    header("Location: matricula.php");
    exit;
} catch(Exception $e) {
    error_log("Erro: " . $e->getMessage());
    $_SESSION['form_error'] = "Ocorreu um erro inesperado.";
    header("Location: matricula.php");
    exit;
}