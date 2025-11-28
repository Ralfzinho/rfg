<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/funcoes.php';
include('../includes/layout_head.php');
include('../includes/layout_nav.php');

/** @var PDO $pdo */

// --------------------------
// Paginação
// --------------------------
$perPage = 6; // 6 cards por página (3 linhas x 2 colunas)
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// total de registros
$totalPunicoes = (int)$pdo->query("SELECT COUNT(*) FROM punicoes")->fetchColumn();
$totalPages = max(1, (int)ceil($totalPunicoes / $perPage));

// --------------------------
// Busca das punições da página atual
// --------------------------
$sqlPunicoes = "
    SELECT 
        pu.*,
        pi.nome       AS piloto_nome,
        pi.numero     AS piloto_numero,
        e.nome        AS equipe_nome,
        c.nome_gp     AS corrida_nome,
        c.data        AS corrida_data
    FROM punicoes pu
    LEFT JOIN pilotos  pi ON pi.id = pu.piloto_id
    LEFT JOIN equipes  e  ON e.id  = pu.equipe_id
    LEFT JOIN corridas c  ON c.id  = pu.corrida_id
    ORDER BY pu.data_punicao DESC, pu.id DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sqlPunicoes);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$punicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapa de tipos (tipo do banco -> slug do filtro + rótulos/cores)
$mapTipos = [
    'tempo' => [
        'slug'      => 'time',
        'label'     => '⏱️ Tempo',
        'badge'     => 'penalty-time',
        'tag_class' => 'bg-indigo-100 text-indigo-800',
    ],
    'grid' => [
        'slug'      => 'grid',
        'label'     => '🏁 Grid',
        'badge'     => 'penalty-grid',
        'tag_class' => 'bg-green-100 text-green-800',
    ],
    'multa' => [
        'slug'      => 'fine',
        'label'     => '💰 Multa',
        'badge'     => 'penalty-fine',
        'tag_class' => 'bg-purple-100 text-purple-800',
    ],
    'advertencia' => [
        'slug'      => 'warning',
        'label'     => '⚠️ Advertência',
        'badge'     => 'penalty-warning',
        'tag_class' => 'bg-yellow-100 text-yellow-800',
    ],
    'desclassificacao' => [
        'slug'      => 'dsq',
        'label'     => '🚫 Desqualificação',
        'badge'     => 'penalty-dsq',
        'tag_class' => 'bg-red-100 text-red-800',
    ],
    'outro' => [
        'slug'      => 'time',
        'label'     => '🔧 Outro',
        'badge'     => 'penalty-time',
        'tag_class' => 'bg-gray-100 text-gray-800',
    ],
];

// funçãozinha pra gerar iniciais do piloto (tipo "MV", "LH")
function iniciais_piloto(?string $nome): string {
    if (!$nome) return '??';
    $parts = preg_split('/\s+/', trim($nome));
    if (!$parts) return '??';
    $first = strtoupper(substr($parts[0], 0, 1));
    $last  = strtoupper(substr(end($parts), 0, 1));
    return $first . $last;
}
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <!-- Título da página -->
    <div class="mb-12">
        <h2 class="text-5xl font-bold text-gray-900 mb-3">⚠️ Punições e Penalidades</h2>
        <p class="text-gray-600 text-lg">
            Acompanhe todas as punições aplicadas aos pilotos na temporada
        </p>
    </div>

    <!-- Filtros -->
    <div class="mb-12 flex flex-wrap gap-4">
        <button
            class="filter-btn btn-primary text-white font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'all')">
            Todas
        </button>
        <button
            class="filter-btn bg-white text-gray-800 border border-gray-200 font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'time')">
            ⏱️ Tempo
        </button>
        <button
            class="filter-btn bg-white text-gray-800 border border-gray-200 font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'grid')">
            🏁 Grid
        </button>
        <button
            class="filter-btn bg-white text-gray-800 border border-gray-200 font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'fine')">
            💰 Multa
        </button>
        <button
            class="filter-btn bg-white text-gray-800 border border-gray-200 font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'warning')">
            ⚠️ Advertência
        </button>
        <button
            class="filter-btn bg-white text-gray-800 border border-gray-200 font-semibold py-3 px-6 rounded-xl"
            onclick="filterPenalties(event, 'dsq')">
            🚫 Desqualificação
        </button>
    </div>

    <!-- Grid de punições -->
    <div id="news-grid" class="grid md:grid-cols-2 gap-8">
        <?php if (count($punicoes) === 0): ?>
            <div class="md:col-span-2">
                <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 px-8 py-12 text-center text-gray-500">
                    Nenhuma punição registrada ainda.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($punicoes as $index => $p): ?>
                <?php
                $tipoDb   = $p['tipo'] ?? 'outro';
                $tipoInfo = $mapTipos[$tipoDb] ?? $mapTipos['outro'];

                $dataPunicao = $p['data_punicao'] ? date('d M Y', strtotime($p['data_punicao'])) : '';
                $dataCorrida = $p['corrida_data'] ? date('d M Y', strtotime($p['corrida_data'])) : '';
                $gpLabel     = $p['corrida_nome']
                    ? '🏁 ' . $p['corrida_nome'] . ($dataCorrida ? " • {$dataCorrida}" : '')
                    : ($dataPunicao ? "Punição em {$dataPunicao}" : '');

                $iniciais = iniciais_piloto($p['piloto_nome'] ?? '');
                $delay    = 0.1 * $index; // animação em cascata
                ?>
                <article
                    class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                    data-type="<?= htmlspecialchars($tipoInfo['slug']) ?>"
                    style="animation-delay: <?= number_format($delay, 1, '.', '') ?>s">
                    <div class="p-8">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="driver-avatar"
                                     style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 35%, #a855f7 100%);">
                                    <?= htmlspecialchars($iniciais) ?>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <?= htmlspecialchars($p['piloto_nome'] ?? 'Piloto desconhecido') ?>
                                    </h3>
                                    <p class="text-gray-600">
                                        <?= htmlspecialchars($p['equipe_nome'] ?? 'Equipe não informada') ?>
                                    </p>
                                </div>
                            </div>
                            <span class="penalty-badge <?= $tipoInfo['badge'] ?>">
                                <?= htmlspecialchars($tipoInfo['label']) ?>
                            </span>
                        </div>

                        <div class="mb-4">
                            <?php if ($gpLabel): ?>
                                <span
                                    class="inline-block <?= $tipoInfo['tag_class'] ?> px-3 py-1 rounded-full text-xs font-semibold mb-3">
                                    <?= htmlspecialchars($gpLabel) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h4 class="text-2xl font-bold text-gray-900 mb-3">
                            <?= htmlspecialchars($p['titulo'] ?? 'Infração sem título') ?>
                        </h4>

                        <?php
                        $desc = $p['descricao'] ?: $p['valor_punicao'];
                        ?>
                        <?php if ($desc): ?>
                            <p class="text-gray-700 mb-6 leading-relaxed">
                                <?= nl2br(htmlspecialchars($desc)) ?>
                            </p>
                        <?php endif; ?>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 text-sm text-gray-600">
                            <span>
                                Valor / Efeito: <strong><?= htmlspecialchars($p['valor_punicao'] ?? '-') ?></strong>
                            </span>
                            <!-- Se quiser colocar "Ler mais" depois, dá pra linkar para uma página de detalhe -->
                            <!-- <button class="text-indigo-600 font-semibold hover:text-indigo-700">Ler mais →</button> -->
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-12 flex justify-center">
            <nav class="inline-flex items-center space-x-2 text-sm">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>"
                       class="px-3 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
                        « Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="px-3 py-2 rounded-xl btn-primary text-white font-semibold">
                            <?= $i ?>
                        </span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>"
                           class="px-3 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>"
                       class="px-3 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
                        Próxima »
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</main>

<script>
    function filterPenalties(evt, type) {
        const cards   = document.querySelectorAll('[data-type]');
        const buttons = document.querySelectorAll('.filter-btn');

        // reset style de todos os botões
        buttons.forEach(btn => {
            btn.classList.remove('btn-primary', 'text-white');
            btn.classList.add('bg-white', 'text-gray-800', 'border', 'border-gray-200');
        });

        // aplica estilo do botão ativo
        if (evt && evt.currentTarget) {
            const activeBtn = evt.currentTarget;
            activeBtn.classList.remove('bg-white', 'text-gray-800', 'border', 'border-gray-200');
            activeBtn.classList.add('btn-primary', 'text-white');
        }

        // filtra os cards
        cards.forEach(card => {
            if (type === 'all' || card.dataset.type === type) {
                card.style.display = 'block';
                card.classList.add('animate-fade-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-fade-in');
            }
        });
    }
</script>

<?php include('../includes/layout_footer.php'); ?>
