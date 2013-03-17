<?php

/**
 *	install.php Fichier d'installation du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus 
 */

	if (!defined('IN_SPYOGAME')) {
		die("Hacking attempt");
	}
	global $db;
	
	/**
	 * Fichier de fonctions du module allyRanking
	 */

	
//Insertion du champs pour la declaration du module dans OGSpy
	$is_ok = false;
	$mod_folder = "allyranking";
	$is_ok = install_mod($mod_folder);
if ($is_ok == true)
	{
	
		//On vérifie que la table xtense_callbacks existe (Xtense2)
		if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
			{
				// Si oui, on récupère le n° d'id du mod
				$query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
				$result = $db->sql_query($query);
				$ally_id = $db->sql_fetch_row($result);
				$ally_id = $ally_id[0];
				// on fait du nettoyage au cas ou
				$query = "DELETE FROM `".$table_prefix."xtense_callbacks"."` WHERE `mod_id`=".$ally_id;
				$db->sql_query($query);
				// Insert les données pour récuperer les informations de la page Alliance
				$query = "INSERT INTO ".$table_prefix."xtense_callbacks"." ( `mod_id` , `function` , `type` )
				VALUES ( '".$ally_id."', 'ally_list', 'ally_list')";
				$db->sql_query($query);
			}
	}
else
  {
  echo  "<script>alert('Désolé, un problème a eu lieu pendant l'installation, corrigez les problèmes survenue et réessayez.');</script>";
  }
?>
