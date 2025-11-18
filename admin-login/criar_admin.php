<?php
// admin-login/criar_admin.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__) . '/includes/funcoes.php';
// Descomente a linha abaixo para exigir login de admin para criar novos admins
// auth_require_role(['admin']);

$title = 'Criar Novo Administrador';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <?php require dirname(__DIR__) . '/includes/layout_head.php'; ?>
</head>
<body class="bg-neutral-100">
  <main class="mx-auto max-w-lg px-4 py-8">
    <div class="bg-white p-6 rounded-xl shadow-lg">
      <h1 class="text-2xl font-bold text-center mb-6">Criar Novo Administrador</h1>

      <?php if (has_flash('erro')): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-800 border border-red-200 rounded-lg text-sm">
          <?= flash('erro') ?>
        </div>
      <?php endif; ?>

      <form method="post" action="/rfg/admin-login/criar_admin_process.php" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" id="name" name="name" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
          <input type="email" id="email" name="email" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
        </div>
        <div>
          <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
          <input type="password" id="senha" name="senha" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Função</label>
            <select id="role" name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="pt-2">
          <button type="submit"
                  class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
            Criar Administrador
          </button>
        </div>
      </form>
    </div>
  </main>
  <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
</body>
</html>
