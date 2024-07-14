<?php

function require_ab_db() {
	global $ab_db;

	require_once 'config/config.php'; // les constantes
	require_once 'Database.php'; // la base de donnees
	require_once 'database/DatabaseObject.php'; //entity manager
	require_once 'PDOconnect.php'; //PDO connect

	if ( isset( $ab_db ) ) {
		return;
	}

	$ab_db = new Database();
}
// A mettre dans init mais pas nécessaire car la partie est déjà initiée
require_ab_db();
