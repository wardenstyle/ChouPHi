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
        function reloadPage() { 
            setTimeout(function() {
                window.location.reload();
            }, 500);
            alert('choupi ajouté !')
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
                        
                        //1 - Récupérer les données directement avec la classe de l'objet 
                        $cats = (new ObjectCat())->get_lines(

                        array(
                            'select_fields' => array('name_cat'
                        )

                        ));

                        if($cats != null) {

                            foreach ($cats as $cat){
                                echo $cat->name_cat.'<br />';
                            }
                        }

                        global $ab_db;
                        $name=null; $race=null; $height=null; $color = null;
                        $tables = $ab_db->tables();
                        //2 - récupérer les données avec la requête sql par instanciation de la classe DatabaseObject
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
                        
                        if (isset($_POST['send']) && $_POST){
                            if(isset($_POST['name_cat'])) $name = $_POST['name_cat'];  
                            if(isset($_POST['height']))$height = $_POST['height'];
                            if(isset($_POST['color']))$color = $_POST['color'];
                            if(isset($_POST['race']))$race = $_POST['race'];
                            $data = [
                                'name_cat' => $name,
                                'height' => $height,
                                'color' => $color,
                                'id_race' => $race
                            ];
                        // 3- utilisez la classe de l'objet pour une insertion
                            $new_cat = (new ObjectCat())->add_line($data); // c'est ObjectCat qui est appelé pour une insertion
                        
                            if ($new_cat) {

                                echo "Insert successful. Last insert ID: " . $new_cat;
                                unset($_POST);                        
                                
                            } else {
                                echo "Insert failed.";
                            }                         
                        }
                        
                        echo "<h4>Ajoutez des chats (pour cet exemple juste le nom sera pris en compte)</h4>
                        
                        <form method='POST'>
                            <label for='nom'>Nom</label>
                            <input type='text' name='name_cat' placeholder='TOM'></input>

                            <label for='height'>Poids(kg)</label>
                            <select name='height' id='height'>
                                <option value='1.5'>1.5</option>
                                <option value='2'>2</option>
                                <option value='3.5'>3.5</option>
                            </select>

                            <label for='color'>Couleur</label>
                            <select name='color' id='color'>
                                <option value='Noir'>Noir</option>
                                <option value='Blanc'>Blanc</option>
                                <option value='Tachete'>Tâcheté</option>
                            </select>

                            <label for='race'>Race</label>
                            <select name='race' id='race'>
                                <option value='1'>Persan</option>
                            </select>

                            <input type='submit' name='send' value='submit' onclick='return reloadPage()'>
                        </form>
                        ";
                        
                    } catch (PDOException $e) {
                        error_log("Erreur lors de la restitution des données: " . $e->getMessage());
                        echo 'Erreur lors de la restitution des données.';
                    }
                    ?>                  
                        
                </div>

                <div><h4>HtmlChoupinator</h4></div>

                <?php 
                //var_dump($cats_race);
                if($cats_race) {
                    $html_gen =  new HtmlChoupinator();
                    $html_gen->add_title(array('title' => 'Tableau généré par Chouphi Framework', 'level' => '3'));
                    $html_gen->add_tab(array(
                        'columns' => array('CODE', 'Nom', 'Poids', 'Couleur','ID', 'Race','CODE_F', 'Famille'),
                        'values' => $cats_race            
	            ));

                $html_gen->generate();

                } else {
                    $html_gen =  new HtmlChoupinator();
                    $html_gen->add_title(array('title' => 'aucun chat trouvé', 'level' => '4'));
                    $html_gen->generate();
                }

                ?>

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

