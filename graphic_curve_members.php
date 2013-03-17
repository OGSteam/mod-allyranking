<?php
/**
 *	graphic_curve_members.php Génération du graphique d'évolution d'un joueur
 *	@package	allyRanking
 *	@author		Jibus sur la base du travail de ben.12
 */

/**
 * Fichier de fonctions du module allyRanking
 */
 
 if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}


if(!isset($pub_member)) die("Nom de joueur Inconnu dans graphic_curve_member.php");


if (!check_var($pub_member, "Text")) {
	exit;
}

$player = $pub_member;


//on recupère le classement
$ranking_1 = array();
$dates = array();

$request = "select player, datadate, points";
$request .= " from ".TABLE_RANK_PLAYER_POINTS;
$request .= " where (player = '".mysql_escape_string($player)."' or player = '".mysql_escape_string($player_comp)."')";
//$request .= " and datadate between ".$start." and ".$end;
$request .= " order by datadate asc";

$result = $db->sql_query($request, false, false);
while (list($player_name, $datadate, $score) = $db->sql_fetch_row($result)) {
	switch(strtolower($player_name)) {
		case strtolower($player) :
		//$ranking_1[] = $score;
		$ranking_1[] = "[" .($datadate * 1000). ", " . $score . "]";
		//$dates[] = $datadate;
		break;
		case strtolower($player_comp) :
		$ranking_2[$datadate] = $score;
		$dates2[] = $datadate;
	}
}

// On créé le graphique

//create_multi_curve($titre, $sous_titre, $data, $names, $conteneur)

$names = array('Points');
$data = array('Points' => $ranking_1);
global $zoom;
$zoom = "true";

$curve = create_multi_curve('Evolution '.$player,'Points',$data,$names,'evol_member');
echo $curve;





?>