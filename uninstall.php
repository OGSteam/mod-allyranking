<?php

/**
 *	uninstall.php Fichier de desinstallation du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus 
 * 	@version 	1.0.0
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

global $db,$table_prefix;

/**
 * Fichier de fonctions du module allyRanking
 */
require_once("mod/allyranking/ARinclude.php");

//On vérifie que la table xtense_callbacks existe (Xtense2)
if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
  {
  // Si oui, on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $ally_id = $db->sql_fetch_row($result);
  $ally_id = $ally_id[0];
  // on fait du nettoyage
  $query = "DELETE FROM `".$table_prefix."xtense_callbacks"."` WHERE `mod_id`=".$ally_id;
  $db->sql_query($query);
  }

$mod_uninstall_name = "allyranking";
$mod_uninstall_table = TABLE_RANK_MEMBERS;
uninstall_mod($mod_uninstall_name,$mod_uninstall_table);

$query = "DELETE FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'";
$db->sql_query($query);


?>
