<?php
// admin/classificacao.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__) . '/includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', dirname(__DIR__) . '/includes/');
$title = 'Classificação — RFG';
global $pdo;

$erro = '';

try {
  // --- Classificação de Equipes a partir de RESULTADOS ---
  $sqlEquipes = "
        SELECT 
            e.id,
            e.nome AS equipe,
            COALESCE(e.foto_url, '') AS logo,
            COALESCE(SUM(r.pontos), 0) AS pontos
        FROM equipes e
        LEFT JOIN resultados r ON r.equipe_id = e.id
        GROUP BY e.id, e.nome, e.foto_url
        ORDER BY pontos DESC, e.nome ASC
    ";
  $equipes = $pdo->query($sqlEquipes)->fetchAll(PDO::FETCH_ASSOC);

  // --- Classificação de Pilotos a partir de RESULTADOS ---
  $sqlPilotos = "
    SELECT 
      p.id,
      p.nome  AS piloto,
      COALESCE(e.nome,'Sem equipe')      AS equipe,
      COALESCE(p.foto_url,'')            AS foto,
      COALESCE(e.cor_primaria,'#111827') AS equipe_cor,
      COALESCE(SUM(r.pontos), 0)         AS pontos
    FROM pilotos p
    LEFT JOIN resultados r ON r.piloto_id = p.id
    LEFT JOIN equipes e    ON e.id = p.equipe_id
    GROUP BY p.id, p.nome, e.nome, p.foto_url, e.cor_primaria
    ORDER BY pontos DESC, p.nome ASC
  ";
  $pilotos = $pdo->query($sqlPilotos)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $erro = 'Erro ao carregar classificação: ' . $e->getMessage();
  $equipes = $pilotos = [];
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
    <!-- Sidebar -->
    <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

    <main class="flex-1 mx-auto max-w-6xl px-4 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Classificação Geral</h1>
        <a href="/rfg/admin/dashboard.php" class="btn-primary text-white font-semibold py-3 px-6 rounded-xl">
          Voltar ao Painel
        </a>
      </div>

      <?php if ($erro): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <div class="grid md:grid-cols-2 gap-8">
        <!-- ====== CLASSIFICAÇÃO DE EQUIPES ====== -->
        <section class="bg-white border rounded-2xl shadow p-4 md:p-6">
          <h2 class="text-lg font-semibold tracking-wide mb-4">Classificação de Equipes</h2>
          <ul class="divide-y">
            <?php if (!empty($equipes)): ?>
              <?php foreach ($equipes as $i => $row): ?>
                <li class="flex items-center justify-between py-3 px-2 <?= $i === 0 ? 'bg-yellow-50 rounded-lg' : '' ?>">
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="w-6 shrink-0 text-neutral-500"><?= $i + 1 ?></span>
                    <img
                      src="<?= htmlspecialchars($row['logo'] ?: 'https://placehold.co/40x40') ?>"
                      alt="Logo da equipe <?= htmlspecialchars($row['equipe']) ?>"
                      class="w-9 h-9 rounded-full object-cover shrink-0">
                    <span class="font-medium truncate">
                      <?= htmlspecialchars($row['equipe']) ?>
                    </span>
                  </div>
                  <span class="font-semibold text-green-600 whitespace-nowrap">
                    <?= (int) $row['pontos'] ?> pts
                  </span>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="py-6 text-center text-neutral-500">
                Nenhum ponto registrado.
              </li>
            <?php endif; ?>
          </ul>
        </section>

        <!-- ====== CLASSIFICAÇÃO DE PILOTOS ====== -->
        <section class="bg-white border rounded-2xl shadow p-4 md:p-6">
          <h2 class="text-lg font-semibold tracking-wide mb-4">Classificação de Pilotos</h2>
          <ul class="divide-y">
            <?php if (!empty($pilotos)): ?>
              <?php foreach ($pilotos as $i => $row): ?>
                <li class="flex items-center justify-between py-3 px-2 <?= $i === 0 ? 'bg-yellow-50 rounded-lg' : '' ?>">
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="w-6 shrink-0 text-neutral-500"><?= $i + 1 ?></span>

                    <!-- Avatar do piloto com fundo da cor da equipe -->
                    <div
                      class="driver-avatar-wrapper w-10 h-10 rounded-full overflow-hidden flex items-center justify-center shrink-0"
                      style="background-color: <?= htmlspecialchars($row['equipe_cor'] ?? '#111827') ?>;">
                      <img
                        src="<?= htmlspecialchars($row['foto'] ?: '/rfg/assets/img/piloto-placeholder.png') ?>"
                        alt="Foto de <?= htmlspecialchars($row['piloto']) ?>"
                        class="driver-avatar">
                    </div>

                    <div class="truncate">
                      <div class="font-medium truncate">
                        <?= htmlspecialchars($row['piloto']) ?>
                      </div>
                      <div class="text-sm text-neutral-500 truncate">
                        <?= htmlspecialchars($row['equipe']) ?>
                      </div>
                    </div>
                  </div>
                  <span class="font-semibold text-green-600 whitespace-nowrap">
                    <?= (int) $row['pontos'] ?> pts
                  </span>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="py-6 text-center text-neutral-500">
                Nenhum ponto registrado.
              </li>
            <?php endif; ?>
          </ul>
        </section>
      </div>

      <p class="mt-6 text-sm text-center text-neutral-600">
        * A pontuação é calculada em tempo real a partir da tabela <code>resultados</code>.
      </p>
    </main>
  </div>

  <?php require INC . 'layout_footer.php'; ?>
</body>

</html>