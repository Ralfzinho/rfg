<?php
// admin/corridas/cadastrar.php
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Cadastrar Corrida — RFG';
global $pdo;

// Busca circuitos para o dropdown
$stmt_circuitos = $pdo->query("SELECT id, nome FROM circuitos ORDER BY nome");
$circuitos = $stmt_circuitos->fetchAll();

$erro = get_flash('erro');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_gp     = trim($_POST['nome_gp'] ?? '');
    $circuito_id = (int)($_POST['circuito_id'] ?? 0);
    $data        = $_POST['data'] ?? '';
    $status      = $_POST['status'] ?? 'agendada';

    if ($nome_gp === '' || $circuito_id <= 0 || $data === '') {
        set_flash('erro', 'Preencha ao menos Nome do GP, Circuito e Data.');
        header('Location: /admin/corridas/cadastrar.php');
        exit;
    } else {
        $sql = "INSERT INTO corridas (nome_gp, circuito_id, data, status)
                VALUES (:nome_gp, :circuito_id, :data, :status)";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':nome_gp'     => $nome_gp,
            ':circuito_id' => $circuito_id,
            ':data'        => $data,
            ':status'      => $status,
        ]);

        set_flash('ok', 'Corrida cadastrada com sucesso.');
        header('Location: /admin/corridas/listar.php');
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
      <h1 class="text-2xl font-bold">Cadastrar Corrida</h1>
      <a href="/admin/corridas/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="grid md:grid-cols-2 gap-4 bg-white border rounded-2xl p-6 shadow">
    <div>
      <label class="block text-sm font-medium">Nome do GP *</label>
      <input name="nome_gp" required class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="GP da Espanha">
    </div>
    <div>
      <label class="block text-sm font-medium">Circuito *</label>
      <select name="circuito_id" required class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="">Selecione um circuito</option>
        <?php foreach ($circuitos as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Data *</label>
      <input type="date" name="data" required class="mt-1 w-full rounded-lg border px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Status</label>
      <select name="status" class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="agendada">Agendada</option>
        <option value="finalizada">Finalizada</option>
        <option value="teste">Teste</option>
      </select>
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-4">
      <a href="/admin/corridas/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
