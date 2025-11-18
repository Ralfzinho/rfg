<?php
// admin-login/criar_admin_process.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__) . '/includes/funcoes.php';
global $pdo;

// Apenas admins podem criar novos admins, mas pode ser ajustado
// auth_require_role(['admin']);

// Se não for POST, redireciona
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /rfg/admin-login/criar_admin.php');
  exit;
}

// Coleta e sanitiza os dados
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$role  = trim($_POST['role'] ?? 'editor');

// Validação básica
if (empty($name) || empty($email) || empty($senha) || !in_array($role, ['admin', 'editor'])) {
  set_flash('erro', 'Todos os campos são obrigatórios.');
  header('Location: /rfg/admin-login/criar_admin.php');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  set_flash('erro', 'Formato de e-mail inválido.');
  header('Location: /rfg/admin-login/criar_admin.php');
  exit;
}

try {
  // Verifica se o e-mail já existe
  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
  $stmt->execute([':email' => $email]);
  if ($stmt->fetch()) {
    set_flash('erro', 'Este e-mail já está em uso.');
    header('Location: /rfg/admin-login/criar_admin.php');
    exit;
  }

  // Hash da senha
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

  // Insere no banco
  $sql = "INSERT INTO usuarios (name, email, password, role) VALUES (:name, :email, :password, :role)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':name'     => $name,
    ':email'    => $email,
    ':password' => $senha_hash,
    ':role'     => $role,
  ]);

  set_flash('ok', 'Administrador criado com sucesso!');
  header('Location: /rfg/admin-login/login.php');
  exit;

} catch (PDOException $e) {
  // Em caso de erro, exibe uma mensagem genérica
  // error_log($e->getMessage()); // log para depuração
  set_flash('erro', 'Ocorreu um erro ao criar o administrador. Tente novamente.');
  header('Location: /rfg/admin-login/criar_admin.php');
  exit;
}
