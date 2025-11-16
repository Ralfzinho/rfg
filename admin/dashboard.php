<?php
// SEMPRE primeiro
session_start();

require_once dirname(__DIR__) . '/includes/funcoes.php';

// exige login; se não tiver, redireciona pro login
auth_require_login();

// pega usuário atual de forma segura
$user = auth_user(); // array com ['id','name','email','role']

$title = 'Painel — Racing for Glory';
?>

<!doctype html>
<html lang="pt-br">

<head>
  <?php require dirname(__DIR__) . '/includes/layout_head.php'; ?>
</head>

<body class="bg-neutral-50 text-neutral-900">
  <?php require dirname(__DIR__) . '/includes/layout_nav.php'; ?>

  <main class="mx-auto max-w-6xl px-4 py-8">
    <div class="mb-6">
      <h2 class="text-2xl font-semibold">Olá, <?= htmlspecialchars($user['name'] ?? 'Usuário') ?></h2>
      <p class="text-neutral-500">Painel em modo <b>mock</b> (sem banco de dados).</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Pilotos -->
      <a href="../../rfg/admin/pilotos/listar.php" class="block rounded-2xl border bg-white p-6 shadow hover:shadow-md transition">
        <h3 class="font-semibold mb-2">Pilotos</h3>
        <p class="text-sm text-neutral-500">Gerenciar lista de pilotos</p>
      </a>

      <!-- Equipes -->
      <a href="../../rfg/admin/equipes/listar.php" class="block rounded-2xl border bg-white p-6 shadow hover:shadow-md transition">
        <h3 class="font-semibold mb-2">Equipes</h3>
        <p class="text-sm text-neutral-500">Gerenciar lista de equipes</p>
      </a>

      <!-- Corridas -->
      <a href="../../rfg/admin/corridas/listar.php" class="block rounded-2xl border bg-white p-6 shadow hover:shadow-md transition">
        <h3 class="font-semibold mb-2">Corridas</h3>
        <p class="text-sm text-neutral-500">Gerenciar corridas e calendário</p>
      </a>

      <!-- Resultados -->
      <a href="../../rfg/admin/resultados/listar.php" class="block rounded-2xl border bg-white p-6 shadow hover:shadow-md transition">
        <h3 class="font-semibold mb-2">Resultados</h3>
        <p class="text-sm text-neutral-500">Lançar resultados de corridas</p>
      </a>

      <!-- Classificação -->
      <a href="../../rfg/admin/classificacao.php" class="block rounded-2xl border bg-white p-6 shadow hover:shadow-md transition">
        <h3 class="font-semibold mb-2">Classificação</h3>
        <p class="text-sm text-neutral-500">Tabela por equipes/pilotos</p>
      </a>
    </div>
  </main>

  <?php require dirname(__DIR__) . '/includes/layout_footer.php'; ?>
</body>

</html>