<?php
include('../includes/db.php'); // Inclui a conexão com o banco de dados

// ---------------------------
// CONSULTAS AO BANCO
// ---------------------------

// Classificação de pilotos (com foto e cor da equipe)
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
$stmtPilotos = $pdo->query($sqlPilotos);
$pilotos = $stmtPilotos->fetchAll(PDO::FETCH_ASSOC);

// Classificação de equipes (com cor_primaria, logo e carro)
$sqlEquipes = "
    SELECT 
      e.id,
      e.nome                             AS equipe,
      COALESCE(e.cor_primaria,'#111827') AS equipe_cor,
      COALESCE(e.foto_url,'')            AS equipe_foto,
      COALESCE(e.carro_url,'')           AS carro_foto,
      COALESCE(SUM(r.pontos), 0)         AS pontos
    FROM equipes e
    LEFT JOIN resultados r ON r.equipe_id = e.id
    GROUP BY e.id, e.nome, e.cor_primaria, e.foto_url, e.carro_url
    ORDER BY pontos DESC, e.nome ASC
";
$stmtEquipes = $pdo->query($sqlEquipes);
$equipes = $stmtEquipes->fetchAll(PDO::FETCH_ASSOC);

// ---------- PAGINAÇÃO PILOTOS ----------
$totalPilotosPorPagina = 10;                                // total na tela (pódio + tabela)
$pilotosPorPagina      = max(0, $totalPilotosPorPagina - 3); // 3 do pódio + resto na tabela

$totalPilotosLista     = max(0, count($pilotos) - 3);       // exclui os 3 do pódio
$totalPaginasPilotos   = max(1, (int)ceil($totalPilotosLista / $pilotosPorPagina));

$currentPagesPilotos   = isset($_GET['page_pilotos']) ? (int)$_GET['page_pilotos'] : 1;
$currentPagesPilotos   = max(1, min($currentPagesPilotos, $totalPaginasPilotos));

$offsetPilotos         = ($currentPagesPilotos - 1) * $pilotosPorPagina;

// ---------- PAGINAÇÃO EQUIPES ----------
$equipesPorPagina      = 10;
$totalEquipesLista     = max(0, count($equipes) - 3);       // exclui as 3 do pódio
$totalPaginasEquipes   = max(1, (int)ceil($totalEquipesLista / $equipesPorPagina));

$currentPagesEquipes   = isset($_GET['page_equipes']) ? (int)$_GET['page_equipes'] : 1;
$currentPagesEquipes   = max(1, min($currentPagesEquipes, $totalPaginasEquipes));

$offsetEquipes         = ($currentPagesEquipes - 1) * $equipesPorPagina;

// ---------- FATIA OS ARRAYS ----------
$topPilotos   = array_slice($pilotos, 0, 3);
$pilotosLista = array_slice($pilotos, 3 + $offsetPilotos, $pilotosPorPagina);

$topEquipes   = array_slice($equipes, 0, 3);
$equipesLista = array_slice($equipes, 3 + $offsetEquipes, $equipesPorPagina);

// Cor da liga (pontos, etc.)
$corLiga = '#f1e363';

// Base da URL sem query string (pra paginação)
$baseUrl = strtok($_SERVER['REQUEST_URI'], '?');

// Helper para iniciais
function getInitials(string $name): string
{
    $name = trim($name);
    if ($name === '') return '';
    $parts = preg_split('/\s+/', $name);
    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 2));
    }
    $first = substr($parts[0], 0, 1);
    $last  = substr($parts[count($parts) - 1], 0, 1);
    return strtoupper($first . $last);
}
?>
<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-10">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">Classificação</h2>
        <p class="text-gray-600 text-lg">
            Acompanhe a classificação atual do campeonato
        </p>
    </div>

    <!-- Abas -->
    <div class="mb-10 flex items-center space-x-4">
        <button onclick="showTab('drivers')" id="drivers-tab"
            class="btn-tab active px-8 py-3 rounded-2xl font-semibold text-lg">
            🏎️ Pilotos
        </button>
        <button onclick="showTab('teams')" id="teams-tab"
            class="btn-tab px-8 py-3 rounded-2xl font-semibold text-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
            🏁 Equipes
        </button>
    </div>

    <?php
    // Arrays auxiliares para os estilos do pódio
    $podiumBorder = ['border-amber-200', 'border-gray-300', 'border-orange-200'];
    $podiumRibbon = ['podium-first', 'podium-second', 'podium-third'];
    ?>

    <!-- ========================= -->
    <!-- ABA: PILOTOS -->
    <!-- ========================= -->
    <div id="drivers-content" class="tab-content">
        <!-- Pódio pilotos -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">🏆 Pódio de Pilotos</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <?php if (count($topPilotos) === 0): ?>
                    <p class="text-gray-500">Nenhum piloto classificado ainda.</p>
                <?php else: ?>

                    <?php
                    // ordem visual: 2º, 1º, 3º
                    if (count($topPilotos) === 1) {
                        $indices = [0];
                    } elseif (count($topPilotos) === 2) {
                        $indices = [1, 0]; // 2º, 1º
                    } else {
                        $indices = [1, 0, 2]; // 2º, 1º, 3º
                    }

                    foreach ($indices as $idx):
                        $p       = $topPilotos[$idx];
                        $posReal = $idx + 1; // 1, 2 ou 3
                        $bgColor = $p['equipe_cor'] ?: '#111827';

                        // define cor da bolinha e da borda conforme a posição real
                        if ($posReal === 1) {
                            $ribbonClass = 'podium-first';   // ouro
                            $borderClass = 'border-yellow-300';
                        } elseif ($posReal === 2) {
                            $ribbonClass = 'podium-second';  // prata
                            $borderClass = 'border-gray-300';
                        } else {
                            $ribbonClass = 'podium-third';   // bronze
                            $borderClass = 'border-orange-300';
                        }
                    ?>
                        <div class="podium-card rounded-3xl overflow-hidden shadow-xl border-2 <?= $borderClass ?>"
                            style="background: <?= htmlspecialchars($bgColor) ?>;">
                            <div class="relative h-80 rounded-3xl overflow-hidden">

                                <?php if (!empty($p['foto'])): ?>
                                    <!-- Foto ocupa o card inteiro, alinhada pro topo pra não cortar o rosto -->
                                    <img src="<?= htmlspecialchars($p['foto']) ?>"
                                        alt="<?= htmlspecialchars($p['piloto']) ?>"
                                        class="absolute inset-0 w-full h-full object-cover object-top">
                                <?php else: ?>
                                    <!-- Sem foto: fundo na cor da equipe com iniciais -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">
                                            <?= htmlspecialchars(getInitials($p['piloto'])) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Fade escuro embaixo para dar contraste no texto -->
                                <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/80 to-transparent"></div>

                                <!-- Bolinha de posição (com as cores ouro/prata/bronze) -->
                                <div class="absolute top-4 left-1/2 -translate-x-1/2">
                                    <div class="<?= $ribbonClass ?> w-12 h-12 rounded-full
                                                 flex items-center justify-center text-white font-bold text-base
                                                 border-4 border-white shadow-lg">
                                        <?= $posReal ?>º
                                    </div>
                                </div>

                                <!-- Nome, equipe e pontos, sobrepostos na parte de baixo -->
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h4 class="text-xl font-bold text-white leading-tight mb-1">
                                        <?= htmlspecialchars($p['piloto']) ?>
                                    </h4>
                                    <p class="text-sm text-gray-100 mb-1">
                                        <?= htmlspecialchars($p['equipe']) ?>
                                    </p>
                                    <p class="text-lg font-extrabold" style="color:#ffffff;">
                                        <?= (int)$p['pontos'] ?> ponto<?= (int)$p['pontos'] == 1 ? '' : 's' ?>
                                    </p>
                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabela completa pilotos (4º em diante, paginado) -->
        <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📊 Classificação Completa de Pilotos</h3>
            <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-gray-700 font-bold uppercase tracking-wider text-sm">Posição</th>
                                <th class="px-8 py-4 text-left text-gray-700 font-bold uppercase tracking-wider text-sm">Piloto</th>
                                <th class="px-8 py-4 text-left text-gray-700 font-bold uppercase tracking-wider text-sm">Equipe</th>
                                <th class="px-8 py-4 text-right text-gray-700 font-bold uppercase tracking-wider text-sm">Pontos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (count($pilotos) === 0): ?>
                                <tr>
                                    <td colspan="4" class="px-8 py-6 text-center text-gray-500">
                                        Nenhum piloto encontrado.
                                    </td>
                                </tr>
                            <?php elseif (count($pilotosLista) === 0): ?>
                                <tr>
                                    <td colspan="4" class="px-8 py-6 text-center text-gray-500">
                                        Apenas pilotos do pódio cadastrados até o momento.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pilotosLista as $index => $p):
                                    // posição real: começa do 4º
                                    $pos = 4 + $offsetPilotos + $index;
                                ?>
                                    <tr class="table-row">
                                        <td class="px-8 py-5">
                                            <div class="position-badge">
                                                <?= $pos ?>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center space-x-4">
                                                <!-- Cor da equipe atrás da foto -->
                                                <div class="w-12 h-12 rounded-full flex items-center justify-center"
                                                    style="background-color: <?= htmlspecialchars($p['equipe_cor']) ?>;">

                                                    <?php if (!empty($p['foto'])): ?>
                                                        <!-- Foto menor para aparecer o anel da cor da equipe -->
                                                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white">
                                                            <img src="<?= htmlspecialchars($p['foto']) ?>"
                                                                alt="<?= htmlspecialchars($p['piloto']) ?>"
                                                                class="w-full h-full object-cover"
                                                                style="object-position: 50% 0%;">
                                                        </div>
                                                    <?php else: ?>
                                                        <!-- Sem foto: iniciais em branco sobre a cor da equipe -->
                                                        <span class="text-white font-bold text-lg">
                                                            <?= htmlspecialchars(getInitials($p['piloto'])) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div>
                                                    <p class="text-gray-900 font-bold text-lg">
                                                        <?= htmlspecialchars($p['piloto']) ?>
                                                    </p>
                                                    <p class="text-gray-500 text-sm">
                                                        <?= htmlspecialchars($p['equipe']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="inline-block px-4 py-2 rounded-xl text-sm font-semibold bg-white"
                                                style="color: <?= htmlspecialchars($p['equipe_cor']) ?>;
                                                       border: 1px solid <?= htmlspecialchars($p['equipe_cor']) ?>;">
                                                <?= htmlspecialchars($p['equipe']) ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-2xl font-bold" style="color:#000000;">
                                                <?= (int)$p['pontos'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPaginasPilotos > 1): ?>
                <nav class="mt-4 flex justify-center">
                    <ul class="inline-flex items-center space-x-1 text-sm">

                        <!-- Anterior -->
                        <li>
                            <?php if ($currentPagesPilotos > 1): ?>
                                <a href="<?= htmlspecialchars($baseUrl . '?page_pilotos=' . ($currentPagesPilotos - 1)) ?>"
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    &laquo; Anterior
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-md border border-gray-200 text-gray-400 cursor-default">
                                    &laquo; Anterior
                                </span>
                            <?php endif; ?>
                        </li>

                        <!-- Números das páginas -->
                        <?php for ($i = 1; $i <= $totalPaginasPilotos; $i++): ?>
                            <li>
                                <?php if ($i === $currentPagesPilotos): ?>
                                    <span class="px-3 py-1 rounded-md border border-indigo-500 bg-indigo-50 text-indigo-700 font-semibold">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($baseUrl . '?page_pilotos=' . $i) ?>"
                                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>

                        <!-- Próximo -->
                        <li>
                            <?php if ($currentPagesPilotos < $totalPaginasPilotos): ?>
                                <a href="<?= htmlspecialchars($baseUrl . '?page_pilotos=' . ($currentPagesPilotos + 1)) ?>"
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    Próximo &raquo;
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-md border border-gray-200 text-gray-400 cursor-default">
                                    Próximo &raquo;
                                </span>
                            <?php endif; ?>
                        </li>

                    </ul>
                </nav>
            <?php endif; ?>
        </div>

    </div> <!-- /drivers-content -->

    <!-- ========================= -->
    <!-- ABA: EQUIPES -->
    <!-- ========================= -->
    <div id="teams-content" class="tab-content hidden">
        <!-- Pódio equipes -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">🏆 Pódio de Equipes</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <?php if (count($topEquipes) === 0): ?>
                    <p class="text-gray-500">Nenhuma equipe classificada ainda.</p>
                <?php else: ?>

                    <?php
                    // ordem visual: 2º, 1º, 3º (igual pilotos)
                    if (count($topEquipes) === 1) {
                        $indicesEquipes = [0];
                    } elseif (count($topEquipes) === 2) {
                        $indicesEquipes = [1, 0]; // 2º, 1º
                    } else {
                        $indicesEquipes = [1, 0, 2]; // 2º, 1º, 3º
                    }

                    foreach ($indicesEquipes as $idx):
                        $e       = $topEquipes[$idx];
                        $posReal = $idx + 1; // 1, 2 ou 3
                        $bgColor = $e['equipe_cor'] ?: '#111827';

                        $carImg  = !empty($e['carro_foto']) ? $e['carro_foto'] : $e['equipe_foto'];
                        $logoImg = $e['equipe_foto'];

                        // ouro / prata / bronze
                        if ($posReal === 1) {
                            $ribbonClass = 'podium-first';
                            $borderClass = 'border-yellow-300';
                        } elseif ($posReal === 2) {
                            $ribbonClass = 'podium-second';
                            $borderClass = 'border-gray-300';
                        } else {
                            $ribbonClass = 'podium-third';
                            $borderClass = 'border-orange-300';
                        }
                    ?>
                        <div class="rounded-3xl overflow-hidden shadow-xl border-2 <?= $borderClass ?>">
                            <div class="relative h-56 md:h-64"
                                style="background: linear-gradient(
                                     90deg,
                                     #000000 0%,
                                     <?= htmlspecialchars($bgColor) ?> 45%,
                                     <?= htmlspecialchars($bgColor) ?> 100%
                                 );">

                                <!-- Medalha de posição -->
                                <div class="absolute top-4 right-4 z-10">
                                    <div class="<?= $ribbonClass ?> w-12 h-12 rounded-full
                                                 flex items-center justify-center text-white font-bold text-base
                                                 border-4 border-white shadow-lg">
                                        <?= $posReal ?>º
                                    </div>
                                </div>

                                <!-- Nome da equipe -->
                                <div class="absolute top-4 left-5 right-20 z-10">
                                    <h4 class="text-2xl font-extrabold text-white drop-shadow-md leading-tight">
                                        <?= htmlspecialchars($e['equipe']) ?>
                                    </h4>
                                </div>

                                <!-- Logo redonda, canto direito inferior -->
                                <?php if (!empty($logoImg)): ?>
                                    <div class="absolute bottom-4 right-4 z-10 w-12 h-12 rounded-full
                                                bg-white/90 flex items-center justify-center shadow-lg overflow-hidden">
                                        <img src="<?= htmlspecialchars($logoImg) ?>"
                                            alt="<?= htmlspecialchars($e['equipe']) ?>"
                                            class="w-full h-full object-contain rounded-full">
                                    </div>
                                <?php endif; ?>

                                <!-- Carro grande com zoom -->
                                <div class="absolute inset-x-0 bottom-0 top-10 flex items-end justify-center pointer-events-none">
                                    <?php if (!empty($carImg)): ?>
                                        <img src="<?= htmlspecialchars($carImg) ?>"
                                            alt="Carro <?= htmlspecialchars($e['equipe']) ?>"
                                            class="w-[135%] max-w-none h-auto object-contain drop-shadow-xl">
                                    <?php else: ?>
                                        <!-- Fallback se não tiver carro -->
                                        <span class="text-5xl font-bold text-white/20">
                                            <?= htmlspecialchars(getInitials($e['equipe'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Pontos, canto esquerdo inferior -->
                                <div class="absolute bottom-4 left-5 z-10">
                                    <p class="text-sm text-white/80">Pontos</p>
                                    <p class="text-3xl font-extrabold text-white drop-shadow-md">
                                        <?= (int)$e['pontos'] ?>
                                    </p>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabela completa equipes (4º em diante, paginado) -->
        <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📊 Classificação Completa de Equipes</h3>
            <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-gray-700 font-bold uppercase tracking-wider text-sm">Posição</th>
                                <th class="px-8 py-4 text-left text-gray-700 font-bold uppercase tracking-wider text-sm">Equipe</th>
                                <th class="px-8 py-4 text-right text-gray-700 font-bold uppercase tracking-wider text-sm">Pontos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (count($equipes) === 0): ?>
                                <tr>
                                    <td colspan="3" class="px-8 py-6 text-center text-gray-500">
                                        Nenhuma equipe encontrada.
                                    </td>
                                </tr>
                            <?php elseif (count($equipesLista) === 0): ?>
                                <tr>
                                    <td colspan="3" class="px-8 py-6 text-center text-gray-500">
                                        Apenas equipes do pódio cadastradas até o momento.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($equipesLista as $index => $e):
                                    // posição real começa do 4º
                                    $pos  = 4 + $offsetEquipes + $index;
                                    $logo = $e['equipe_foto'] ?? '';
                                ?>
                                    <tr class="table-row">
                                        <td class="px-8 py-5">
                                            <div class="position-badge">
                                                <?= $pos ?>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 border-gray-200"
                                                    style="background-color: <?= htmlspecialchars($e['equipe_cor']) ?>;">

                                                    <?php if (!empty($logo)): ?>
                                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-white">
                                                            <img src="<?= htmlspecialchars($logo) ?>"
                                                                alt="<?= htmlspecialchars($e['equipe']) ?>"
                                                                class="w-full h-full object-contain">
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-white font-bold text-lg">
                                                            <?= htmlspecialchars(getInitials($e['equipe'])) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-lg"
                                                        style="color: <?= htmlspecialchars($e['equipe_cor']) ?>;">
                                                        <?= htmlspecialchars($e['equipe']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-2xl font-bold" style="color:<?= $corLiga ?>;">
                                                <?= (int)$e['pontos'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPaginasEquipes > 1): ?>
                <nav class="mt-4 flex justify-center">
                    <ul class="inline-flex items-center space-x-1 text-sm">

                        <!-- Anterior -->
                        <li>
                            <?php if ($currentPagesEquipes > 1): ?>
                                <a href="<?= htmlspecialchars($baseUrl . '?page_equipes=' . ($currentPagesEquipes - 1)) ?>"
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    &laquo; Anterior
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-md border border-gray-200 text-gray-400 cursor-default">
                                    &laquo; Anterior
                                </span>
                            <?php endif; ?>
                        </li>

                        <!-- Números das páginas -->
                        <?php for ($i = 1; $i <= $totalPaginasEquipes; $i++): ?>
                            <li>
                                <?php if ($i === $currentPagesEquipes): ?>
                                    <span class="px-3 py-1 rounded-md border border-indigo-500 bg-indigo-50 text-indigo-700 font-semibold">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($baseUrl . '?page_equipes=' . $i) ?>"
                                        class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>

                        <!-- Próximo -->
                        <li>
                            <?php if ($currentPagesEquipes < $totalPaginasEquipes): ?>
                                <a href="<?= htmlspecialchars($baseUrl . '?page_equipes=' . ($currentPagesEquipes + 1)) ?>"
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    Próximo &raquo;
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-md border border-gray-200 text-gray-400 cursor-default">
                                    Próximo &raquo;
                                </span>
                            <?php endif; ?>
                        </li>

                    </ul>
                </nav>
            <?php endif; ?>
        </div>

    </div> <!-- /teams-content -->

</div> <!-- /container -->

<script>
    function showTab(tabName) {
        // Esconde todos os conteúdos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Mostra o conteúdo selecionado
        document.getElementById(tabName + '-content').classList.remove('hidden');

        // Atualiza botões
        document.querySelectorAll('.btn-tab').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        });

        const activeBtn = document.getElementById(tabName + '-tab');
        activeBtn.classList.add('active');
        activeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
    }
</script>

<?php include('../includes/layout_footer.php'); ?>