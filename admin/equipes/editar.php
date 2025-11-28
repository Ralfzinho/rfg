<?php
// admin/equipes/editar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
global $pdo;

$title = 'Editar Equipe — RFG';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  set_flash('erro', 'Equipe não encontrada.');
  header('Location: /admin/equipes/listar.php');
  exit;
}

/**
 * Campos esperados na tabela `equipes`:
 * id, nome, sigla, chefe_equipe, status, pontos, posicao, foto_url
 */

// Se enviou o form, atualiza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome         = trim($_POST['nome'] ?? '');
  $sigla        = trim($_POST['sigla'] ?? '');
  $chefe_equipe = trim($_POST['chefe_equipe'] ?? '');
  $status       = trim($_POST['status'] ?? 'ativa');
  $pontos       = (int)($_POST['pontos'] ?? 0);
  $posicao      = $_POST['posicao'] !== '' ? (int)$_POST['posicao'] : null;
  $foto_url     = trim($_POST['foto_url'] ?? '');

  if ($nome === '') {
    set_flash('erro', 'Informe ao menos o nome da equipe.');
  } else {
    $sql = "UPDATE equipes
            SET nome         = :nome,
                sigla        = :sigla,
                chefe_equipe = :chefe_equipe,
                status       = :status,
                pontos       = :pontos,
                posicao      = :posicao,
                foto_url     = :foto_url
            WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'         => $nome,
      ':sigla'        => $sigla,
      ':chefe_equipe' => $chefe_equipe,
      ':status'       => $status,
      ':pontos'       => $pontos,
      ':posicao'      => $posicao,
      ':foto_url'     => $foto_url !== '' ? $foto_url : null,
      ':id'           => $id,
    ]);

    set_flash('ok', 'Equipe atualizada com sucesso.');
    header('Location: /admin/equipes/listar.php');
    exit;
  }
}

// Busca equipe
$st = $pdo->prepare("
  SELECT id, nome, sigla, chefe_equipe, status, pontos, posicao, foto_url
  FROM equipes
  WHERE id = :id
");
$st->execute([':id' => $id]);
$equipe = $st->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
  set_flash('erro', 'Equipe não encontrada.');
  header('Location: /admin/equipes/listar.php');
  exit;
}

$ok   = get_flash('ok');
$erro = get_flash('erro');
?>
<!doctype html>
<html lang="pt-br">

<head>
  <?php require INC . 'layout_head.php'; ?>
</head>

<body class="bg-gray-500 text-neutral-900">
  <?php require INC . 'layout_nav.php'; ?>

  <div class="flex">
    <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 mx-auto max-w-3xl px-4 py-8">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-racing font-bold text-black uppercase tracking-wider">
            Editar <span class="text-racing-gold">Equipe</span>
          </h1>
          <p class="text-gray-700 text-sm mt-1">
            Ajuste as informações da equipe e salve as alterações.
          </p>
        </div>
        <a href="/admin/equipes/listar.php"
          class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
          Voltar
        </a>
      </div>

      <?php if ($ok): ?>
        <div class="mb-4 rounded-xl border border-green-500/40 bg-green-500/10 text-green-700 px-4 py-3 text-sm">
          <?= htmlspecialchars($ok) ?>
        </div>
      <?php endif; ?>

      <?php if ($erro): ?>
        <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 text-red-700 px-4 py-3 text-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="admin-card rounded-2xl p-6 racing-glow bg-white space-y-4">
        <!-- Nome -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome da Equipe <span class="text-red-500">*</span>
          </label>
          <input
            name="nome"
            type="text"
            required
            value="<?= htmlspecialchars($equipe['nome']) ?>"
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-racing-gold focus:border-transparent">
        </div>

        <!-- Sigla + Status -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Sigla / Abreviação
            </label>
            <input
              name="sigla"
              type="text"
              maxlength="5"
              value="<?= htmlspecialchars($equipe['sigla']) ?>"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                     focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select
              name="status"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                     focus:ring-2 focus:ring-racing-gold focus:border-transparent">
              <option value="ativa"   <?= ($equipe['status'] ?? 'ativa') === 'ativa'   ? 'selected' : '' ?>>Ativa</option>
              <option value="inativa" <?= ($equipe['status'] ?? 'ativa') === 'inativa' ? 'selected' : '' ?>>Inativa</option>
            </select>
          </div>
        </div>

        <!-- Foto / Logo -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            URL do Logo / Foto
          </label>
          <input
            name="foto_url"
            type="url"
            value="<?= htmlspecialchars($equipe['foto_url'] ?? '') ?>"
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-racing-gold focus:border-transparent"
            placeholder="https://logo.clearbit.com/ferrari.com">
          <p class="text-xs text-gray-500 mt-1">
            Você pode usar um link de logo, por exemplo:
            <code class="font-mono text-[11px] bg-gray-100 px-1 py-0.5 rounded">https://logo.clearbit.com/ferrari.com</code>
          </p>

          <?php if (!empty($equipe['foto_url'])): ?>
            <div class="mt-3">
              <p class="text-xs text-gray-500 mb-1">Pré-visualização atual:</p>
              <img
                src="<?= htmlspecialchars($equipe['foto_url']) ?>"
                alt="Logo <?= htmlspecialchars($equipe['nome']) ?>"
                class="h-12 bg-white rounded-md border border-gray-200 p-1 object-contain">
            </div>
          <?php endif; ?>
        </div>

        <!-- Pontos + Posição -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Pontos
            </label>
            <input
              name="pontos"
              type="number"
              min="0"
              value="<?= (int)$equipe['pontos'] ?>"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                     focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Posição no Campeonato
            </label>
            <input
              name="posicao"
              type="number"
              min="1"
              value="<?= $equipe['posicao'] !== null ? (int)$equipe['posicao'] : '' ?>"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                     focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
          <a href="/admin/equipes/listar.php"
            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-100">
            Cancelar
          </a>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
            Salvar Alterações
          </button>
        </div>
      </form>
    </main>
  </div>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>
