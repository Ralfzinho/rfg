<?php
// Define o caminho para o diretório de includes
define('INC', dirname(__DIR__) . '/includes/');
?>
<?php include(INC . 'layout_head.php'); ?>
<?php include(INC . 'layout_nav.php'); ?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Recuperação de Senha</h1>
        <p class="text-gray-600">
            Esta funcionalidade ainda não foi implementada.
        </p>
        <a href="/rfg/index.php" class="mt-6 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Voltar à Página Inicial</a>
    </div>
</main>

<?php include(INC . 'layout_footer.php'); ?>