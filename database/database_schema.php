<?php

/**
 * Fonction pour obtenir le schéma de la base de données
 */
function ab_get_db_schema() {

    global $ab_db;

    $charset_collate = get_charset_collate();
    $tables = $ab_db->tables();

    // Create your Tables here
    $ab_tables = [

        "CREATE TABLE {$tables['family']} (
            id_family INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_family VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB;

        CREATE TABLE {$tables['race']} (
            id_race INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_race VARCHAR(50) NOT NULL,
            id_family INT NOT NULL,
            FOREIGN KEY (id_family) REFERENCES {$tables['family']}(id_family)
        )ENGINE=InnoDB;

        CREATE TABLE {$tables['cat']} (
            code_cat INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_cat VARCHAR(50),
            height VARCHAR(50),
            color VARCHAR(50) NOT NULL,
            id_race INT NOT NULL,
            FOREIGN KEY (id_race) REFERENCES {$tables['race']}(id_race)
        ) ENGINE=InnoDB;",

    ];

    return $ab_tables;
}

/**
 * Fonction pour obtenir le charset collate
 */
function get_charset_collate() {
    global $charset, $collate;
    $charset_collate = "DEFAULT CHARACTER SET $charset COLLATE $collate";
    return $charset_collate;
}


/** Les ALTER TABLES */
// add your FOREIGNE KEY here 
function ab_get_db_alter_schema() {

    global $ab_db;
    $tables = $ab_db->tables();

    $ab_alter_tables = ["

    ALTER TABLE {$tables['race']}
        ADD CONSTRAINT fk_race_family
        FOREIGN KEY (id_family) REFERENCES {$tables['family']}(id_family)

    "];

    return $ab_alter_tables;

}
