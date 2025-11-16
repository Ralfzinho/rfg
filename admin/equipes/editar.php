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
  header('Location: /rfg/admin/equipes/listar.php');
  exit;
}

/**
 * Campos esperados na tabela `equipes`:
 * id, nome, sigla, chefe_equipe, status, pontos, posicao
 */

// Se enviou o form, atualiza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome         = trim($_POST['nome'] ?? '');
  $sigla        = trim($_POST['sigla'] ?? '');
  $chefe_equipe = trim($_POST['chefe_equipe'] ?? '');
  $status       = trim($_POST['status'] ?? 'ativa');
  $pontos       = (int)($_POST['pontos'] ?? 0);
  $posicao      = $_POST['posicao'] !== '' ? (int)$_POST['posicao'] : null;

  if ($nome === '') {
    set_flash('erro', 'Informe ao menos o nome da equipe.');
  } else {
    $sql = "UPDATE equipes
            SET nome = :nome,
                sigla = :sigla,
                chefe_equipe = :chefe_equipe,
                status = :status,
                pontos = :pontos,
                posicao = :posicao
            WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'         => $nome,
      ':sigla'        => $sigla,
      ':chefe_equipe' => $chefe_equipe,
      ':status'       => $status,
      ':pontos'       => $pontos,
      ':posicao'      => $posicao,
      ':id'           => $id,
    ]);

    set_flash('ok', 'Equipe atualizada com sucesso.');
    header('Location: /rfg/admin/equipes/listar.php');
    exit;
  }
}

// Busca equipe
$st = $pdo->prepare("
  SELECT id, nome, sigla, chefe_equipe, status, pontos, posicao
  FROM equipes
  WHERE id = :id
");
$st->execute([':id' => $id]);
$equipe = $st->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
  set_flash('erro', 'Equipe não encontrada.');
  header('Location: /rfg/admin/equipes/listar.php');
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

<body class="bg-neutral-900 text-white">
  <?php require INC . 'layout_nav.php'; ?>

  <div class="flex">
    <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 mx-auto max-w-3xl px-4 py-8">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-racing font-bold text-white uppercase tracking-wider">
            Editar <span class="text-racing-gold">Equipe</span>
          </h1>
          <p class="text-gray-400 text-sm mt-1">
            Ajuste as informações da equipe e salve as alterações.
          </p>
        </div>
        <a href="/rfg/admin/equipes/listar.php"
          class="px-4 py-2 rounded-lg border border-neutral-600 text-gray-200 hover:bg-neutral-800 text-sm font-medium">
          Voltar
        </a>
      </div>

      <?php if ($ok): ?>
        <div class="mb-4 rounded-xl border border-green-500/40 bg-green-500/10 text-green-300 px-4 py-3 text-sm">
          <?= htmlspecialchars($ok) ?>
        </div>
      <?php endif; ?>

      <?php if ($erro): ?>
        <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 text-red-300 px-4 py-3 text-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="admin-card rounded-2xl p-6 racing-glow space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-200 mb-1">
            Nome da Equipe <span class="text-red-400">*</span>
          </label>
          <input
            name="nome"
            type="text"
            required
            value="<?= htmlspecialchars($equipe['nome']) ?>"
            class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-200 mb-1">
              Sigla / Abreviação
            </label>
            <input
              name="sigla"
              type="text"
              maxlength="5"
              value="<?= htmlspecialchars($equipe['sigla']) ?>"
              class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-200 mb-1">
              Status
            </label>
            <select
              name="status"
              class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
              <option value="ativa" <?= ($equipe['status'] ?? 'ativa') === 'ativa'   ? 'selected' : '' ?>>Ativa</option>
              <option value="inativa" <?= ($equipe['status'] ?? 'ativa') === 'inativa' ? 'selected' : '' ?>>Inativa</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-200 mb-1">
            Chefe de Equipe
          </label>
          <input
            name="chefe_equipe"
            type="text"
            value="<?= htmlspecialchars($equipe['chefe_equipe']) ?>"
            class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-200 mb-1">
              Pontos
            </label>
            <input
              name="pontos"
              type="number"
              min="0"
              value="<?= (int)$equipe['pontos'] ?>"
              class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-200 mb-1">
              Posição no Campeonato
            </label>
            <input
              name="posicao"
              type="number"
              min="1"
              value="<?= $equipe['posicao'] !== null ? (int)$equipe['posicao'] : '' ?>"
              class="w-full rounded-lg border border-neutral-700 bg-neutral-900 text-white px-4 py-2.5 focus:ring-2 focus:ring-racing-gold focus:border-transparent">
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-neutral-800 mt-4">
          <a href="/rfg/admin/equipes/listar.php"
            class="px-5 py-2.5 rounded-lg border border-neutral-600 text-gray-200 font-semibold hover:bg-neutral-800">
            Cancelar
          </a>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-lg bg-racing-gold text-black font-bold font-racing uppercase tracking-wider hover:brightness-110 shadow-lg hover:shadow-xl transition-all">
            Salvar Alterações
          </button>
        </div>
      </form>
    </main>
  </div>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>