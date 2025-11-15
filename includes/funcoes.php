<?php
declare(strict_types=1);

/**
 * includes/funcoes.php
 * - Inicia sessão
 * - Carrega DB com caminho absoluto
 * - Autenticação (login/logout/guardas)
 * - Flash messages
 */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Carrega a conexão com o banco (db.php fica no MESMO diretório de funcoes.php)
require_once __DIR__ . '/db.php'; // usa $pdo

/**
 * Autentica usuário no banco (tabela: usuarios)
 * Campos esperados: id, name, email, password (hash), role
 */
function auth_login(string $email, string $password): bool {
    global $pdo;

    $sql = "SELECT id, name, email, password, role
            FROM usuarios
            WHERE email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id'    => (int)$user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        return true;
    }

    return false;
}

/** Usuário atual (ou null) */
function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/** Está logado? */
function auth_check(): bool {
    return isset($_SESSION['user']);
}

/** Papel atual (role) */
function auth_role(): ?string {
    return $_SESSION['user']['role'] ?? null;
}

/** Verifica se o papel é exatamente $role */
function auth_is(string $role): bool {
    return auth_role() === $role;
}

/** Exige login (redireciona se não estiver logado) */
function auth_require_login(): void {
    if (!auth_check()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: /rfg/admin-login/login.php?redirect=' . $redirect);
        exit;
    }
}

/** Exige que o papel esteja entre $roles */
function auth_require_role(array $roles): void {
    auth_require_login();
    if (!in_array(auth_role(), $roles, true)) {
        header('Location: /rfg/admin-login/sem_permissao.php');
        exit;
    }
}

/** Logout e redireciona para login */
function auth_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header('Location: /rfg/admin-login/login.php');
    exit;
}

/** Flash messages simples */
function set_flash(string $key, string $msg): void {
    $_SESSION['flash'][$key] = $msg;
}

function get_flash(string $key): ?string {
    if (!isset($_SESSION['flash'][$key])) return null;
    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
}