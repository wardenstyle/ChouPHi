<?php 

require_once 'config/config.php';
require_once 'database/init.php';
require_once 'database/database_upgrade.php'; //inclus les fonctions installation et de desinstallation

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Vérification du mot de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
    } else {
        $error = 'Mot de passe incorrect.';
    }
}

// Vérification de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: '. $_SERVER['PHP_SELF'] );
    exit;
}

// Vérifier si l'utilisateur est authentifié
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ) {

    if (isset($_POST['install'])) {

        // Exécuter la vérification et éventuellement l'installation
        check_and_run_install();

    } elseif (isset($_POST['uninstall'])) {

        ab_db_uninstall();

    } elseif (isset($_POST['unlock'])) {
        // Supprimer le fichier de verrouillage
        if (file_exists('installed.lock')) {
            unlink('installed.lock');
        echo 'le fichier de verrouillage a été supprimé.';
    }
    }
} else {
    //echo isset($error) ? $error : '';
}
?>

<!DOCTYPE html>
<html lang="en">
    <script type="text/javascript">
        function confirmUninstall() {
            return confirm('Êtes-vous sûr de vouloir désinstaller ? Cette action est irréversible.');
        }
        function confirmUnlock() {
            return confirm('Attention ! supprimer le fichier de vérouillage permettra une nouvelle installation dans laquelle vous perdrez toutes les données.');
        }
    </script>


<body>
        <div>
            <div>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ): ?>
                <div>
                    <form method="POST" >
                        <div class="form-group">
                            <div>
                                <div>
                                    <h5>Base de données</h5>
                                    <p>Installer ou désinstaller la base de données du site.</p>
                                    <button type="submit" name="install" >Installer</button>
                                    <button type="submit" name="uninstall"  onclick="return confirmUninstall();">Désinstaller</button>
                                    <button type="submit" name="unlock" onclick="return confirmUnlock();">Déverouiller</button>

                                </div>
                            </div>
                        </div>
                    </form>
                    <p>Restitution des données </p>

                    <p>/\_/\ <br />
                    ( o.o )<br />
                    > ^ <<br />
                    </p>
                    <h4>Restitution des chats</h4>
                    <?php

                    try {
                        require_once 'database/database_load.php';
                        
                        // Récupérer les données directement avec la classe de l'objet 
                        $cats = (new ObjectCat())->get_lines(

                        array(
                            'select_fields' => array('name_cat'
                        )

                        ));

                        if($cats != null) {

                            foreach ($cats as $cat){
                                echo $cat->name_cat;
                            }
                        }


                        global $ab_db;
                        $tables = $ab_db->tables();
                        // récupérer les données avec la requête sql par instanciation de la classe DatabaseObject
                        $sql = "SELECT * FROM {$tables['cat']} AS c
                        INNER JOIN {$tables['race']} AS r ON c.id_race = r.id_race
                        INNER JOIN {$tables['family']} AS f ON r.id_family = f.id_family;";
                        $cats_race= (new DatabaseObject($sql))->get_lines();

                        if($cats_race !=null) {

                            foreach ($cats_race as $cat){
                                echo 
                            "
                            <table style='border 1px solid'>
                                <tr>
                                    <th>Nom</th>
                                    <th>Poids(kg)</th>
                                    <th>Couleur</th>
                                    <th>Race</th>
                                    <th>Famille</th>
                                </tr>
                                <tr>
                                    <td>$cat->name_cat</td>
                                    <td>$cat->height</td>
                                    <td>$cat->color</td>
                                    <td>$cat->name_race</td>
                                    <td>$cat->name_family</td>
                                </tr>

                            </table>
                            
                            ";
                            }
                            

                        }else{
                            echo 'aucun chat en base';
                        }

                    } catch (PDOException $e) {
                        error_log("Erreur lors de la restitution des données: " . $e->getMessage());
                        echo 'Erreur lors de la restitution des données.';
                      }
                    ?>                  
                        
                </div>

                <a href="?logout=true">Quitter</a>

            <?php else : ?>
                <h1>Accès restreint</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="password">Mot de passe administrateur:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <button type="submit" name="submit">Se connecter</button>
                </form>
                <?php if(isset($error)) echo '<p>' . $error . '</p>'; ?>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
