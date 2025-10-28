<<<<<<< HEAD
<?php
// admin/pilotos/cadastrar.php
session_start();
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Cadastrar Piloto — RFG';

$_SESSION['mock_pilotos'] ??= [];
$ok = ''; $erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome   = trim($_POST['nome'] ?? '');
  $numero = (int)($_POST['numero'] ?? 0);
  $pais   = trim($_POST['pais'] ?? '');
  $equipe = trim($_POST['equipe'] ?? '');
  $foto   = trim($_POST['foto_url'] ?? '');
  $nasc   = $_POST['nascimento'] ?? '';

  if ($nome === '' || $numero <= 0) {
    $erro = 'Informe ao menos Nome e Número.';
  } else {
    // MOCK
    $_SESSION['mock_pilotos'][] = [
      'id'         => uniqid('drv_'),
      'nome'       => $nome,
      'numero'     => $numero,
      'pais'       => $pais,
      'equipe'     => $equipe,
      'foto_url'   => $foto,
      'nascimento' => $nasc,
      'created'    => date('c'),
    ];
    $ok = 'Piloto cadastrado (mock).';

    /* ===== FUTURO (PDO)
    require_once dirname(__DIR__, 2) . '/includes/db.php';
    $sql = "INSERT INTO pilotos (nome, numero, pais, equipe, foto_url, nascimento)
            VALUES (:nome,:numero,:pais,:equipe,:foto,:nasc)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome' => $nome, ':numero' => $numero, ':pais' => $pais,
      ':equipe' => $equipe, ':foto' => $foto, ':nasc' => $nasc
    ]);
    $ok = 'Piloto cadastrado no banco.';
    */
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-3xl px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Cadastrar Piloto</h1>
    <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Nome *</label>
      <input name="nome" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Oscar Piastri">
    </div>
    <div>
      <label class="block text-sm font-medium">Número *</label>
      <input name="numero" type="number" min="1" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="81">
    </div>
    <div>
      <label class="block text-sm font-medium">País</label>
      <input name="pais" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Austrália">
    </div>
    <div>
      <label class="block text-sm font-medium">Equipe</label>
      <input name="equipe" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="McLaren">
    </div>
    <div>
      <label class="block text-sm font-medium">Data de nascimento</label>
      <input type="date" name="nascimento" class="mt-1 w-full rounded-lg border px-3 py-2">
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Foto (URL)</label>
      <input name="foto_url" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="https://...">
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-2">
      <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
=======
<?php
// admin/pilotos/cadastrar.php
session_start();
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Cadastrar Piloto — RFG';

$_SESSION['mock_pilotos'] ??= [];
$ok = ''; $erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome   = trim($_POST['nome'] ?? '');
  $numero = (int)($_POST['numero'] ?? 0);
  $pais   = trim($_POST['pais'] ?? '');
  $equipe = trim($_POST['equipe'] ?? '');
  $foto   = trim($_POST['foto_url'] ?? '');
  $nasc   = $_POST['nascimento'] ?? '';

  if ($nome === '' || $numero <= 0) {
    $erro = 'Informe ao menos Nome e Número.';
  } else {
    // MOCK
    $_SESSION['mock_pilotos'][] = [
      'id'         => uniqid('drv_'),
      'nome'       => $nome,
      'numero'     => $numero,
      'pais'       => $pais,
      'equipe'     => $equipe,
      'foto_url'   => $foto,
      'nascimento' => $nasc,
      'created'    => date('c'),
    ];
    $ok = 'Piloto cadastrado (mock).';

    /* ===== FUTURO (PDO)
    require_once dirname(__DIR__, 2) . '/includes/db.php';
    $sql = "INSERT INTO pilotos (nome, numero, pais, equipe, foto_url, nascimento)
            VALUES (:nome,:numero,:pais,:equipe,:foto,:nasc)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome' => $nome, ':numero' => $numero, ':pais' => $pais,
      ':equipe' => $equipe, ':foto' => $foto, ':nasc' => $nasc
    ]);
    $ok = 'Piloto cadastrado no banco.';
    */
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-3xl px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Cadastrar Piloto</h1>
    <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Nome *</label>
      <input name="nome" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Oscar Piastri">
    </div>
    <div>
      <label class="block text-sm font-medium">Número *</label>
      <input name="numero" type="number" min="1" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="81">
    </div>
    <div>
      <label class="block text-sm font-medium">País</label>
      <input name="pais" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Austrália">
    </div>
    <div>
      <label class="block text-sm font-medium">Equipe</label>
      <input name="equipe" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="McLaren">
    </div>
    <div>
      <label class="block text-sm font-medium">Data de nascimento</label>
      <input type="date" name="nascimento" class="mt-1 w-full rounded-lg border px-3 py-2">
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Foto (URL)</label>
      <input name="foto_url" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="https://...">
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-2">
      <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
>>>>>>> 69bb93605bbc7806118eb2d75e23a16c5d146d8b
