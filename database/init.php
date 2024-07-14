<?php

/**
 * Initialisation de la base de donnée et création des tables
 */
require_once ('config.php');
require_once CHOUPHI_DB; //fonction PDO
require_once 'database_load.php'; // Chargement de la base

$charset = 'utf8mb4';
$collate = 'utf8mb4_unicode_ci';

function check_and_run_install() {
    // Vérifier si le fichier de verrouillage existe
    if (file_exists('installed.lock')) {
        return; // Installation déjà effectuée
    }

    // Connexion à la base de données
    $pdo = get_pdo_connection_1();
    if ($pdo === null) {
        die('Erreur de connexion à la base de données.');
    }

    try {
        // Créer la base de données si elle n'existe pas
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `your_database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (Exception $e) {
            error_log('Erreur lors de la création de la base de données: ' . $e->getMessage());
            echo 'init.php : erreur lors de la création de la base de données. Il est possible que la base de données existe déjà';
            throw $e;
        }

        // Se reconnecter à la nouvelle base de données
        $pdo->exec("USE `your_database`");

        // Liste des tables à vérifier
        $tables = ['cat', 'race', 'family'];
        $tables_exist = true;

        foreach ($tables as $table) {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() == 0) {
                // Si une table manque, sortir de la boucle
                $tables_exist = false;
                break;
            }
        }

        if (!$tables_exist) {
            // Lancer l'installation si des tables manquent
            require_once 'database_upgrade.php'; // Contient la fonction ab_bd_install() et de connexion à la bdd
            ab_bd_install();

            // Créer un fichier pour indiquer que l'installation est terminée
            file_put_contents('installed.lock', 'Installation terminée');
            echo 'installation terminée';
        }

    } catch (Exception $e) {
        error_log('Erreur lors de la vérification des tables: ' . $e->getMessage());
        echo 'init.php : Une erreur est survenue. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
    }
}
