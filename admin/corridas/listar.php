<?php
include_once("../../includes/layout_head.php");
include_once("../../includes/layout_nav.php");
?>

<div class="container mt-4">
    <h2 class="mb-4">Listar Corridas</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome da Corrida</th>
                <th>Data</th>
                <th>Local</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aqui entra loop das corridas -->
            <tr>
                <td>1</td>
                <td>Grande Prêmio do Brasil</td>
                <td>2025-05-10</td>
                <td>Interlagos</td>
                <td>
                    <a href="editar.php?id=1" class="btn btn-sm btn-primary">Editar</a>
                    <a href="resultados.php?id=1" class="btn btn-sm btn-success">Resultados</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
include_once("../../includes/layout_footer.php");
?>
