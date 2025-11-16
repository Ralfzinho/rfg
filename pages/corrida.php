<?php
// Inclui a conexão com o banco de dados
include('../includes/db.php');
include('../includes/funcoes.php');

// Código para exibir as corridas
?>

<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<?php
// ===============================
// Consulta com filtros (reusa seus campos)
// ===============================
$onlyFuture = isset($_GET['futuras']) && (int)$_GET['futuras'] === 1;

$statusFilter = [];
if (!empty($_GET['status'])) {
    // aceita "agendada,teste,finalizada"
    $statusFilter = array_filter(array_map('trim', explode(',', $_GET['status'])));
}

// Monta SQL
$sql = "
  SELECT c.id, c.nome_gp, circ.NOME AS circuito, c.data, c.status
  FROM corridas c
  JOIN circuitos circ ON circ.ID = c.circuito_id
  WHERE 1=1
";

$params = [];

// Filtro: só futuras
if ($onlyFuture) {
    $sql .= " AND DATE(c.data) >= CURRENT_DATE ";
}

// Filtro: status IN (...)
if (!empty($statusFilter)) {
    $in = [];
    foreach ($statusFilter as $i => $st) {
        $key = ":st{$i}";
        $in[] = $key;
        $params[$key] = $st;
    }
    $sql .= " AND c.status IN (" . implode(',', $in) . ") ";
}

$sql .= " ORDER BY c.data ASC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$corridas = $stmt->fetchAll();

// Helpers
$meses = [1 => 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
?>
<section id="proximas-corridas" class="bg-neutral-900 py-12 md:py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-center text-[clamp(28px,5vw,48px)] font-extrabold text-white">
            Corridas da Temporada
        </h2>

        <div class="relative max-w-5xl mx-auto mt-6">
            <!-- Trilho central em dourado -->
            <div class="pointer-events-none absolute left-1/2 -translate-x-1/2 top-0 bottom-0 w-[2px] bg-[#FFD700]"></div>

            <?php if (!empty($corridas)): ?>
                <?php foreach ($corridas as $i => $row):
                    $id       = (int)$row['id'];
                    $gp       = htmlspecialchars($row['nome_gp'] ?? '', ENT_QUOTES, 'UTF-8');
                    $circuito = htmlspecialchars($row['circuito'] ?? '', ENT_QUOTES, 'UTF-8');
                    $status   = htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8');

                    // Data (suporta DATE ou DATETIME)
                    $ts  = !empty($row['data']) ? strtotime($row['data']) : null;
                    $dia = $ts ? date('d', $ts) : '--';
                    $mes = $ts ? ($meses[(int)date('n', $ts)] ?? '') : '';

                    // Badge igual ao da sua tabela
                    $badgeCls = ($row['status'] === 'agendada')
                        ? 'bg-yellow-200 text-yellow-800'
                        : 'bg-green-200 text-green-800';

                    // Alternância (zigue-zague) no md+: pares à esquerda, ímpares à direita
                    $isRight = ($i % 2 === 1); // ímpares vão para a esquerda do trilho (card à esquerda)
                    // Classes condicionais de ordem no md+
                    $dateColCls = $isRight ? 'md:order-3' : 'md:order-1'; // data troca de lado
                    $cardColCls = $isRight ? 'md:order-1' : 'md:order-3'; // card troca de lado
                ?>
                    <div class="relative grid grid-cols-1 md:grid-cols-[1fr,1.5rem,1fr] gap-4 md:gap-6 py-6 md:py-10">
                        <!-- DATA -->
                        <div class="order-1 <?= $dateColCls ?> text-center md:text-right pr-0 md:pr-2 md:py-10">
                            <div class="text-4xl md:text-5xl leading-none font-extrabold text-white"><?= $dia ?></div>
                            <div class="uppercase text-[11px] md:text-xs tracking-widest text-white/70"><?= $mes ?></div>
                        </div>

                        <!-- MARCADOR CENTRAL -->
                        <div class="order-2 relative w-6 justify-self-center md:justify-self-auto">
                            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                        h-5 w-5 rounded-full bg-[#FFD700] ring-4 ring-black/40"></div>
                        </div>

                        <!-- CARD -->
                        <div class="order-3 <?= $cardColCls ?> bg-white/5 rounded-xl p-4 md:p-5 text-white">
                            <h3 class="font-semibold"><?= $gp ?></h3>
                            <p class="mt-1 text-white/70 text-sm"><?= $circuito ?></p>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <a href="/pages/corrida_detalhe.php?id=<?= $id ?>"
                                    class="inline-block text-sm px-4 py-2 rounded-md bg-[#FFD700] text-black hover:bg-[#E6C200] transition">
                                    Ver detalhes
                                </a>
                                <span class="px-2 py-1 text-xs font-medium uppercase rounded-full <?= $badgeCls ?>">
                                    <?= $status ?>
                                </span>
                                <span class="text-xs text-white/70">
                                    <?= $ts ? date('d/m/Y', $ts) : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-white/70 py-12">Nenhuma corrida encontrada com os filtros atuais.</p>
            <?php endif; ?>
        </div>

        <!-- Mini filtros (opcionais) -->
        <div class="max-w-5xl mx-auto mt-6 flex flex-wrap gap-3 justify-center">
            <a href="?futuras=1" class="text-xs px-3 py-1.5 rounded border border-white/20 text-white hover:bg-white/10">Só futuras</a>
            <a href="?status=agendada,teste" class="text-xs px-3 py-1.5 rounded border border-white/20 text-white hover:bg-white/10">Agendada + Teste</a>
            <a href="?" class="text-xs px-3 py-1.5 rounded border border-white/20 text-white hover:bg-white/10">Limpar filtros</a>
        </div>
    </div>
</section>


<?php include('../includes/layout_footer.php'); ?>