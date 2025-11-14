<?php
// admin/pilotos/editar.php
session_start();
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Editar Piloto — RFG';
global $pdo;

// Valida ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /admin/pilotos/listar.php');
    exit;
}

// Busca piloto
$st_piloto = $pdo->prepare("SELECT * FROM pilotos WHERE id = ?");
$st_piloto->execute([$id]);
$piloto = $st_piloto->fetch(PDO::FETCH_ASSOC);

if (!$piloto) {
    set_flash('erro', 'Piloto não encontrado.');
    header('Location: /admin/pilotos/listar.php');
    exit;
}

// Busca equipes para o dropdown
$stmt_equipes = $pdo->query("SELECT id, nome FROM equipes ORDER BY nome");
$equipes = $stmt_equipes->fetchAll();

// Pega erro/ok da sessão, se houver
$erro = get_flash('erro');
$ok = get_flash('ok');

// Processa o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $numero    = (int)($_POST['numero'] ?? 0);
    $pais      = trim($_POST['pais'] ?? '');
    $equipe_id = (int)($_POST['equipe_id'] ?? 0);
    $foto_url  = trim($_POST['foto_url'] ?? '');

    if ($nome === '' || $numero <= 0 || $equipe_id <= 0) {
        $erro = 'Informe ao menos Nome, Número e Equipe.';
    } else {
        $sql = "UPDATE pilotos
                SET nome = :nome, numero = :numero, pais = :pais, equipe_id = :equipe_id, foto_url = :foto_url
                WHERE id = :id";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':nome'      => $nome,
            ':numero'    => $numero,
            ':pais'      => $pais,
            ':equipe_id' => $equipe_id,
            ':foto_url'  => $foto_url,
            ':id'        => $id
        ]);

        set_flash('ok', 'Piloto atualizado com sucesso.');
        // Recarrega a página para ver a msg e os dados atualizados
        header("Location: /admin/pilotos/editar.php?id=$id");
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
    <h1 class="text-2xl font-bold">Editar Piloto</h1>
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
      <input name="nome" required class="mt-1 w-full rounded-lg border px-3 py-2" value="<?= htmlspecialchars($piloto['nome']) ?>">
    </div>
    <div>
      <label class="block text-sm font-medium">Número *</label>
      <input name="numero" type="number" min="1" required class="mt-1 w-full rounded-lg border px-3 py-2" value="<?= (int)$piloto['numero'] ?>">
    </div>
    <div>
      <label class="block text-sm font-medium">País</label>
      <input name="pais" class="mt-1 w-full rounded-lg border px-3 py-2" value="<?= htmlspecialchars($piloto['pais'] ?? '') ?>">
    </div>
    <div>
      <label class="block text-sm font-medium">Equipe *</label>
      <select name="equipe_id" required class="mt-1 w-full rounded-lg border px-3 py-2">
        <option value="">Selecione uma equipe</option>
        <?php foreach ($equipes as $e): ?>
          <option value="<?= $e['id'] ?>" <?= ($e['id'] === $piloto['equipe_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Foto (URL)</label>
      <input name="foto_url" class="mt-1 w-full rounded-lg border px-3 py-2" value="<?= htmlspecialchars($piloto['foto_url'] ?? '') ?>">
    </div>

    <div class="md:col-span-2 flex gap-3 justify-end mt-2">
      <a href="/admin/pilotos/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar Alterações</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
