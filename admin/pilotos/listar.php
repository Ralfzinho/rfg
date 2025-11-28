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
  header('Location: /rfg/admin/pilotos/listar.php');
  exit;
}

// Lógica de cadastro via modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
  $nome      = trim($_POST['nome'] ?? '');
  $numero    = (int)($_POST['numero'] ?? 0);
  $pais      = trim($_POST['pais'] ?? '');
  $equipe_id = (int)($_POST['equipe_id'] ?? 0);
  $foto_url  = trim($_POST['foto_url'] ?? '');
  $status    = trim($_POST['status'] ?? 'ativo');

  if ($nome === '' || $numero <= 0 || $equipe_id <= 0) {
    set_flash('erro', 'Informe ao menos Nome, Número e Equipe.');
  } else {
    $sql = "INSERT INTO pilotos (nome, numero, pais, equipe_id, foto_url, status)
            VALUES (:nome, :numero, :pais, :equipe_id, :foto_url, :status)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':nome'      => $nome,
      ':numero'    => $numero,
      ':pais'      => $pais,
      ':equipe_id' => $equipe_id,
      ':foto_url'  => $foto_url,
      ':status'    => $status
    ]);

    set_flash('ok', 'Piloto cadastrado com sucesso.');
  }

  header('Location: /rfg/admin/pilotos/listar.php');
  exit;
}

// Busca equipes para o dropdown
$stmt_equipes = $pdo->query("SELECT id, nome FROM equipes ORDER BY nome");
$equipes = $stmt_equipes->fetchAll();

// Busca de dados (agora trazendo a cor da equipe)
$pilotos = $pdo->query("
  SELECT
    p.id,
    p.nome,
    p.numero,
    p.pais,
    p.foto_url,
    p.status,
    e.nome        AS equipe_nome,
    e.cor_primaria AS equipe_cor
  FROM pilotos p
  LEFT JOIN equipes e ON p.equipe_id = e.id
  ORDER BY p.nome
")->fetchAll(PDO::FETCH_ASSOC);

// Pega mensagens
$ok   = get_flash('ok');
$erro = get_flash('erro');

// Função para gerar iniciais
function getInitials($name)
{
  $parts = explode(' ', $name);
  if (count($parts) >= 2) {
    return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
  }
  return strtoupper(substr($name, 0, 2));
}

// Função para gerar cor do avatar (fallback se não tiver cor_primaria)
function getAvatarColor($index)
{
  $colors = [
    'from-blue-500 to-indigo-600',
    'from-red-500 to-pink-600',
    'from-green-500 to-emerald-600',
    'from-purple-500 to-violet-600',
    'from-orange-500 to-amber-600',
    'from-cyan-500 to-teal-600',
  ];
  return $colors[$index % count($colors)];
}
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

    <main class="flex-1 mx-auto max-w-7xl px-4 py-8">
      <!-- Header Section -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <div class="modern-border pl-6 mb-4">
            <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
              Gerenciar <span class="text-yellow-600">Pilotos</span>
            </h2>
          </div>
          <p class="text-gray-600 text-lg">Cadastro e gerenciamento de pilotos</p>
        </div>
        <button onclick="showModal('pilot-modal')" class="btn-primary text-white font-semibold py-3 px-6 rounded-xl">
          + Novo Piloto
        </button>
      </div>

      <!-- Flash Messages -->
      <?php if ($ok): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm shadow-sm">
          <?= htmlspecialchars($ok) ?>
        </div>
      <?php endif; ?>
      <?php if ($erro): ?>
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm shadow-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <!-- Pilots Table -->
      <div class="modern-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">Piloto</th>
                <th class="px-6 py-4 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">Equipe</th>
                <th class="px-6 py-4 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">Nacionalidade</th>
                <th class="px-6 py-4 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">Status</th>
                <th class="px-6 py-4 text-left text-gray-700 font-semibold uppercase tracking-wider text-sm">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php if (empty($pilotos)): ?>
                <tr>
                  <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                      <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                      <p class="text-lg font-medium">Nenhum piloto cadastrado</p>
                      <p class="text-sm text-gray-400">Clique em "Novo Piloto" para adicionar o primeiro</p>
                    </div>
                  </td>
                </tr>
              <?php else: foreach ($pilotos as $index => $p):
                $initials    = getInitials($p['nome']);
                $avatarColor = getAvatarColor($index);
                $status      = $p['status'] ?? 'ativo';
                $statusClass = strtolower($status) === 'ativo' ? 'status-active' : 'status-inactive';
              ?>
                  <tr class="table-row hover:bg-gray-50 transition-colors">
                    <!-- Piloto -->
                    <td class="px-6 py-4">
                      <div class="flex items-center space-x-4">
                        <?php if (!empty($p['foto_url'])): ?>
                          <!-- Foto com fundo da cor da equipe -->
                          <div
                            class="driver-avatar-wrapper w-12 h-12 rounded-full overflow-hidden flex items-center justify-center shrink-0"
                            style="background-color: <?= htmlspecialchars($p['equipe_cor'] ?? '#111827') ?>;"
                          >
                            <img
                              src="<?= htmlspecialchars($p['foto_url']) ?>"
                              class="driver-avatares"
                              alt="<?= htmlspecialchars($p['nome']) ?>"
                            >
                          </div>
                        <?php else: ?>
                          <!-- Sem foto: círculo colorido com iniciais -->
                          <div
                            class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 text-white font-bold text-sm"
                            style="background-color: <?= htmlspecialchars($p['equipe_cor'] ?? '#4b5563') ?>;"
                          >
                            <?= htmlspecialchars($initials) ?>
                          </div>
                        <?php endif; ?>

                        <div>
                          <p class="text-gray-900 font-semibold"><?= htmlspecialchars($p['nome']) ?></p>
                          <p class="text-gray-500 text-sm">#<?= (int)$p['numero'] ?></p>
                        </div>
                      </div>
                    </td>

                    <!-- Equipe -->
                    <td class="px-6 py-4 text-gray-900 font-medium">
                      <?= htmlspecialchars($p['equipe_nome'] ?? 'Sem equipe') ?>
                    </td>

                    <!-- Nacionalidade -->
                    <td class="px-6 py-4 text-gray-900">
                      <?= htmlspecialchars($p['pais']) ?>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                      <span class="<?= $statusClass ?> text-white text-xs px-2 py-1 rounded-md font-medium inline-block">
                        <?= htmlspecialchars(ucfirst($status)) ?>
                      </span>
                    </td>

                    <!-- Ações -->
                    <td class="px-6 py-4">
                      <div class="flex space-x-2">
                        <!-- Editar -->
                        <a href="/rfg/admin/pilotos/editar.php?id=<?= $p['id'] ?>"
                          class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Editar">
                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                          </svg>
                        </a>

                        <!-- Excluir -->
                        <form method="post" action="/rfg/admin/pilotos/listar.php" class="inline-block"
                          onsubmit="return confirm('Tem certeza que deseja remover este piloto?')">
                          <input type="hidden" name="del" value="<?= $p['id'] ?>">
                          <button type="submit"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Excluir">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                              <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                            </svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Cadastrar Piloto -->
  <div id="pilot-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 modal">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all">
      <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="text-xl font-bold text-gray-800">Novo Piloto</h3>
        <button onclick="hideModal('pilot-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form method="post" action="/rfg/admin/pilotos/listar.php" class="p-6 space-y-4">
        <input type="hidden" name="cadastrar" value="1">

        <!-- Nome -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Nome do Piloto <span class="text-red-500">*</span>
          </label>
          <input name="nome" type="text" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <!-- Número + País -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Número <span class="text-red-500">*</span>
            </label>
            <input name="numero" type="number" min="1" max="99" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              País
            </label>
            <input name="pais" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          </div>
        </div>

        <!-- Equipe + Status -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Equipe <span class="text-red-500">*</span>
            </label>
            <select name="equipe_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="">Selecione uma equipe</option>
              <?php foreach ($equipes as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Status
            </label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
              <option value="ativo">Ativo</option>
              <option value="inativo">Inativo</option>
            </select>
          </div>
        </div>

        <!-- Foto -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            URL da Foto
          </label>
          <input name="foto_url" type="url" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
          <p class="mt-1 text-xs text-gray-500">
            Cole o link direto da imagem do piloto (ex.: https://...)
          </p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
          <button type="button" onclick="hideModal('pilot-modal')" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
            Cancelar
          </button>
          <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg hover:shadow-xl transition-all">
            Salvar Piloto
          </button>
        </div>
      </form>
    </div>
  </div>

  <?php require INC . 'layout_footer.php'; ?>

  <script>
    // Funções para controlar modais
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

    // Previne fechar ao clicar dentro do conteúdo do modal
    document.querySelectorAll('.modal > div').forEach(function(modalContent) {
      modalContent.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });
  </script>
</body>

</html>
