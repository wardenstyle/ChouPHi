<?php

class Database {

    /**
     * fonction get_results
     * retourne le résultat des tables (similaire à la fonction Wordpress)
     */
    function get_results($query, $output) {
        global $ab_db;

        $pdo = get_pdo_connection();
        if ($pdo === null) {
            return;
        }

        try {
            $stmt = $pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log('Erreur dans Database:get_results ' . $e->getMessage());
            echo 'Miaouuu ! Une erreur est survenue lors de la lecture des données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
        }
    }

    /**
     * fonction insert
     * permet l'insertion des données dans les tables (similaire à la fonction Wordpress)
     */
    function insert($table, $filter_data)
    {
        $pdo = get_pdo_connection();
        if ($pdo === null) {
            return false;
        }
    
        try {
            // On récupère toutes les clés présentes
            $columns = array_keys($filter_data);
            // On construit les préfixes :table1, :table2, :table3
            $prefix = array_map(function($col) { return ":$col"; }, $columns);
    
            // On construit la requête: INSERT INTO your_table (name, email, age) VALUES (:name, :email, :age)
            $sql = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $prefix) . ")";
            $statement = $pdo->prepare($sql);
    
            // On insert
            foreach ($filter_data as $key => &$value) {
                $statement->bindParam(":$key", $value);
            }
//            var_dump($sql);
//           var_dump($statement);
            // On exécute
            if ($statement->execute()) {
                return $pdo->lastInsertId();
            } else {
                return false;
            }

         } catch (Exception $e) {
             error_log('Erreur dans Database:insert() ' . $e->getMessage());
             echo 'Miaouuu ! Une erreur est survenue lors de l\'insertion des données. Veuillez vérifier les journaux d\'erreurs pour plus de détails.';
         }
    }

    public $prefix = '';
    public $tables;
    // Stocker les valeurs des tables dans un tableau associatif
    private $tableData = [];

    // Les champs
    private $init_fields;
    private $fields;

    public function __construct() {
        $this->loadConfig(); // charger mon fichier de config
        $this->set_prefix("choupi_"); // Ajoutez le préfixe sur vos tables 
        //$this->set_prefix(""); // si vous ne voulez pas prefixer vos tables
        require_once('database_fields.php');
    }
    /**
     * fonction pour récuperer et initialiser les tables
     */
    private function loadConfig() {
        // Lire le fichier de configuration
        $config = include('config_tables.php');

        // Initialiser les tables à partir de la configuration
        $this->tables = $config['tables'];

        // Initialiser les données des tables dans le tableau associatif
        foreach ($this->tables as $table) {
            $this->tableData[$table] = null;
        }
    }

    // Méthodes pour accéder aux données des tables
    public function getTableData($tableName) {
        return $this->tableData[$tableName] ?? null; // évite undefined index
    }

    public function setTableData($tableName, $data) {
        if (array_key_exists($tableName, $this->tableData)) {
            $this->tableData[$tableName] = $data;
        } else {
            throw new Exception("Table $tableName does not exist.");
        }
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

        foreach ($this->tables as $table) {
            $this->tableData[$table] = $prefix . $table;
        }

        // Réinitialiser les champs
        $this->fields = null;

        return $old_prefix;
    }

    public function tables($with_prefix = true) {
        $tables = $this->tables;

        if ($with_prefix) {
            $prefixed_tables = [];
            foreach ($tables as $table) {
                $prefixed_tables[$table] = $this->prefix . $table;
            }
            return $prefixed_tables;
        }

        return $tables;
    }

    public function fields() {
        // Calculer que si c'est non initialisé
        if (!isset($this->fields)) {
            $tables = $this->tables();
            $default_values = [
                'autoinc' => false,
                'key' => false,
                'type' => 'string',
                'mandatory' => true,
                'in_select' => true,
                'caption' => false,
                'update' => true,
            ];

            foreach ($this->init_fields as $t => $cols) {
                foreach ($cols as $col => $values) {
                    // On utilise le nom de la vraie table comme key
                    // On merge la valeur défaut
                    $this->fields[$tables[$t]][$col] = array_merge($default_values, $values);
                }
            }
        }
        return $this->fields;
    }
}