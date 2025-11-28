<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtém o caminho do script atual para determinar a página ativa.
// Exemplos: /admin/dashboard.php, /admin/pilotos/listar.php
$currentPath = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '/');

// Define a estrutura do menu da barra lateral.
// Cada item contém:
// - href: O link de destino.
// - label: O texto a ser exibido.
// - active_check: Uma string ou array de strings para verificar se o item está ativo.
// - svg_path: O caminho do ícone SVG.
$menuItems = [
    [
        'href' => '/rfg/admin/dashboard.php',
        'label' => 'Dashboard',
        'active_check' => 'dashboard.php',
        'svg_path' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'
    ],
    [
        'href' => '/rfg/admin/pilotos/listar.php',
        'label' => 'Pilotos',
        'active_check' => '/admin/pilotos/',
        'svg_path' => 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'
    ],
    [
        'href' => '/rfg/admin/equipes/listar.php',
        'label' => 'Equipes',
        'active_check' => '/admin/equipes/',
        'svg_path' => 'M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2 1l-3 4-3-4c-.46-.63-1.2-1-2-1H4.46c-.8 0-1.49.59-1.62 1.37L.34 16H2.5v6h2v-6h2.12l.5-1.5h5.76l.5 1.5H15v6h2z'
    ],
    [
        'href' => '/rfg/admin/corridas/listar.php',
        'label' => 'Corridas',
        'active_check' => '/admin/corridas/listar',
        'svg_path' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'
    ],
    [
        'href' => '/rfg/admin/corridas/resultados.php',
        'label' => 'Resultados',
        'active_check' => '/admin/corridas/resultados',
        'svg_path' => 'M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H19v2h-1.5v17.5c0 .83-.67 1.5-1.5 1.5H8c-.83 0-1.5-.67-1.5-1.5V4H5V2h3.5c0-.83.67-1.5 1.5-1.5h4c.83 0 1.5.67 1.5 1.5H19.5z'
    ],
    [
        'href' => '/rfg/admin/classificacao.php',
        'label' => 'Classificação',
        'active_check' => 'classificacao.php',
        'svg_path' => 'M7.5 21H2V9h5.5v12zm7.25-18h-5.5v18h5.5V3zM22 11h-5.5v10H22V11z'
    ],
    [
        'href' => '/rfg/admin/usuarios/listar.php',
        'label' => 'Usuários',
        'active_check' => '/admin/usuarios/',
        'svg_path' => 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z'
    ],
        [
        'href' => '/rfg/admin/corridas/punicao.php',
        'label' => 'Punições',
        'active_check' => 'corridas/punicao.php', 
        'svg_path' => 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z'
    ],
];
?>

<aside class="w-72 bg-gray-50 min-h-screen border-r border-gray-200">
    <nav class="p-6">
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">
                Menu Principal
            </h2>
        </div>
        <ul class="space-y-2">
            <?php foreach ($menuItems as $item) : ?>
                <?php
                // Verifica se o item de menu atual corresponde à página ativa.
                $isActive = str_contains($currentPath, $item['active_check']);

                // Classes CSS para o item de menu.
                $linkClasses = 'w-full flex items-center space-x-3 px-4 py-3 text-left rounded-xl transition-all duration-200';
                $activeClasses = 'bg-yellow-400 text-white shadow-lg';
                $inactiveClasses = 'text-gray-600 hover:text-gray-900 hover:bg-gray-100';

                // Classes CSS para o ícone.
                $iconContainerClasses = 'w-8 h-8 rounded-lg flex items-center justify-center';
                $activeIconContainerClasses = 'bg-yellow-500';
                $inactiveIconContainerClasses = 'bg-gray-100';

                $iconClasses = 'w-4 h-4';
                $activeIconClasses = 'text-white';
                $inactiveIconClasses = 'text-racing-gold';
                ?>
                <li>
                    <a href="<?= $item['href'] ?>"
                        class="<?= $linkClasses ?> <?= $isActive ? $activeClasses : $inactiveClasses ?>">
                        <div class="<?= $iconContainerClasses ?> <?= $isActive ? $activeIconContainerClasses : $inactiveIconContainerClasses ?>">
                            <svg class="<?= $iconClasses ?> <?= $isActive ? $activeIconClasses : $inactiveIconClasses ?>" fill="currentColor" viewBox="0 0 24 24">
                                <path d="<?= $item['svg_path'] ?>" />
                            </svg>
                        </div>
                        <span class="font-semibold"><?= $item['label'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>