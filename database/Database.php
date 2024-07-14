<?php 

class Database {

/**
* fonction get_results
* retourne le résultat des tables (similaire à la fonction Wordpress)
*/
function get_results($query,$output) {

    global $ab_db;

    $pdo = get_pdo_connection();
    if ($pdo === null) {
        return;
    }

    try {

        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);

    } catch (Exception $e) {

        error_log('Erreur dans database: ' . $e->getMessage());
        echo 'Une erreur est survenue lors de la lecture des données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
    }
}

public $prefix = '';

// Attention à l'ordre des tables à cause des contraintes, classer dans l'ordre pour la suppression des tables
// Prendre l'ordre inverser de la création des contraintes
    
public $tables = array(	

    // put your tables name 
    'family',		
    'race',
    'cat',

);

// Les champs
private $init_fields;
private $fields;

// Les noms des tables
public $cat;
public $race; 
public $family; 

// Ajoutez le préfixe sur vos tables ici 
public function __construct( ) {

    $this->set_prefix("ch_");
    //$this->set_prefix("");
    require_once( 'database_fields.php' );
}

/**
 * met à jour les tables en fonction du nouveau prefixe
 */
public function set_prefix($prefix) {
        if (preg_match('|[^a-z0-9_]|i', $prefix)) {
            throw new InvalidArgumentException('Le préfixe doit être une chaîne de caractères valide.');
        }

        $old_prefix = $this->prefix;
        $this->prefix = $prefix;

        foreach ($this->tables() as $table => $prefixed_table) {
            $this->$table = $prefixed_table;
        }

        // Réinitialiser les champs
        $this->fields = null;
        
        return $old_prefix;
}

public function tables( $with_prefix = true ) {
        $tables = $this->tables;

        if ( $with_prefix ) {
            $prefix   = $this->prefix;
            foreach ( $tables as $k => $table ) {
                $tables[ $table ] = $prefix . $table;
                unset( $tables[ $k ] );
            }
        }

        return $tables;
}

public function fields(){
        // Calculer que si c'est non initialisé
        if (!isset($this->fields)) {
            $tables = $this->tables();
            $default_values = 
                array(
                    'autoinc' => false,
                    'key' => false,
                    'type' => 'string',
                    'mandatory' => true,
                    'in_select' => true,
                    'caption' => false,
                    'update' => true,
                    );
                    
            foreach($this->init_fields as $t => $cols)
            {
                foreach($cols as $col => $values) {
                    // On utilise le nom de la vrai table comme key
                    // On merge la valeur défaut
                    $this->fields[$tables[$t]][$col] = array_merge($default_values, $values);
                }
            }
        }
        return $this->fields;
    }

}