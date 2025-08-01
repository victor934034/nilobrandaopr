<?php
session_start();

if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}

$configPath = __DIR__ . '/../php/config.php';
if (!file_exists($configPath)) {
    die("Erro: Arquivo de configuração não encontrado em: " . $configPath);
}
require_once $configPath;

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
$erro = null;
$curso = null;

$periodos = ['Manhã', 'Tarde', 'Noite'];

// Verifica se foi passado um ID válido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: cursos.php');
    exit;
}

try {
    $conn = getDBConnection();
    
    // Busca os dados do curso
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
    $stmt->execute([$id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$curso) {
        header('Location: cursos.php');
        exit;
    }
    
    // Processa o formulário quando submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $vagas = $_POST['vagas'] ?? 0;
        $descricao = trim($_POST['descricao'] ?? '');
        $periodo = $_POST['periodo'] ?? '';
        $idade = trim($_POST['idade'] ?? '');
        $imagem_nome = $curso['imagem'] ?? null;
        $caminho_completo = null;
        
        // Validações
        if (empty($nome)) {
            $erro = "O nome do curso é obrigatório.";
        } elseif (!is_numeric($vagas) || $vagas < 0) {
            $erro = "O número de vagas deve ser um valor positivo.";
        } elseif (!in_array($periodo, $periodos)) {
            $erro = "Período inválido.";
        } elseif (empty($idade)) {
            $erro = "A faixa etária é obrigatória.";
        } elseif (!preg_match('/^(\d+\s?[-a]\s?\d+|\d+)\s?(anos|ano)?$/i', $idade)) {
            $erro = "Formato de faixa etária inválido. Use '6-10 anos' ou similar.";
        } else {
            // Processamento da imagem (se enviada)
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $diretorio_imagens = __DIR__ . '/../assets/images/cursos/';
                if (!file_exists($diretorio_imagens)) {
                    mkdir($diretorio_imagens, 0777, true);
                }
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['imagem']['tmp_name']);
                finfo_close($finfo);
                
                $extensoes_permitidas = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif'
                ];
                
                if (array_key_exists($mime, $extensoes_permitidas)) {
                    $extensao = $extensoes_permitidas[$mime];
                    $imagem_nome = uniqid('curso_') . '.' . $extensao;
                    $caminho_completo = $diretorio_imagens . $imagem_nome;
                    
                    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
                        $erro = "Erro ao fazer upload da imagem.";
                    } else {
                        // Remove a imagem antiga se existir e for diferente da nova
                        if (!empty($curso['imagem'])) {
                            $imagem_antiga = $diretorio_imagens . $curso['imagem'];
                            if (file_exists($imagem_antiga) && $curso['imagem'] !== $imagem_nome) {
                                @unlink($imagem_antiga);
                            }
                        }
                    }
                } else {
                    $erro = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
                }
            }
            
            if (!$erro) {
                // Atualiza o curso no banco de dados
                $stmt = $conn->prepare("UPDATE cursos SET 
                                      nome = ?, 
                                      vagas = ?, 
                                      descricao = ?, 
                                      periodo = ?, 
                                      imagem = ?, 
                                      idade = ?
                                      WHERE id = ?");
                
                if ($stmt->execute([$nome, $vagas, $descricao, $periodo, $imagem_nome, $idade, $id])) {
                    $_SESSION['mensagem'] = "Curso atualizado com sucesso!";
                    header("Location: cursos.php");
                    exit;
                } else {
                    // Se houve erro no banco, remove a imagem que foi enviada
                    if ($caminho_completo && file_exists($caminho_completo)) {
                        @unlink($caminho_completo);
                    }
                    $erro = "Erro ao atualizar curso. Tente novamente.";
                }
            }
        }
    }
} catch(PDOException $e) {
    error_log("Erro ao editar curso: " . $e->getMessage());
    $erro = "Erro ao conectar com o banco de dados. Por favor, tente novamente.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_header.php'; ?>
        
        <main class="admin-main">
            <h1><i class="fas fa-edit"></i> Editar Curso</h1>
            
            <?php if ($mensagem): ?>
                <div class="alert success"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="alert error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" class="form-container">
                <div class="form-group">
                    <label for="nome">Nome do Curso*</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?= htmlspecialchars($curso['nome'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="idade">Faixa Etária*</label>
                    <input type="text" id="idade" name="idade" required 
                           value="<?= htmlspecialchars($curso['idade'] ?? '') ?>"
                           placeholder="Ex: 6-10 anos, 11-14 anos, adultos, etc.">
                </div>
                
                <div class="form-group">
                    <label for="vagas">Número de Vagas*</label>
                    <input type="number" id="vagas" name="vagas" min="0" 
                           value="<?= htmlspecialchars($curso['vagas'] ?? 10) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"><?= htmlspecialchars($curso['descricao'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="periodo">Período*</label>
                    <select id="periodo" name="periodo" required>
                        <option value="">Selecione o período</option>
                        <?php foreach ($periodos as $p): ?>
                            <option value="<?= $p ?>" <?= ($curso['periodo'] ?? '') === $p ? 'selected' : '' ?>>
                                <?= $p ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="imagem">Imagem do Curso</label>
                    <?php if (!empty($curso['imagem']) && file_exists(__DIR__ . '/../assets/images/cursos/' . $curso['imagem'])): ?>
                        <img src="../assets/images/cursos/<?= htmlspecialchars($curso['imagem']) ?>" 
                             class="curso-image-preview" alt="Imagem do curso">
                        <small>Imagem atual (deixe em branco para manter)</small>
                    <?php endif; ?>
                    <input type="file" id="imagem" name="imagem" accept="image/*">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="cursos.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>