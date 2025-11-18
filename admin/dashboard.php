<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/funcoes.php';

// exige que seja admin
auth_require_role(['admin']);

global $pdo;

$user  = auth_user();
$title = 'Painel — Racing for Glory';

// =============== CARDS DE ESTATÍSTICA ===============
$totalPilotos = (int) $pdo->query("
    SELECT COUNT(*) 
    FROM pilotos 
    WHERE status = 'ativo'
")->fetchColumn();

$equipesAtivas = (int) $pdo->query("
    SELECT COUNT(*) 
    FROM equipes 
    WHERE status = 'ativa'
")->fetchColumn();

// corridas do ano atual
$totalCorridas = (int) $pdo->query("
    SELECT COUNT(*) 
    FROM corridas 
    WHERE YEAR(data) = YEAR(CURDATE())
")->fetchColumn();

// corridas com resultado concluído
$corridasConcluidas = (int) $pdo->query("
    SELECT COUNT(DISTINCT corrida_id) 
    FROM resultados 
    WHERE status = 'concluido'
")->fetchColumn();

// total de usuários
$totalUsuarios = (int) $pdo->query("
    SELECT COUNT(*) 
    FROM usuarios
")->fetchColumn();

// =============== PRÓXIMAS CORRIDAS ===============
$proximasCorridas = $pdo->query("
    SELECT 
        c.id,
        c.nome_gp,
        c.data,
        c.status,
        circ.nome        AS circuito_nome,
        circ.localizacao,
        circ.pais
    FROM corridas c
    LEFT JOIN circuitos circ ON circ.id = c.circuito_id
    WHERE c.data >= CURDATE()
    ORDER BY c.data ASC
    LIMIT 2
")->fetchAll(PDO::FETCH_ASSOC);

// =============== ATIVIDADES RECENTES (LOGINS) ===============
$atividadesRecentes = $pdo->query("
    SELECT 
        la.data_login,
        la.ip,
        la.navegador,
        u.name,
        u.email
    FROM logs_acesso la
    LEFT JOIN usuarios u ON u.id = la.usuario_id
    ORDER BY la.data_login DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Helper para mostrar "há X min/horas/dias"
function tempoDecorrido(string $data): string
{
    $ts = strtotime($data);
    if ($ts === false) {
        return '';
    }

    $diff = time() - $ts;
    if ($diff < 60) {
        return 'Agora mesmo';
    }

    $min = floor($diff / 60);
    if ($min < 60) {
        return 'Há ' . $min . ' min';
    }

    $horas = floor($min / 60);
    if ($horas < 24) {
        return 'Há ' . $horas . ' h';
    }

    $dias = floor($horas / 24);
    return 'Há ' . $dias . ' dia' . ($dias > 1 ? 's' : '');
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <?php require dirname(__DIR__) . '/includes/layout_head.php'; ?>
</head>

<body class="bg-white font-modern">
    <!-- Header -->
    <?php require dirname(__DIR__) . '/includes/layout_nav.php'; ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php require dirname(__DIR__) . '/includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-8 bg-gray-50">
            <!-- ================= DASHBOARD SECTION ================= -->
            <div id="dashboard-section" class="section">
                <div class="mb-8">
                    <div class="modern-border pl-6 mb-4">
                        <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
                            Dashboard <span class="text-primary">Geral</span>
                        </h2>
                    </div>
                    <p class="text-gray-600 text-lg">Visão geral do sistema de gerenciamento F1</p>
                </div>

                <!-- ====== Cards de Estatísticas ====== -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Pilotos -->
                    <div class="modern-card rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                    Total Pilotos
                                </p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    <?= $totalPilotos ?>
                                </p>
                                <p class="text-green-600 text-sm font-medium mt-1">
                                    Pilotos ativos no campeonato
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Equipes Ativas -->
                    <div class="modern-card rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                    Equipes Ativas
                                </p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    <?= $equipesAtivas ?>
                                </p>
                                <p class="text-blue-600 text-sm font-medium mt-1">
                                    Equipes cadastradas e ativas
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2 1l-3 4-3-4c-.46-.63-1.2-1-2-1H4.46c-.8 0-1.49.59-1.62 1.37L.34 16H2.5v6h2v-6h2.12l.5-1.5h5.76l.5 1.5H15v6h2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Corridas do Ano -->
                    <div class="modern-card rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                    Corridas <?= date('Y') ?>
                                </p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    <?= $totalCorridas ?>
                                </p>
                                <p class="text-amber-600 text-sm font-medium mt-1">
                                    <?= $corridasConcluidas ?> finalizadas
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Usuários -->
                    <div class="modern-card rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                    Usuários
                                </p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    <?= $totalUsuarios ?>
                                </p>
                                <p class="text-green-600 text-sm font-medium mt-1">
                                    Contas cadastradas no painel
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2 1l-3 4-3-4c-.46-.63-1.2-1-2-1H4.46c-.8 0-1.49.59-1.62 1.37L.34 16H2.5v6h2v-6h2.12l.5-1.5h5.76l.5 1.5H15v6h2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====== Atividades Recentes + Próximas Corridas ====== -->
                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Atividades Recentes -->
                    <div class="modern-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Atividades Recentes</h3>

                        <?php if (!empty($atividadesRecentes)): ?>
                            <div class="space-y-4">
                                <?php foreach ($atividadesRecentes as $i => $atv): ?>
                                    <?php
                                    $cores = ['bg-green-500', 'bg-blue-500', 'bg-amber-500', 'bg-purple-500', 'bg-rose-500'];
                                    $cor   = $cores[$i % count($cores)];
                                    ?>
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="w-3 h-3 rounded-full <?= $cor ?>"></div>
                                        <div class="flex-1">
                                            <p class="text-gray-900 text-sm font-medium">
                                                <?= htmlspecialchars($atv['name'] ?? 'Usuário desconhecido') ?>
                                                fez login
                                            </p>
                                            <p class="text-gray-500 text-xs mt-1">
                                                <?= tempoDecorrido($atv['data_login']) ?>
                                                • IP <?= htmlspecialchars($atv['ip'] ?? '-') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">
                                Nenhuma atividade registrada ainda.
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Próximas Corridas -->
                    <div class="modern-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Próximas Corridas</h3>

                        <?php if (!empty($proximasCorridas)): ?>
                            <div class="space-y-4">
                                <?php foreach ($proximasCorridas as $i => $corrida): ?>
                                    <?php
                                    $cardClasses = $i === 0
                                        ? 'from-indigo-50 to-purple-50 border-indigo-100'
                                        : 'from-amber-50 to-orange-50 border-amber-100';

                                    $dataTs = strtotime($corrida['data']);
                                    $diaMes = date('d M', $dataTs);

                                    $status = strtolower($corrida['status'] ?? '');
                                    $badgeText  = 'Agendada';
                                    $badgeClass = 'text-gray-700 bg-gray-200';

                                    if ($status === 'próxima' || $status === 'proxima') {
                                        $badgeText  = 'Próxima';
                                        $badgeClass = 'text-amber-700 bg-amber-100';
                                    } elseif ($status === 'teste') {
                                        $badgeText  = 'Teste';
                                        $badgeClass = 'text-blue-700 bg-blue-100';
                                    } elseif ($status === 'finalizada') {
                                        $badgeText  = 'Finalizada';
                                        $badgeClass = 'text-green-700 bg-green-100';
                                    }
                                    ?>
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r <?= $cardClasses ?> rounded-xl border">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-gray-900 font-semibold">
                                                    <?= htmlspecialchars($corrida['nome_gp']) ?>
                                                </p>
                                                <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($badgeText) ?>
                                                </span>
                                            </div>
                                            <p class="text-gray-600 text-sm">
                                                <?= htmlspecialchars(($corrida['localizacao'] ?? '') . ', ' . ($corrida['pais'] ?? '')) ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-primary font-bold text-lg">
                                                <?= $diaMes ?>
                                            </p>
                                            <p class="text-gray-500 text-sm">
                                                Horário a definir
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">
                                Nenhuma corrida futura cadastrada.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ====== OUTRAS SECTIONS (corridas / resultados / standings / usuários) ====== -->
            <!-- Aqui mantive exatamente como você já tinha, só copiei do arquivo original -->

            <!-- Pilots Section -->
            <div id="races-section" class="section hidden">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <div class="racing-border pl-4 mb-4">
                            <h2 class="text-3xl font-racing font-bold text-white uppercase tracking-wider">
                                Gerenciar <span class="text-racing-gold">Corridas</span>
                            </h2>
                        </div>
                        <p class="text-gray-400">Calendário e gerenciamento de corridas</p>
                    </div>
                    <button onclick="showModal('race-modal')" class="btn-racing text-black font-bold py-3 px-6 rounded-lg font-racing uppercase tracking-wider">
                        + Nova Corrida
                    </button>
                </div>

                <!-- Calendar View (estático por enquanto) -->
                <div class="admin-card rounded-xl p-6 racing-glow mb-6">
                    <h3 class="text-xl font-racing font-bold text-white mb-4">Calendário 2024</h3>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-800 rounded-lg p-4 border-l-4 border-green-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold">GP do Bahrein</h4>
                                <span class="text-green-400 text-xs px-2 py-1 bg-green-900 rounded">Finalizada</span>
                            </div>
                            <p class="text-gray-400 text-sm">02 Mar 2024 • Sakhir</p>
                            <p class="text-racing-gold text-sm font-bold mt-2">Vencedor: Max Verstappen</p>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-4 border-l-4 border-yellow-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold">GP do Canadá</h4>
                                <span class="text-yellow-400 text-xs px-2 py-1 bg-yellow-900 rounded">Próxima</span>
                            </div>
                            <p class="text-gray-400 text-sm">09 Jun 2024 • Montreal</p>
                            <p class="text-gray-500 text-sm mt-2">Aguardando resultado</p>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-4 border-l-4 border-gray-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold">GP da Espanha</h4>
                                <span class="text-gray-400 text-xs px-2 py-1 bg-gray-700 rounded">Agendada</span>
                            </div>
                            <p class="text-gray-400 text-sm">23 Jun 2024 • Barcelona</p>
                            <p class="text-gray-500 text-sm mt-2">Aguardando</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div id="results-section" class="section hidden">
                <div class="mb-8">
                    <div class="racing-border pl-4 mb-4">
                        <h2 class="text-3xl font-racing font-bold text-white uppercase tracking-wider">
                            Gerenciar <span class="text-racing-gold">Resultados</span>
                        </h2>
                    </div>
                    <p class="text-gray-400">Cadastro e gerenciamento de resultados das corridas</p>
                </div>

                <div class="space-y-6">
                    <div class="admin-card rounded-xl p-6 racing-glow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-racing font-bold text-white">GP de Mônaco - 26 Mai 2024</h3>
                            <button class="btn-racing text-black font-bold py-2 px-4 rounded text-sm">
                                Editar Resultado
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-700">
                                        <th class="text-left py-2 text-racing-gold font-racing">Pos</th>
                                        <th class="text-left py-2 text-racing-gold font-racing">Piloto</th>
                                        <th class="text-left py-2 text-racing-gold font-racing">Equipe</th>
                                        <th class="text-left py-2 text-racing-gold font-racing">Tempo</th>
                                        <th class="text-left py-2 text-racing-gold font-racing">Pontos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-800">
                                        <td class="py-2 text-racing-gold font-bold">1º</td>
                                        <td class="py-2 text-white">Charles Leclerc</td>
                                        <td class="py-2 text-white">Ferrari</td>
                                        <td class="py-2 text-white font-mono">1:29:34.508</td>
                                        <td class="py-2 text-racing-gold font-bold">25</td>
                                    </tr>
                                    <tr class="border-b border-gray-800">
                                        <td class="py-2 text-gray-300">2º</td>
                                        <td class="py-2 text-white">Oscar Piastri</td>
                                        <td class="py-2 text-white">McLaren</td>
                                        <td class="py-2 text-white font-mono">+7.152</td>
                                        <td class="py-2 text-racing-gold font-bold">18</td>
                                    </tr>
                                    <tr class="border-b border-gray-800">
                                        <td class="py-2 text-gray-300">3º</td>
                                        <td class="py-2 text-white">Carlos Sainz</td>
                                        <td class="py-2 text-white">Ferrari</td>
                                        <td class="py-2 text-white font-mono">+7.585</td>
                                        <td class="py-2 text-racing-gold font-bold">15</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Standings Section -->
            <div id="standings-section" class="section hidden">
                <div class="mb-8">
                    <div class="racing-border pl-4 mb-4">
                        <h2 class="text-3xl font-racing font-bold text-white uppercase tracking-wider">
                            Classificação <span class="text-racing-gold">Geral</span>
                        </h2>
                    </div>
                    <p class="text-gray-400">Classificação atual do campeonato</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-6">
                    <!-- Drivers Championship -->
                    <div class="admin-card rounded-xl p-6 racing-glow">
                        <h3 class="text-xl font-racing font-bold text-white mb-4">Campeonato de Pilotos</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-racing-gold bg-opacity-20 rounded-lg border border-racing-gold">
                                <div class="flex items-center space-x-3">
                                    <span class="text-racing-gold font-racing font-bold text-lg">1º</span>
                                    <div>
                                        <p class="text-white font-bold">Max Verstappen</p>
                                        <p class="text-gray-400 text-sm">Red Bull Racing</p>
                                    </div>
                                </div>
                                <span class="text-racing-gold font-racing font-bold text-xl">169</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-300 font-racing font-bold text-lg">2º</span>
                                    <div>
                                        <p class="text-white font-bold">Charles Leclerc</p>
                                        <p class="text-gray-400 text-sm">Ferrari</p>
                                    </div>
                                </div>
                                <span class="text-white font-racing font-bold text-xl">118</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-300 font-racing font-bold text-lg">3º</span>
                                    <div>
                                        <p class="text-white font-bold">Lando Norris</p>
                                        <p class="text-gray-400 text-sm">McLaren</p>
                                    </div>
                                </div>
                                <span class="text-white font-racing font-bold text-xl">87</span>
                            </div>
                        </div>
                    </div>

                    <!-- Constructors Championship -->
                    <div class="admin-card rounded-xl p-6 racing-glow">
                        <h3 class="text-xl font-racing font-bold text-white mb-4">Campeonato de Construtores</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-racing-gold bg-opacity-20 rounded-lg border border-racing-gold">
                                <div class="flex items-center space-x-3">
                                    <span class="text-racing-gold font-racing font-bold text-lg">1º</span>
                                    <div>
                                        <p class="text-white font-bold">Red Bull Racing</p>
                                        <p class="text-gray-400 text-sm">RB20</p>
                                    </div>
                                </div>
                                <span class="text-racing-gold font-racing font-bold text-xl">245</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-300 font-racing font-bold text-lg">2º</span>
                                    <div>
                                        <p class="text-white font-bold">Ferrari</p>
                                        <p class="text-gray-400 text-sm">SF-24</p>
                                    </div>
                                </div>
                                <span class="text-white font-racing font-bold text-xl">189</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-300 font-racing font-bold text-lg">3º</span>
                                    <div>
                                        <p class="text-white font-bold">McLaren</p>
                                        <p class="text-gray-400 text-sm">MCL38</p>
                                    </div>
                                </div>
                                <span class="text-white font-racing font-bold text-xl">154</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div id="users-section" class="section hidden">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <div class="racing-border pl-4 mb-4">
                            <h2 class="text-3xl font-racing font-bold text-white uppercase tracking-wider">
                                Gerenciar <span class="text-racing-gold">Usuários</span>
                            </h2>
                        </div>
                        <p class="text-gray-400">Cadastro e gerenciamento de usuários do sistema</p>
                    </div>
                    <button onclick="showModal('user-modal')" class="btn-racing text-black font-bold py-3 px-6 rounded-lg font-racing uppercase tracking-wider">
                        + Novo Usuário
                    </button>
                </div>

                <!-- Tabela de usuários (ainda estática por enquanto) -->
                <div class="admin-card rounded-xl overflow-hidden racing-glow">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">Usuário</th>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">E-mail</th>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">Cadastro</th>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-racing-gold font-racing font-bold uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr class="table-row">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-racing-gold rounded-full flex items-center justify-center">
                                                <span class="text-black font-bold text-sm">AM</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">Admin Master</p>
                                                <p class="text-gray-400 text-sm">@admin_master</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-white">admin@racingforglory.com</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">Admin</span>
                                    </td>
                                    <td class="px-6 py-4 text-white">01/01/2024</td>
                                    <td class="px-6 py-4">
                                        <span class="status-active text-white text-xs px-2 py-1 rounded-full">Ativo</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-400 hover:text-blue-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                </svg>
                                            </button>
                                            <button class="text-red-400 hover:text-red-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">JS</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">João Silva</p>
                                                <p class="text-gray-400 text-sm">@joao_silva</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-white">joao@email.com</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">Usuário</span>
                                    </td>
                                    <td class="px-6 py-4 text-white">15/05/2024</td>
                                    <td class="px-6 py-4">
                                        <span class="status-active text-white text-xs px-2 py-1 rounded-full">Ativo</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-400 hover:text-blue-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                </svg>
                                            </button>
                                            <button class="text-red-400 hover:text-red-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Pilot Modal (ainda dummy) -->
    <div id="pilot-modal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="modern-card rounded-2xl p-8 max-w-md w-full mx-4 modern-shadow">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Novo Piloto</h3>
                <button onclick="hideModal('pilot-modal')" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </button>
            </div>
            <form class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nome Completo</label>
                    <input type="text" class="modern-input w-full rounded-xl px-4 py-3 text-gray-900 focus:outline-none" placeholder="Ex: Max Verstappen">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Número</label>
                    <input type="number" class="modern-input w-full rounded-xl px-4 py-3 text-gray-900 focus:outline-none" placeholder="Ex: 1">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Equipe</label>
                    <select class="modern-input w-full rounded-xl px-4 py-3 text-gray-900 focus:outline-none">
                        <option>Selecione uma equipe</option>
                        <option>Red Bull Racing</option>
                        <option>Ferrari</option>
                        <option>McLaren</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nacionalidade</label>
                    <input type="text" class="modern-input w-full rounded-xl px-4 py-3 text-gray-900 focus:outline-none" placeholder="Ex: Holanda">
                </div>
                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="hideModal('pilot-modal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 btn-primary text-white font-semibold py-3 px-4 rounded-xl">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Message Container -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
</body>

</html>