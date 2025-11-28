<?php
// admin/corridas/listar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Corridas — RFG';

/** @var PDO $pdo */
global $pdo;

// -------------------------
// LÓGICA DE EXCLUSÃO
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del'])) {
  $id = (int)$_POST['del'];

  if ($id > 0) {
    $st = $pdo->prepare("DELETE FROM corridas WHERE id = ?");
    $st->execute([$id]);
    set_flash('ok', 'Corrida removida com sucesso.');
  } else {
    set_flash('erro', 'ID de corrida inválido.');
  }

  header('Location: /admin/corridas/listar.php');
  exit;
}

// -------------------------
// LÓGICA DE CADASTRO (MODAL)
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_corrida'])) {
  $nome_gp       = trim($_POST['nome_gp'] ?? '');
  $data          = trim($_POST['data'] ?? '');
  $status        = trim($_POST['status'] ?? 'Agendada');
  $circuito_nome = trim($_POST['circuito_nome'] ?? '');

  if ($nome_gp === '' || $data === '' || $circuito_nome === '') {
    set_flash('erro', 'Informe pelo menos Nome do GP, Data e Circuito.');
  } else {
    try {
      $pdo->beginTransaction();

      // 1) Buscar circuito pelo nome
      $st = $pdo->prepare("SELECT id FROM circuitos WHERE nome = :nome LIMIT 1");
      $st->execute([':nome' => $circuito_nome]);
      $circuito_id = (int) $st->fetchColumn();

      // 2) Se não existir, cria
      if ($circuito_id <= 0) {
        $st = $pdo->prepare("INSERT INTO circuitos (nome) VALUES (:nome)");
        $st->execute([':nome' => $circuito_nome]);
        $circuito_id = (int) $pdo->lastInsertId();
      }

      // 3) Inserir corrida usando circuito_id (coluna do banco)
      $sql = "INSERT INTO corridas (nome_gp, data, status, circuito_id)
              VALUES (:nome_gp, :data, :status, :circuito_id)";
      $st = $pdo->prepare($sql);
      $st->execute([
        ':nome_gp'     => $nome_gp,
        ':data'        => $data,
        ':status'      => $status,
        ':circuito_id' => $circuito_id,
      ]);

      $pdo->commit();
      set_flash('ok', 'Corrida cadastrada com sucesso.');
    } catch (Exception $e) {
      $pdo->rollBack();
      set_flash('erro', 'Erro ao cadastrar corrida.');
    }
  }

  header('Location: /admin/corridas/listar.php');
  exit;
}

// -------------------------
// LÓGICA DE EDIÇÃO (MODAL)
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_corrida'])) {
  $id          = (int)($_POST['id'] ?? 0);
  $nome_gp     = trim($_POST['nome_gp'] ?? '');
  $data        = trim($_POST['data'] ?? '');
  $status      = trim($_POST['status'] ?? 'Agendada');
  $circuito_id = (int)($_POST['circuito_id'] ?? 0);

  if ($id <= 0) {
    set_flash('erro', 'Corrida inválida.');
  } elseif ($nome_gp === '' || $data === '' || $circuito_id <= 0) {
    set_flash('erro', 'Informe pelo menos Nome do GP, Data e Circuito.');
  } else {
    $sql = "UPDATE corridas
                SET nome_gp = :nome_gp,
                    data = :data,
                    status = :status,
                    circuito_id = :circuito_id
                WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome_gp'     => $nome_gp,
      ':data'        => $data,
      ':status'      => $status,
      ':circuito_id' => $circuito_id,
      ':id'          => $id,
    ]);

    set_flash('ok', 'Corrida atualizada com sucesso.');
  }

  header('Location: /admin/corridas/listar.php');
  exit;
}

// -------------------------
// BUSCA CIRCUITOS (para os selects)
// -------------------------
$circuitos = $pdo->query("
  SELECT id, nome
  FROM circuitos
  ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// BUSCA CORRIDAS
// -------------------------
$corridas = $pdo->query("
  SELECT
    c.id,
    c.nome_gp,
    c.data,
    c.status,
    c.circuito_id,
    circ.nome AS circuito_nome
  FROM corridas c
  LEFT JOIN circuitos circ ON c.circuito_id = circ.id
  ORDER BY c.data DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Mensagens
$ok   = get_flash('ok');
$erro = get_flash('erro');

// -------------------------
// HELPERS
// -------------------------
function corrida_status_classes(string $status): array
{
  $s = mb_strtolower($status, 'UTF-8');

  if (str_contains($s, 'final')) {
    return [
      'border'     => 'border-green-500',
      'badge_bg'   => 'bg-green-900',
      'badge_text' => 'text-green-400',
      'label'      => 'Finalizada',
    ];
  }

  if (str_contains($s, 'cancel')) {
    return [
      'border'     => 'border-red-500',
      'badge_bg'   => 'bg-red-900',
      'badge_text' => 'text-red-400',
      'label'      => 'Cancelada',
    ];
  }

  if (str_contains($s, 'próx') || str_contains($s, 'prox')) {
    return [
      'border'     => 'border-yellow-500',
      'badge_bg'   => 'bg-yellow-900',
      'badge_text' => 'text-yellow-400',
      'label'      => 'Próxima',
    ];
  }

  return [
    'border'     => 'border-gray-500',
    'badge_bg'   => 'bg-gray-700',
    'badge_text' => 'text-gray-300',
    'label'      => 'Agendada',
  ];
}

function formatar_data_corrida(?string $data): string
{
  if (!$data) return '-';
  $ts = strtotime($data);
  if (!$ts) return $data;
  return date('d/m/Y', $ts);
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
      <!-- Header estilo racing -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <div class="racing-border pl-4 mb-4">
            <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
              Gerenciar <span class="text-yellow-600">Corridas</span>
            </h2>
          </div>
          <p class="text-gray-400">Calendário e gerenciamento de corridas</p>
        </div>

        <!-- Agora abre modal de cadastro -->
        <button
          type="button"
          onclick="showModal('race-modal')"
          class="btn-primary text-white font-semibold py-3 px-6 rounded-xl">
          + Nova Corrida
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

      <!-- Grid de Corridas -->
      <?php if (empty($corridas)): ?>
        <div class="admin-card rounded-xl p-8 racing-glow flex flex-col items-center justify-center text-center">
          <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <p class="text-lg font-semibold text-white mb-1">Nenhuma corrida cadastrada</p>
          <p class="text-sm text-gray-400">
            Clique em <span class="text-racing-gold font-semibold">"Nova Corrida"</span> para adicionar a primeira etapa.
          </p>
        </div>
      <?php else: ?>
        <div class="admin-card rounded-xl p-6 racing-glow mb-6">
          <h3 class="text-xl font-racing font-bold black-white mb-4">Calendário de Corridas</h3>

          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($corridas as $c): ?>
              <?php
              $classes   = corrida_status_classes($c['status'] ?? '');
              $border    = $classes['border'];
              $badgeBg   = $classes['badge_bg'];
              $badgeText = $classes['badge_text'];
              $label     = $classes['label'];

              $dataFmt   = formatar_data_corrida($c['data'] ?? null);
              $circuito  = $c['circuito_nome'] ?: 'Circuito não informado';
              ?>
              <div class="bg-white rounded-lg p-4 border-l-4 <?= $border ?> shadow-md">
                <div class="flex justify-between items-start mb-2">
                  <h4 class="text-gray-900 font-bold">
                    <?= htmlspecialchars($c['nome_gp']) ?>
                  </h4>
                  <span class="<?= $badgeText ?> text-xs px-2 py-1 <?= $badgeBg ?> rounded">
                    <?= htmlspecialchars($label) ?>
                  </span>
                </div>

                <p class="text-gray-600 text-sm">
                  <?= htmlspecialchars($dataFmt) ?> • <?= htmlspecialchars($circuito) ?>
                </p>

                <p class="text-gray-500 text-sm mt-2">
                  Use o botão <span class="text-racing-gold font-semibold">Resultados</span> para gerenciar o pódio.
                </p>

                <div class="flex flex-wrap gap-2 mt-4">
                  <a
                    href="/admin/corridas/resultados.php?id=<?= (int)$c['id'] ?>"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-xs md:text-sm text-center transition-colors">
                    Resultados
                  </a>

                  <!-- Botão de editar abre modal preenchido -->
                  <button
                    type="button"
                    class="flex-1 bg-gray-800 hover:bg-gray-900 text-white py-2 px-3 rounded text-xs md:text-sm text-center transition-colors btn-edit-race"
                    data-id="<?= (int)$c['id'] ?>"
                    data-nome_gp="<?= htmlspecialchars($c['nome_gp'], ENT_QUOTES, 'UTF-8') ?>"
                    data-data="<?= htmlspecialchars($c['data'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    data-status="<?= htmlspecialchars($c['status'] ?? 'Agendada', ENT_QUOTES, 'UTF-8') ?>"
                    data-circuito_id="<?= (int)($c['circuito_id'] ?? 0) ?>">
                    Editar
                  </button>

                  <form method="post" action="/admin/corridas/listar.php"
                    class="flex-1"
                    onsubmit="return confirm('Remover esta corrida?');">
                    <input type="hidden" name="del" value="<?= (int)$c['id'] ?>">
                    <button
                      type="submit"
                      class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-xs md:text-sm transition-colors">
                      Excluir
                    </button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <!-- MODAL CADASTRAR CORRIDA -->
  <div id="race-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-2xl font-racing font-bold text-gray-900 uppercase tracking-wider">
          Nova <span class="text-racing-gold">Corrida</span>
        </h3>
        <button onclick="hideModal('race-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form method="post" action="/admin/corridas/listar.php" class="p-6 space-y-4">
        <input type="hidden" name="cadastrar_corrida" value="1">

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome do GP <span class="text-red-500">*</span>
          </label>
          <input
            name="nome_gp"
            type="text"
            required
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            placeholder="Ex: GP do Bahrein">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Data <span class="text-red-500">*</span>
            </label>
            <input
              name="data"
              type="date"
              required
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select
              name="status"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="Agendada">Agendada</option>
              <option value="Próxima">Próxima</option>
              <option value="Finalizada">Finalizada</option>
              <option value="Cancelada">Cancelada</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-200 mb-1">
            Circuito <span class="text-red-400">*</span>
          </label>
          <input
            name="circuito_nome"
            type="text"
            required
            class="w-full rounded-lg border border-gray-700 bg-white-900 text-gray px-4 py-2.5
           focus:ring-2 focus:ring-racing-gold focus:border-transparent"
            placeholder="Ex: Sakhir, Interlagos, Montreal...">
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
          <button
            type="button"
            onclick="hideModal('race-modal')"
            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50">
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold
                 hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
            Cadastrar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDITAR CORRIDA -->
  <div id="race-edit-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-2xl font-racing font-bold text-gray-900 uppercase tracking-wider">
          Editar <span class="text-racing-gold">Corrida</span>
        </h3>
        <button onclick="hideModal('race-edit-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form method="post" action="/admin/corridas/listar.php" class="p-6 space-y-4">
        <input type="hidden" name="editar_corrida" value="1">
        <input type="hidden" name="id" value="">

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome do GP <span class="text-red-500">*</span>
          </label>
          <input
            name="nome_gp"
            type="text"
            required
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Data <span class="text-red-500">*</span>
            </label>
            <input
              name="data"
              type="date"
              required
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select
              name="status"
              class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                   focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="Agendada">Agendada</option>
              <option value="Próxima">Próxima</option>
              <option value="Finalizada">Finalizada</option>
              <option value="Cancelada">Cancelada</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Circuito <span class="text-red-500">*</span>
          </label>
          <select
            name="circuito_id"
            required
            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-4 py-2.5
                 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
            <option value="">Selecione um circuito</option>
            <?php foreach ($circuitos as $circ): ?>
              <option value="<?= (int)$circ['id'] ?>"><?= htmlspecialchars($circ['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
          <button
            type="button"
            onclick="hideModal('race-edit-modal')"
            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50">
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold
                 hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
            Salvar Alterações
          </button>
        </div>
      </form>
    </div>
  </div>


  <script>
    // Funções genéricas de modal
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

    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal')) {
        hideModal(e.target.id);
      }
    });

    document.addEventListener('DOMContentLoaded', function() {
      // Impede fechar ao clicar dentro do conteúdo
      document.querySelectorAll('.modal > div').forEach(function(content) {
        content.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      });

      // Bind dos botões de editar para preencher o modal
      document.querySelectorAll('.btn-edit-race').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const modal = document.getElementById('race-edit-modal');
          if (!modal) return;

          modal.querySelector('input[name="id"]').value = this.dataset.id || '';
          modal.querySelector('input[name="nome_gp"]').value = this.dataset.nome_gp || '';
          modal.querySelector('input[name="data"]').value = this.dataset.data || '';

          const statusSelect = modal.querySelector('select[name="status"]');
          const circuitoSelect = modal.querySelector('select[name="circuito_id"]');

          if (statusSelect) {
            statusSelect.value = this.dataset.status || 'Agendada';
          }
          if (circuitoSelect) {
            circuitoSelect.value = this.dataset.circuito_id || '';
          }

          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });
      });
    });
  </script>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>