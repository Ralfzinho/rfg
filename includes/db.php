<<<<<<< HEAD
<?php
// Conexão com MySQL (XAMPP: usuário root sem senha por padrão)
$host = 'localhost';
$db   = 'race_for_glory'; // troque pelo nome do seu banco
$user = 'root';
$pass = '';
$charset = 'utf8mb4';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

=======
<?php
// Conexão com MySQL (XAMPP: usuário root sem senha por padrão)
$host = 'localhost';
$db   = 'race_for_glory'; // troque pelo nome do seu banco
$user = 'root';
$pass = '';
$charset = 'utf8mb4';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

>>>>>>> 69bb93605bbc7806118eb2d75e23a16c5d146d8b
?>