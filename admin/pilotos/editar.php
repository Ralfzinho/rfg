<?php
// admin/pilotos/editar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
global $pdo;

$title = 'Editar Piloto — RFG';

// ID do piloto
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  set_flash('erro', 'Piloto não encontrado.');
  header('Location: /admin/pilotos/listar.php');
  exit;
}

// Busca equipes para o select
$stmt_equipes = $pdo->query("SELECT id, nome FROM equipes ORDER BY nome");
$equipes = $stmt_equipes->fetchAll(PDO::FETCH_ASSOC);

// Se enviou o form (POST) → atualiza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $numero = (int) ($_POST['numero'] ?? 0);
  $pais = trim($_POST['pais'] ?? '');
  $equipe_id = (int) ($_POST['equipe_id'] ?? 0);
  $foto_url = trim($_POST['foto_url'] ?? '');
  $status = trim($_POST['status'] ?? 'ativo');

  if ($nome === '' || $numero <= 0 || $equipe_id <= 0) {
    set_flash('erro', 'Informe ao menos Nome, Número e Equipe.');
  } else {
    $sql = "UPDATE pilotos
                SET nome      = :nome,
                    numero    = :numero,
                    pais      = :pais,
                    equipe_id = :equipe_id,
                    foto_url  = :foto_url,
                    status    = :status
                WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome' => $nome,
      ':numero' => $numero,
      ':pais' => $pais,
      ':equipe_id' => $equipe_id,
      ':foto_url' => $foto_url,
      ':status' => $status,
      ':id' => $id,
    ]);

    set_flash('ok', 'Piloto atualizado com sucesso.');
    header('Location: /admin/pilotos/listar.php');
    exit;
  }
}

// Busca dados do piloto para preencher o form
$st = $pdo->prepare("
  SELECT id, nome, numero, pais, equipe_id, foto_url, status
  FROM pilotos
  WHERE id = :id
");
$st->execute([':id' => $id]);
$piloto = $st->fetch(PDO::FETCH_ASSOC);

if (!$piloto) {
  set_flash('erro', 'Piloto não encontrado.');
  header('Location: /admin/pilotos/listar.php');
  exit;
}

$ok = get_flash('ok');
$erro = get_flash('erro');
?>
<!doctype html>
<html lang="pt-br">

<head>
  <?php require INC . 'layout_head.php'; ?>
</head>

<body class="bg-neutral-50 text-neutral-900">
  <?php require INC . 'layout_nav.php'; ?>

  <div class="flex">
    <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 mx-auto max-w-3xl px-4 py-8">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Editar Piloto</h1>
        <a href="/admin/pilotos/listar.php"
          class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">
          Voltar
        </a>
      </div>

      <?php if ($ok): ?>
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">
          <?= htmlspecialchars($ok) ?>
        </div>
      <?php endif; ?>

      <?php if ($erro): ?>
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="bg-white border rounded-2xl shadow p-6 space-y-4">
        <!-- Nome -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome do Piloto <span class="text-red-500">*</span>
          </label>
          <input name="nome" type="text" required value="<?= htmlspecialchars($piloto['nome']) ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <!-- Número + País -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Número <span class="text-red-500">*</span>
            </label>
            <input name="numero" type="number" min="1" max="99" required value="<?= (int) $piloto['numero'] ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              País
            </label>
            <input name="pais" type="text" value="<?= htmlspecialchars($piloto['pais']) ?>" class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>
        </div>

        <!-- Equipe + Status -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Equipe <span class="text-red-500">*</span>
            </label>
            <select name="equipe_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="">Selecione uma equipe</option>
              <?php foreach ($equipes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $piloto['equipe_id'] == $e['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="ativo" <?= ($piloto['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
              <option value="inativo" <?= ($piloto['status'] ?? 'ativo') === 'inativo' ? 'selected' : '' ?>>Inativo
              </option>
            </select>
          </div>
        </div>

        <!-- Foto -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            URL da Foto
          </label>
          <input name="foto_url" type="url" value="<?= htmlspecialchars($piloto['foto_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
 class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          <p class="mt-1 text-xs text-gray-500">
            Cole o link direto da imagem do piloto (ex.: https://...)
          </p>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-3 pt-4 border-top border-gray-200">
          <a href="/admin/pilotos/listar.php"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
            Cancelar
          </a>
          <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600
                   text-white font-semibold hover:from-yellow-600 hover:to-yellow-700
                   shadow-lg hover:shadow-xl transition-all">
            Salvar Alterações
          </button>
        </div>
      </form>
    </main>
  </div>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>