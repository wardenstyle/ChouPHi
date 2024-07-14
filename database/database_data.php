<?php

function ab_get_data() {

    //mettez les insertions de données ici
    global $ab_db;

    $insert_table = ["
    
    INSERT INTO $ab_db->family (`id_family`, `name_family`) VALUES
    (1, 'vertébré')  
    ;

    INSERT INTO $ab_db->race (`id_race`, `name_race`, `id_family`) VALUES
    (1, 'Persan', '1')  
    ;

    INSERT INTO $ab_db->cat (`code_cat`, `name_cat`, `height`,`color`, `id_race`) VALUES
    (1, 'Choupi', '2.3','noir', '1')  
    ;
     
    "];
    return $insert_table;

}