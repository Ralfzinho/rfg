<?php
// admin/pilotos/salvar.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__, 2) . '/includes/funcoes.php';
auth_require_role(['admin', 'editor']);

global $pdo;

// Se acessarem via GET, só redireciona
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /admin/pilotos/listar.php');
  exit;
}

// Coleta e trata os dados do formulário
$nome      = trim($_POST['nome'] ?? '');
$numero    = (int)($_POST['numero'] ?? 0);
$pais      = trim($_POST['pais'] ?? '');
$equipe_id = (int)($_POST['equipe_id'] ?? 0);
$foto_url  = trim($_POST['foto_url'] ?? '');
$status    = trim($_POST['status'] ?? 'ativo'); // precisa da coluna status na tabela

// Validação básica
if ($nome === '' || $numero <= 0 || $equipe_id <= 0) {
  set_flash('erro', 'Informe ao menos Nome, Número e Equipe.');
  header('Location: /admin/pilotos/listar.php');
  exit;
}

try {
  // Se sua tabela AINDA não tiver a coluna "status",
  // use a versão sem o campo status:
  //
  // $sql = "INSERT INTO pilotos (nome, numero, pais, equipe_id, foto_url)
  //         VALUES (:nome, :numero, :pais, :equipe_id, :foto_url)";

  $sql = "INSERT INTO pilotos (nome, numero, pais, equipe_id, foto_url, status)
            VALUES (:nome, :numero, :pais, :equipe_id, :foto_url, :status)";

  $st = $pdo->prepare($sql);
  $st->execute([
    ':nome'      => $nome,
    ':numero'    => $numero,
    ':pais'      => $pais,
    ':equipe_id' => $equipe_id,
    ':foto_url'  => $foto_url,
    ':status'    => $status,
  ]);

  set_flash('ok', 'Piloto cadastrado com sucesso.');
} catch (PDOException $e) {
  // Log se quiser (arquivo / erro interno)
  // error_log($e->getMessage());
  set_flash('erro', 'Erro ao cadastrar piloto. Tente novamente.');
}

// Sempre volta para a listagem
header('Location: /admin/pilotos/listar.php');
exit;
