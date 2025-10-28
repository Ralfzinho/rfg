<?php
// Inclui a conexão com o banco de dados
include('../includes/db.php');
?>

<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Lista de Pilotos da Temporada</h1>

    <!-- Lista de Pilotos -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full table-auto text-sm">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <th class="py-3 px-4">Posição</th>
                    <th class="py-3 px-4">Piloto</th>
                    <th class="py-3 px-4">Equipe</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para a lista de pilotos da temporada
                $sql = "SELECT p.nome as piloto, e.nome as equipe, p.status
                        FROM Pilotos p
                        JOIN Equipes e ON e.id = p.equipe_id
                        WHERE p.status = 'ativo' -- Filtra apenas pilotos ativos
                        ORDER BY p.nome ASC";

                // Preparando a consulta usando PDO
                $stmt = $pdo->query($sql);

                $pos = 1;
                while ($row = $stmt->fetch()) {
                    echo "
                    <tr class='border-b'>
                        <td class='py-3 px-4'>".$pos++."</td>
                        <td class='py-3 px-4'>".$row['piloto']."</td>
                        <td class='py-3 px-4'>".$row['equipe']."</td>
                        <td class='py-3 px-4'>".$row['status']."</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/layout_footer.php'); ?>
