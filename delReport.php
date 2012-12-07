<?php

/**
 *	delReport.php Suppression d'un enregistrement en fonction de $pub_datadate
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

/**
 * Fichier de fonctions du module allyRanking
 */
require_once("mod/allyranking/ARinclude.php");


$query  = "DELETE FROM ". TABLE_RANK_MEMBERS;
$query .= "  WHERE datadate = ".mysql_escape_string($pub_datadate);
$result = $db->sql_query($query,DEBUG);

redirection("index.php?action=allyranking&subaction=ranking");
?>