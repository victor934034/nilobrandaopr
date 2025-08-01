<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}

$configPath = __DIR__ . '/../php/config.php';
if (!file_exists($configPath)) {
    die("Erro: Arquivo de configuração não encontrado em: " . $configPath);
}
require_once $configPath;

// Inicializa variáveis
$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
$error = null;
$cursos = [];

try {
    $conn = getDBConnection();
    
    // Processar atualização de vagas
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vagas'])) {
        $vagasAtualizadas = 0;
        
        foreach ($_POST['vagas'] as $id => $vagas) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            $vagas = filter_var($vagas, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0]
            ]);
            
            if ($id !== false && $vagas !== false) {
                $stmt = $conn->prepare('UPDATE cursos SET vagas = :vagas WHERE id = :id');
                $stmt->bindParam(':vagas', $vagas, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    $vagasAtualizadas++;
                }
            }
        }
        
        $_SESSION['mensagem'] = $vagasAtualizadas > 0 
            ? "Vagas atualizadas para $vagasAtualizadas curso(s)!" 
            : "Nenhuma alteração foi realizada.";
            
        header("Location: cursos.php");
        exit;
    }
    
    // Atualize esta consulta para incluir todos os campos necessários
    $stmt = $conn->query('SELECT id, nome, slug, vagas, descricao, periodo, idade FROM cursos ORDER BY nome');
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Erro no cursos.php: " . $e->getMessage());
    $error = "Erro ao conectar com o banco de dados. Por favor, tente novamente.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .vagas-input {
            width: 70px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .curso-desc {
            font-size: 0.9em;
            color: #666;
            margin-top: 4px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_header.php'; ?>
        
        <main class="admin-main">
            <h1><i class="fas fa-book"></i> Gerenciar Cursos</h1>
            
            <?php if ($mensagem): ?>
                <div class="alert success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <table class="matriculas-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Faixa Etária</th>
                            <th>Vagas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cursos)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhum curso cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($curso['nome']) ?></strong>
                                    <?php if (!empty($curso['descricao'])): ?>
                                        <div class="curso-desc"><?= htmlspecialchars($curso['descricao']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($curso['idade'] ?? 'Não definida') ?></td>
                                <td>
                                    <input type="number" 
                                           name="vagas[<?= $curso['id'] ?>]" 
                                           value="<?= $curso['vagas'] ?>" 
                                           min="0"
                                           class="vagas-input"
                                           required>
                                </td>
                                <td>
                                    <a href="editar_curso.php?id=<?= $curso['id'] ?>" class="btn-view">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="excluir_curso.php?id=<?= $curso['id'] ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Tem certeza que deseja excluir este curso?');">
                                        <i class="fas fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="adicionar_curso.php" class="btn">
                        <i class="fas fa-plus"></i> Adicionar Curso
                    </a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>