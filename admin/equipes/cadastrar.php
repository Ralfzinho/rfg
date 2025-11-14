<?php
// admin/equipes/cadastrar.php
require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Cadastrar Equipe — RFG';
global $pdo;

$erro = get_flash('erro');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $pais = trim($_POST['pais'] ?? '');

    if ($nome === '' || $pais === '') {
        set_flash('erro', 'Preencha Nome e País.');
        header('Location: /admin/equipes/cadastrar.php');
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO equipes (nome, pais) VALUES (:nome, :pais)");
        $stmt->execute([':nome' => $nome, ':pais' => $pais]);

        set_flash('ok', 'Equipe cadastrada com sucesso.');
        header('Location: /admin/equipes/listar.php');
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
    <h1 class="text-2xl font-bold">Cadastrar Equipe</h1>
    <a href="/admin/equipes/listar.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($erro): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="bg-white border rounded-2xl p-6 shadow space-y-4">
    <div>
      <label class="block text-sm font-medium">Nome da Equipe *</label>
      <input type="text" name="nome" required class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">País *</label>
      <input type="text" name="pais" required class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div class="flex gap-3 justify-end mt-2">
      <a href="/admin/equipes/listar.php" class="px-4 py-2 rounded border">Cancelar</a>
      <button type="submit" class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">Salvar</button>
    </div>
  </form>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
