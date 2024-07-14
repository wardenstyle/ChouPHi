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


        // Executer les insertions des cles etrangeres du schéma ( a venir )
        // $sql = pl_get_alter_schema();
        // if (!is_array($sql)) {
        //     $sql = explode(';', $sql);
        //     $sql = array_filter($sql);
        // }

        echo 'Les tables ont été créées avec succès.';
        
    } catch (Exception $e) {
        // Gestion des exceptions
        error_log('Erreur dans ab_bd_install: ' . $e->getMessage());
        echo 'Une erreur est survenue lors de l\'installation de la base de données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
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

    $pdo = get_pdo_connection();
    if ($pdo === null) {
        return;
    }

    try {

        $tables = $ab_db->tables();
        // Suppression des tables dans l'ordre adéquat
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }

        // Supprimer le fichier installed.lock
        if (file_exists('installed.lock')) {
                unlink('installed.lock');
        }
        
        echo "Les tables ont été supprimées avec succès et le fichier de verrouillage a été supprimé.";

    } catch (Exception $e) {

        error_log('Erreur dans ab_db_uninstall: ' . $e->getMessage());
        echo 'Une erreur est survenue lors de la désinstallation de la base de données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
    }
}
