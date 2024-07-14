<?php 

/**
 * la fonction
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database'); //define your database name
define('DB_USER', 'root');
define('DB_PASS', '');

function get_pdo_connection() {
	
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
    $options = [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];

    try {

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    } catch (PDOException $e) {

        error_log('Erreur de connexion à la base de données: ' . $e->getMessage());
		header('Location: erreur.php');
		exit('Erreur : ' . $e->getMessage());
    }

    return $pdo;
}

/**
 * Cette fonction permettra de contrôler si la base existe ou non
 */
function get_pdo_connection_1() {

    $dsn = 'mysql:host=localhost'; // Serveur MySQL (sans le nom de la base de données)
    $username = 'root';
    $password = '';

    try {
        // Connexion au serveur MySQL
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        error_log('Erreur de connexion à la base de données: ' . $e->getMessage());
        echo 'Une erreur est survenue lors de la connexion à la base de données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
        return null;
    }
}
?>