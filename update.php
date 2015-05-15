<?php
/**
 *	update.php Fichier de mise à jour du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus 
 *	@version	1.0.0
 */

	if (!defined('IN_SPYOGAME')) {
	    die("Hacking attempt");
	}

	global $db;

// Avant tout, faire le ménage pour que les requetes s'executent correctement !
if( $db->sql_numrows( $db->sql_query("SHOW TABLES LIKE '".$table_prefix.TABLE_MOD."'"))){

	$db->sql_query("DELETE FROM ".TABLE_MOD." WHERE title='allyranking'",DEBUG,true);
	$db->sql_query("DROP TABLE IF EXISTS ".TABLE_RANK_MEMBERS,DEBUG,true);
	$db->sql_query("DELETE FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'",DEBUG,true);
}
	// Mettre à jour la version
	$mod_folder = "allyranking";
	$mod_name = "allyranking";
	update_mod($mod_folder,$mod_name);
	
 //On vérifie que la table xtense_callbacks existe (Xtense2)
if( $db->sql_numrows( $db->sql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
  {
  // Si oui, on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $ally_id = $db->sql_fetch_row($result);
  $ally_id = $ally_id[0];
  // on fait du nettoyage au cas ou
  $query = "DELETE FROM `".$table_prefix."xtense_callbacks"."` WHERE `mod_id`=".$ally_id;
  $db->sql_query($query);

  }

	?>