<?php $logged = isset($_SESSION['user']); ?>
<header class="bg-dark text-white sticky top-0 z-50">
  <nav class="relative mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
    <!-- Brand -->
    <div class="flex items-center gap-3">
      <a href="/rfg/index.php" class="flex items-center gap-3 font-extrabold text-xl tracking-wide">
        <img src="/rfg/assets/img/logo.png" alt="Logo da Liga" class="h-9 w-9 object-contain" />
        <span>RACE FOR <span class="text-[#FFD700]">GLORY</span></span>
      </a>
    </div>


    <!-- Botão hambúrguer (só aparece no mobile) -->
    <button id="menuBtn"
      class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
      aria-controls="mobilePanel" aria-expanded="false" aria-label="Abrir menu">
      <svg id="iconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
      <svg id="iconClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Menu desktop -->
    <div class="hidden md:block">
      <ul id="mainMenu" class="flex gap-6 items-center">
        <li><a class="text-white/90 hover:text-white" href="/rfg/index.php">Início</a></li>
        <li><a class="text-white/90 hover:text-white" href="/rfg/pages/corrida.php">Corridas</a></li>
        <li><a class="text-white/90 hover:text-white" href="/rfg/pages/classificacao.php">Classificação</a></li>
        <li><a class="text-white/90 hover:text-white" href="/rfg/pages/equipes.php">Equipes</a></li>
        <li><a class="text-white/90 hover:text-white" href="/rfg/pages/pilotos.php">Pilotos</a></li>
        <li><a class="text-white/90 hover:text-white" href="/rfg/pages/sobre_nos.php">Contato</a></li>

        <?php if ($logged): ?>
          <li><a class="inline-flex items-center px-3 py-1.5 rounded bg-primary text-white"
              href="/rfg/admin/dashboard.php">Painel</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/admin-login/logout.php">Sair</a>
          </li>
        <?php else: ?>
          <li><a class="inline-flex items-center px-3 py-1.5 rounded border border-white/20 hover:border-white"
              href="/rfg/pages/conta.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Painel mobile (dropdown) -->
    <div id="mobilePanel"
      class="hidden absolute left-0 right-0 top-full bg-dark/95 backdrop-blur border-t border-white/10 md:hidden">
      <div class="mx-auto max-w-6xl px-4 py-3">
        <ul class="flex flex-col gap-3">
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/index.php">Início</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/pages/corridas.php">Corridas</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white"
              href="/rfg/pages/classificacao.php">Classificação</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/pages/equipes.php">Equipes</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/pages/pilotos.php">Pilotos</a></li>
          <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/pages/sobre_nos.php">Contato</a></li>

          <?php if ($logged): ?>
            <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/admin/dashboard.php">Painel</a></li>
            <li><a class="block py-2 text-white/90 hover:text-white"
                href="/rfg/admin-login/logout.php">Sair</a></li>
          <?php else: ?>
            <li><a class="block py-2 text-white/90 hover:text-white" href="/rfg/pages/conta.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<script>
  (function () {
    const btn = document.getElementById('menuBtn');
    const mobile = document.getElementById('mobilePanel');
    const iconOpen = document.getElementById('iconOpen');
    const iconClose = document.getElementById('iconClose');

    if (!btn || !mobile) return;

    btn.addEventListener('click', () => {
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      iconOpen.classList.toggle('hidden');
      iconClose.classList.toggle('hidden');
      mobile.classList.toggle('hidden');
    });

    // Fecha ao redimensionar para desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        mobile.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
      }
    });

    // Fecha ao clicar fora (opcional)
    document.addEventListener('click', (e) => {
      const nav = btn.closest('nav');
      if (!nav.contains(e.target) && window.innerWidth < 768) {
        mobile.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
      }
    });
  })();
</script>