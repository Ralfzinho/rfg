<?php
// admin/corridas/listar.php
require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Corridas — RFG';
global $pdo;

// Lógica de exclusão
if (isset($_POST['del'])) {
    $id = (int)$_POST['del'];
    $st = $pdo->prepare("DELETE FROM corridas WHERE id = ?");
    $st->execute([$id]);

    set_flash('ok', 'Corrida removida com sucesso.');
    header('Location: /admin/corridas/listar.php');
    exit;
}

// Busca de dados
$corridas = $pdo->query("
  SELECT
    c.id,
    c.nome_gp,
    c.data,
    c.status,
    circ.nome as circuito_nome
  FROM corridas c
  LEFT JOIN circuitos circ ON c.circuito_id = circ.id
  ORDER BY c.data DESC
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
      <h1 class="text-2xl font-bold">Corridas</h1>
      <a href="/admin/corridas/cadastrar.php" class="px-4 py-2 rounded bg-primary text-white hover:bg-red-700">+ Cadastrar</a>
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
            <th class="px-4 py-3">Nome do GP</th>
            <th class="px-4 py-3">Circuito</th>
            <th class="px-4 py-3">Data</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($corridas)): ?>
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-neutral-500">
                Nenhuma corrida cadastrada.
              </td>
            </tr>
          <?php else: foreach ($corridas as $c): ?>
              <tr class="border-t">
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($c['nome_gp']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['circuito_nome'] ?? 'N/D') ?></td>
                <td class="px-4 py-2"><?= date("d/m/Y", strtotime($c['data'])) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['status']) ?></td>
                <td class="px-4 py-2 text-right">
                  <a href="/admin/corridas/resultados.php?id=<?= $c['id'] ?>" class="px-3 py-1.5 rounded border bg-blue-500 text-white hover:bg-blue-600">Resultados</a>
                  <a href="/admin/corridas/editar.php?id=<?= $c['id'] ?>" class="px-3 py-1.5 rounded border hover:bg-neutral-50 ml-2">Editar</a>
                  <form method="post" action="/admin/corridas/listar.php" class="inline-block ml-2" onsubmit="return confirm('Remover esta corrida?')">
                    <input type="hidden" name="del" value="<?= $c['id'] ?>">
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
