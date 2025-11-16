<?php
// admin-login/login_process.php
declare(strict_types=1);

// funcoes.php já inicia a sessão (se precisar) e carrega o db.php ($pdo)
require_once __DIR__ . '/../includes/funcoes.php';

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /rfg/admin-login/login.php');
    exit;
}

// Coleta dados do formulário
$email    = trim($_POST['usuario'] ?? '');
$senha    = $_POST['senha'] ?? '';
$redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? '');

// Validação básica
if ($email === '' || $senha === '') {
    if (!function_exists('set_flash')) {
        $_SESSION['flash']['login_error'] = 'Preencha e-mail e senha.';
    } else {
        set_flash('login_error', 'Preencha e-mail e senha.');
    }

    header('Location: /rfg/admin-login/login.php');
    exit;
}

try {
    // TENTA LOGAR usando a função centralizada
    if (!auth_login($email, $senha)) {
        if (!function_exists('set_flash')) {
            $_SESSION['flash']['login_error'] = 'Usuário ou senha inválidos.';
        } else {
            set_flash('login_error', 'Usuário ou senha inválidos.');
        }

        header('Location: /rfg/admin-login/login.php');
        exit;
    }

    // Segurança de sessão: novo ID após login
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    // Descobre o papel atual
    $role = auth_role(); // admin | editor | viewer (ou o que você usar)

    // Destino padrão
    $dest = '/rfg/index.php';
    if ($role === 'admin') {
        $dest = '/rfg/admin/dashboard.php';
    } elseif ($role === 'editor') {
        $dest = '/rfg/editor/dashboard.php';
    }

    // Se veio redirect e for uma rota interna segura, sobrescreve o destino
    if (is_string($redirect)
        && $redirect !== ''
        && str_starts_with($redirect, '/')
        && !str_contains($redirect, "\n")) {
        $dest = $redirect;
    }

    // Cabeçalhos anti-cache (opcional)
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    // Vai pro painel
    header("Location: {$dest}");
    exit;

} catch (Throwable $e) {
    // Em produção, loga o erro certinho
    if (!function_exists('set_flash')) {
        $_SESSION['flash']['login_error'] = 'Erro interno ao autenticar.';
    } else {
        set_flash('login_error', 'Erro interno ao autenticar.');
    }

    header('Location: /rfg/admin-login/login.php');
    exit;
}

?>