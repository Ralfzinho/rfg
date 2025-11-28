<?php
// admin/usuarios/editar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';
auth_require_role(['admin']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Editar Usuário — RFG';
global $pdo;

$currentUser = auth_user();

// id do usuário a editar
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('erro', 'Usuário inválido.');
    header('Location: /admin/usuarios/listar.php');
    exit;
}

// ======================= POST: SALVAR =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $role   = $_POST['role']        ?? 'editor';
    $status = $_POST['status']      ?? 'ativo';

    $novaSenha     = $_POST['nova_senha']     ?? '';
    $confirmaSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '') {
        set_flash('erro', 'Informe Nome e E-mail.');
        header('Location: /admin/usuarios/editar.php?id=' . $id);
        exit;
    }

    // proteção: não deixar o próprio usuário se "matar" (tirar admin/ativo)
    $editingSelf = ((int)$currentUser['id'] === $id);
    if ($editingSelf && ($role !== 'admin' || $status !== 'ativo')) {
        set_flash('erro', 'Você não pode remover seu próprio acesso de administrador ou desativar a sua conta.');
        header('Location: /admin/usuarios/editar.php?id=' . $id);
        exit;
    }

    // se for alterar senha, valida
    $params = [
        ':name'   => $nome,
        ':email'  => $email,
        ':role'   => $role,
        ':status' => $status,
        ':id'     => $id,
    ];

    $set = "name = :name, email = :email, role = :role, status = :status";

    if ($novaSenha !== '' || $confirmaSenha !== '') {
        if ($novaSenha !== $confirmaSenha) {
            set_flash('erro', 'A nova senha e a confirmação não conferem.');
            header('Location: /admin/usuarios/editar.php?id=' . $id);
            exit;
        }
        if (strlen($novaSenha) < 4) {
            set_flash('erro', 'A nova senha deve ter pelo menos 4 caracteres.');
            header('Location: /admin/usuarios/editar.php?id=' . $id);
            exit;
        }

        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $set .= ", senha_hash = :senha_hash, password = :password";
        $params[':senha_hash'] = $hash;
        $params[':password']   = $hash; // mantém a coluna password em sincronia, se ainda for usada
    }

    $sql = "UPDATE usuarios SET $set WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute($params);

    set_flash('ok', 'Usuário atualizado com sucesso.');
    header('Location: /admin/usuarios/listar.php');
    exit;
}

// ======================= GET: CARREGAR DADOS =======================
$st = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$st->execute([$id]);
$usuario = $st->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    set_flash('erro', 'Usuário não encontrado.');
    header('Location: /admin/usuarios/listar.php');
    exit;
}

$ok   = get_flash('ok');
$erro = get_flash('erro');

$nome   = $usuario['name']       ?? '';
$email  = $usuario['email']      ?? '';
$role   = $usuario['role']       ?? 'editor';
$status = $usuario['status']     ?? 'ativo';
$dataCadastroRaw = $usuario['created_at'] ?? ($usuario['dt_cadastro'] ?? null);
$dataCadastro    = $dataCadastroRaw ? date('d/m/Y H:i', strtotime($dataCadastroRaw)) : '-';

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

        <main class="flex-1 mx-auto max-w-4xl px-4 py-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">
                        Editar <span class="text-yellow-600">Usuário</span>
                    </h1>
                    <p class="text-gray-600 text-sm">
                        Ajuste os dados do usuário. Deixe a senha em branco para não alterar.
                    </p>
                </div>
                <a href="/admin/usuarios/listar.php"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Voltar
                </a>
            </div>

            <?php if ($ok): ?>
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm shadow-sm">
                    <?= htmlspecialchars($ok) ?>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm shadow-sm">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border rounded-2xl shadow p-6 space-y-4">
                <div class="text-sm text-gray-500 mb-2">
                    <span class="font-semibold">ID:</span> <?= (int)$usuario['id'] ?> •
                    <span class="font-semibold">Cadastro:</span> <?= htmlspecialchars($dataCadastro) ?>
                </div>

                <form method="post" action="/admin/usuarios/editar.php?id=<?= (int)$usuario['id'] ?>" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Nome completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="nome"
                            required
                            value="<?= htmlspecialchars($nome) ?>"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                            name="email"
                            required
                            value="<?= htmlspecialchars($email) ?>"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Tipo
                            </label>
                            <select name="role"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
                                <option value="admin" <?= $role === 'admin'  ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Status
                            </label>
                            <select name="status"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="ativo" <?= $status === 'ativo'   ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                        <h2 class="text-sm font-semibold text-gray-800 mb-2">Alterar Senha</h2>
                        <p class="text-xs text-gray-500 mb-3">
                            Preencha os campos abaixo apenas se desejar definir uma nova senha.
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Nova senha
                                </label>
                                <input type="password"
                                    name="nova_senha"
                                    minlength="4"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Confirmar nova senha
                                </label>
                                <input type="password"
                                    name="confirmar_senha"
                                    minlength="4"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
                        <a href="/admin/usuarios/listar.php"
                            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <?php require INC . 'layout_footer.php'; ?>
</body>

</html>