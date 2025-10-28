<?php
include('../includes/db.php');
include('../includes/funcoes.php');

$id = $_GET['id'] ?? 0;
?>
<?php include('../includes/layout_head.php'); ?>
<?php include('../includes/layout_nav.php'); ?>

<div class="container mt-5">
    <?php
    $sql = "SELECT c.nome_gp, circ.NOME as circuito, c.data, c.voltas_previstas, c.status 
            FROM Corrida c
            JOIN Circuito circ ON circ.ID = c.circuito_id
            WHERE c.id = $id";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $corrida = $res->fetch_assoc();
        echo "<h1>".$corrida['nome_gp']."</h1>";
        echo "<p><b>Circuito:</b> ".$corrida['circuito']."</p>";
        echo "<p><b>Data:</b> ".date("d/m/Y", strtotime($corrida['data']))."</p>";
        echo "<p><b>Voltas Previstas:</b> ".$corrida['voltas_previstas']."</p>";
        echo "<p><b>Status:</b> ".$corrida['status']."</p>";
    }

    echo "<h2 class='mt-4'>Resultados</h2>";

    $sqlRes = "SELECT r.posicao, p.nome as piloto, e.nome as equipe, r.pontos, r.volta_mais_rapida
               FROM Resultados r
               JOIN Pilotos p ON p.id = r.piloto_id
               JOIN Equipes e ON e.id = r.equipe_id
               WHERE r.corrida_id = $id
               ORDER BY r.posicao ASC";
    $resRes = $conn->query($sqlRes);

    if ($resRes->num_rows > 0) {
        echo "<table class='table table-striped'><tr><th>Posição</th><th>Piloto</th><th>Equipe</th><th>Pontos</th><th>Volta + Rápida</th></tr>";
        while ($row = $resRes->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['posicao']."</td>
                    <td>".$row['piloto']."</td>
                    <td>".$row['equipe']."</td>
                    <td>".$row['pontos']."</td>
                    <td>".$row['volta_mais_rapida']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum resultado cadastrado ainda.</p>";
    }
    ?>
</div>

<?php include('../includes/layout_footer.php'); ?>
