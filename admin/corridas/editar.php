<?php
// admin/corridas/editar.php
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Editar Corrida — RFG';
global $pdo;

// Valida ID
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: /admin/corridas/listar.php');
  exit;
}

// Busca corrida
$st_corrida = $pdo->prepare("SELECT * FROM corridas WHERE id = ?");
$st_corrida->execute([$id]);
$corrida = $st_corrida->fetch(PDO::FETCH_ASSOC);

if (!$corrida) {
  set_flash('erro', 'Corrida não encontrada.');
  header('Location: /admin/corridas/listar.php');
  exit;
}

// Busca circuitos para o dropdown
$stmt_circuitos = $pdo->query("SELECT id, nome FROM circuitos ORDER BY nome");
$circuitos = $stmt_circuitos->fetchAll();

$erro = get_flash('erro');
$ok = get_flash('ok');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome_gp = trim($_POST['nome_gp'] ?? '');
  $circuito_id = (int) ($_POST['circuito_id'] ?? 0);
  $data = $_POST['data'] ?? '';
  $status = $_POST['status'] ?? 'agendada';

  if ($nome_gp === '' || $circuito_id <= 0 || $data === '') {
    $erro = 'Preencha ao menos Nome do GP, Circuito e Data.';
  } else {
    $sql = "UPDATE corridas
                SET nome_gp = :nome_gp, circuito_id = :circuito_id, data = :data, status = :status
                WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome_gp' => $nome_gp,
      ':circuito_id' => $circuito_id,
      ':data' => $data,
      ':status' => $status,
      ':id' => $id
    ]);

    set_flash('ok', 'Corrida atualizada com sucesso.');
    header("Location: /admin/corridas/editar.php?id=$id");
    exit;
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
      <h1 class="text-2xl font-bold">Editar Corrida</h1>
      <a href="/admin/corridas/listar.php" class="px-4 py-2 rounded border">Voltar</a>
    </div>

    <?php if ($ok): ?>
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
        <?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
        <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
      <div>
        <label class="block text-sm font-medium">Nome do GP *</label>
        <input name="nome_gp" required class="mt-1 w-full rounded-lg border px-3 py-2"
          value="<?= htmlspecialchars($corrida['nome_gp']) ?>">
      </div>
      <div>
        <label class="block text-sm font-medium">Circuito *</label>
        <select name="circuito_id" required class="mt-1 w-full rounded-lg border px-3 py-2">
          <option value="">Selecione um circuito</option>
          <?php foreach ($circuitos as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($c['id'] === $corrida['circuito_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Data *</label>
        <input type="date" name="data" required class="mt-1 w-full rounded-lg border px-3 py-2"
          value="<?= htmlspecialchars($corrida['data']) ?>">
      </div>
      <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="mt-1 w-full rounded-lg border px-3 py-2">
          <option value="agendada" <?= ($corrida['status'] === 'agendada') ? 'selected' : '' ?>>Agendada</option>
          <option value="finalizada" <?= ($corrida['status'] === 'finalizada') ? 'selected' : '' ?>>Finalizada</option>
          <option value="teste" <?= ($corrida['status'] === 'teste') ? 'selected' : '' ?>>Teste</option>
        </select>
      </div>

      <div class="md:col-span-2 flex gap-3 justify-end mt-4">
        <a href="/admin/corridas/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
        <button class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold
                 hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">>Salvar
          Alterações</button>
      </div>
    </form>
  </main>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>