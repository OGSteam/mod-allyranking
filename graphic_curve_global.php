<?php
/**
 *	graphic_curve_global.php Affiche toutes les courbes des membres (y compris wings)
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */
	
require_once("mod/allyranking/ARinclude.php");

	
// Récupérer la liste des membres dont on veut voir les vals sur le graph...

$in_list=" IN (";
for ($nb=0;$nb<count($mblist);$nb++)
{
	$in_list .= "'".$db->sql_escape_string($mblist[$nb])."',"; 		
}	
$in_list = substr($in_list,0,strlen($in_list)-1).") ";
//dbg($in_list);



if ($mblist=="")
	$in_list = "=' IN (NULL) '";
	
	
	// Récupérer toutes les données des joueurs :
	// Nom, date, points. (alliance a conserver pour correspondance couleur page detail.php
//	dbg($pub_list);
	$query = "SELECT datadate, player, points FROM ".TABLE_RANK_PLAYER_POINTS." a where player ".$in_list." ORDER BY player,datadate";

	$result = $db->sql_query($query,false,true);
	if ($db->sql_numrows($result) != 0 )
	{
		$min_points = NULL;
		$max_points = NULL;
		while (list($datadate,$player,$points)=$db->sql_fetch_row($result))
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

				//$clsmt_prepare = isset($clsmt[$memb][$dat]) ? $clsmt[$memb][$dat] : NULL;
				if(isset($clsmt[$memb][$dat]))
					$ranking[$j][] = "[" .($dat * 1000). ", " . $clsmt[$memb][$dat] . "]";
			}
		}

	// On créé le graphique

	foreach($members as $key => $member){
		$data[$member] = $ranking[$key]; // Valeur des séries
		$names[] = $member; // Nom des séries
	}

	global $zoom;
	$zoom = "true";

	$curve = create_multi_curve('Comparaison','Points',$data,$names,'curve_evol_details');
	echo $curve;
	
	}
?>


