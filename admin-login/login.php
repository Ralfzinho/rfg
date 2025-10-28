<?php $title = 'Login — Racing for Glory';
include __DIR__ . '/../includes/layout_head.php'; ?>
<section class="min-h-screen grid place-items-center bg-neutral-100">
  <div class="w-full max-w-md bg-white shadow rounded-2xl p-8">
    <h1 class="text-2xl font-bold text-center mb-1">Racing for Glory</h1>
    <p class="text-center text-neutral-500 mb-6">Acesse o painel</p>

    <?php if (!empty($_GET['err'])): ?>
      <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2">E-mail ou senha inválidos.</div>
    <?php endif; ?>
    <form method="post" action="/rfg/admin-login/login_process.php" class="space-y-4">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '/admin/dashboard.php') ?>">
      <div>
        <label class="block text-sm font-medium mb-1">E-mail</label>
        <input type="email" name="email" required
          class="w-full rounded-lg border border-neutral-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Senha</label>
        <input type="password" name="password" required
          class="w-full rounded-lg border border-neutral-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
      </div>
      <button class="w-full bg-dark text-white rounded-lg py-2.5 hover:opacity-90">Entrar</button>
      </form>

      <p class="text-xs text-neutral-500 mt-4"> Faça o Login para acessar as opções do painel</p>
  </div>
</section>
<?php include __DIR__ . '/../includes/layout_footer.php'; ?>