<<<<<<< HEAD
<?php
// admin/corridas/cadastrar.php
session_start();

require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']); // só admin/editor

// p/ facilitar os includes de layout
define('INC', dirname(__DIR__, 2) . '/includes/');

$title = 'Cadastrar Corrida — RFG';

$ok = ''; $erro = '';

// inicializa armazenamento mock
if (!isset($_SESSION['mock_corridas'])) $_SESSION['mock_corridas'] = [];

// submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome     = trim($_POST['nome'] ?? '');
  $circuito = trim($_POST['circuito'] ?? '');
  $cidade   = trim($_POST['cidade'] ?? '');
  $pais     = trim($_POST['pais'] ?? '');
  $data     = $_POST['data'] ?? '';
  $voltas   = (int)($_POST['voltas'] ?? 0);
  $status   = $_POST['status'] ?? 'agendada';

  if ($nome === '' || $circuito === '' || $data === '') {
    $erro = 'Preencha ao menos Nome do GP, Circuito e Data.';
  } else {
    // ===== MOCK: salva na sessão =====
    $row = [
      'id'       => uniqid('gp_'),
      'nome'     => $nome,
      'circuito' => $circuito,
      'cidade'   => $cidade,
      'pais'     => $pais,
      'data'     => $data,
      'voltas'   => $voltas,
      'status'   => $status,
      'created'  => date('c'),
    ];
    $_SESSION['mock_corridas'][] = $row;
    $ok = 'Corrida cadastrada (mock).';

    /* ===== FUTURO (PDO/MySQL)
    require_once dirname(__DIR__, 2) . '/includes/db.php'; // $pdo
    $sql = "INSERT INTO corridas (nome, circuito, cidade, pais, data, voltas, status)
            VALUES (:nome, :circuito, :cidade, :pais, :data, :voltas, :status)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'     => $nome,
      ':circuito' => $circuito,
      ':cidade'   => $cidade,
      ':pais'     => $pais,
      ':data'     => $data,
      ':voltas'   => $voltas,
      ':status'   => $status,
    ]);
    $ok = 'Corrida cadastrada no banco.';
    */
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-5xl px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Cadastrar Corrida</h1>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
    <div>
      <label class="block text-sm font-medium">Nome do GP *</label>
      <input name="nome" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="GP da Espanha">
    </div>
    <div>
      <label class="block text-sm font-medium">Circuito *</label>
      <input name="circuito" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Circuit de Barcelona-Catalunya">
    </div>
    <div>
      <label class="block text-sm font-medium">Cidade</label>
      <input name="cidade" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Barcelona">
    </div>
    <div>
      <label class="block text-sm font-medium">País</label>
      <input name="pais" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Espanha">
    </div>
    <div>
      <label class="block text-sm font-medium">Data *</label>
      <input type="date" name="data" required class="mt-1 w-full rounded-lg border px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Voltas</label>
      <input type="number" min="1" name="voltas" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="66">
    </div>
    <div>
      <label class="block text-sm font-medium">Status</label>
      <select name="status" class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="agendada">Agendada</option>
        <option value="concluida">Concluída</option>
        <option value="cancelada">Cancelada</option>
      </select>
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-4">
      <a href="/admin/dashboard.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>

  <!-- lista mock só para conferir -->
  <?php if (!empty($_SESSION['mock_corridas'])): ?>
    <div class="mt-8">
      <h2 class="font-semibold mb-3">Corridas cadastradas (mock)</h2>
      <div class="space-y-2">
        <?php foreach (array_reverse($_SESSION['mock_corridas']) as $c): ?>
          <div class="rounded-lg border bg-white p-4 text-sm">
            <b><?= htmlspecialchars($c['nome']) ?></b> — <?= htmlspecialchars($c['circuito']) ?> —
            <?= htmlspecialchars($c['data']) ?> (<?= htmlspecialchars($c['status']) ?>)
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
=======
<?php
// admin/corridas/cadastrar.php
session_start();

require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']); // só admin/editor

// p/ facilitar os includes de layout
define('INC', dirname(__DIR__, 2) . '/includes/');

$title = 'Cadastrar Corrida — RFG';

$ok = ''; $erro = '';

// inicializa armazenamento mock
if (!isset($_SESSION['mock_corridas'])) $_SESSION['mock_corridas'] = [];

// submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome     = trim($_POST['nome'] ?? '');
  $circuito = trim($_POST['circuito'] ?? '');
  $cidade   = trim($_POST['cidade'] ?? '');
  $pais     = trim($_POST['pais'] ?? '');
  $data     = $_POST['data'] ?? '';
  $voltas   = (int)($_POST['voltas'] ?? 0);
  $status   = $_POST['status'] ?? 'agendada';

  if ($nome === '' || $circuito === '' || $data === '') {
    $erro = 'Preencha ao menos Nome do GP, Circuito e Data.';
  } else {
    // ===== MOCK: salva na sessão =====
    $row = [
      'id'       => uniqid('gp_'),
      'nome'     => $nome,
      'circuito' => $circuito,
      'cidade'   => $cidade,
      'pais'     => $pais,
      'data'     => $data,
      'voltas'   => $voltas,
      'status'   => $status,
      'created'  => date('c'),
    ];
    $_SESSION['mock_corridas'][] = $row;
    $ok = 'Corrida cadastrada (mock).';

    /* ===== FUTURO (PDO/MySQL)
    require_once dirname(__DIR__, 2) . '/includes/db.php'; // $pdo
    $sql = "INSERT INTO corridas (nome, circuito, cidade, pais, data, voltas, status)
            VALUES (:nome, :circuito, :cidade, :pais, :data, :voltas, :status)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'     => $nome,
      ':circuito' => $circuito,
      ':cidade'   => $cidade,
      ':pais'     => $pais,
      ':data'     => $data,
      ':voltas'   => $voltas,
      ':status'   => $status,
    ]);
    $ok = 'Corrida cadastrada no banco.';
    */
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-5xl px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Cadastrar Corrida</h1>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
    <div>
      <label class="block text-sm font-medium">Nome do GP *</label>
      <input name="nome" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="GP da Espanha">
    </div>
    <div>
      <label class="block text-sm font-medium">Circuito *</label>
      <input name="circuito" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Circuit de Barcelona-Catalunya">
    </div>
    <div>
      <label class="block text-sm font-medium">Cidade</label>
      <input name="cidade" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Barcelona">
    </div>
    <div>
      <label class="block text-sm font-medium">País</label>
      <input name="pais" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Espanha">
    </div>
    <div>
      <label class="block text-sm font-medium">Data *</label>
      <input type="date" name="data" required class="mt-1 w-full rounded-lg border px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Voltas</label>
      <input type="number" min="1" name="voltas" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="66">
    </div>
    <div>
      <label class="block text-sm font-medium">Status</label>
      <select name="status" class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="agendada">Agendada</option>
        <option value="concluida">Concluída</option>
        <option value="cancelada">Cancelada</option>
      </select>
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-4">
      <a href="/admin/dashboard.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>

  <!-- lista mock só para conferir -->
  <?php if (!empty($_SESSION['mock_corridas'])): ?>
    <div class="mt-8">
      <h2 class="font-semibold mb-3">Corridas cadastradas (mock)</h2>
      <div class="space-y-2">
        <?php foreach (array_reverse($_SESSION['mock_corridas']) as $c): ?>
          <div class="rounded-lg border bg-white p-4 text-sm">
            <b><?= htmlspecialchars($c['nome']) ?></b> — <?= htmlspecialchars($c['circuito']) ?> —
            <?= htmlspecialchars($c['data']) ?> (<?= htmlspecialchars($c['status']) ?>)
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
>>>>>>> 69bb93605bbc7806118eb2d75e23a16c5d146d8b
</html>