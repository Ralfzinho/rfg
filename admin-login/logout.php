<?php
// admin-login/logout.php
session_start();
require_once dirname(__DIR__) . '/includes/funcoes.php';
auth_logout(); // destrói a sessão e redireciona

?>