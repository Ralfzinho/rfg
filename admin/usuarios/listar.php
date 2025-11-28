<?php
// admin/usuarios/listar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/funcoes.php';

// só admin pode mexer em usuários
auth_require_role(['admin']);

define('INC', __DIR__ . '/../../includes/');
$title = 'Usuários — RFG';
global $pdo;

$currentUser = auth_user();

// ========== EXCLUSÃO ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del'])) {
    $id = (int)$_POST['del'];

    if ($id === (int)$currentUser['id']) {
        set_flash('erro', 'Você não pode excluir o próprio usuário logado.');
    } else {
        $st = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $st->execute([$id]);
        set_flash('ok', 'Usuário removido com sucesso.');
    }

    header('Location: /admin/usuarios/listar.php');
    exit;
}

// ========== CADASTRO ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    // VEM DO FORM: name="nome"
    $nome   = trim($_POST['nome']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $senha  = $_POST['senha']      ?? '';
    $role   = $_POST['role']       ?? 'editor';
    $status = $_POST['status']     ?? 'ativo';

    if ($nome === '' || $email === '' || $senha === '') {
        set_flash('erro', 'Informe Nome, E-mail e Senha.');
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // AQUI usamos a coluna `name` do banco
        $sql = "INSERT INTO usuarios (`name`, email, senha_hash, role, status)
                VALUES (:name, :email, :senha_hash, :role, :status)";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':name'       => $nome,       // valor do form
            ':email'      => $email,
            ':senha_hash' => $senhaHash,
            ':role'       => $role,
            ':status'     => $status,
        ]);

        set_flash('ok', 'Usuário cadastrado com sucesso.');
    }

    header('Location: /admin/usuarios/listar.php');
    exit;
}

// ========== BUSCA USUÁRIOS ==========
// ordena pela coluna `name`
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY `name`")->fetchAll(PDO::FETCH_ASSOC);

// mensagens flash
$ok   = get_flash('ok');
$erro = get_flash('erro');

// helpers
function user_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

function user_avatar_color(int $index): string
{
    $colors = [
        'from-yellow-500 to-amber-600',
        'from-blue-500 to-indigo-600',
        'from-red-500 to-pink-600',
        'from-green-500 to-emerald-600',
        'from-purple-500 to-violet-600',
        'from-cyan-500 to-teal-600',
    ];
    return $colors[$index % count($colors)];
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
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <div class="racing-border pl-4 mb-4">
                        <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
                            Gerenciar <span class="text-yellow-600">Usuários</span>
                        </h2>
                    </div>
                    <p class="text-gray-600">Cadastro e gerenciamento de usuários do sistema</p>
                </div>
                <button onclick="showModal('user-modal')"
                    class="btn-primary text-white font-semibold py-3 px-6 rounded-xl">
                    + Novo Usuário
                </button>
            </div>

            <!-- Flash messages -->
            <?php if ($ok): ?>
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm shadow-sm">
                    <?= htmlspecialchars($ok) ?>
                </div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm shadow-sm">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="admin-card rounded-xl overflow-hidden racing-glow">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    Usuário
                                </th>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    E-mail
                                </th>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    Cadastro
                                </th>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-left text-racing-black font-racing font-bold uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-300">
                                        Nenhum usuário cadastrado.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $idx => $u):
                                    // AQUI usamos a coluna `name` que veio do banco
                                    $nome   = $u['name'] ?? '';
                                    $iniciais  = user_initials($nome);
                                    $grad      = user_avatar_color($idx);
                                    $email     = $u['email'] ?? '';
                                    $username  = $u['usuario'] ?? '';
                                    if ($username === '' && $email !== '') {
                                        $username = strtok($email, '@');
                                    }
                                    $usernameDisplay = $username ? '@' . $username : '';

                                    $role  = $u['role'] ?? 'user';
                                    $roleLabel = $role === 'admin'
                                        ? 'Admin'
                                        : ($role === 'editor' ? 'Editor' : 'Usuário');
                                    $roleClass = $role === 'admin'
                                        ? 'bg-red-600'
                                        : 'bg-blue-600';

                                    $status = strtolower($u['status'] ?? 'ativo');
                                    $statusClass = $status === 'ativo' ? 'status-active' : 'status-inactive';

                                    $rawDate = $u['created_at'] ?? ($u['dt_cadastro'] ?? null);
                                    $cadastro = $rawDate ? date('d/m/Y', strtotime($rawDate)) : '-';

                                    $isSelf = ((int)$u['id'] === (int)$currentUser['id']);
                                ?>
                                    <tr class="table-row">
                                        <!-- Usuário -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br <?= $grad ?> flex items-center justify-center">
                                                    <span class="text-black font-bold text-sm"><?= htmlspecialchars($iniciais) ?></span>
                                                </div>
                                                <div>
                                                    <p class="text-black font-medium">
                                                        <?= htmlspecialchars($nome) ?>
                                                    </p>
                                                    <?php if ($usernameDisplay): ?>
                                                        <p class="text-black-400 text-sm">
                                                            <?= htmlspecialchars($usernameDisplay) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- E-mail -->
                                        <td class="px-6 py-4 text-black">
                                            <?= htmlspecialchars($email) ?>
                                        </td>

                                        <!-- Tipo -->
                                        <td class="px-6 py-4">
                                            <span class="<?= $roleClass ?> text-white text-xs px-2 py-1 rounded-full">
                                                <?= htmlspecialchars($roleLabel) ?>
                                            </span>
                                        </td>

                                        <!-- Cadastro -->
                                        <td class="px-6 py-4 text-black">
                                            <?= htmlspecialchars($cadastro) ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            <span class="<?= $statusClass ?> text-black text-xs px-2 py-1 rounded-full">
                                                <?= htmlspecialchars(ucfirst($status)) ?>
                                            </span>
                                        </td>

                                        <!-- Ações -->
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <a href="/admin/usuarios/editar.php?id=<?= (int)$u['id'] ?>"
                                                    class="text-blue-400 hover:text-blue-300"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                    </svg>
                                                </a>

                                                <?php if (!$isSelf): ?>
                                                    <form method="post" action="/admin/usuarios/listar.php"
                                                        class="inline-block"
                                                        onsubmit="return confirm('Tem certeza que deseja remover este usuário?')">
                                                        <input type="hidden" name="del" value="<?= (int)$u['id'] ?>">
                                                        <button type="submit"
                                                            class="text-red-400 hover:text-red-300"
                                                            title="Excluir">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Novo Usuário -->
    <div id="user-modal" class="modal hidden fixed inset-0 bg-white bg-opacity-60 items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900">Cadastrar Novo Usuário</h3>
                <button onclick="hideModal('user-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="post" action="/admin/usuarios/listar.php" class="p-6 space-y-4">
                <input type="hidden" name="cadastrar" value="1">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Nome completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="Ex: Admin Master">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        E-mail <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="admin@racingforglory.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Senha <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="senha" required minlength="4"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="Defina uma senha">
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Tipo
                        </label>
                        <select name="role"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Status
                        </label>
                        <select name="status"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
                    <button type="button" onclick="hideModal('user-modal')"
                        class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold hover:from-yellow-600 hover:to-yellow-700 shadow-lg">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php require INC . 'layout_footer.php'; ?>

    <script>
        window.showModal = function(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        window.hideModal = function(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                hideModal(e.target.id);
            }
        });

        document.querySelectorAll('.modal > div').forEach(function(content) {
            content.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>

</body>

</html>