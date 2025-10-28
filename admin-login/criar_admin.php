<?php
require_once __DIR__ . '/../includes/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome && $email && $senha) {
        // verifica se já existe admin
        $check = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role = 'admin'")->fetchColumn();

        if ($check > 0) {
            $msg = "Já existe um administrador cadastrado!";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            // ✅ Corrigido: campos corretos do banco
            $stmt = $pdo->prepare("INSERT INTO usuarios (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$nome, $email, $hash]);

            $msg = "Administrador criado com sucesso!";
        }
    } else {
        $msg = "Preencha todos os campos.";
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Criar Admin - Race for Glory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Criar Admin</h1>

    <?php if ($msg): ?>
      <div class="mb-4 p-3 rounded text-white <?= str_contains($msg,'sucesso') ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Nome</label>
        <input type="text" name="nome" required class="w-full border rounded p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" required class="w-full border rounded p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Senha</label>
        <input type="password" name="senha" required class="w-full border rounded p-2">
      </div>
      <button type="submit" class="w-full bg-[#FFD700] hover:bg-[#E6C200] text-black font-semibold py-2 rounded">
        Criar Admin
      </button>
    </form>
  </div>
</body>
</html>
