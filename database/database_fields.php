<?php

// initialisation des champs
$this->init_fields = array(

	'rdv' =>
		array(
			'id' => array('type'=>'number', 'autoinc' => true, 'key' => true),
			'jour' => array('type'=>'date'),
			'id_medecin' => array('type' => 'number'),
            'nom' => array('type' => 'string'),
            'email' => array('type' => 'string'),
            'annulation' => array('type' => 'string'),
		),
	'user' =>
		array(
			'code' => array('type'=>'number', 'autoinc' => true, 'key' => true),
			'nom_user' => array('type' => 'string'),
            'email_user' => array('type' => 'string'),
            'mdp' => array('type' => 'string'),
			'type_compte' => array('type' => 'string'),
            'id_user' => array('type' => 'number'),
		),
	'medecin' => 
		array(
			'id_medecin' => array('type'=>'number', 'autoinc' => true, 'key' => true), 
            'nom_medecin' => array('type' => 'string'),
            'email_medecin' => array('type' => 'string'),
            'disponibilite' => array('type' => 'string'),
            'specialite' => array('type' => 'string','caption' => true),
            'image' => array('type' => 'string'),
            'code_user' => array('type' => 'number'),
		),
	'service_status' => 
		array(
			'id' => array('type'=>'number', 'autoinc' => true, 'key' => true), 
			'status' => array('type' => 'string', 'caption' => true),
		),
	
);