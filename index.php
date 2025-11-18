<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

define('INC', __DIR__ . '/includes/');

// carrega as funções + conexão PDO
require_once INC . 'funcoes.php';

// torna o $pdo visível neste arquivo
global $pdo;
?>
<!doctype html>
<html lang="pt-br">
<?php require INC . 'layout_head.php'; ?>

<body class="bg-neutral-50 text-neutral-900 antialiased">

  <!-- Header -->
  <?php require INC . 'layout_nav.php'; ?>
  <main>
    <!-- Hero -->
    <section class="relative text-white ">
      <!-- overlay gradiente (dourado → preto) -->
      <div class="absolute inset-0 bg-gradient-to-r from-[#C9A300]/90 via-neutral-900/90 to-black"></div>

      <!-- conteúdo -->
      <div class="relative mx-auto max-w-7xl px-6 py-16">
        <div class="grid items-center gap-10 md:grid-cols-2">
          <!-- copy -->
          <div class="space-y-6">
            <span
              class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-wider ring-1 ring-white/20 backdrop-blur">
              <span class="h-1.5 w-1.5 rounded-full bg-[#C9A300]"></span>
              Temporada 2025
            </span>

            <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05]">
              Adrenalina em cada <span
                class="bg-clip-text text-transparent bg-gradient-to-r from-[#C9A300] to-yellow-300">curva</span>.
            </h1>

            <p class="text-white/80 text-lg">
              A Corrida para a Glória. Um campeonato. Vários rivais.
            </p>

            <div class="flex flex-wrap gap-3 pt-2">
              <a href="/rfg/pages/temporada.php"
                class="px-5 py-2.5 rounded-xl text-black bg-[#C9A300] hover:bg-[#E6C200] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A300]/60 transition">
                Ver temporada
              </a>
              <a href="/rfg/pages/calendario.php"
                class="px-5 py-2.5 rounded-xl border border-white/20 hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/40 transition">
                Calendário
              </a>
            </div>
          </div>

          <!-- vídeo -->
          <div class="rounded-3xl overflow-hidden ring-1 ring-white/10 shadow-2xl bg-white/5 backdrop-blur-md">
            <video class="block w-full aspect-video" poster="/rfg/assets/img/background_video.png" autoplay muted loop
              playsinline preload="metadata">
              <source src="/rfg/assets/img/videobackground.mp4" type="video/mp4" />
              Seu navegador não suporta vídeo HTML5.
            </video>
          </div>
        </div>
      </div>

      <!-- borda decorativa inferior -->
      <div
        class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent">
      </div>
    </section>

    <!-- ===== SECTION 2 – HISTÓRIA / GRID ===== -->
    <section id="historia" class="relative min-h-[101vh] py-20 md:py-28 bg-neutral-900 overflow-hidden">
      <div class="absolute inset-0 -z-0">
        <img src="assets/img/background_historia_1.jpg" class="w-full h-full object-cover" alt="Fundo veloz" />
      </div>
      <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/60 via-black/70 to-black/80"></div>

      <div class="relative z-20 max-w-7xl mx-auto px-6">
        <h2 class="text-center text-4xl md:text-6xl font-extrabold tracking-tight">
          <span class="text-white">RACE FOR</span> <span class="text-[#C9A300]">GLORY</span><span
            class="text-white">—</span> <span class="text-[#C9A300]">Nossa história</span>
        </h2>
        <p class="mt-6 text-center text-white/80 max-w-3xl mx-auto">
          Em 23 de novembro de 2021, um grupo de amigos se conectou no lobby de corridas. O que começou como diversão
          casual virou uma equipe unida por um mesmo sonho: competir e vencer.
        </p>

        <div class="mt-16 grid gap-10 md:gap-14 md:grid-cols-2">
          <!-- card -->
          <article class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg hover:bg-white/[0.07] transition">
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#C9A300] uppercase tracking-wide">O nome e o símbolo
            </h3>
            <p class="mt-4 text-white/90 leading-relaxed">
              O nome RACE FOR GLORY representa liderança, pioneirismo e excelência — sempre à frente. Nosso símbolo, o
              lobo, traduz a força da coletividade: como uma alcateia, somos mais fortes juntos.
            </p>
          </article>

          <article class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg hover:bg-white/[0.07] transition">
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#C9A300] uppercase tracking-wide">Trajetória de sucesso
            </h3>
            <p class="mt-4 text-white/90 leading-relaxed">
              Em poucos anos, nos consolidamos como uma das equipes mais respeitadas no automobilismo virtual,
              acumulando títulos e expandindo para novas plataformas e desafios.
            </p>
          </article>

          <article class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg hover:bg-white/[0.07] transition">
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#C9A300] uppercase tracking-wide">Além das pistas</h3>
            <p class="mt-4 text-white/90 leading-relaxed">
              Compartilhamos bastidores, evolução dos pilotos e nossa preparação estratégica. O trabalho em equipe é o
              que nos torna únicos.
            </p>
          </article>

          <article class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg hover:bg-white/[0.07] transition">
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#C9A300] uppercase tracking-wide">O futuro é veloz</h3>
            <p class="mt-4 text-white/90 leading-relaxed">
              Estamos sempre em movimento, buscando talentos, parcerias e conquistas. O próximo pódio é só o começo.
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== SECTION 3 – INSTAGRAM / CALL ===== -->
    <section id="instagram" class="relative py-20 md:py-24 bg-white text-black">
      <div class="max-w-7xl mx-auto px-6 grid items-center gap-12 lg:grid-cols-3">
        <!-- ESQUERDA -->
        <div class="order-2 lg:order-1 lg:text-left text-center">
          <p class="text-5xl md:text-6xl leading-none font-extrabold">
            SIGA A <span class="text-[#C9A300]">RACE FOR GLORY</span><br class="hidden lg:block"> NO INSTAGRAM
          </p>
          <a href="https://www.instagram.com/raceforgloryof" target="_blank"
            class="mt-6 inline-block rounded-lg px-4 py-2 bg-black text-white hover:bg-neutral-800 transition">
            @raceforgloryof
          </a>
          <p class="mt-2 text-black/70 font-semibold tracking-wide">#RACE FOR GLORY</p>
        </div>

        <!-- IMAGEM AO CENTRO -->
        <div class="order-1 lg:order-2 justify-self-center">
          <img src="/rfg/assets/img/mockup_instagram.png" alt="Mockup Instagram"
            class="w-[300px] md:w-[420px] rotate-[-8deg] rounded-3xl" loading="lazy" />
        </div>

        <!-- DIREITA -->
        <div class="order-3 lg:order-3 lg:text-right text-center">
          <p class="mt-8 text-xl md:text-2xl font-semibold">
            FIQUE POR DENTRO DE TODAS AS <span class="text-[#C9A300]">NOVIDADES</span>
          </p>
          <p class="mt-2 text-black/70">Novos posts, bastidores e clipes toda semana.</p>
        </div>
      </div>
    </section>
    <!-- ===== SECTION 4 – DESTAQUES / PODIUM ===== -->
    <?php
    global $pdo;

    // Busca top 3 pilotos por pontos
    $stmt = $pdo->query("
  SELECT 
    p.id,
    p.nome,
    p.pontos,
    p.pais,
    p.foto_url,
    e.nome      AS equipe_nome,
    e.foto_url  AS equipe_foto_url   
FROM pilotos p
LEFT JOIN equipes e ON e.id = p.equipe_id
WHERE p.status = 'ativo'
ORDER BY p.pontos DESC
LIMIT 3;
");
    $podium = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Quebra em 1º, 2º, 3º
    $first = $podium[0] ?? null;
    $second = $podium[1] ?? null;
    $third = $podium[2] ?? null;

    // Função pra separar nome e sobrenome
    function splitName($nome)
    {
      $parts = explode(' ', trim($nome));
      if (count($parts) <= 1) {
        return [$nome, ''];
      }
      $last = array_pop($parts);
      return [implode(' ', $parts), $last];
    }
    ?>
    <section class="py-12 bg-neutral-50">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="mb-8 text-center text-3xl md:text-4xl font-extrabold tracking-tight">
          Destaques
        </h2>

        <?php if ($first && $second && $third): ?>
          <div class="podium-container">
            <!-- ================= 2º LUGAR ================= -->
            <?php list($nome2, $sobrenome2) = splitName($second['nome']); ?>
            <div class="card card-second">
              <div class="position">2<span class="position-suffix">ND</span></div>

              <div class="driver-info">
                <h2 class="driver-name">
                  <?= htmlspecialchars($nome2) ?>
                  <?php if ($sobrenome2): ?>
                    <span class="last-name"><?= htmlspecialchars($sobrenome2) ?></span>
                  <?php endif; ?>
                </h2>

                <div class="team-row">
                  <span class="team-logo">
                    <img
                      src="<?= htmlspecialchars($second['equipe_foto_url'] ?? '/rfg/assets/img/team-placeholder.png') ?>"
                      alt="<?= htmlspecialchars($second['equipe_nome'] ?? 'Equipe') ?>" loading="lazy">
                  </span>
                </div>
              </div>

              <div class="driver-image">
                <img class="driver-photo"
                  src="<?= htmlspecialchars($second['foto_url'] ?: '/rfg/assets/img/piloto-placeholder.png') ?>"
                  alt="<?= htmlspecialchars($second['nome']) ?>">
              </div>

              <div class="points">
                <?= (int) $second['pontos'] ?> <span class="pts-label">PTS</span>
              </div>

              <div class="dotted-pattern"></div>
            </div>

            <!-- ================= 1º LUGAR ================= -->
            <?php list($nome1, $sobrenome1) = splitName($first['nome']); ?>
            <div class="card card-first">
              <div class="position">1<span class="position-suffix">ST</span></div>

              <div class="driver-info">
                <h2 class="driver-name">
                  <?= htmlspecialchars($nome1) ?>
                  <?php if ($sobrenome1): ?>
                    <span class="last-name"><?= htmlspecialchars($sobrenome1) ?></span>
                  <?php endif; ?>
                </h2>

                <div class="team-row">
                  <span class="team-logo">
                    <img
                      src="<?= htmlspecialchars($first['equipe_foto_url'] ?? '/rfg/assets/img/team-placeholder.png') ?>"
                      alt="<?= htmlspecialchars($first['equipe_nome'] ?? 'Equipe') ?>" loading="lazy">
                  </span>
                </div>
              </div>

              <div class="driver-image">
                <img class="driver-photo"
                  src="<?= htmlspecialchars($first['foto_url'] ?: '/rfg/assets/img/piloto-placeholder.png') ?>"
                  alt="<?= htmlspecialchars($first['nome']) ?>">
              </div>

              <div class="points">
                <?= (int) $first['pontos'] ?> <span class="pts-label">PTS</span>
              </div>

              <div class="dotted-pattern"></div>
            </div>

            <!-- ================= 3º LUGAR ================= -->
            <?php list($nome3, $sobrenome3) = splitName($third['nome']); ?>
            <div class="card card-third">
              <div class="position">3<span class="position-suffix">RD</span></div>

              <div class="driver-info">
                <h2 class="driver-name">
                  <?= htmlspecialchars($nome3) ?>
                  <?php if ($sobrenome3): ?>
                    <span class="last-name"><?= htmlspecialchars($sobrenome3) ?></span>
                  <?php endif; ?>
                </h2>

                <div class="team-row">
                  <span class="team-logo">
                    <img
                      src="<?= htmlspecialchars($third['equipe_foto_url'] ?? '/rfg/assets/img/team-placeholder.png') ?>"
                      alt="<?= htmlspecialchars($third['equipe_nome'] ?? 'Equipe') ?>" loading="lazy">
                  </span>
                </div>
              </div>

              <div class="driver-image">
                <img class="driver-photo"
                  src="<?= htmlspecialchars($third['foto_url'] ?: '/rfg/assets/img/piloto-placeholder.png') ?>"
                  alt="<?= htmlspecialchars($third['nome']) ?>">
              </div>

              <div class="points">
                <?= (int) $third['pontos'] ?> <span class="pts-label">PTS</span>
              </div>

              <div class="dotted-pattern"></div>
            </div>
          </div>
        <?php else: ?>
          <p class="text-center text-gray-500">
            Ainda não há pilotos suficientes para montar o pódio.
          </p>
        <?php endif; ?>
      </div>
    </section>


    <?php require INC . 'layout_footer.php'; ?>
  </main>
  <!-- Scripts -->
  <script src="/rfg/assets/JavaScript/noticias_card_tailwind.js"></script>
  <script src="/rfg/assets/JavaScript/classificacao_tailwind.js"></script>
</body>

</html>