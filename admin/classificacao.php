<?php
// admin/classificacao.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__) . '/includes/funcoes.php'; // carrega $pdo e helpers de auth
auth_require_role(['admin','editor']);

define('INC', dirname(__DIR__) . '/includes/');
$title = 'Classificação — RFG';

$erro = '';

// =========================
// CONSULTAS
// =========================
try {
  // --- Equipes: soma de pontos por equipe
  $sqlEquipes = "
    SELECT 
      e.id,
      e.nome AS equipe,
      COALESCE(e.logo_url, '') AS logo,
      COALESCE(SUM(c.pontos), 0) AS pontos
    FROM equipes e
    LEFT JOIN classificacao c ON c.equipe_id = e.id
    /* Para filtrar por temporada, junte com corridas:
       LEFT JOIN corridas co ON co.id = c.corrida_id
       WHERE YEAR(co.data) = 2025
    */
    GROUP BY e.id, e.nome, e.logo_url
    ORDER BY pontos DESC, e.nome ASC
  ";
  $equipes = $pdo->query($sqlEquipes)->fetchAll(PDO::FETCH_ASSOC);

  // --- Pilotos: soma de pontos por piloto (com equipe e logo)
  $sqlPilotos = "
    SELECT 
      p.id,
      p.nome  AS piloto,
      COALESCE(e.nome,'Sem equipe') AS equipe,
      COALESCE(e.logo_url,'')       AS logo,
      COALESCE(SUM(c.pontos), 0)    AS pontos
    FROM pilotos p
    LEFT JOIN classificacao c ON c.piloto_id = p.id
    LEFT JOIN equipes e       ON e.id = p.equipe_id
    /* Para filtrar por temporada:
       LEFT JOIN corridas co ON co.id = c.corrida_id
       WHERE YEAR(co.data) = 2025
    */
    GROUP BY p.id, p.nome, e.nome, e.logo_url
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
<head><?php require INC . 'layout_head.php'; ?></head>
<body class="bg-neutral-50 text-neutral-900">
<?php require INC . 'layout_nav.php'; ?>

<main class="mx-auto max-w-6xl px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Classificação</h1>
    <a href="/rfg/admin/dashboard.php" class="px-4 py-2 rounded border">Voltar</a>
  </div>

  <?php if ($erro): ?>
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
      <?= htmlspecialchars($erro) ?>
    </div>
  <?php endif; ?>

  <!-- ====== CLASSIFICAÇÃO DE EQUIPES ====== -->
  <section class="bg-white border rounded-2xl shadow p-4 md:p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold tracking-wide">CLASSIFICAÇÃO — EQUIPES</h2>
      <a class="px-3 py-1.5 rounded border hover:bg-neutral-50" href="/rfg/pages/classificacao.php">VER COMPLETA</a>
    </div>

    <ul class="divide-y">
      <?php if (!empty($equipes)): ?>
        <?php foreach ($equipes as $i => $row): ?>
          <li class="flex items-center justify-between py-3 px-2 <?= $i===0 ? 'bg-yellow-50 rounded-lg' : '' ?>">
            <div class="flex items-center gap-3 min-w-0">
              <span class="w-6 shrink-0 text-neutral-500"><?= $i+1 ?></span>
              <img src="<?= htmlspecialchars($row['logo'] ?: 'https://placehold.co/40x40') ?>"
                   alt="Logo da equipe"
                   class="w-9 h-9 rounded-full object-cover shrink-0">
              <span class="font-medium truncate"><?= htmlspecialchars($row['equipe'] ?? '') ?></span>
            </div>
            <span class="font-semibold text-green-600 whitespace-nowrap"><?= (int)($row['pontos'] ?? 0) ?> pts</span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="py-6 text-center text-neutral-500">Sem dados de classificação para equipes.</li>
      <?php endif; ?>
    </ul>
  </section>

  <!-- ====== CLASSIFICAÇÃO DE PILOTOS ====== -->
  <section class="bg-white border rounded-2xl shadow p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold tracking-wide">CLASSIFICAÇÃO — PILOTOS</h2>
      <a class="px-3 py-1.5 rounded border hover:bg-neutral-50" href="/rfg/pages/classificacao.php#pilotos">VER COMPLETA</a>
    </div>

    <ul class="divide-y">
      <?php if (!empty($pilotos)): ?>
        <?php foreach ($pilotos as $i => $row): ?>
          <li class="flex items-center justify-between py-3 px-2 <?= $i===0 ? 'bg-yellow-50 rounded-lg' : '' ?>">
            <div class="flex items-center gap-3 min-w-0">
              <span class="w-6 shrink-0 text-neutral-500"><?= $i+1 ?></span>
              <img src="<?= htmlspecialchars($row['logo'] ?: 'https://placehold.co/40x40') ?>"
                   alt="Logo da equipe"
                   class="w-9 h-9 rounded-full object-cover shrink-0">
              <div class="truncate">
                <div class="font-medium truncate"><?= htmlspecialchars($row['piloto'] ?? '') ?></div>
                <div class="text-sm text-neutral-500 truncate"><?= htmlspecialchars($row['equipe'] ?? 'Sem equipe') ?></div>
              </div>
            </div>
            <span class="font-semibold text-green-600 whitespace-nowrap"><?= (int)($row['pontos'] ?? 0) ?> pts</span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="py-6 text-center text-neutral-500">Sem dados de classificação para pilotos.</li>
      <?php endif; ?>
    </ul>
  </section>

  <p class="mt-4 text-sm text-neutral-600">
    * A pontuação é calculada automaticamente a partir da tabela <code>classificacao</code>.
    Para filtrar por temporada, junte a tabela <code>corridas</code> e aplique <code>YEAR(data)</code>.
  </p>
</main>

<?php require INC . 'layout_footer.php'; ?>
</body>
</html>
