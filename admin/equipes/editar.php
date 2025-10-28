<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_login();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: listar.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM equipes WHERE id = ?");
$stmt->execute([$id]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    die('Equipe não encontrada.');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $chefe = trim($_POST['chefe'] ?? '');

    if ($nome === '' || $pais === '' || $chefe === '') {
        $erro = 'Preencha todos os campos!';
    } else {
        $stmt = $pdo->prepare("UPDATE equipes SET nome = ?, pais = ?, chefe = ? WHERE id = ?");
        $stmt->execute([$nome, $pais, $chefe, $id]);
        header('Location: listar.php?sucesso=1');
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-br">
<?php include __DIR__ . '/../../includes/layout_head.php'; ?>
<body class="bg-neutral-50 text-neutral-900">
<?php include __DIR__ . '/../../includes/layout_nav.php'; ?>

<main class="mx-auto max-w-3xl px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Editar Equipe</h1>

  <?php if ($erro): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block mb-1">Nome da Equipe</label>
      <input type="text" name="nome" value="<?= htmlspecialchars($equipe['nome']) ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block mb-1">País</label>
      <input type="text" name="pais" value="<?= htmlspecialchars($equipe['pais']) ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block mb-1">Chefe da Equipe</label>
      <input type="text" name="chefe" value="<?= htmlspecialchars($equipe['chefe']) ?>" class="w-full border rounded px-3 py-2">
    </div>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Salvar Alterações</button>
  </form>
</main>

<?php include __DIR__ . '/../../includes/layout_footer.php'; ?>
</body>
</html>
