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
	require_once("library/artichow/LinePlot.class.php");  
	// define("DEBUG",false);	
	
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
	$query = "SELECT datadate, player, points".$case_clause." FROM ".TABLE_RANK_MEMBERS." a where player ".$in_list." ORDER BY case_val,player,datadate";
	//echo $query;
	//dbg($query);
	//die();
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

			// Recherche des extremes en points pour borner l'axe des ordonnées
			if ($min_points == NULL)
				$min_points = $points;
			if ($max_points == NULL)
				$max_points = $points;
			if ($min_points > $points)
				$min_points = $points;
			if ($max_points < $points)
				$max_points = $points;
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

		// Construire l'axe horizontal des dates
		$j = 0;

		for ($i=0; $i<$nbdates; $i++) 
		{
			$datadate = min($dates) + (60*60*8*$i);
			if( $dates[$j] > ($datadate - (60*60*2)) && $dates[$j] < ($datadate + (60*60*2)) ) 
			{
				$datadate = $dates[$j];
				$j++;
			}
			$data_date[] = strftime("%d %b", min($dates) + (60*60*8*($i)));
		}	
	}
	
	//------------------------
	// Construction de l'image
	$graph = new Graph(800, 600);
   	$graph->setAntiAliasing(TRUE);
   
   	$blue 	= new Blue;
   	$red 	= new Red;
   	$green 	= new Green;
   	$yellow = new Yellow;
   	$cyan 	= new Cyan;
   	$magenta= new Magenta;
  	$orange = new Orange;
   	$pink 	= new Pink;
   	$purple = new Purple;
   	
   	$border = new Border();

	// Construire un tableau des couleurs sur lequel on tournera pour 
	// chaque membre (avec un modulo)
   	$tbcolor = array ($red,$blue,$green,$yellow,$cyan,$magenta,$orange,$pink,$purple);
   	
   
	if($nbdates > 10) $nblabels = 10;
	else $nblabels = $nbdates; 	
 
  	// PlotGroup   
   	$group = new PlotGroup;
   	$group->setPadding(40, 40);
   	$group->setBackgroundColor(new Color(0, 0, 20));
 
 	// La grille 
 	$group->grid->setNobackground();
  	$group->grid->setColor(new Color(255, 255, 255, 25));
 	$group->grid->setInterval(1, (round($nbdates/20)>1 ? (round($nbdates*1.5)/40) : 1));
  	// $group->grid->setInterval(1, 1);

 	$group->grid->setType(2);

  	//Axe horizontal des dates 
  	$group->axis->bottom->setTickInterval((round($nbdates/20)>1 ? (round($nbdates*1.5)/40) : 1));
 	$group->axis->bottom->setLabelNumber($nblabels);
 	$group->axis->bottom->setColor(new White);
 	
 	$group->axis->bottom->label->setCallbackFunction('setDate');
	$group->axis->bottom->label->move(0,12);
	$group->axis->bottom->label->setColor(new White);
	
	// $group->axis->bottom->auto(true);
 
  	// Bornes sur l'axe des points...
   	// Arrondir proprement les bornes min et max de l'axe.
	$top = 1;
	while ($top<$max_points)
	{
		$top = $top * 10;	
		$interval = $top/10;	
	}	

	$bot = $top;

	while($top >= ($max_points+$interval))
	{
		$top = $top - $interval;	
	}

	while ($bot>$min_points)
	{
		$bot = $bot-$interval;
	}

	
 	// Ajouter les courbes des membres
   	for ($j=0;$j<$nbmembers;$j++)
   	{
   		for ($i=0;$i<$nbdates;$i++)
   		{
   			$dat = $merge[$i];
   			$memb = $members[$j];
			if ($clsmt[$memb][$dat]!= NULL)
   			$values[$i] = $clsmt[$memb][$dat]-$bot;
			else 
			$values[$i] = $clsmt[$memb][$dat];
   		}
   
   		$plot = new LinePlot($values);
   		$plot->setColor($tbcolor[$j % (sizeof($tbcolor)-1) ]);
   		$plot->setYAxis(PLOT_LEFT);
   
   		// $plot->mark->setType(1);
		// $plot->mark->setSize(7);
		// $plot->mark->setFill(new White);
   		// $plot->mark->border->show();
	
		$group->add($plot);
   	}
	
	// Axe vertical des points
	$group->axis->left->label->setCallbackFunction('setY');
	$group->axis->left->label->setColor(new White);
   	
 	
	$group->setYMax($top-$bot);
	$group->setYMin($bot-$bot);

	
   	$group->axis->left->setColor(new White);
   
   	$graph->add($group);
   	$graph->draw(); 
?>


