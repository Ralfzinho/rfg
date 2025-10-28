<<<<<<< HEAD
<?php
// admin/corridas/resultados.php
session_start();

require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Resultados — RFG';

// bases mock
if (!isset($_SESSION['mock_corridas']))   $_SESSION['mock_corridas'] = [];
if (!isset($_SESSION['mock_resultados'])) $_SESSION['mock_resultados'] = [];

$ok = ''; $erro = '';

$corridas = $_SESSION['mock_corridas']; // para o select

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $corrida_id = $_POST['corrida_id'] ?? '';
  $posicoes   = $_POST['pos'] ?? [];     // arrays paralelos
  $pilotos    = $_POST['piloto'] ?? [];
  $equipes    = $_POST['equipe'] ?? [];
  $pontos     = $_POST['pontos'] ?? [];

  if ($corrida_id === '' || empty($posicoes)) {
    $erro = 'Escolha a corrida e preencha ao menos um resultado.';
  } else {
    // monta lista
    $lista = [];
    for ($i = 0; $i < count($posicoes); $i++) {
      if (trim($pilotos[$i] ?? '') === '') continue; // ignora linhas vazias
      $lista[] = [
        'pos'    => (int)($posicoes[$i] ?? 0),
        'piloto' => trim($pilotos[$i] ?? ''),
        'equipe' => trim($equipes[$i] ?? ''),
        'pontos' => (int)($pontos[$i] ?? 0),
      ];
    }

    if (empty($lista)) {
      $erro = 'Preencha ao menos uma linha válida.';
    } else {
      // ===== MOCK: grava na sessão =====
      $_SESSION['mock_resultados'][$corrida_id] = [
        'corrida_id' => $corrida_id,
        'itens'      => $lista,
        'saved_at'   => date('c'),
      ];
      $ok = 'Resultados salvos (mock).';

      /* ===== FUTURO (PDO/MySQL)
      require_once dirname(__DIR__, 2) . '/includes/db.php';
      // exemplo básico: limpa antigos e insere novamente
      $pdo->beginTransaction();
      $pdo->prepare("DELETE FROM resultados WHERE corrida_id = ?")->execute([$corrida_id]);

      $ins = $pdo->prepare("INSERT INTO resultados (corrida_id, pos, piloto, equipe, pontos)
                            VALUES (?, ?, ?, ?, ?)");
      foreach ($lista as $r) {
        $ins->execute([$corrida_id, $r['pos'], $r['piloto'], $r['equipe'], $r['pontos']]);
      }
      $pdo->commit();
      $ok = 'Resultados salvos no banco.';
      */
    }
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-6xl px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Resultados da Corrida</h1>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="bg-white border rounded-2xl p-6 shadow space-y-4">
    <div>
      <label class="block text-sm font-medium">Corrida</label>
      <select name="corrida_id" class="mt-1 w-full rounded-lg border px-3 py-2" required>
        <option value="">Selecione...</option>
        <?php foreach ($corridas as $c): ?>
          <option value="<?= htmlspecialchars($c['id']) ?>">
            <?= htmlspecialchars($c['nome'].' — '.$c['data']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="py-2 pr-3">Pos.</th>
            <th class="py-2 pr-3">Piloto</th>
            <th class="py-2 pr-3">Equipe</th>
            <th class="py-2 pr-3">Pontos</th>
          </tr>
        </thead>
        <tbody id="linhas">
          <?php for ($i=1; $i<=10; $i++): ?>
            <tr class="border-b">
              <td class="py-1 pr-3 w-14">
                <input name="pos[]" type="number" value="<?= $i ?>" class="w-16 rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3">
                <input name="piloto[]" placeholder="Nome do piloto" class="w-full rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3">
                <input name="equipe[]" placeholder="Equipe" class="w-full rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3 w-24">
                <input name="pontos[]" type="number" value="<?= 26 - ($i-1)*2 < 0 ? 0 : 26 - ($i-1)*2 ?>" class="w-24 rounded border px-2 py-1">
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <button type="button" id="addRow" class="px-4 py-2 rounded border">+ Linha</button>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>

  <!-- bloco de conferência do mock -->
  <?php if (!empty($_SESSION['mock_resultados'])): ?>
    <div class="mt-8">
      <h2 class="font-semibold mb-3">Resultados salvos (mock)</h2>
      <?php foreach ($_SESSION['mock_resultados'] as $rid => $pack): ?>
        <div class="rounded-lg border bg-white p-4 mb-3">
          <div class="text-sm text-neutral-600 mb-2">corrida_id: <code><?= htmlspecialchars($rid) ?></code></div>
          <ol class="list-decimal ml-6 text-sm">
            <?php foreach ($pack['itens'] as $r): ?>
              <li><?= htmlspecialchars($r['pos'].'º - '.$r['piloto'].' ('.$r['equipe'].') — '.$r['pontos'].' pts') ?></li>
            <?php endforeach; ?>
          </ol>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php require INC . 'layout_footer.php'; ?>

<script>
// adiciona linhas dinamicamente
document.getElementById('addRow')?.addEventListener('click', () => {
  const tbody = document.getElementById('linhas');
  const tr = document.createElement('tr');
  tr.className = 'border-b';
  tr.innerHTML = `
    <td class="py-1 pr-3 w-14">
      <input name="pos[]" type="number" class="w-16 rounded border px-2 py-1" value="">
    </td>
    <td class="py-1 pr-3">
      <input name="piloto[]" placeholder="Nome do piloto" class="w-full rounded border px-2 py-1">
    </td>
    <td class="py-1 pr-3">
      <input name="equipe[]" placeholder="Equipe" class="w-full rounded border px-2 py-1">
    </td>
    <td class="py-1 pr-3 w-24">
      <input name="pontos[]" type="number" class="w-24 rounded border px-2 py-1" value="0">
    </td>
  `;
  tbody.appendChild(tr);
});
</script>
</body>
</html>
=======
<?php
// admin/corridas/resultados.php
session_start();

require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Resultados — RFG';

// bases mock
if (!isset($_SESSION['mock_corridas']))   $_SESSION['mock_corridas'] = [];
if (!isset($_SESSION['mock_resultados'])) $_SESSION['mock_resultados'] = [];

$ok = ''; $erro = '';

$corridas = $_SESSION['mock_corridas']; // para o select

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $corrida_id = $_POST['corrida_id'] ?? '';
  $posicoes   = $_POST['pos'] ?? [];     // arrays paralelos
  $pilotos    = $_POST['piloto'] ?? [];
  $equipes    = $_POST['equipe'] ?? [];
  $pontos     = $_POST['pontos'] ?? [];

  if ($corrida_id === '' || empty($posicoes)) {
    $erro = 'Escolha a corrida e preencha ao menos um resultado.';
  } else {
    // monta lista
    $lista = [];
    for ($i = 0; $i < count($posicoes); $i++) {
      if (trim($pilotos[$i] ?? '') === '') continue; // ignora linhas vazias
      $lista[] = [
        'pos'    => (int)($posicoes[$i] ?? 0),
        'piloto' => trim($pilotos[$i] ?? ''),
        'equipe' => trim($equipes[$i] ?? ''),
        'pontos' => (int)($pontos[$i] ?? 0),
      ];
    }

    if (empty($lista)) {
      $erro = 'Preencha ao menos uma linha válida.';
    } else {
      // ===== MOCK: grava na sessão =====
      $_SESSION['mock_resultados'][$corrida_id] = [
        'corrida_id' => $corrida_id,
        'itens'      => $lista,
        'saved_at'   => date('c'),
      ];
      $ok = 'Resultados salvos (mock).';

      /* ===== FUTURO (PDO/MySQL)
      require_once dirname(__DIR__, 2) . '/includes/db.php';
      // exemplo básico: limpa antigos e insere novamente
      $pdo->beginTransaction();
      $pdo->prepare("DELETE FROM resultados WHERE corrida_id = ?")->execute([$corrida_id]);

      $ins = $pdo->prepare("INSERT INTO resultados (corrida_id, pos, piloto, equipe, pontos)
                            VALUES (?, ?, ?, ?, ?)");
      foreach ($lista as $r) {
        $ins->execute([$corrida_id, $r['pos'], $r['piloto'], $r['equipe'], $r['pontos']]);
      }
      $pdo->commit();
      $ok = 'Resultados salvos no banco.';
      */
    }
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-6xl px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Resultados da Corrida</h1>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="bg-white border rounded-2xl p-6 shadow space-y-4">
    <div>
      <label class="block text-sm font-medium">Corrida</label>
      <select name="corrida_id" class="mt-1 w-full rounded-lg border px-3 py-2" required>
        <option value="">Selecione...</option>
        <?php foreach ($corridas as $c): ?>
          <option value="<?= htmlspecialchars($c['id']) ?>">
            <?= htmlspecialchars($c['nome'].' — '.$c['data']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="py-2 pr-3">Pos.</th>
            <th class="py-2 pr-3">Piloto</th>
            <th class="py-2 pr-3">Equipe</th>
            <th class="py-2 pr-3">Pontos</th>
          </tr>
        </thead>
        <tbody id="linhas">
          <?php for ($i=1; $i<=10; $i++): ?>
            <tr class="border-b">
              <td class="py-1 pr-3 w-14">
                <input name="pos[]" type="number" value="<?= $i ?>" class="w-16 rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3">
                <input name="piloto[]" placeholder="Nome do piloto" class="w-full rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3">
                <input name="equipe[]" placeholder="Equipe" class="w-full rounded border px-2 py-1">
              </td>
              <td class="py-1 pr-3 w-24">
                <input name="pontos[]" type="number" value="<?= 26 - ($i-1)*2 < 0 ? 0 : 26 - ($i-1)*2 ?>" class="w-24 rounded border px-2 py-1">
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <button type="button" id="addRow" class="px-4 py-2 rounded border">+ Linha</button>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>

  <!-- bloco de conferência do mock -->
  <?php if (!empty($_SESSION['mock_resultados'])): ?>
    <div class="mt-8">
      <h2 class="font-semibold mb-3">Resultados salvos (mock)</h2>
      <?php foreach ($_SESSION['mock_resultados'] as $rid => $pack): ?>
        <div class="rounded-lg border bg-white p-4 mb-3">
          <div class="text-sm text-neutral-600 mb-2">corrida_id: <code><?= htmlspecialchars($rid) ?></code></div>
          <ol class="list-decimal ml-6 text-sm">
            <?php foreach ($pack['itens'] as $r): ?>
              <li><?= htmlspecialchars($r['pos'].'º - '.$r['piloto'].' ('.$r['equipe'].') — '.$r['pontos'].' pts') ?></li>
            <?php endforeach; ?>
          </ol>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php require INC . 'layout_footer.php'; ?>

<script>
// adiciona linhas dinamicamente
document.getElementById('addRow')?.addEventListener('click', () => {
  const tbody = document.getElementById('linhas');
  const tr = document.createElement('tr');
  tr.className = 'border-b';
  tr.innerHTML = `
    <td class="py-1 pr-3 w-14">
      <input name="pos[]" type="number" class="w-16 rounded border px-2 py-1" value="">
    </td>
    <td class="py-1 pr-3">
      <input name="piloto[]" placeholder="Nome do piloto" class="w-full rounded border px-2 py-1">
    </td>
    <td class="py-1 pr-3">
      <input name="equipe[]" placeholder="Equipe" class="w-full rounded border px-2 py-1">
    </td>
    <td class="py-1 pr-3 w-24">
      <input name="pontos[]" type="number" class="w-24 rounded border px-2 py-1" value="0">
    </td>
  `;
  tbody.appendChild(tr);
});
</script>
</body>
</html>
>>>>>>> 69bb93605bbc7806118eb2d75e23a16c5d146d8b
