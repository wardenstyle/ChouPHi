<?php

// initialisation des champs
//create your fields tables here
$this->init_fields = array(

	'family' =>
		array(

			'id_family' => array('type'=>'number', 'autoinc' => true, 'key' => true),
			'name_family' => array('type'=>'string'),
		),

	'race' =>
		array(

			'id_race' => array('type'=>'number', 'autoinc' => true, 'key' => true),
			'name_race' => array('type' => 'string'),
            'id_family' => array('type' => 'number','key' => true),

		),
	'cat' => 
		array(

			'code_cat' => array('type'=>'number', 'autoinc' => true, 'key' => true), 
            'name_cat' => array('type' => 'string'),
            'height' => array('type' => 'string'),
            'color' => array('type' => 'string'),
			'id_race' => array('type' => 'number','key' => true),
		),
	
);