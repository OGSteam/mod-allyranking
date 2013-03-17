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

$mod_uninstall_name = "allyranking";

uninstall_mod($mod_uninstall_name);

$query = "DELETE FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'";
$db->sql_query($query);


?>
