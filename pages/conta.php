<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// inclui utilitários (auth_login, auth_is, etc.) e puxa $pdo via includes/db.php
require_once dirname(__DIR__) . '/includes/funcoes.php';

// constante para facilitar includes de layout a partir de /pages
define('INC', dirname(__DIR__) . '/includes/');

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['usuario'] ?? '');
  $senha = $_POST['senha'] ?? '';

  if ($email === '' || $senha === '') {
    $erro = 'Preencha usuário e senha.';
  } elseif (auth_login($email, $senha)) {
    // redireciona conforme o papel (usando caminhos absolutos pra não quebrar)
    if (auth_is('admin')) {
      header('Location: /rfg/admin/dashboard.php');
      exit;
    } elseif (auth_is('editor')) {
      header('Location: /rfg/editor/dashboard.php');
      exit;
    } else {
      header('Location: /rfg/index.php');
      exit;
    }
  } else {
    $erro = 'Usuário ou senha inválidos.';
  }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <?php require INC . 'layout_head.php'; ?>
</head>

<body class="flex flex-col min-h-screen bg-neutral-50 text-neutral-900 antialiased">

  <!-- Header -->
  <?php require INC . 'layout_nav.php'; ?>

  <!-- Área principal -->
  <main class="min-h-screen bg-neutral-900 text-white login-bg relative overflow-hidden">
    <!-- overlay/gradiente sutil -->
    <div class="absolute inset-0 bg-gradient-to-r from-[#C9A300]/10 via-neutral-900/60 to-black pointer-events-none">
    </div>

    <section class="relative z-10 container mx-auto px-4 py-12">
      <!-- Cabeçalho da página -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center">
            <!-- Ícone simples -->
            <img src="/rfg/assets/img/logo.png" alt="Logo da Liga" class="h-9 w-9 object-contain"/>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold tracking-wide">
            RACING FOR <span class="text-[#C9A300]">GLORY</span>
          </h1>
        </div>

        <a href="/rfg/index.php" class="text-white/80 hover:text-[#C9A300] transition uppercase tracking-wide text-sm">
          ← Voltar ao site
        </a>
      </div>

      <!-- Grid principal -->
      <div class="grid lg:grid-cols-2 gap-8 items-center">
        <!-- Lado visual -->
        <div class="hidden lg:block">
          <div class="rounded-2xl overflow-hidden ring-1 ring-white/10 shadow-2xl racing-glow">
            <div class="relative">
              <video class="w-full h-[420px] object-cover" src="/rfg/assets/img/videobackground.mp4"
                poster="/rfg/assets/img/background_video.png" autoplay muted loop playsinline preload="metadata">
                <!-- fontes alternativas (opcional) -->
                <source src="/rfg/assets/img/videobackground.webm" type="video/webm">
                <source src="/rfg/assets/img/videobackground.mp4" type="video/mp4">
                Seu navegador não suporta vídeo HTML5.
              </video>
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              <div class="absolute bottom-4 left-4">
                <div
                  class="inline-flex items-center bg-[#C9A300] text-black px-3 py-1 rounded-full text-xs font-semibold">
                  Acesso ao painel
                </div>
                <h2 class="mt-2 text-2xl font-bold">
                  Entre para acelerar sua temporada
                </h2>
                <p class="text-white/80 text-sm">Gerencie corridas, classificação e muito mais.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Card de Login -->
        <div class="flex justify-center">
          <div
            class="w-full max-w-md login-card bg-neutral-900/80 backdrop-blur rounded-2xl border border-[#C9A300]/20 p-6 md:p-8 shadow-2xl">
            <div class="text-center mb-6">
              <div class="inline-flex items-center racing-border pl-4">
                <h3 class="text-3xl font-extrabold uppercase tracking-wider">Login</h3>
              </div>
              <p class="text-white/70 mt-2">Acesse sua conta do campeonato</p>
            </div>

            <?php if (!empty($erro)): ?>
              <div class="mb-4 rounded-lg border border-red-400/40 bg-red-500/10 text-red-300 px-3 py-2 text-sm">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
              </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-5" autocomplete="off">
              <!-- E-mail (mantém name="usuario" para o PHP) -->
              <div>
                <label for="usuario"
                  class="block text-sm font-medium text-white/80 mb-1 uppercase tracking-wide">E-mail</label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#C9A300]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path
                        d="M20 4H4a2 2 0 0 0-2 2v1l10 6 10-6V6a2 2 0 0 0-2-2zm0 4.236-8 4.8-8-4.8V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.236z" />
                    </svg>
                  </span>
                  <input type="email" name="usuario" id="usuario" placeholder="admin@rfg.local"
                    value="<?= htmlspecialchars($_POST['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="input-racing w-full bg-neutral-800 border border-neutral-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#C9A300]/40 focus:border-[#C9A300]"
                    required>
                </div>
              </div>

              <!-- Senha (mantém name="senha" para o PHP) -->
              <div>
                <label for="senha"
                  class="block text-sm font-medium text-white/80 mb-1 uppercase tracking-wide">Senha</label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#C9A300]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path
                        d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm-3 8V6a3 3 0 0 1 6 0v3H9zm3 9a2 2 0 1 1 .001-4.001A2 2 0 0 1 12 18z" />
                    </svg>
                  </span>
                  <input type="password" name="senha" id="senha" placeholder="Sua senha"
                    class="input-racing w-full bg-neutral-800 border border-neutral-700 rounded-lg pl-10 pr-12 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#C9A300]/40 focus:border-[#C9A300]"
                    required>
                  <button type="button"
                    onclick="(function(){const i=document.getElementById('senha');i.type=i.type==='password'?'text':'password';})();"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#C9A300] hover:text-yellow-400 transition"
                    aria-label="Mostrar senha">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5A5 5 0 1 1 12 7a5 5 0 0 1 0 10z" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Lembrar + Esqueci -->
              <div class="flex items-center justify-between">
                <label class="flex items-center">
                  <input id="lembrar" type="checkbox"
                    class="rounded border-neutral-600 bg-neutral-800 text-[#C9A300] focus:ring-[#C9A300]">
                  <span class="ml-2 text-sm text-white/80">Lembrar-me</span>
                </label>
                <a href="#" class="text-sm text-[#C9A300] hover:text-yellow-400 transition">Esqueceu a senha?</a>
              </div>

              <!-- Botão -->
              <button type="submit"
                class="btn-racing w-full text-black font-bold py-3 px-4 rounded-lg uppercase tracking-wider text-lg
                     bg-gradient-to-br from-[#C9A300] to-[#B8920A] hover:from-[#B8920A] hover:to-[#A67F00]
                     transition will-change-transform focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A300]/50">
                Entrar na pista
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>


  <!-- Footer -->
  <?php require INC . 'layout_footer.php'; ?>

</body>

</html>