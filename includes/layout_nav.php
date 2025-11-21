<?php
// Garante que a sessão exista para ler $_SESSION['user']
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logged = isset($_SESSION['user']);
$user   = $_SESSION['user'] ?? null;
?>

<header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
  <div class="px-6 py-4">
    <div class="flex items-center justify-between">
      <!-- Brand -->
      <div class="flex items-center space-x-4">
        <a href="/rfg/index.php" class="flex items-center gap-3">
          <img src="/rfg/assets/img/logo.png" alt="Logo da Liga" class="h-9 w-9 object-contain" />
          <span class="font-extrabold text-xl tracking-wide text-gray-900">
            RACE FOR <span class="text-[#FFD700]">GLORY</span>
          </span>
        </a>
      </div>

      <!-- Menu Desktop -->
      <nav class="hidden lg:block">
        <ul class="flex gap-6 items-center">
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/index.php">Início</a></li>
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/calendario.php">Calendário</a></li>
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/temporada.php">Temporada</a></li>
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/equipes.php">Equipes</a></li>
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/punicoes.php">Punições</a></li>
          <li><a class="text-gray-600 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/sobre_nos.php">Contato</a></li>

          <?php if ($logged): ?>
            <li>
              <a class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium transition-colors"
                 href="/rfg/admin/dashboard.php">
                Painel
              </a>
            </li>
          <?php else: ?>
            <li>
              <a class="inline-flex items-center px-4 py-2 rounded-lg border-2 border-gray-300 hover:border-indigo-600 text-gray-700 hover:text-indigo-600 font-medium transition-colors"
                 href="/rfg/pages/conta.php">
                Login
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

      <!-- Right Section (User + Hambúrguer) -->
      <div class="flex items-center space-x-4">

        <?php if ($logged): ?>
          <!-- Notifications -->
          <button class="relative p-2 text-gray-500 hover:text-indigo-600 transition-colors rounded-lg hover:bg-gray-50">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
            </svg>
            <span class="absolute top-1 right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
          </button>

          <!-- User Menu -->
          <div class="hidden sm:flex items-center space-x-3">
            <div class="text-right">
              <p class="text-gray-900 font-semibold text-sm">
                <?= htmlspecialchars($user['name'] ?? 'Admin Master', ENT_QUOTES, 'UTF-8') ?>
              </p>
              <p class="text-gray-500 text-xs">
                <?= htmlspecialchars(ucfirst($user['role'] ?? 'admin'), ENT_QUOTES, 'UTF-8') ?>
              </p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
              <span class="text-white font-bold text-sm">
                <?php
                  $initials = 'AM';
                  if (!empty($user['name'])) {
                      $parts = explode(' ', $user['name']);
                      $first = strtoupper(substr($parts[0], 0, 1));
                      $second = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '';
                      $initials = $first . $second;
                  }
                  echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8');
                ?>
              </span>
            </div>
          </div>

          <!-- Logout Button (opcional em nav público) -->
          <a href="/rfg/admin-login/logout.php"
             class="hidden sm:inline-flex items-center px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 text-sm font-medium transition-colors">
            Sair
          </a>
        <?php endif; ?>

        <!-- Botão hambúrguer (mobile) -->
        <button id="menuBtn"
          class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors"
          aria-controls="mobilePanel" aria-expanded="false" aria-label="Abrir menu">
          <svg id="iconOpen" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="iconClose" class="w-6 h-6 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Painel Mobile -->
  <div id="mobilePanel" class="lg:hidden hidden border-t border-gray-200 bg-white">
    <nav class="px-6 py-4">
      <ul class="flex flex-col gap-3">
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/index.php">Início</a></li>
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/corrida.php">Corridas</a></li>
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/classificacao.php">Classificação</a></li>
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/equipes.php">Equipes</a></li>
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/pilotos.php">Pilotos</a></li>
        <li><a class="block text-gray-700 hover:text-gray-900 font-medium transition-colors" href="/rfg/pages/sobre_nos.php">Contato</a></li>

        <?php if ($logged): ?>
          <li>
            <a class="block text-center mt-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium transition-colors"
               href="/rfg/admin/dashboard.php">
              Painel
            </a>
          </li>
          <li>
            <a class="block text-center mt-1 px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 font-medium transition-colors"
               href="/rfg/admin-login/logout.php">
              Sair
            </a>
          </li>
        <?php else: ?>
          <li>
            <a class="block text-center mt-2 px-4 py-2 rounded-lg border-2 border-gray-300 hover:border-indigo-600 text-gray-700 hover:text-indigo-600 font-medium transition-colors"
               href="/rfg/pages/conta.php">
              Login
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<script>
  (function() {
    const btn       = document.getElementById('menuBtn');
    const mobile    = document.getElementById('mobilePanel');
    const iconOpen  = document.getElementById('iconOpen');
    const iconClose = document.getElementById('iconClose');

    if (!btn || !mobile) return;

    function closeMenu() {
      mobile.classList.add('hidden');
      btn.setAttribute('aria-expanded', 'false');
      iconOpen.classList.remove('hidden');
      iconClose.classList.add('hidden');
    }

    btn.addEventListener('click', () => {
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      iconOpen.classList.toggle('hidden');
      iconClose.classList.toggle('hidden');
      mobile.classList.toggle('hidden');
    });

    // Fecha ao redimensionar para desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024) { // lg breakpoint
        closeMenu();
      }
    });

    // Fecha ao clicar fora do header + mobile panel
    document.addEventListener('click', (e) => {
      if (window.innerWidth >= 1024) return; // só se importa no mobile

      const clickInsideButton = btn.contains(e.target);
      const clickInsideMobile = mobile.contains(e.target);

      if (!clickInsideButton && !clickInsideMobile) {
        closeMenu();
      }
    });
  })();
</script>
