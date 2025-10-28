<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RACE FOR GLORY - Página Inicial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
  <link href="./assets/css/style.css" rel="stylesheet" />
</head>

<body>
  <header class="bg-dark text-white py-3">
    <nav class="navbar navbar-expand-lg navbar-dark container">
      <a class="navbar-brand fw-bold fs-4" href="#">RACE FOR GLORY</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav gap-2">
          <li class="nav-item">
            <a class="nav-link text-white" href="../index.php">Início</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="#">Calendário</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="#">Temporada</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="/rfg/pages/sobre_nos.php">Contato</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="/rfg/pages/conta.php">Login</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Hero Section -->
  <section class="bg-dark text-white text-center py-5 hero">
    <div class="container">
      <h1 class="display-3 fw-bold">Velocidade. Emoção. Competição.</h1>
      <p class="lead">Acompanhe cada curva da temporada RACE FOR GLORY 2025</p>
      <a href="#temporadas" class="btn btn-danger mt-4 px-5 py-2">Explorar Temporadas</a>
    </div>
  </section>

  <!-- Destaques -->

  <!-- Grid de Categorias -->
  <section id="temporadas" class="py-5 bg-white text-center">
    <div class="container">
      <h2 class="fw-bold mb-4 text-center">Explore o Universo RACE FOR GLORY</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 p-4 bg-dark text-white text-center">
            <h4>Temporadas</h4>
            <p>Histórico completo de todas as temporadas anteriores.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 p-4 bg-dark text-white text-center">
            <h4>Equipes</h4>
            <p>Veja estatísticas e formações de todas as escuderias.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 p-4 bg-dark text-white text-center">
            <h4>Pilotos</h4>
            <p>Conheça os competidores e seus desempenhos.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Vídeo ou Destaque da Comunidade -->
  <section class="py-5 bg-secondary text-white text-center">
    <div class="container">
      <h2 class="mb-4">Uma comunidade movida por paixão</h2>
      <p class="mb-4">Confira como criamos experiências únicas em cada etapa. Registre-se como piloto ou torcedor!</p>
      <div class="ratio ratio-16x9">
        <iframe src="https://www.youtube.com/embed/QyVZ9ZTZqLs" title="Vídeo destaque" allowfullscreen></iframe>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="mb-4 fw-bold text-center">Dúvidas Frequentes</h2>
      <div class="accordion" id="faq">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
              Como posso me cadastrar como piloto?
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faq">
            <div class="accordion-body">
              Basta acessar a página “login” no menu e clicar me "cadastrar" e preencher o formulário de inscrição com seus dados e equipe desejada.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
              As corridas são simuladas ou jogadas ao vivo?
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faq">
            <div class="accordion-body">
              As corridas são disputadas ao vivo em simuladores de F1 com regras adaptadas e calendário oficial da liga.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Rodapé -->
  <footer class="bg-dark text-white text-center py-4">
    <div class="container">
      <p class="mb-2">Junte-se à comunidade RACE FOR GLORY</p>
      <div class="d-flex justify-content-center gap-3">
        <a href="#" class="text-white text-decoration-none">Instagram</a>
        <a href="#" class="text-white text-decoration-none">Twitter</a>
        <a href="#" class="text-white text-decoration-none">YouTube</a>
      </div>
      <p class="mt-3 small">© 2025 RACE FOR GLORY - Todos os direitos reservados</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>