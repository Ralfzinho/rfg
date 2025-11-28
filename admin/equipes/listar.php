<?php
// admin/equipes/listar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
global $pdo;

$title = 'Equipes — RFG';

/**
 * ATENÇÃO:
 * Para este arquivo funcionar sem erro, a tabela `equipes` deve ter pelo menos:
 *  id, nome, sigla, chefe_equipe, foto_url, status, pontos, posicao
 */

// Lógica de exclusão
if (isset($_POST['del'])) {
  $id = (int)$_POST['del'];

  if ($id > 0) {
    $st = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
    $st->execute([$id]);
    set_flash('ok', 'Equipe removida com sucesso.');
  } else {
    set_flash('erro', 'ID de equipe inválido.');
  }

  header('Location: /admin/equipes/listar.php');
  exit;
}

// Lógica de cadastro via modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_equipe'])) {
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
    $sql = "INSERT INTO equipes (nome, sigla, chefe_equipe, foto_url, status, pontos, posicao)
            VALUES (:nome, :sigla, :chefe_equipe, :foto_url, :status, :pontos, :posicao)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'         => $nome,
      ':sigla'        => $sigla,
      ':chefe_equipe' => $chefe_equipe,
      ':foto_url'     => $foto_url,
      ':status'       => $status,
      ':pontos'       => $pontos,
      ':posicao'      => $posicao,
    ]);

    set_flash('ok', 'Equipe cadastrada com sucesso.');
  }

  header('Location: /admin/equipes/listar.php');
  exit;
}

// Busca equipes + total de pilotos
$sql = "
  SELECT
    e.*,
    (SELECT COUNT(*) FROM pilotos p WHERE p.equipe_id = e.id) AS total_pilotos
  FROM equipes e
  ORDER BY e.posicao IS NULL, e.posicao, e.nome
";
$equipes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Pega mensagens
$ok   = get_flash('ok');
$erro = get_flash('erro');

// Helpers
function equipe_iniciais(array $e): string
{
  if (!empty($e['sigla'])) {
    return strtoupper($e['sigla']);
  }

  $nome = $e['nome'] ?? '';
  $parts = preg_split('/\s+/', trim($nome));
  if (count($parts) >= 2) {
    return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
  }
  return strtoupper(substr($nome, 0, 2));
}

function equipe_status_badge(string $status): string
{
  $status = strtolower($status);
  if ($status === 'inativa' || $status === 'inativo') {
    return 'bg-gray-600';
  }
  return 'bg-green-600';
}

function equipe_status_label(string $status): string
{
  $status = strtolower($status);
  if ($status === 'inativa' || $status === 'inativo') {
    return 'Inativa';
  }
  return 'Ativa';
}

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

    <main class="flex-1 mx-auto max-w-7xl px-4 py-8">
      <!-- Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <div class="modern-border pl-6 mb-4">
            <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
              Gerenciar <span class="text-yellow-600">Equipes</span>
            </h2>
          </div>
          <p class="text-gray-400">Cadastro e gerenciamento de equipes</p>
        </div>
        <button
          onclick="showModal('team-modal')"
          class="btn-primary text-white font-semibold py-3 px-6 rounded-xl">
          + Nova Equipe
        </button>
      </div>

      <!-- Flash messages -->
      <?php if ($ok): ?>
        <div class="mb-6 rounded-xl border border-green-500/40 bg-green-500/10 text-green-300 px-4 py-3 text-sm">
          <?= htmlspecialchars($ok) ?>
        </div>
      <?php endif; ?>

      <?php if ($erro): ?>
        <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 text-red-300 px-4 py-3 text-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <!-- Grid de Equipes -->
      <?php if (empty($equipes)): ?>
        <div class="admin-card rounded-xl p-8 racing-glow flex flex-col items-center justify-center text-center">
          <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <p class="text-lg font-semibold text-white mb-1">Nenhuma equipe cadastrada</p>
          <p class="text-sm text-gray-400">Clique em <span class="text-racing-gold font-semibold">"Nova Equipe"</span> para adicionar a primeira.</p>
        </div>
      <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($equipes as $e): ?>
            <?php
            $iniciais    = equipe_iniciais($e);
            $statusClass = equipe_status_badge($e['status'] ?? 'ativa');
            $statusLabel = equipe_status_label($e['status'] ?? 'ativa');
            $chefe       = $e['chefe_equipe'] ?? 'Não informado';
            $pontos      = isset($e['pontos']) ? (int)$e['pontos'] : 0;
            $posicao     = isset($e['posicao']) && $e['posicao'] ? (int)$e['posicao'] . 'º' : '-';
            $totalPilotos = (int)($e['total_pilotos'] ?? 0);
            $foto_url    = trim($e['foto_url'] ?? '');
            ?>
            <div class="admin-card rounded-xl p-6 racing-glow bg-white text-gray-900">
              <div class="flex items-center justify-between mb-4">
                <?php if ($foto_url !== ''): ?>
                  <div class="w-12 h-12 rounded-full overflow-hidden bg-neutral-200 flex items-center justify-center">
                    <img
                      src="<?= htmlspecialchars($foto_url) ?>"
                      alt="<?= htmlspecialchars($e['nome']) ?>"
                      class="w-full h-full object-cover">
                  </div>
                <?php else: ?>
                  <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold"><?= htmlspecialchars($iniciais) ?></span>
                  </div>
                <?php endif; ?>

                <span class="<?= $statusClass ?> text-white text-xs px-2 py-1 rounded-full">
                  <?= htmlspecialchars($statusLabel) ?>
                </span>
              </div>

              <h3 class="font-racing font-bold text-lg mb-1">
                <?= htmlspecialchars($e['nome']) ?>
              </h3>
              <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Pilotos:</span>
                  <span class="text-gray-900"><?= $totalPilotos ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Pontos:</span>
                  <span class="text-racing-gold font-bold"><?= $pontos ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Posição:</span>
                  <span class="text-gray-900"><?= htmlspecialchars($posicao) ?></span>
                </div>
              </div>

              <div class="flex space-x-2">
                <a
                  href="/admin/equipes/editar.php?id=<?= (int)$e['id'] ?>"
                  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm transition-colors text-center">
                  Editar
                </a>

                <form method="post" action="/admin/equipes/listar.php" class="flex-1"
                  onsubmit="return confirm('Tem certeza que deseja remover esta equipe?');">
                  <input type="hidden" name="del" value="<?= (int)$e['id'] ?>">
                  <button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm transition-colors">
                    Excluir
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <!-- Modal Cadastrar Equipe -->
  <div id="team-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-2xl font-racing font-bold text-gray-900 uppercase tracking-wider">
          Nova <span class="text-racing-gold">Equipe</span>
        </h3>
        <button onclick="hideModal('team-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form method="post" action="/admin/equipes/listar.php" class="p-6 space-y-4">
        <input type="hidden" name="cadastrar_equipe" value="1">

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome da Equipe <span class="text-red-500">*</span>
          </label>
          <input
            name="nome"
            type="text"
            required
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            placeholder="Ex: Red Bull Racing">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Sigla / Abreviação
            </label>
            <input
              name="sigla"
              type="text"
              maxlength="5"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              placeholder="Ex: RB">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select
              name="status"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="ativa">Ativa</option>
              <option value="inativa">Inativa</option>
            </select>
          </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Pontos
            </label>
            <input
              name="pontos"
              type="number"
              min="0"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              placeholder="Ex: 245">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Posição no Campeonato
            </label>
            <input
              name="posicao"
              type="number"
              min="1"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              placeholder="Ex: 1">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Logo / Foto (URL)
          </label>
          <input
            name="foto_url"
            type="url"
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            placeholder="https://exemplo.com/logo-equipe.png">
          <p class="mt-1 text-xs text-gray-500">Cole o link direto da imagem da equipe.</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
          <button
            type="button"
            onclick="hideModal('team-modal')"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
            Cadastrar Equipe
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Controle dos modais
    window.showModal = function(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    window.hideModal = function(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    // Fecha modal clicando fora
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal')) {
        hideModal(e.target.id);
      }
    });

    // Impede fechar ao clicar dentro do conteúdo
    document.querySelectorAll('.modal > div').forEach(function(content) {
      content.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });
  </script>
</body>

</html>
