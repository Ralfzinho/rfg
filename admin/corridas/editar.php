<?php
include_once("../../includes/layout_head.php");
include_once("../../includes/layout_nav.php");
?>

<div class="container mt-4">
    <h2 class="mb-4">Editar Corrida</h2>

    <form action="editar_process.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?? ''; ?>">

        <div class="mb-3">
            <label class="form-label">Nome da Corrida</label>
            <input type="text" name="nome" class="form-control" value="Grande Prêmio do Brasil">
        </div>

        <div class="mb-3">
            <label class="form-label">Data</label>
            <input type="date" name="data" class="form-control" value="2025-05-10">
        </div>

        <div class="mb-3">
            <label class="form-label">Local</label>
            <input type="text" name="local" class="form-control" value="Interlagos">
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>

<?php
include_once("../../includes/layout_footer.php");
?>
