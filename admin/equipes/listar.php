<?php
include_once("../../includes/layout_head.php");
include_once("../../includes/layout_nav.php");
?>

<div class="container mt-4">
    <h2 class="mb-4">Listar Equipes</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>País</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop de equipes -->
            <tr>
                <td>1</td>
                <td>Ferrari</td>
                <td>Itália</td>
                <td>
                    <a href="editar.php?id=1" class="btn btn-sm btn-primary">Editar</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
include_once("../../includes/layout_footer.php");
?>
