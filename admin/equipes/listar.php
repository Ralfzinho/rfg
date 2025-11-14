<?php
// admin/equipes/listar.php
require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Equipes — RFG';
global $pdo;

// Lógica de exclusão
if (isset($_POST['del'])) {
    $id = (int)$_POST['del'];

    // Antes de excluir a equipe, desvincular pilotos associados a ela
    $st_pilotos = $pdo->prepare("UPDATE pilotos SET equipe_id = NULL WHERE equipe_id = ?");
    $st_pilotos->execute([$id]);

    // Agora excluir a equipe
    $st_equipe = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
    $st_equipe->execute([$id]);

    set_flash('ok', 'Equipe removida com sucesso.');
    header('Location: /admin/equipes/listar.php');
    exit;
}

// Busca de dados
$equipes = $pdo->query("SELECT id, nome, pais FROM equipes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Pega mensagens
$ok = get_flash('ok');
$erro = get_flash('erro');
?>
<!doctype html>
<html lang="pt-br">

<head><?php require INC . 'layout_head.php'; ?></head>

<body class="bg-neutral-50 text-neutral-900">
  <?php require INC . 'layout_nav.php'; ?>

  <main class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Equipes</h1>
      <a href="/admin/equipes/cadastrar.php" class="px-4 py-2 rounded bg-primary text-white hover:bg-red-700">+ Cadastrar</a>
    </div>

    <?php if ($ok): ?>
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="bg-white border rounded-2xl shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50">
          <tr class="text-left">
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Nome</th>
            <th class="px-4 py-3">País</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($equipes)): ?>
            <tr>
              <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                Nenhuma equipe cadastrada.
              </td>
            </tr>
          <?php else: foreach ($equipes as $e): ?>
              <tr class="border-t">
                <td class="px-4 py-2 font-semibold"><?= $e['id'] ?></td>
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($e['nome']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($e['pais']) ?></td>
                <td class="px-4 py-2 text-right">
                  <a href="/admin/equipes/editar.php?id=<?= $e['id'] ?>" class="px-3 py-1.5 rounded border hover:bg-neutral-50">Editar</a>
                  <form method="post" action="/admin/equipes/listar.php" class="inline-block ml-2" onsubmit="return confirm('Remover esta equipe? Os pilotos associados não serão excluídos, mas ficarão sem equipe.')">
                    <input type="hidden" name="del" value="<?= $e['id'] ?>">
                    <button type="submit" class="px-3 py-1.5 rounded border text-red-600 hover:bg-red-50">Excluir</button>
                  </form>
                </td>
              </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>
