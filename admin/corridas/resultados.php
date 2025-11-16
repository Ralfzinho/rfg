<?php
// admin/corridas/resultados.php
require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin', 'editor']);

define('INC', dirname(__DIR__, 2) . '/includes/');
$title = 'Resultados da Corrida — RFG';
global $pdo;

// Busca corridas e pilotos para os dropdowns
$corridas = $pdo->query("SELECT id, nome_gp, data FROM corridas ORDER BY data DESC")->fetchAll();
$pilotos = $pdo->query("SELECT id, nome FROM pilotos ORDER BY nome")->fetchAll();

$corrida_id_selecionada = (int)($_GET['corrida_id'] ?? 0);
$resultados_existentes = [];

if ($corrida_id_selecionada > 0) {
    $st = $pdo->prepare("SELECT * FROM resultados WHERE corrida_id = ? ORDER BY posicao ASC");
    $st->execute([$corrida_id_selecionada]);
    $resultados_existentes = $st->fetchAll();
}

$ok = get_flash('ok');
$erro = get_flash('erro');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $corrida_id = (int)($_POST['corrida_id'] ?? 0);
    $posicoes   = $_POST['posicao'] ?? [];
    $piloto_ids = $_POST['piloto_id'] ?? [];
    $pontos     = $_POST['pontos'] ?? [];

    if ($corrida_id <= 0) {
        $erro = 'Selecione uma corrida válida.';
    } else {
        $pdo->beginTransaction();
        try {
            // Limpa resultados antigos para esta corrida
            $pdo->prepare("DELETE FROM resultados WHERE corrida_id = ?")->execute([$corrida_id]);

            // Insere os novos resultados
            $sql_insert = "INSERT INTO resultados (corrida_id, piloto_id, equipe_id, posicao, pontos)
                           SELECT :corrida_id, :piloto_id, p.equipe_id, :posicao, :pontos
                           FROM pilotos p WHERE p.id = :piloto_id_ref";

            $stmt_insert = $pdo->prepare($sql_insert);

            for ($i = 0; $i < count($piloto_ids); $i++) {
                $piloto_id = (int)$piloto_ids[$i];
                $posicao   = (int)$posicoes[$i];
                $ponto     = (int)$pontos[$i];

                if ($piloto_id > 0 && $posicao > 0) {
                    $stmt_insert->execute([
                        ':corrida_id'    => $corrida_id,
                        ':piloto_id'     => $piloto_id,
                        ':posicao'       => $posicao,
                        ':pontos'        => $ponto,
                        ':piloto_id_ref' => $piloto_id
                    ]);
                }
            }
            $pdo->commit();
            set_flash('ok', 'Resultados salvos com sucesso.');
            header("Location: /rfg/admin/corridas/resultados.php?corrida_id=" . $corrida_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('erro', 'Ocorreu um erro ao salvar os resultados: ' . $e->getMessage());
            header("Location: /rfg/admin/corridas/resultados.php?corrida_id=" . $corrida_id);
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <?php require INC . 'layout_head.php'; ?>
</head>

<body class="bg-neutral-50 text-neutral-900">
    <?php require INC . 'layout_nav.php'; ?>

    <div class="flex">
        <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>

        <main class="flex-1 mx-auto max-w-6xl px-4 py-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Lançar Resultados da Corrida</h1>
                <a href="/rfg/admin/corridas/listar.php" class="px-4 py-2 rounded border">Voltar</a>
            </div>

            <?php if ($ok): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
                    <?= htmlspecialchars($ok) ?>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border rounded-2xl p-6 shadow space-y-4">
                <form method="get" class="flex items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium">Selecione a Corrida</label>
                        <select name="corrida_id"
                            class="mt-1 w-full rounded-lg border px-3 py-2"
                            required
                            onchange="this.form.submit()">
                            <option value="">Selecione...</option>
                            <?php foreach ($corridas as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($c['id'] === $corrida_id_selecionada) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome_gp'] . ' — ' . date("d/m/Y", strtotime($c['data']))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <?php if ($corrida_id_selecionada > 0): ?>
                    <form method="post">
                        <input type="hidden" name="corrida_id" value="<?= $corrida_id_selecionada ?>">
                        <div class="overflow-x-auto mt-6">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b">
                                        <th class="py-2 pr-3">Posição</th>
                                        <th class="py-2 pr-3">Piloto</th>
                                        <th class="py-2 pr-3">Pontos</th>
                                    </tr>
                                </thead>
                                <tbody id="linhas-resultados">
                                    <?php
                                    $linhas_a_exibir = max(20, count($resultados_existentes));
                                    for ($i = 0; $i < $linhas_a_exibir; $i++):
                                        $res = $resultados_existentes[$i] ?? null;
                                    ?>
                                        <tr class="border-b">
                                            <td class="py-1 pr-3 w-24">
                                                <input name="posicao[]"
                                                    type="number"
                                                    class="w-full rounded border px-2 py-1"
                                                    value="<?= htmlspecialchars($res['posicao'] ?? ($i + 1)) ?>">
                                            </td>
                                            <td class="py-1 pr-3">
                                                <select name="piloto_id[]" class="w-full rounded border px-2 py-1">
                                                    <option value="">Selecione o piloto</option>
                                                    <?php foreach ($pilotos as $p): ?>
                                                        <option value="<?= $p['id'] ?>" <?= ($res && $res['piloto_id'] == $p['id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($p['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="py-1 pr-3 w-24">
                                                <input name="pontos[]"
                                                    type="number"
                                                    class="w-full rounded border px-2 py-1"
                                                    value="<?= htmlspecialchars($res['pontos'] ?? '0') ?>">
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button class="px-5 py-2 rounded bg-primary text-white hover:bg-red-700">
                                Salvar Resultados
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php require INC . 'layout_footer.php'; ?>
</body>

</html>