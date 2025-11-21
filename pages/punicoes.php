<?php
declare(strict_types=1);

// Se você já usa funcoes.php para iniciar sessão / puxar db, mantém.
// Se não usar, pode remover essa linha.
require_once __DIR__ . '/../includes/funcoes.php';

include('../includes/layout_head.php');
include('../includes/layout_nav.php');
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
        <button class="filter-btn active px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'all')">
            Todas
        </button>
        <button class="filter-btn px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'time')">
            ⏱️ Tempo
        </button>
        <button class="filter-btn px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'grid')">
            🏁 Grid
        </button>
        <button class="filter-btn px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'fine')">
            💰 Multa
        </button>
        <button class="filter-btn px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'warning')">
            ⚠️ Advertência
        </button>
        <button class="filter-btn px-6 py-3 rounded-xl font-semibold bg-white"
                onclick="filterPenalties(event, 'dsq')">
            🚫 Desqualificação
        </button>
    </div>

    <!-- Grid de punições -->
    <div id="news-grid" class="grid md:grid-cols-2 gap-8">

        <!-- Penalty 1 - Verstappen -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="time">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                            MV
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Max Verstappen</h3>
                            <p class="text-gray-600">🇳🇱 Red Bull Racing</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-time">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                  clip-rule="evenodd"/>
                        </svg>
                        +10 segundos
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP do México • 27 Out 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Penalidade por ultrapassagem fora da pista</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Verstappen recebeu duas penalidades de 10 segundos no GP do México após incidentes com Lando Norris.
                    O piloto holandês forçou Norris para fora da pista na curva 4 e depois ganhou vantagem ao ultrapassá-lo
                    pela área de escape na curva 8.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 1.2k visualizações</span>
                        <span>💬 45 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 2 - Alonso -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="grid" style="animation-delay: 0.1s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                            FA
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Fernando Alonso</h3>
                            <p class="text-gray-600">🇪🇸 Aston Martin</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-grid">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        -3 posições
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP da Austrália • 24 Mar 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Punição por condução perigosa com Russell</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Fernando Alonso recebeu penalidade de 3 posições no grid após o incidente com George Russell na
                    classificação. Os comissários determinaram que Alonso travou anormalmente cedo antes da curva,
                    causando o acidente de Russell.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 890 visualizações</span>
                        <span>💬 32 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 3 - Magnussen -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="fine" style="animation-delay: 0.2s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #475569 0%, #64748b 100%);">
                            KM
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Kevin Magnussen</h3>
                            <p class="text-gray-600">🇩🇰 Haas</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-fine">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                  clip-rule="evenodd"/>
                        </svg>
                        €25.000
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP de Miami • 5 Mai 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Multa por excesso de velocidade no pit lane</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Kevin Magnussen foi multado em €25.000 após ser flagrado excedendo o limite de velocidade no pit lane
                    durante os treinos livres. O piloto dinamarquês estava 15 km/h acima do limite permitido de 80 km/h.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 654 visualizações</span>
                        <span>💬 18 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 4 - Sainz -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="warning" style="animation-delay: 0.3s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                            CS
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Carlos Sainz</h3>
                            <p class="text-gray-600">🇪🇸 Ferrari</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-warning">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Advertência
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP do Japão • 7 Abr 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Advertência por impedir piloto na classificação</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Carlos Sainz recebeu uma advertência formal após impedir Yuki Tsunoda durante o Q2 da classificação.
                    O piloto espanhol estava em uma volta lenta e não percebeu a aproximação do piloto japonês em volta rápida.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 534 visualizações</span>
                        <span>💬 12 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 5 - Perez -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="time" style="animation-delay: 0.4s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                            SP
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Sergio Pérez</h3>
                            <p class="text-gray-600">🇲🇽 Red Bull Racing</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-time">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                  clip-rule="evenodd"/>
                        </svg>
                        +5 segundos
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP do Canadá • 9 Jun 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Penalidade por causar colisão com Gasly</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Sergio Pérez recebeu 5 segundos de penalidade após ser considerado culpado pela colisão com Pierre Gasly
                    na primeira volta. O piloto mexicano fechou o espaço na curva 1, causando contato e danos no carro da Alpine.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 876 visualizações</span>
                        <span>💬 28 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 6 - Leclerc -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="dsq" style="animation-delay: 0.5s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                            CL
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Charles Leclerc</h3>
                            <p class="text-gray-600">🇲🇨 Ferrari</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-dsq">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Desqualificado
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP dos EUA • 20 Out 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Desqualificação por irregularidade no assoalho</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Charles Leclerc foi desqualificado do GP dos EUA após a fiscalização técnica pós-corrida detectar que
                    o assoalho da Ferrari estava excessivamente desgastado, violando o regulamento técnico. A equipe perdeu
                    os pontos conquistados pelo piloto.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 2.1k visualizações</span>
                        <span>💬 67 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 7 - Hamilton -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="grid" style="animation-delay: 0.6s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);">
                            LH
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Lewis Hamilton</h3>
                            <p class="text-gray-600">🇬🇧 Mercedes</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-grid">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        -5 posições
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP da Bélgica • 28 Jul 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Penalidade por troca de unidade de potência</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Lewis Hamilton recebeu penalidade de 5 posições no grid após a Mercedes optar por instalar uma nova
                    unidade de potência em seu carro, excedendo o limite permitido para a temporada. A equipe buscava mais
                    confiabilidade para o resto do campeonato.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 1.5k visualizações</span>
                        <span>💬 41 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

        <!-- Penalty 8 - Norris -->
        <article class="news-card rounded-3xl overflow-hidden shadow-xl border border-gray-200 animate-fade-in"
                 data-type="time" style="animation-delay: 0.7s">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="driver-avatar"
                             style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
                            LN
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Lando Norris</h3>
                            <p class="text-gray-600">🇬🇧 McLaren</p>
                        </div>
                    </div>
                    <span class="penalty-badge penalty-time">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                  clip-rule="evenodd"/>
                        </svg>
                        +5 segundos
                    </span>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        🏁 GP dos EUA • 20 Out 2024
                    </span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">Penalidade por ultrapassar fora dos limites da pista</h4>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Lando Norris recebeu 5 segundos de penalidade após ultrapassar Max Verstappen fora dos limites da
                    pista na curva 12. Os comissários determinaram que Norris estava completamente fora da pista ao
                    completar a manobra sobre Verstappen.
                </p>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-600">
                        <span>👁️ 1.8k visualizações</span>
                        <span>💬 52 comentários</span>
                    </div>
                    <button class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Ler mais →
                    </button>
                </div>
            </div>
        </article>

    </div>

    <!-- Botão "Carregar mais" -->
    <div class="mt-12 text-center">
        <button
            class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            Carregar mais notícias
        </button>
    </div>
</main>

<script>
    function filterPenalties(evt, type) {
        const cards = document.querySelectorAll('[data-type]');
        const buttons = document.querySelectorAll('.filter-btn');

        // remove "active" de todos
        buttons.forEach(btn => btn.classList.remove('active'));

        // adiciona "active" no botão clicado
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add('active');
        }

        // mostra/esconde cards
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
