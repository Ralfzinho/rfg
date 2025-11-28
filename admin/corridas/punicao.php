<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// inclui utilitários e conexão ($pdo)
require_once __DIR__ . '/../../includes/funcoes.php';

if (!defined('INC')) {
    define('INC', __DIR__ . '/../../includes/');
}

/** @var PDO $pdo */

// ----------------------------------------
// Carrega listas auxiliares (pilotos, equipes, corridas)
// ----------------------------------------
$sqlPilotos = "
    SELECT p.id, p.nome, p.numero, e.nome AS equipe
    FROM pilotos p
    LEFT JOIN equipes e ON e.id = p.equipe_id
    ORDER BY p.nome
";
$pilotos = $pdo->query($sqlPilotos)->fetchAll(PDO::FETCH_ASSOC);

$sqlEquipes = "
    SELECT id, nome
    FROM equipes
    ORDER BY nome
";
$equipes = $pdo->query($sqlEquipes)->fetchAll(PDO::FETCH_ASSOC);

$sqlCorridas = "
    SELECT id, nome_gp, data
    FROM corridas
    ORDER BY data
";
$corridas = $pdo->query($sqlCorridas)->fetchAll(PDO::FETCH_ASSOC);

// tipos de punição (mesmos valores do ENUM da tabela)
$tiposPunicao = [
    'tempo'             => '⏱️ Tempo',
    'grid'              => '🏁 Grid',
    'multa'             => '💰 Multa',
    'advertencia'       => '⚠️ Advertência',
    'desclassificacao'  => '🚫 Desclassificação',
    'outro'             => '🔧 Outro',
];

// ----------------------------------------
// Tratamento do POST (aplicar punição)
// ----------------------------------------
$erros = [];
$old   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $piloto_id    = isset($_POST['piloto_id']) ? (int) $_POST['piloto_id'] : 0;
    $equipe_id    = isset($_POST['equipe_id']) ? (int) $_POST['equipe_id'] : 0;
    $corrida_id   = isset($_POST['corrida_id']) ? (int) $_POST['corrida_id'] : 0;
    $tipo         = $_POST['tipo'] ?? '';
    $valor        = trim($_POST['valor_punicao'] ?? '');
    $titulo       = trim($_POST['titulo'] ?? '');
    $descricao    = trim($_POST['descricao'] ?? '');
    $data_punicao = $_POST['data_punicao'] ?? '';

    if ($piloto_id <= 0) {
        $erros[] = 'Selecione um piloto.';
    }
    if ($equipe_id <= 0) {
        $erros[] = 'Selecione uma equipe.';
    }
    if ($corrida_id <= 0) {
        $erros[] = 'Selecione o Grande Prêmio.';
    }
    if (!array_key_exists($tipo, $tiposPunicao)) {
        $erros[] = 'Selecione o tipo de punição.';
    }
    if ($valor === '') {
        $erros[] = 'Informe o valor/efeito da punição.';
    }
    if ($titulo === '') {
        $erros[] = 'Informe um título para a infração.';
    }
    if ($data_punicao === '') {
        $erros[] = 'Informe a data da punição.';
    }

    if (empty($erros)) {
        $sqlInsert = "
            INSERT INTO punicoes (
                corrida_id,
                piloto_id,
                equipe_id,
                tipo,
                valor_punicao,
                titulo,
                descricao,
                data_punicao,
                status
            ) VALUES (
                :corrida_id,
                :piloto_id,
                :equipe_id,
                :tipo,
                :valor_punicao,
                :titulo,
                :descricao,
                :data_punicao,
                'aplicada'
            )
        ";

        $stmt = $pdo->prepare($sqlInsert);
        $stmt->execute([
            ':corrida_id'   => $corrida_id,
            ':piloto_id'    => $piloto_id,
            ':equipe_id'    => $equipe_id,
            ':tipo'         => $tipo,
            ':valor_punicao' => $valor,
            ':titulo'       => $titulo,
            ':descricao'    => $descricao !== '' ? $descricao : null,
            ':data_punicao' => $data_punicao,
        ]);

        if (function_exists('set_flash')) {
            set_flash('punicao_sucesso', 'Punição aplicada com sucesso!');
        } else {
            $_SESSION['flash']['punicao_sucesso'] = 'Punição aplicada com sucesso!';
        }

        header('Location: punicao.php');
        exit;
    } else {
        $msgErro = implode('<br>', $erros);
        if (function_exists('set_flash')) {
            set_flash('punicao_erro', $msgErro);
        } else {
            $_SESSION['flash']['punicao_erro'] = $msgErro;
        }
    }
}

// pega mensagens de flash (se houver)
$flashSucesso = $_SESSION['flash']['punicao_sucesso'] ?? null;
$flashErro    = $_SESSION['flash']['punicao_erro'] ?? null;
unset($_SESSION['flash']['punicao_sucesso'], $_SESSION['flash']['punicao_erro']);

// ----------------------------------------
// Lista de punições já aplicadas
// ----------------------------------------
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
";
$punicoes = $pdo->query($sqlPunicoes)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php require INC . 'layout_head.php'; ?>
</head>

<body class="bg-gray-100">
    <?php require INC . 'layout_nav.php'; ?>
    <div class="min-h-screen flex">
        <!-- Sidebar do Admin -->
        <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>

        <!-- Conteúdo principal -->
        <main class="flex-1 bg-gray-50">
            <div class="max-w-7xl mx-auto px-8 py-10">
                <!-- Título da página -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                            ⚠️
                        </span>
                        Gestão de Punições
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Aplique e gerencie punições aos pilotos da temporada 2024.
                    </p>
                </div>

                <!-- Mensagens de feedback -->
                <?php if ($flashSucesso): ?>
                    <div class="mb-6 rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                        <?= $flashSucesso ?>
                    </div>
                <?php endif; ?>

                <?php if ($flashErro): ?>
                    <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                        <?= $flashErro ?>
                    </div>
                <?php endif; ?>

                <!-- Card: Aplicar Nova Punição -->
                <section class="mb-10">
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-200">
                        <!-- Cabeçalho -->
                        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                                    +
                                </span>
                                <div>
                                    <h2 class="text-lg md:text-xl font-semibold text-gray-900">
                                        Aplicar Nova Punição
                                    </h2>
                                    <p class="text-sm text-gray-500">
                                        Preencha os dados abaixo para registrar uma punição.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulário -->
                        <form method="post" class="px-8 py-8 space-y-6">
                            <!-- Linha 1: Piloto / Equipe -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Piloto
                                    </label>
                                    <select name="piloto_id"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                        <option value="">Selecione o piloto...</option>
                                        <?php foreach ($pilotos as $p): ?>
                                            <option value="<?= (int)$p['id'] ?>"
                                                <?= (isset($old['piloto_id']) && (int)$old['piloto_id'] === (int)$p['id']) ? 'selected' : '' ?>>
                                                #<?= htmlspecialchars((string)$p['numero']) ?> - <?= htmlspecialchars($p['nome']) ?>
                                                <?php if (!empty($p['equipe'])): ?>
                                                    (<?= htmlspecialchars($p['equipe']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Equipe
                                    </label>
                                    <select name="equipe_id"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                        <option value="">Selecione a equipe...</option>
                                        <?php foreach ($equipes as $e): ?>
                                            <option value="<?= (int)$e['id'] ?>"
                                                <?= (isset($old['equipe_id']) && (int)$old['equipe_id'] === (int)$e['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($e['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Linha 2: Tipo / GP -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Tipo de Punição
                                    </label>
                                    <select name="tipo"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                        <option value="">Selecione...</option>
                                        <?php foreach ($tiposPunicao as $key => $label): ?>
                                            <option value="<?= htmlspecialchars($key) ?>"
                                                <?= (isset($old['tipo']) && $old['tipo'] === $key) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Grande Prêmio
                                    </label>
                                    <select name="corrida_id"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                        <option value="">Selecione o GP...</option>
                                        <?php foreach ($corridas as $c): ?>
                                            <?php
                                            $dataLabel = $c['data'] ? date('d/m/Y', strtotime($c['data'])) : '';
                                            ?>
                                            <option value="<?= (int)$c['id'] ?>"
                                                <?= (isset($old['corrida_id']) && (int)$old['corrida_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['nome_gp']) ?>
                                                <?= $dataLabel ? ' • ' . $dataLabel : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Linha 3: Valor / Data -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Valor / Efeito da Punição
                                    </label>
                                    <input type="text" name="valor_punicao"
                                        value="<?= htmlspecialchars($old['valor_punicao'] ?? '') ?>"
                                        placeholder="Ex: +10 segundos, -3 posições, €25.000"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Data da Punição
                                    </label>
                                    <input type="date" name="data_punicao"
                                        value="<?= htmlspecialchars($old['data_punicao'] ?? '') ?>"
                                        class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                </div>
                            </div>

                            <!-- Linha 4: Título -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Título da Infração
                                </label>
                                <input type="text" name="titulo"
                                    value="<?= htmlspecialchars($old['titulo'] ?? '') ?>"
                                    placeholder="Ex: Causar colisão, ultrapassagem fora da pista..."
                                    class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                            </div>

                            <!-- Linha 5: Descrição -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Descrição Detalhada
                                </label>
                                <textarea name="descricao" rows="4"
                                    placeholder="Descreva a infração cometida pelo piloto..."
                                    class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm resize-none"><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="reset"
                                    class="px-5 py-2.5 rounded-2xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                    Limpar
                                </button>
                                <button type="submit"
                                    class="px-7 py-2.5 rounded-2xl bg-yellow-600 text-sm font-semibold text-white shadow-md hover:bg-yellow-700 hover:shadow-lg transition">
                                    Aplicar Punição
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Card: Punições Aplicadas -->
                <section>
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-200">
                        <!-- Cabeçalho -->
                        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                    📄
                                </span>
                                <div>
                                    <h2 class="text-lg md:text-xl font-semibold text-gray-900">
                                        Punições Aplicadas
                                    </h2>
                                    <p class="text-sm text-gray-500">
                                        Histórico de punições registradas no campeonato.
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                <?= count($punicoes) ?> puniç<?= count($punicoes) === 1 ? 'ão' : 'ões' ?>
                            </span>
                        </div>

                        <!-- Conteúdo -->
                        <div class="px-8 py-6">
                            <?php if (count($punicoes) === 0): ?>
                                <div class="py-12 flex flex-col items-center justify-center text-center text-gray-500">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                        ✖
                                    </div>
                                    <p class="font-semibold">Nenhuma punição aplicada ainda</p>
                                    <p class="text-xs text-gray-400">
                                        Use o formulário acima para adicionar punições.
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">GP / Data</th>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">Piloto</th>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">Equipe</th>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">Tipo</th>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">Detalhes</th>
                                                <th class="px-3 py-3 text-left font-semibold text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <?php foreach ($punicoes as $pu): ?>
                                                <?php
                                                $dataCorrida = $pu['corrida_data'] ? date('d/m/Y', strtotime($pu['corrida_data'])) : '-';
                                                $dataPunicao = $pu['data_punicao'] ? date('d/m/Y', strtotime($pu['data_punicao'])) : '-';
                                                $tipoLabel   = $tiposPunicao[$pu['tipo']] ?? ucfirst($pu['tipo']);
                                                $status      = $pu['status'] ?? 'aplicada';
                                                ?>
                                                <tr>
                                                    <td class="px-3 py-3 align-top">
                                                        <div class="font-semibold text-gray-900">
                                                            <?= htmlspecialchars($pu['corrida_nome'] ?? '—') ?>
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Corrida: <?= $dataCorrida ?> • Punição: <?= $dataPunicao ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 align-top">
                                                        <div class="font-semibold text-gray-900">
                                                            <?= htmlspecialchars($pu['piloto_nome'] ?? '—') ?>
                                                        </div>
                                                        <?php if (!empty($pu['piloto_numero'])): ?>
                                                            <div class="text-xs text-gray-500">
                                                                #<?= htmlspecialchars((string)$pu['piloto_numero']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="px-3 py-3 align-top">
                                                        <span class="inline-flex items-center rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-50">
                                                            <?= htmlspecialchars($pu['equipe_nome'] ?? '—') ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 align-top">
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                            <?= htmlspecialchars($tipoLabel) ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 align-top">
                                                        <div class="font-semibold text-gray-900">
                                                            <?= htmlspecialchars($pu['titulo']) ?>
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            <?= htmlspecialchars($pu['valor_punicao']) ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 align-top">
                                                        <?php
                                                        $statusLabel = [
                                                            'aplicada'    => 'Aplicada',
                                                            'em_revisao'  => 'Em revisão',
                                                            'anulada'     => 'Anulada',
                                                        ][$status] ?? ucfirst($status);

                                                        $statusClasses = match ($status) {
                                                            'aplicada'   => 'bg-green-100 text-green-700',
                                                            'anulada'    => 'bg-red-100 text-red-700',
                                                            'em_revisao' => 'bg-yellow-100 text-yellow-700',
                                                            default      => 'bg-gray-100 text-gray-600',
                                                        };
                                                        ?>
                                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusClasses ?>">
                                                            <?= $statusLabel ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <?php require INC . 'layout_footer.php'; ?>
</body>

</html>