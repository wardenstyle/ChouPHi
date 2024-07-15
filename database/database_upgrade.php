<?php

function ab_bd_install() {

    global $ab_db;

    $pdo = get_pdo_connection();
    if ($pdo === null) {

        return; //si la connexion echoue
    }
    
    try {

        // Inclure les fichiers requis

        require_once('database_schema.php');
        require_once('database_data.php');

        // Exécuter le schéma de la base de données
        $sql = ab_get_db_schema();
        dbDelta($pdo, $sql);  // Adapter dbDelta pour utiliser PDO

        //Executer les insertions de donnees

        $sql = ab_get_data();
        dbDelta($pdo, $sql);


        // Executer les insertions des cles etrangeres du schéma
        $sql = ab_get_db_alter_schema();
        if (!is_array($sql)) {
            $sql = explode(';', $sql);
            $sql = array_filter($sql);
        }

        echo 'Choupi ! Les tables ont été créées avec succès.';
        
    } catch (Exception $e) {
        // Gestion des exceptions
        error_log('Erreur dans ab_bd_install: ' . $e->getMessage());
        echo 'Miouuu ! Une erreur est survenue lors de l\'installation de la base de données.Essayez de dévérouiller la base. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
    }
}

// Fonction pour exécuter dbDelta avec PDO
function dbDelta($pdo, $sql) {
    if (!is_array($sql)) {
        $sql = explode(';', $sql);
        $sql = array_filter($sql);
    }

    foreach ($sql as $qry) {
        try {
            $pdo->exec($qry);
        } catch (Exception $e) {
            error_log('Erreur dans dbDelta: ' . $e->getMessage());
        }
    }
}

/**
 * desinstaller la base
 */
function ab_db_uninstall() {

    global $ab_db;

    // Obtenir les noms des tables avec préfixe
    $tables = $ab_db->tables();

    // Obtenir la connexion PDO
    $pdo = get_pdo_connection();
    if ($pdo === null) {
        return;
    }

    try {
        // Vérifier que $ab_db est correctement initialisé
        if (!$ab_db instanceof Database) {
            throw new Exception('L\'objet $ab_db n\'est pas une instance de la classe Database.');
        }

        // Désactiver les vérifications de clés étrangères
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // Suppression des tables dans l'ordre adéquat
        foreach (array_reverse($tables) as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }

        // Réactiver les vérifications de clés étrangères
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        // Supprimer le fichier installed.lock
        if (file_exists('installed.lock')) {
                unlink('installed.lock');
        }

        echo "Choupi ! Les tables ont été supprimées avec succès et le fichier de verrouillage a été supprimé.";
    } catch (Exception $e) {
        // Réactiver les vérifications de clés étrangères en cas d'erreur
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        error_log('Erreur dans ab_db_uninstall: ' . $e->getMessage());
        echo 'Miaouuu ! Une erreur est survenue lors de la désinstallation de la base de données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
    }
}
