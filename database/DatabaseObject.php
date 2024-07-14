<?php
/**
 * Entity manager (em) fait maison 
 */
// Définitions des constantes
if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}
if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}
if (!defined('OBJECT_K')) {
    define('OBJECT_K', 'OBJECT_K');
}

class DatabaseObject
{
    private $sql;

    public function __construct($sql)
    {
        $this->sql = $sql;
    }

    private function custom_get_lines(string $output)
    {
        global $ab_db;
        return $ab_db->get_results($this->sql, $output);
    }

    public function get_lines()
    {
        return $this->custom_get_lines(OBJECT_K);
    }

    public function get_lines_array()
    {
        return $this->custom_get_lines(ARRAY_A);
    }
}

/**
 * Classe abstraite qui servira dinterface generique pour la lecture des tables de la bdd
 */
abstract class DatabaseTableObject
{
    abstract protected static function get_table();
    // lecture
    private function custom_get_lines(string $output, $options = array())
    {
        global $ab_db;
        $table = static::get_table();

        if (isset($options['select_fields'])) {
            $data_fields = $options['select_fields'];
        } else {
            $data_fields = array_keys(array_filter($ab_db->fields()[$table], function ($var) {
                return $var['in_select'];
            }));
        }

        $whereClause = isset($options['where']) ? " WHERE " . $options['where'] : "";

        $sql = "SELECT " . implode(",", $data_fields) . " FROM $table" . $whereClause;
        return $ab_db->get_results($sql, $output);
    }

    public function get_lines($options = array())
    {
        return $this->custom_get_lines(OBJECT_K, $options);
    }

    public function get_lines_array($options = array())
    {
        return $this->custom_get_lines(ARRAY_A, $options);
    }

    public function get_key_name()
    {
        global $ab_db;
        $table = static::get_table();

        $key_fields = array_keys(array_filter($ab_db->fields()[$table], function ($var) {
            return $var['key'];
        }));

        if (!isset($key_fields[0])) {
            throw new Exception('Id not defined.');
        }

        return $key_fields[0];
    }

    public function custom_get_line($id, string $output)
    {
        global $ab_db;
        $table = static::get_table();

        $where = $this->get_key_name() . ' = ' . $id;
        $results = $this->get_lines(['where' => $where]);

        if (isset($results[$id])) {
            return ($output == ARRAY_A) ? get_object_vars($results[$id]) : $results[$id];
        }

        return null;
    }

    public function get_line($id)
    {
        return $this->custom_get_line($id, OBJECT_K);
    }

    public function get_line_array($id)
    {
        return $this->custom_get_line($id, ARRAY_A);
    }

    public function get_list_key_id($captions_fields = null, $separator = " - ")
    {
        global $ab_db;
        $table = static::get_table();
        $id_field = $this->get_key_name();

        if ($captions_fields) {
            $captions_fields = array_fill_keys($captions_fields, "");
        } else {
            $captions_fields = array_keys(array_filter($ab_db->fields()[$table], function ($var) {
                return $var['caption'];
            }));
        }

        $results = $this->get_lines([
            'select_fields' => array_merge([$id_field => $id_field], $captions_fields)
        ]);

        $list = [];
        foreach ($results as $result) {
            $captions = array_map(function($field) use ($result) {
                return $result->$field;
            }, $captions_fields);
            $list[$result->$id_field] = implode($separator, $captions);
        }

        return $list;
    }
}
/**
 * Créer les classes de vos objets ici exemple : Cat, Race, Familly
 */
class ObjectCat extends DatabaseTableObject
{
    protected static function get_table()
    {
        global $ab_db;
        return $ab_db->cat;
    }
}

class ObjectRace extends DatabaseTableObject
{
    protected static function get_table()
    {
        global $ab_db;
        return $ab_db->race;
    }
}

class ObjectFamilly extends DatabaseTableObject
{
    protected static function get_table()
    {
        global $ab_db;
        return $ab_db->family;
    }
}

?>