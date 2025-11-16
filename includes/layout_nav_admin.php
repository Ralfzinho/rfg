<?php
require_once __DIR__ . '/../includes/funcoes.php';
define('INC', dirname(__DIR__) . '/includes/');


$user = auth_user();
?>
<nav class="bg-neutral-900 text-white border-b border-neutral-800">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img src="/rfg/assets/img/logo.png" alt="Logo da Liga" class="h-8 w-8 object-contain">
            <span class="font-bold tracking-wider text-sm uppercase">
                Painel <span class="text-[#C9A300]">RFG</span>
            </span>
        </div>

        <div class="flex items-center space-x-4 text-sm">
            <a href="/rfg/admin/dashboard.php" class="hover:text-[#C9A300]">Dashboard</a>
            <a href="/rfg/admin/pilotos.php" class="hover:text-[#C9A300]">Pilotos</a>
            <a href="/rfg/admin/equipes.php" class="hover:text-[#C9A300]">Equipes</a>
            <a href="/rfg/admin/corridas.php" class="hover:text-[#C9A300]">Corridas</a>
            <a href="/rfg/admin/resultados.php" class="hover:text-[#C9A300]">Resultados</a>

            <?php if ($user): ?>
                <span class="text-xs text-white/70 border-l border-neutral-700 pl-3">
                    <?= htmlspecialchars($user['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>
                    <span class="uppercase text-[10px] text-[#C9A300] ml-1">
                        (<?= htmlspecialchars($user['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                    </span>
                </span>
            <?php endif; ?>

            <a href="/rfg/admin-login/logout.php"
                class="ml-3 inline-flex items-center px-3 py-1 rounded-full bg-red-600/80 hover:bg-red-500 text-xs font-semibold">
                Sair
            </a>
        </div>
    </div>
</nav>