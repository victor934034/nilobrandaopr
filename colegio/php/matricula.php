<?php
include 'header.php';

// Exibir mensagens de erro/sucesso
if (isset($_SESSION['form_error'])) {
    echo '<div class="alert error">' . htmlspecialchars($_SESSION['form_error']) . '</div>';
    unset($_SESSION['form_error']);
}

if (isset($_SESSION['form_success'])) {
    echo '<div class="alert success">' . htmlspecialchars($_SESSION['form_success']) . '</div>';
    unset($_SESSION['form_success']);
}

// No topo do matricula.php, após incluir o header
$errosPossiveis = [
    'metodo_invalido' => 'Método de requisição inválido',
    'campo_obrigatorio' => 'Preencha todos os campos obrigatórios',
    'email_invalido' => 'E-mail inválido',
    'curso_invalido' => 'Curso selecionado é inválido',
    'erro_banco_dados' => 'Erro ao processar sua solicitação',
    'erro_processamento' => 'Erro no processamento dos dados'
];

if (isset($_GET['error'])) {
    $mensagemErro = $errosPossiveis[$_GET['error']] ?? 'Ocorreu um erro';
    echo '<div class="alert error">' . htmlspecialchars($mensagemErro) . '</div>';
}

if (isset($_GET['success'])) {
    echo '<div class="alert success">Matrícula enviada com sucesso!</div>';
}


// Verifica se veio de um curso específico
$curso = $_GET['curso'] ?? '';
$cursosDisponiveis = [
    
    'ensino-tecnico' => 'Curso Técnico em Informática'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Matrícula - Colégio Nilo Brandão</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos específicos para a página de matrícula */
        .matricula-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .matricula-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #2980b9;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="matricula-container">
            <h1 class="matricula-title">Solicitação de Matrícula</h1>
            
            <form id="form-matricula" action="processa_matricula.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="curso">Curso de Interesse:</label>
                    <select id="curso" name="curso" class="form-control" required>
                        <option value="" disabled selected>Selecione um curso</option>
                        <?php foreach ($cursosDisponiveis as $slug => $nomeCurso): ?>
                            <option value="<?= htmlspecialchars($slug) ?>" <?= $curso === $slug ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nomeCurso) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mensagem">Mensagem (opcional):</label>
                    <textarea id="mensagem" name="mensagem" class="form-control"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Enviar Solicitação</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>