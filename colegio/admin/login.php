<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Em produção, use credenciais seguras e hash de senha
    $usuario_correto = 'admin';
    $senha_correta = '123456'; // Troque por uma senha segura
    
    if ($_POST['usuario'] === $usuario_correto && $_POST['senha'] === $senha_correta) {
        $_SESSION['logado'] = true;
        header('Location: cursos.php');
        exit;
    } else {
        $erro = "Usuário ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { width: 300px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-top: 0; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Login</h1>
        
        <?php if (isset($erro)): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div>
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>