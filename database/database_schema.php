<?php

/**
 * Fonction pour obtenir le schéma de la base de données
 */
function ab_get_db_schema() {
    global $ab_db;

    $charset_collate = get_charset_collate();

    // Create your Tables here
    $ab_tables = [
       
        "CREATE TABLE $ab_db->family (
            id_family INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_family VARCHAR(50) NOT NULL

        ) $charset_collate",

        "CREATE TABLE $ab_db->race (
            id_race INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_race VARCHAR(50) NOT NULL,
            id_family INT NOT NULL
        ) $charset_collate",

        "CREATE TABLE $ab_db->cat (
            code_cat INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name_cat VARCHAR(50),
            height VARCHAR(50),
            color VARCHAR(50) NOT NULL,
            id_race INT NOT NULL
        ) $charset_collate",
        
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
// function ab_get_db_alter_schema() {

//     return $ab_alter_tables;

// }