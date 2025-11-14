<?php
// admin/equipes/editar.php
require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Editar Equipe — RFG';
global $pdo;

// Valida ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /admin/equipes/listar.php');
    exit;
}

// Busca equipe
$st_equipe = $pdo->prepare("SELECT * FROM equipes WHERE id = ?");
$st_equipe->execute([$id]);
$equipe = $st_equipe->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    set_flash('erro', 'Equipe não encontrada.');
    header('Location: /admin/equipes/listar.php');
    exit;
}

// Pega mensagens
$erro = get_flash('erro');
$ok = get_flash('ok');

// Processa o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $pais = trim($_POST['pais'] ?? '');

    if ($nome === '' || $pais === '') {
        $erro = 'Preencha Nome e País.';
    } else {
        $sql = "UPDATE equipes SET nome = :nome, pais = :pais WHERE id = :id";
        $st = $pdo->prepare($sql);
        $st->execute([':nome' => $nome, ':pais' => $pais, ':id' => $id]);

        set_flash('ok', 'Equipe atualizada com sucesso.');
        header("Location: /admin/equipes/editar.php?id=$id");
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
    <h1 class="text-2xl font-bold">Editar Equipe</h1>
    <a href="/admin/equipes/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($ok): ?>
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="bg-white border rounded-2xl p-6 shadow space-y-4">
    <div>
      <label class="block text-sm font-medium">Nome da Equipe *</label>
      <input type="text" name="nome" required class="mt-1 w-full border rounded px-3 py-2" value="<?= htmlspecialchars($equipe['nome']) ?>">
    </div>
    <div>
      <label class="block text-sm font-medium">País *</label>
      <input type="text" name="pais" required class="mt-1 w-full border rounded px-3 py-2" value="<?= htmlspecialchars($equipe['pais']) ?>">
    </div>
    <div class="flex gap-3 justify-end mt-2">
      <a href="/admin/equipes/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button type="submit" class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar Alterações</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
