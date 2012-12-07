<?php
 /**
*   _xtense.php - fichier d'interface avec Xtense2
*   @package allyRanking
*   @author ericc
*   @link http://www.ogsteam.fr
*   @version : 1.0.0
*   created	: 25/02/2008
*   modified	:
**/
// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

if(class_exists("Callback")){
	class allyRanking_Callback extends Callback {
		public $version = '2.3.9';
		public function ally_list($rapport){
			global $io;
			if(ally_list($rapport))
				return Io::SUCCESS;
			else
				return Io::ERROR;
		}
		public function getCallbacks() {
			return array(
					array(
							'function' => 'ally_list',
							'type' => 'ally_list'
					)
			);
	   }
	}
}


// Version minimum de Xtense2
$xtense_version="2.3.0";

function ally_list($rapport){
	global $db, $table_prefix, $user_data;
	define("TABLE_RANK_MEMBERS",$table_prefix."rank_members");
	
	// récupère le tag d'alliance
	$alliance = $rapport['tag'];
	// définition du time slot (0h00,8h00,16h00)
	$hour = date("H");
	if ($hour < 8)
		$hour = 0;
	elseif (($hour >= 8) && ($hour < 16))
		$hour = 8;
	else
		$hour = 16;
	// recrée le timestamp UNIX
	$datadate = mktime($hour, 0, 0, date("n"), date("j"), date("Y"));
	// vérifie dans la DB si une entrée existe
	$query = "SELECT * FROM `".TABLE_RANK_MEMBERS."` WHERE `datadate`='".$datadate."' and `ally`='".$alliance."' LIMIT 1";
	$result = $db->sql_query($query);
	// si on trouve 1 ligne alors on sort, les données existent déjà dans la DB
	if ($db->sql_numrows($result) == 1)
		return FALSE;
	// On boucle dans la liste des résultats et on insert dans la DB
	for ($i=0; $i < count($rapport['list']); $i++){
	  $query = "INSERT INTO `".TABLE_RANK_MEMBERS."` VALUES (".$datadate.",'".$rapport['list'][$i]['pseudo']."',".($rapport['list'][$i]['points']).",'".$alliance."',".$user_data['user_id'].")";
	  $db->sql_query($query);
	}
	return TRUE;
}

?>