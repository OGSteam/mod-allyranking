<?php
/**
 *	allyRankingMain.php Page d'accès au module allyRanking
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

// définition de la variable si inexistante (notice)
if (!isset($pub_subaction)) $pub_subaction = 'ranking';

// Placer ici l'appel pour graphiques pour éviter problème de header
switch ($pub_subaction)
{
	//
	case "graphic" :
		require_once("mod/allyranking/graphic_curve_members.php");
	break;

	// 
	case "graphicglobal" :
		require_once("mod/allyranking/graphic_curve_global.php");
	break;
}

require_once("views/page_header.php");

switch($pub_subaction)
{

	//
	case "config" : 
		if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) 	
			require_once("mod/allyranking/config.php");
	break;

	//
	case "ranking" :
		require_once("mod/allyranking/allyRanking.php");
	break;
	
	//
	case "report" :
		//
		// TODO - Controler le droit d'ajout
		//
		require_once("mod/allyranking/postReport.php");
	break;
	
	//
	case "detail" : 
		require_once("mod/allyranking/detail.php");
	break;
	
	//
	case "datasend" :
		//
		// TODO - Controler le droit d'ajout
		//
		require_once("mod/allyranking/postReport.php");
	break;
	
	//
	case "dropranking" :
		require_once("mod/allyranking/delReport.php");
	break;
	
	
	// Cas particulier : Mise à jour du logiciel
	case "update" :
		require_once("mod/allyranking/update.php");
	break;
	
	//
	default :
		require_once("mod/allyranking/allyRanking.php");
	break;
}
?>


