<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUri  = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = strtok($currentUri, '?'); // remove ?corrida_id=3 etc.

function menu_active(string $prefix, string $currentPath): bool
{
    return strpos($currentPath, $prefix) === 0;
}

$isDashboard     = menu_active('/rfg/admin/dashboard.php',    $currentPath);
$isPilotos       = menu_active('/rfg/admin/pilotos',              $currentPath);
$isEquipes       = menu_active('/rfg/admin/equipes',              $currentPath);
$isCorridas      = menu_active('/rfg/admin/corridas/listar',      $currentPath);
$isResultados    = menu_active('/rfg/admin/corridas/resultados',  $currentPath);
$isClassificacao = menu_active('/rfg/admin/classificacao.php',       $currentPath);
$isUsuarios      = menu_active('/rfg/admin/usuarios',             $currentPath);
?>

<aside class="w-72 bg-gray-50 min-h-screen border-r border-gray-200">
    <nav class="p-6">
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">
                Menu Principal
            </h2>
        </div>
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="/rfg/admin/dashboard.php"
                    data-section="dashboard"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isDashboard ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isDashboard ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isDashboard ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Dashboard</span>
                </a>
            </li>

            <!-- Pilotos -->
            <li>
                <a href="/rfg/admin/pilotos/listar.php"
                    data-section="pilotos"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isPilotos ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isPilotos ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isPilotos ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Pilotos</span>
                </a>
            </li>

            <!-- Equipes -->
            <li>
                <a href="/rfg/admin/equipes/listar.php"
                    data-section="equipes"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isEquipes ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isEquipes ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isEquipes ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2 1l-3 4-3-4c-.46-.63-1.2-1-2-1H4.46c-.8 0-1.49.59-1.62 1.37L.34 16H2.5v6h2v-6h2.12l.5-1.5h5.76l.5 1.5H15v6h2z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Equipes</span>
                </a>
            </li>

            <!-- Corridas -->
            <li>
                <a href="/rfg/admin/corridas/listar.php"
                    data-section="corridas"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isCorridas ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isCorridas ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isCorridas ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Corridas</span>
                </a>
            </li>

            <!-- Resultados -->
            <li>
                <a href="/rfg/admin/corridas/resultados.php"
                    data-section="resultados"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isResultados ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isResultados ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isResultados ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H19v2h-1.5v17.5c0 .83-.67 1.5-1.5 1.5H8c-.83 0-1.5-.67-1.5-1.5V4H5V2h3.5c0-.83.67-1.5 1.5-1.5h4c.83 0 1.5.67 1.5 1.5H19.5z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Resultados</span>
                </a>
            </li>

            <!-- Classificação -->
            <li>
                <a href="/rfg/admin/classificacao.php"
                    data-section="classificacoes"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isClassificacao ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isClassificacao ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isClassificacao ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.5 21H2V9h5.5v12zm7.25-18h-5.5v18h5.5V3zM22 11h-5.5v10H22V11z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Classificação</span>
                </a>
            </li>

            <!-- Usuários -->
            <li>
                <a href="/rfg/admin/usuarios/listar.php"
                    data-section="usuarios"
                    class="sidebar-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200
                   <?= $isUsuarios ? 'bg-yellow-400 text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $isUsuarios ? 'bg-yellow-500' : 'bg-gray-100' ?>">
                        <svg class="w-4 h-4 <?= $isUsuarios ? 'text-white' : 'text-racing-gold' ?>" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    </div>
                    <span class="font-semibold">Usuários</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>