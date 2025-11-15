<?php
// Define o caminho para o diretório de includes
define('INC', dirname(__DIR__) . '/includes/');
?>
<?php include(INC . 'layout_head.php'); ?>
<?php include(INC . 'layout_nav.php'); ?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Sobre Nós</h1>
        <p class="text-gray-600">
            Esta página está em construção. Em breve, você encontrará mais informações sobre o campeonato Race for Glory.
        </p>
    </div>
</main>

<?php include(INC . 'layout_footer.php'); ?>