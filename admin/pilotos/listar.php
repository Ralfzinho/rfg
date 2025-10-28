<?php
// admin/pilotos/listar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Sobe duas pastas (de /admin/pilotos) até a raiz do projeto e entra em /includes
require_once __DIR__ . '/../../includes/funcoes.php';

// Agora você pode usar a sua função de auth
auth_require_role(['admin', 'editor']);

// Caminho absoluto para os layouts em /includes
define('INC', __DIR__ . '/../../includes/');

$title = 'Pilotos — RFG';

// inicializa mock na sessão
$_SESSION['mock_pilotos'] ??= [];

// remover (mock)
if (isset($_GET['del'])) {
  $id = $_GET['del'];
  $_SESSION['mock_pilotos'] = array_values(
    array_filter($_SESSION['mock_pilotos'], fn($p) => $p['id'] !== $id)
  );
  header('Location: /admin/pilotos/listar.php?ok=1');
  exit;

  /* ===== FUTURO (PDO)
  require_once __DIR__ . '/../../includes/db.php';
  $st = $pdo->prepare("DELETE FROM pilotos WHERE id = ?");
  $st->execute([$id]);
  header('Location: /admin/pilotos/listar.php?ok=1'); exit;
  */
}

$pilotos = $_SESSION['mock_pilotos'];

/* ===== FUTURO (PDO)
require_once __DIR__ . '/../../includes/db.php';
$pilotos = $pdo->query("
  SELECT id, nome, numero, pais, equipe, foto_url
  FROM pilotos
  ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);
*/
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

    <?php if (isset($_GET['ok'])): ?>
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
        Operação realizada.
      </div>
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
                  <img src="<?= htmlspecialchars($p['foto_url'] ?: 'https://placehold.co/48x48') ?>"
                    class="w-12 h-12 rounded-full object-cover" alt="">
                </td>
                <td class="px-4 py-2 font-semibold"><?= (int)($p['numero'] ?? 0) ?></td>
                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($p['nome'] ?? '') ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($p['equipe'] ?? '') ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($p['pais'] ?? '') ?></td>
                <td class="px-4 py-2 text-right">
                  <a href="/admin/pilotos/editar.php?id=<?= urlencode($p['id']) ?>"
                    class="px-3 py-1.5 rounded border hover:bg-neutral-50">Editar</a>
                  <a href="/admin/pilotos/listar.php?del=<?= urlencode($p['id']) ?>"
                    class="ml-2 px-3 py-1.5 rounded border text-red-600 hover:bg-red-50"
                    onclick="return confirm('Remover este piloto?')">Excluir</a>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>