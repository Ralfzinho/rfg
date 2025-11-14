<?php
// admin/pilotos/listar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Pilotos — RFG';
global $pdo;

// Lógica de exclusão
if (isset($_POST['del'])) {
  $id = (int)$_POST['del'];
  $st = $pdo->prepare("DELETE FROM pilotos WHERE id = ?");
  $st->execute([$id]);

  set_flash('ok', 'Piloto removido com sucesso.');
  header('Location: /admin/pilotos/listar.php');
  exit;
}

// Busca de dados
$pilotos = $pdo->query("
  SELECT
    p.id,
    p.nome,
    p.numero,
    p.pais,
    p.foto_url,
    e.nome as equipe_nome
  FROM pilotos p
  LEFT JOIN equipes e ON p.equipe_id = e.id
  ORDER BY p.nome
")->fetchAll(PDO::FETCH_ASSOC);

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
      <h1 class="text-2xl font-bold">Pilotos</h1>
      <a href="/admin/pilotos/cadastrar.php" class="px-4 py-2 rounded bg-primary text-white hover:bg-red-700">+ Cadastrar</a>
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
            <th class="px-4 py-3">Foto</th>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Nome</th>
            <th class="px-4 py-3">Equipe</th>
            <th class="px-4 py-3">País</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($pilotos)): ?>
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-neutral-500">
                Nenhum piloto cadastrado.
              </td>
            </tr>
          <?php else: foreach ($pilotos as $p): ?>
              <tr class="border-t">
                <td class="px-4 py-2">
                  <img src="<?= htmlspecialchars($p['foto_url'] ?: 'https://placehold.co/48x48') ?>" class="w-12 h-12 rounded-full object-cover" alt="">
                </td>
                <td class="px-4 py-2 font-semibold"><?= (int)$p['numero'] ?></td>
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($p['nome']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($p['equipe_nome'] ?? 'Sem equipe') ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($p['pais']) ?></td>
                <td class="px-4 py-2 text-right">
                  <a href="/admin/pilotos/editar.php?id=<?= $p['id'] ?>" class="px-3 py-1.5 rounded border hover:bg-neutral-50">Editar</a>
                  <form method="post" action="/admin/pilotos/listar.php" class="inline-block ml-2" onsubmit="return confirm('Remover este piloto?')">
                    <input type="hidden" name="del" value="<?= $p['id'] ?>">
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
