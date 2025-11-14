<?php
// admin/pilotos/cadastrar.php
session_start();
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Cadastrar Piloto — RFG';

// Busca equipes para o dropdown
global $pdo;
$stmt_equipes = $pdo->query("SELECT id, nome FROM equipes ORDER BY nome");
$equipes = $stmt_equipes->fetchAll();

// Pega erro da sessão, se houver, para exibir no formulário
$erro = get_flash('erro');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome      = trim($_POST['nome'] ?? '');
  $numero    = (int)($_POST['numero'] ?? 0);
  $pais      = trim($_POST['pais'] ?? '');
  $equipe_id = (int)($_POST['equipe_id'] ?? 0);
  $foto_url  = trim($_POST['foto_url'] ?? '');

  if ($nome === '' || $numero <= 0 || $equipe_id <= 0) {
    set_flash('erro', 'Informe ao menos Nome, Número e Equipe.');
    // Redireciona de volta para o formulário para exibir o erro
    header('Location: /admin/pilotos/cadastrar.php');
    exit;
  } else {
    $sql = "INSERT INTO pilotos (nome, numero, pais, equipe_id, foto_url)
            VALUES (:nome, :numero, :pais, :equipe_id, :foto_url)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'      => $nome,
      ':numero'    => $numero,
      ':pais'      => $pais,
      ':equipe_id' => $equipe_id,
      ':foto_url'  => $foto_url
    ]);

    // Define msg de sucesso e redireciona para listagem
    set_flash('ok', 'Piloto cadastrado com sucesso.');
    header('Location: /admin/pilotos/listar.php');
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
    <h1 class="text-2xl font-bold">Cadastrar Piloto</h1>
    <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

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
      <label class="block text-sm font-medium">Equipe *</label>
      <select name="equipe_id" required class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="">Selecione uma equipe</option>
        <?php foreach ($equipes as $e): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
        <?php endforeach; ?>
      </select>
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
