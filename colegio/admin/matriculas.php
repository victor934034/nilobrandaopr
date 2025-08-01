<?php
session_start();

// Verifica e corrige o caminho do config.php
$configPath = __DIR__ . '/../php/config.php';
if (!file_exists($configPath)) {
    die("Erro: Arquivo de configuração não encontrado em: " . $configPath);
}
require_once $configPath;

// Verifica se o usuário está logado
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// Inicializa variáveis
$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
$erro = null;
$matriculas = [];

// Processa ações (deletar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {
    $ids = $_POST['ids'] ?? [];
    
    if (!empty($ids)) {
        try {
            $conn = getDBConnection();
            
            // Filtra e valida os IDs
            $ids = array_filter($ids, function($id) {
                return filter_var($id, FILTER_VALIDATE_INT) !== false;
            });
            
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $conn->prepare("DELETE FROM matriculas WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                
                $_SESSION['mensagem'] = "Matrículas removidas com sucesso!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $erro = "IDs inválidos para exclusão.";
            }
        } catch(PDOException $e) {
            error_log("Erro ao remover matrículas: " . $e->getMessage());
            $erro = "Erro ao remover matrículas. Tente novamente.";
        }
    } else {
        $erro = "Nenhuma matrícula selecionada para exclusão.";
    }
}

// Busca todas as matrículas
try {
    $conn = getDBConnection();
    $stmt = $conn->query("SELECT * FROM matriculas ORDER BY data_criacao DESC");
    $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Erro ao carregar matrículas: " . $e->getMessage());
    $erro = "Erro ao carregar matrículas. Tente novamente.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Matrículas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .text-center { text-align: center; }
        .actions-cell { white-space: nowrap; }
        #select-all { margin: 0; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php 
        // Verifica se o arquivo do header existe
        $headerPath = __DIR__ . '/admin_header.php';
        if (file_exists($headerPath)) {
            include $headerPath;
        } else {
            echo "<header>Administração - Matrículas</header>";
        }
        ?>
        
        <main class="admin-main">
            <h1><i class="fas fa-users"></i> Gerenciar Matrículas</h1>
            
            <?php if ($mensagem): ?>
                <div class="alert success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="alert error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            
            <form method="post" class="matriculas-form" onsubmit="return confirm('Tem certeza que deseja excluir as matrículas selecionadas?');">
            
                <div class="actions">
    <button type="submit" name="deletar" class="btn btn-danger">
        <i class="fas fa-trash"></i> Excluir Selecionadas
    </button>
    <a href="exportar_matriculas.php" class="btn">
        <i class="fas fa-file-export"></i> Exportar para Excel
    </a>

</div>
                
                <table class="matriculas-table">
                    <thead>
                        <tr>
                            <th width="50px"><input type="checkbox" id="select-all"></th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Curso</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($matriculas)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma matrícula encontrada</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matriculas as $matricula): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= htmlspecialchars($matricula['id']) ?>"></td>
                                <td><?= htmlspecialchars($matricula['nome']) ?></td>
                                <td><?= htmlspecialchars($matricula['email']) ?></td>
                                <td><?= htmlspecialchars($matricula['telefone']) ?></td>
                                <td><?= htmlspecialchars($matricula['curso']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($matricula['data_criacao'])) ?></td>
                                <td class="actions-cell">
                                    <a href="detalhes_matricula.php?id=<?= $matricula['id'] ?>" class="btn-view" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="deletar_matricula.php?id=<?= $matricula['id'] ?>" class="btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta matrícula?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </main>
    </div>

    <script>
        // Selecionar todos
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Desmarcar "selecionar todos" se algum checkbox for desmarcado
        document.querySelectorAll('input[name="ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    document.getElementById('select-all').checked = false;
                }
            });
        });
    </script>
</body>
</html>