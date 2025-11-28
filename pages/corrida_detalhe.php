<?php
include('../includes/db.php');
include('../includes/funcoes.php');

// Safely get the ID from the URL, casting to an integer.
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if the ID is invalid.
if ($id <= 0) {
    header("Location: /pages/corrida.php"); // Redirect to the main races page
    exit;
}
?>
<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<div class="container mx-auto px-4 py-8">
    <?php
    // Prepare and execute the first query for the race details.
    $sql_corrida = "SELECT c.nome_gp, circ.NOME as circuito, c.data, c.voltas_previstas, c.status
                    FROM Corrida c
                    JOIN Circuito circ ON circ.ID = c.circuito_id
                    WHERE c.id = :id";
    $stmt_corrida = $pdo->prepare($sql_corrida);
    $stmt_corrida->execute([':id' => $id]);
    $corrida = $stmt_corrida->fetch();

    if ($corrida) {
        echo "<h1 class='text-3xl font-bold mb-4'>" . htmlspecialchars($corrida['nome_gp']) . "</h1>";
        echo "<p><b>Circuito:</b> " . htmlspecialchars($corrida['circuito']) . "</p>";
        echo "<p><b>Data:</b> " . date("d/m/Y", strtotime($corrida['data'])) . "</p>";
        echo "<p><b>Voltas Previstas:</b> " . htmlspecialchars((string)$corrida['voltas_previstas']) . "</p>";
        echo "<p><b>Status:</b> " . htmlspecialchars($corrida['status']) . "</p>";
    } else {
        echo "<p class='text-red-500'>Corrida não encontrada.</p>";
    }

    echo "<h2 class='text-2xl font-bold mt-8 mb-4'>Resultados</h2>";

    // Prepare and execute the second query for the race results.
    $sql_resultados = "SELECT r.posicao, p.nome as piloto, e.nome as equipe, r.pontos, r.volta_mais_rapida
                       FROM Resultados r
                       JOIN Pilotos p ON p.id = r.piloto_id
                       JOIN Equipes e ON e.id = r.equipe_id
                       WHERE r.corrida_id = :id
                       ORDER BY r.posicao ASC";
    $stmt_resultados = $pdo->prepare($sql_resultados);
    $stmt_resultados->execute([':id' => $id]);
    $resultados = $stmt_resultados->fetchAll();

    if ($resultados) {
        echo "<div class='overflow-x-auto bg-white shadow rounded-lg'>";
        echo "<table class='min-w-full table-auto'>";
        echo "<thead><tr class='bg-gray-100'><th class='py-3 px-4'>Posição</th><th class='py-3 px-4'>Piloto</th><th class='py-3 px-4'>Equipe</th><th class='py-3 px-4'>Pontos</th><th class='py-3 px-4'>Volta + Rápida</th></tr></thead>";
        echo "<tbody>";
        foreach ($resultados as $row) {
            echo "<tr class='border-b'>
                    <td class='py-3 px-4'>" . htmlspecialchars((string)$row['posicao']) . "</td>
                    <td class='py-3 px-4'>" . htmlspecialchars($row['piloto']) . "</td>
                    <td class='py-3 px-4'>" . htmlspecialchars($row['equipe']) . "</td>
                    <td class='py-3 px-4'>" . htmlspecialchars((string)$row['pontos']) . "</td>
                    <td class='py-3 px-4'>" . ($row['volta_mais_rapida'] ? 'Sim' : 'Não') . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<p>Nenhum resultado cadastrado ainda.</p>";
    }
    ?>
</div>

<?php include('../includes/layout_footer.php'); ?>