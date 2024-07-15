<?php

function ab_get_data() {

    global $ab_db;

    $tables = $ab_db->tables();

    //put inserts data here
    $insert_table = ["
    
    INSERT INTO {$tables['family']} (`id_family`, `name_family`) VALUES
    (1, 'vertébré')  
    ;

    INSERT INTO {$tables['race']} (`id_race`, `name_race`, `id_family`) VALUES
    (1, 'Persan', '1')  
    ;

    INSERT INTO {$tables['cat']} (`code_cat`, `name_cat`, `height`,`color`, `id_race`) VALUES
    (1, 'Choupi', '2.3','noir', '1')  
    ;
     
    "];

    return $insert_table;

}