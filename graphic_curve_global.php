<?php
/**
 *	graphic_curve_global.php Affiche toutes les courbes des membres (y compris wings)
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */
	

	// Fonctions d'affichage pour les labels des axes
	function setDate($value) 
	{
		global $data_date;
		return $data_date[$value];
	}
	
	function setY($value) 
	{
		global $bot;
		$value+=$bot;
		if($value >= 1000) return round($value);
		elseif($value >= 100) return round($value,1);
		else return round($value,2);
	}


	/**
	 * Fichier de fonctions du module allyRanking
	 */
	require_once("mod/allyranking/ARinclude.php");

	/**
	* Fichier des fonctions de la lib artichow
	*/
	
	// Récupérer la liste des membres dont on veut voir les vals sur le graph...
	$mblist = explode(",",$pub_mblist);	
	if (isset($pub_mblist))
	{
		$in_list=" IN (";
		for ($nb=0;$nb<count($mblist);$nb++)
		{
			$in_list .= "'".mysql_real_escape_string($mblist[$nb])."',"; 		
		}	
		$in_list = substr($in_list,0,strlen($in_list)-1).") ";
		//dbg($in_list);
	}	
	
	if ($pub_mblist=="")
		$in_list = "=' IN (NULL) '";
	
	$allies = get_allies();

	$case_clause="";	

	if ($allies != false)
	{			
		//---------------------------------------------
		// Préparer une clause where pour les alliances

		$where_allies = " WHERE (ally='".mysql_real_escape_string($allies[0])."' ";
		for ($i=1;$i<count($allies);$i++)
			$where_allies .= " OR ally='".mysql_real_escape_string($allies[$i])."' "; 
		$where_allies .=") ";

		// Construction de la clause CASE WHEN d'ordonnancement des allies
		$case_clause=" ,CASE a.ally";
		for($i=0;$i<count($allies);$i++)
		{
			$case_clause .= " WHEN '".mysql_real_escape_string($allies[$i])."' THEN ".$i." ";
		}
		$case_clause .= " END case_val ";

	}
	
	
	// Récupérer toutes les données des joueurs :
	// Nom, date, points. (alliance a conserver pour correspondance couleur page detail.php
//	dbg($pub_list);
	$query = "SELECT datadate, player, points".$case_clause." FROM ".TABLE_RANK_PLAYER_POINTS." a where player ".$in_list." ORDER BY case_val,player,datadate";

	$result = $db->sql_query($query,false,true);
	if ($db->sql_numrows($result) != 0 )
	{
		$min_points = NULL;
		$max_points = NULL;
		while (list($datadate,$player,$points,$ally)=$db->sql_fetch_row($result))
		{
			// Récupérer toutes les dates distinctes,ordonnées
			// echo ( (((int) (($datadate*4)/(3600*8)))/4)*3600*8 ."<BR>");
			$datadate = (((int) (($datadate*4)/(3600*8)))/4)*3600*8;
			$dates[]   = $datadate;
			$members[] = $player;
			$clsmt[$player][$datadate] = $points;

		}

		// A la sortie de boucle, virer les doublons dans la table des dates et des membres
		// et réindicer les tableaux à partir de zéro
		$dates 	 = array_slice(array_unique ($dates),0);
		$members = array_slice(array_unique ($members),0);

		// déterminer le nombre d'intervalles de 8h entre les dates min et max, 
		// puis l'arondir à l'unité au dessus
		$nbdates = ceil((max($dates) - min($dates)) / (60*60*8))+1;

		// Créer un tableau des intervalles de dates entre datemin et datemax
		for ($i=0; $i<$nbdates; $i++) 
		{
			$curdate = min($dates) + (60*60*8*$i);  
			$merge[$i] = $curdate;
		}

		$nbdates   = sizeof($merge);
		$nbmembers = sizeof($members);


		// Construire les datas pour chaque membre	
		// Pour chaque date, pour chaque joueur, trouver le nombre de points
		for ($i=0 ; $i < $nbdates ; $i++)
		{
			for ($j=0 ; $j < $nbmembers ; $j++)
			{
				$dat  = $merge[$i];
				$memb = $members[$j];
				$clsmt[$memb][$dat] = isset($clsmt[$memb][$dat]) ? $clsmt[$memb][$dat] : NULL;
			}
		}

print_r($clsmt);
?>


