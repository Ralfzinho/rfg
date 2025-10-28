<?php
// Inclui a conexão com o banco de dados
include('../includes/db.php');
?>

<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Classificação das Equipes</h1>

    <!-- Classificação das Equipes -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <th class="py-3 px-4">Posição</th>
                    <th class="py-3 px-4">Equipe</th>
                    <th class="py-3 px-4">Pontos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para a classificação das equipes
                $sql = "SELECT e.nome as equipe, SUM(r.pontos) as pontos
                        FROM Resultados r
                        JOIN Equipes e ON e.id = r.equipe_id
                        GROUP BY e.id
                        ORDER BY pontos DESC";

                // Preparando a consulta usando PDO
                $stmt = $pdo->query($sql);

                $pos = 1;
                while ($row = $stmt->fetch()) {
                    echo "
                    <tr class='border-b'>
                        <td class='py-3 px-4'>".$pos++."</td>
                        <td class='py-3 px-4'>".$row['equipe']."</td>
                        <td class='py-3 px-4'>".$row['pontos']."</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/layout_footer.php'); ?>
