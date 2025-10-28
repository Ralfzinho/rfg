<<<<<<< HEAD
<?php
// admin-login/login_process.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/db.php';       // => $pdo (PDO conectado)
require_once __DIR__ . '/../includes/funcoes.php';  // set_flash/get_flash (opcional), helpers

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /rfg/admin-login/login.php');
  exit;
}

// Coleta dados
$email    = trim($_POST['usuario'] ?? '');
$senha    = $_POST['senha'] ?? '';
$redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? '');

// Validação básica
if ($email === '' || $senha === '') {
  if (!function_exists('set_flash')) {
    // fallback simples
    $_SESSION['flash']['login_error'] = 'Preencha e-mail e senha.';
  } else {
    set_flash('login_error', 'Preencha e-mail e senha.');
  }
  header('Location: /rfg/admin-login/login.php');
  exit;
}

try {
  // Busca usuário por e-mail
  $sql  = "SELECT id, nome, email, senha AS senha_hash, role FROM usuarios WHERE email = :email LIMIT 1";
  $$stmt = $pdo->prepare($sql);
  $stmt->execute(['email' => $email]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  // Valida credenciais
  if (!$u || !password_verify($senha, $u['senha_hash'])) {
    if (!function_exists('set_flash')) {
      $_SESSION['flash']['login_error'] = 'Usuário ou senha inválidos.';
    } else {
      set_flash('login_error', 'Usuário ou senha inválidos.');
    }
    header('Location: /rfg/admin-login/login.php');
    exit;
  }

  // Segurança de sessão
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
  }

  // Guarda usuário na sessão
  $_SESSION['user'] = [
    'id'    => (int)$u['id'],
    'name'  => $u['nome'],
    'email' => $u['email'],
    'role'  => $u['role'], // admin | editor | piloto
  ];

  // Define destino padrão por papel
  $dest = '/';
  if ($u['role'] === 'admin')  $dest = '/admin/dashboard.php';
  if ($u['role'] === 'editor') $dest = '/editor/dashboard.php';

  // Se veio redirect, usa (mas só se for um path interno seguro)
  if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, '/') && !str_contains($redirect, "\n")) {
    $dest = $redirect;
  }

  // Cabeçalhos anti-cache (opcional, ajuda ao deslogar/logar)
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');

  header("Location: {$dest}");
  exit;

} catch (Throwable $e) {
  // Logue o erro de verdade em produção
  if (!function_exists('set_flash')) {
    $_SESSION['flash']['login_error'] = 'Erro interno ao autenticar.';
  } else {
    set_flash('login_error', 'Erro interno ao autenticar.');
  }
  header('Location: /rfg/admin-login/login.php');
  exit;
}

=======
<?php
// admin-login/login_process.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/db.php';       // => $pdo (PDO conectado)
require_once __DIR__ . '/../includes/funcoes.php';  // set_flash/get_flash (opcional), helpers

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /rfg/admin-login/login.php');
  exit;
}

// Coleta dados
$email    = trim($_POST['usuario'] ?? '');
$senha    = $_POST['senha'] ?? '';
$redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? '');

// Validação básica
if ($email === '' || $senha === '') {
  if (!function_exists('set_flash')) {
    // fallback simples
    $_SESSION['flash']['login_error'] = 'Preencha e-mail e senha.';
  } else {
    set_flash('login_error', 'Preencha e-mail e senha.');
  }
  header('Location: /rfg/admin-login/login.php');
  exit;
}

try {
  // Busca usuário por e-mail
  $sql  = "SELECT id, nome, email, senha AS senha_hash, role FROM usuarios WHERE email = :email LIMIT 1";
  $$stmt = $pdo->prepare($sql);
  $stmt->execute(['email' => $email]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  // Valida credenciais
  if (!$u || !password_verify($senha, $u['senha_hash'])) {
    if (!function_exists('set_flash')) {
      $_SESSION['flash']['login_error'] = 'Usuário ou senha inválidos.';
    } else {
      set_flash('login_error', 'Usuário ou senha inválidos.');
    }
    header('Location: /rfg/admin-login/login.php');
    exit;
  }

  // Segurança de sessão
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
  }

  // Guarda usuário na sessão
  $_SESSION['user'] = [
    'id'    => (int)$u['id'],
    'name'  => $u['nome'],
    'email' => $u['email'],
    'role'  => $u['role'], // admin | editor | piloto
  ];

  // Define destino padrão por papel
  $dest = '/';
  if ($u['role'] === 'admin')  $dest = '/admin/dashboard.php';
  if ($u['role'] === 'editor') $dest = '/editor/dashboard.php';

  // Se veio redirect, usa (mas só se for um path interno seguro)
  if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, '/') && !str_contains($redirect, "\n")) {
    $dest = $redirect;
  }

  // Cabeçalhos anti-cache (opcional, ajuda ao deslogar/logar)
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');

  header("Location: {$dest}");
  exit;

} catch (Throwable $e) {
  // Logue o erro de verdade em produção
  if (!function_exists('set_flash')) {
    $_SESSION['flash']['login_error'] = 'Erro interno ao autenticar.';
  } else {
    set_flash('login_error', 'Erro interno ao autenticar.');
  }
  header('Location: /rfg/admin-login/login.php');
  exit;
}

>>>>>>> 69bb93605bbc7806118eb2d75e23a16c5d146d8b
?>