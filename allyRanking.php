<?php

/**														
 *	allyRanking.php	Gestion affichage classement
 *	@package	allyRanking
 *	@author		Jibus
 */

/**
 * Fichier de fonctions du module allyRanking
 */
//require_once("mod/allyranking/ARinclude.php");

//define("DEBUG",false);

	global $pub_show_delta, $pub_show_top1500, $pub_show_bbcode;

	if (!$pub_show_top1500)
	{
		$pub_show_delta = false;
	}

//================================================================================
//================================================================================
function ARtableau_options()
{
	global $pub_show_delta, $pub_show_top1500, $pub_show_tabtext, $pub_mode;
	?>
		<tr>
			<td class='c' colspan='2'>Options d'affichage</td>
		</tr>
		<tr>
			<th>Affichage des résultats des alliances :</th>
			<th>
				<INPUT TYPE="radio" name="mode" value="fusion" onclick="this.form.submit();" <?php if (!isset($pub_mode)||$pub_mode=="fusion") echo "CHECKED";?> > Fusionner les classements 				
				<INPUT TYPE="radio" name="mode" value="sorted" onclick="this.form.submit();" <?php if ($pub_mode=="sorted") echo "CHECKED";?>> Séparer les classements
			</th>
		</tr>
		<tr>
			<th>Afficher le classement en tableau texte</th>
      <th><input type='checkbox' name='show_tabtext' value='true' onclick='this.form.submit();' <?php if ($pub_show_tabtext==true) echo "CHECKED";?> ></th>
		</tr>
	<?php
}

/**
 * Génère le bloc de selection de dates de la page Classement et inclu le bloc d'options de classement.
 * @param string $wa Clause WHERE pour la sélection des alliances.
 */
function ARpage_top_block()
{
	global $db, $pub_date, $user_data;
	
	$request = "select datadate , count( distinct ally ) AS nb from ".TABLE_RANK_PLAYER_POINTS." WHERE ".get_allies_for_where_sql_clause()." group by datadate order by datadate desc";
	$result = $db->sql_query($request);
	?>
		<br>
		<table width='700' border=0>
		<?php 
			$ranking48_img  = '<img width="48" height="48" SRC="mod/allyranking/images/ranking48.png" name="ranking48" align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">'; 
			echo "\t\t\t\t<tr><td class='c' width='50'>".$ranking48_img."</td><td class='c' width='750'>Classement des alliances</td></tr>\n";
		?>
		</table>
		<table width='700'>
			<form style='margin:0px;padding:0px;' action="" method="POST">
			<?php ARtableau_options();?>
			<tr>
				<td class='c' colspan='2'>Date du rapport</td>
			</tr>
			<tr>
				<th colspan="2">
					<table border="0">
            <tr>
              <td>
						    <input type="hidden" name="action" value="allyranking"/>
						    <input type="hidden" name="subaction" value="ranking"/>
						    <select name="date" onchange="this.form.submit();">
							  <option value='lastreport'> Derniers rapports </option>
							  <?php
							  while(list($datadate,$nb)=$db->sql_fetch_row($result))
							   {
								 echo "\t\t\t\t\t\t<option value='$datadate'";
							   if ($pub_date==$datadate)
							     {	
							     echo " selected ";
							     $sel_date = strftime("%d %b %Y %Hh", $datadate);
                   }
                 echo ">".strftime("%d %b %Y %Hh", $datadate)." (".$nb.")</option>\n"; 
							   }
							  ?>
						    </select>
	                                                   	
					   </td>
</tr>
					</table>
				</th>
			</tr>
			</form>
		</table>

	<?php
}


/**																	
 * Génère les entêtes du tableau de classement				
 * @param string $ally nom de l'alliance pour cas "sorted	"	
 */
function ARtable_header($ally=null){

	global $pub_show_top1500;
	global $pub_show_delta;
	//--------------------------------
	// Entete du tableau de classement
	
	//--------
	// Ligne 1
	
	if (isset($ally)) echo "<tr><td class='c'colspan='6'>Alliance <font color='lime'>$ally</font></td></tr> ";
	echo "<tr><td class='c' colspan='5'>Classement interne</td>";
	if ($pub_show_top1500)	echo "<td class='c' colspan='2'>TOP 1500</td>";
	if ($pub_show_delta) 	echo "<td class='c'>&nbsp;</td>";
	echo "<td class='c'>&nbsp;</td></tr>";
	
	//--------
	// Ligne 2
	
	echo "<tr><td class='c'>Rang</td><td class='c'>Joueur</td><td class='c'>Alliance</td><td class='c'>Points</td><td class='c'>Date rapport</td>";
	echo "<td class='c'>Rang</td><td class='c'>Date rapport</td>";
	echo "<td class='c'>Graph</td></tr>";
}

/**																	
 * Génère les entêtes du tableau de classement en BBCode				
 * @param string $ally nom de l'alliance pour cas "sorted	"	
 */
function ARtable_header_tabtxt($ally=null){

	global $pub_show_top1500;
	//--------------------------------
	// Entete du tableau de classement
	
	//--------
	// Ligne 1
	
	if (isset($ally)) {$bb = "[    Alliance    $ally    ]\n<br>";}else{ $bb="";}
	$bb .= "[                              Classement interne                              ]";
	$bb .= "[        TOP 1500        ]";
	$bb .="\n<br>";
	//--------
	// Ligne 2
	
	$bb .= "[ Rang |        Joueur        |    Alliance     |   Points   |   Date rapport  ]";
	$bb .= "[ Rang |   Date rapport  ]";
	$bb .= "\n<br>";
	$bb = str_replace(" ","&nbsp;",$bb);

	$bb .= "[------------------------------------------------------------------------------]";
	$bb .= "[------------------------]";
	$bb .="\n<br>";

	echo $bb;
}


/**																			
 * Génère le bloc d'affichage du graphique d'évolution de membre	
 */	
function ARevo_member(){

	global $db;
	global $pub_member;
	
	//--------------------------------------------
	// Déterminer les bornes de date pour le graph
	
	$query = "SELECT MIN(datadate), MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='".mysql_real_escape_string($pub_member)."' GROUP BY player";
	$result = $db->sql_query($query);
    list($min,$max)=$db->sql_fetch_row($result);
	
	//----------------------------------                                     
	// Affichage du graphique en 700x350
	
	if (isset($pub_member))
	{
		echo "<table width='700'>\n";
		echo "\t<tr><td class='c' colspan='5'>Evolution du joueur $pub_member</td></tr>\n";
		echo "\t<tr><td id='evol_member'></td></tr>\n";
		include('./mod/allyranking/graphic_curve_members.php');
		echo "</table>\n";
	}
}


/**																		
 * Génère l'ensemble de la page de classement en fn° du mode		
 * @param string $sortmode accepte deux valeurs : "fusion","sorted" 	
 */
function ARgalaxy_show_ranking_members($sortmode){

	global $db, $pub_show_delta, $pub_date;

	$pub_show_delta   = (bool) $pub_show_delta;

	//---------------------------------
	// Récupérer la liste des alliances
	
	$case_clause = "";
	$order_clause = "";
	
	if (get_allies() != false)
	{			
		ARpage_top_block();

		$nothing_to_do = false;
		//$where_clause=" 0=1 ";
		if ($pub_date == 'lastreport')
		{
			//-----------------------------------------------------
			// L'utilisateur veut les derniers rapports d'alliances
	
			//-------------------------------------------------
			// Trouver la date max de chaque rapport d'alliance
			// et générer la requete par morceaux la requete de
			// classement des alliances
			
			$request = "SELECT ally,MAX(datadate) AS maxdate FROM ".TABLE_RANK_PLAYER_POINTS." WHERE ".get_allies_for_where_sql_clause()." GROUP BY ally"; 
			$result = $db->sql_query($request);

			if ($db->sql_numrows($result)!=0)				
			{
				//dbg("Entre dans la boucle");
				// Boucler pour chaque ally...
				$where_clause = "";
				while (list($ally,$maxdate) = $db->sql_fetch_row($result))
				{
					$a = mysql_real_escape_string($ally);
					$b = intval($maxdate);
					$where_clause .= " OR (a.ally='".$a."' AND a.datadate=".$b.")";				
				}
				// On dégage le 1er OR
				$where_clause = substr($where_clause,3)." ";
			}
			else
			{
				$nothing_to_do = true;	
			}
		}
		else
		{
			//--------------------------------------------------
			// L'utilisateur veut les rapports d'une date donnée

			$where_clause = get_allies_for_where_sql_clause() . " AND a.datadate = ".$pub_date." "; 			
		}
		
		//-------------------------------------------------
		if (($sortmode == "fusion")&&(!$nothing_to_do))
		{
			// Classement général trans alliances
			$order_clause = " ORDER BY points DESC ";
			
					
			// Remplacer la requete pour compatibilité MySQL<4.1
			// Trouver la liste des joueurs de TABLE_RANK_MEMBERS pour la where_clause définie

			echo "<table width='700'>\n";				
			ARtable_header();

			$query = "SELECT a.player,a.ally,a.points,a.datadate FROM ".TABLE_RANK_PLAYER_POINTS." a WHERE ".$where_clause.$order_clause;
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{
				$rang++;	
				
				if ($pub_show_top1500)
				{
					// Chercher si un eventuel classement général pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouvé récupérer les valeurs
					if ($datadate2!=NULL)
					{
						
						// dbg("Max date trouvé : $maxdate");
						$request = " SELECT rank FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player' and datadate=$datadate2";
						$result1 = $db->sql_query($request);
						list($rank) = $db->sql_fetch_row($result1);
						$delta = abs($datadate - $datadate2);
					}
					else
					{
						$datadate2="";
						$rank="&nbsp;";
						$delta = "&nbsp;";
					}
				}
				
				$dd = strftime("%d %b %Y %Hh", $datadate);		

					if ($datadate2 != '') 
						$dd2 = strftime("%d %b %Y %Hh", $datadate2);
					else
						$dd2 = '&nbsp;';
					
					$delta = prec_time((int)$delta);
				
					
				echo "<tr>\n";
				echo "\t<th>$rang</th>"
					."<th><a href='index.php?action=search&type_search=player&string_search=$player&strict=on'>$player</a></th>"
					."<th><a href='index.php?action=search&type_search=ally&string_search=$ally&strict=on'>$ally</a></th>"
					."<th>".number_format("$points",0,'','.')."</th>"
					."<th>$dd</th>\n";
				
				echo "<th>$rank</th><th>$dd2</th>\n";

				echo "\t<th>\n"
					."\t\t<a href='index.php?action=".MODULE_ACTION."&subaction=ranking&date=$datadate&member=$player'>\n"
					."\t\t\t<img src='mod/".MODULE_DIR."/images/graph_icon16.gif'>\n"
					."\t\t</a>\n"
					."\t</th>\n";		
				echo "</tr>\n";	
			}			

			echo "<tr><td class='c' colspan='6'>&nbsp;</td></tr>";
			echo "</table>";
			//dbg ("($sortmode,$pub_date) - $request<br>");
		
		}
		else if (($sortmode == "sorted")&&(!$nothing_to_do))
		{
			
			// Construction de la clause CASE WHEN d'ordonnancement des allies
			$case_clause=" ,CASE a.ally";
			for($i=0;$i<count($allies);$i++)
			{
				$case_clause .= " WHEN '".mysql_real_escape_string($allies[$i])."' THEN ".$i." ";
			}
			$case_clause .= " END case_val ";
			$order_clause = " ORDER BY case_val, points DESC ";


			echo "<table width='700'>\n";

			$query = "SELECT a.player,a.ally,a.points,a.datadate ".$case_clause." FROM ".TABLE_RANK_PLAYER_POINTS." a WHERE ".$where_clause.$order_clause;
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			$lastally='';
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{				
				if ($lastally != $ally)
				{
					ARtable_header($ally);
					$lastally = $ally;
					$rang = 0;
				}

				$rang++;	
			
				if ($pub_show_top1500)
				{
					// Chercher si un eventuel classement général pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouvé récupérer les valeurs
					if ($datadate2!=NULL)
					{
						
						// dbg("Max date trouvé : $maxdate");
						$request = " SELECT rank FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player' and datadate=$datadate2";
						$result1 = $db->sql_query($request);
						list($rank) = $db->sql_fetch_row($result1);
						$delta = abs($datadate - $datadate2);
					}
					else
					{
						$datadate2="";
						$rank="&nbsp;";
						$delta = "&nbsp;";
					}
				}	

				$dd = strftime("%d %b %Y %Hh", $datadate);		

				echo "<tr>\n";
				echo "\t<th>$rang</th>"
					."<th><a href='index.php?action=search&type_search=player&string_search=$player&strict=on'>$player</a></th>"
					."<th><a href='index.php?action=search&type_search=ally&string_search=$ally&strict=on'>$ally</a></th>"
					."<th>$points</th>"
					."<th>$dd</th>\n";

				echo "\t<th>\n"
					."\t\t<a href='index.php?action=".MODULE_ACTION."&subaction=ranking&date=$datadate&mode=$sortmode&member=$player'>\n"
					."\t\t\t<img src='mod/".MODULE_DIR."/images/graph_icon16.gif'>\n"
					."\t\t</a>\n"
					."\t</th>\n";		
				echo "</tr>\n";			
			}
			echo "<tr><td class='c' colspan='6'>&nbsp;</td></tr>";
			echo "</table>";
		}
	}
	else
	{
		?>
		<table width='700'>
			<tr><td class="c">Visualisation des résultats impossible</td></tr>
			<tr>
			<th><br>Le module n'est pas configuré : La liste des alliances est vide.<br>&nbsp;</th>
			</tr>
		</table>
		<?php
	}
	ARevo_member();
}

/**																		
 * Génère l'ensemble de la page de classement en BBCode en fn° du mode		
 * @param string $sortmode accepte deux valeurs : "fusion","sorted" 	
 */
function ARgalaxy_show_ranking_members_tabtxt($sortmode){

	global $db, $pub_date, $rank;

//---------------------------------
	// Récupérer la liste des alliances
	
	$case_clause = "";
	$order_clause = "";
	
	if (get_allies() != false)
	{			
		ARpage_top_block();

		$nothing_to_do = false;
		//$where_clause=" 0=1 ";
		if ($pub_date == 'lastreport')
		{
			//-----------------------------------------------------
			// L'utilisateur veut les derniers rapports d'alliances
	
			//-------------------------------------------------
			// Trouver la date max de chaque rapport d'alliance
			// et générer la requete par morceaux la requete de
			// classement des alliances
			
			$request = "SELECT ally,MAX(datadate) AS maxdate FROM ".TABLE_RANK_PLAYER_POINTS." WHERE ".get_allies_for_where_sql_clause()." GROUP BY ally"; 
			$result = $db->sql_query($request);

			if ($db->sql_numrows($result)!=0)				
			{
				//dbg("Entre dans la boucle");
				// Boucler pour chaque ally...
				$where_clause = "";
				while (list($ally,$maxdate) = $db->sql_fetch_row($result))
				{
					$a = mysql_real_escape_string($ally);
					$b = intval($maxdate);
					$where_clause .= " OR (a.ally='".$a."' AND a.datadate=".$b.")";				
				}
				// On dégage le 1er OR
				$where_clause = substr($where_clause,3)." ";
			}
			else
			{
				$nothing_to_do = true;	
			}
		}
		else
		{
			//--------------------------------------------------
			// L'utilisateur veut les rapports d'une date donnée

			$where_clause = get_allies_for_where_sql_clause() . " AND a.datadate = ".$pub_date." "; 			
		}

		
		//-------------------------------------------------
		if (($sortmode == "fusion")&&(!$nothing_to_do))
		{
			// Classement général trans alliances
			$order_clause = " ORDER BY points DESC ";
					
			// Trouver la liste des joueurs de TABLE_RANK_MEMBERS pour la where_clause définie

			echo "<table><tr><td class='f'><font face='courier new'>";		
			ARtable_header_tabtxt();

			$query = "SELECT a.player,a.ally,a.points,a.datadate FROM ".TABLE_RANK_PLAYER_POINTS." a WHERE ".$where_clause.$order_clause;
			//dbg($query);
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{
				$rang++;	
				
				// Chercher si un eventuel classement général pour ce joueur
				// Date du dernier classement TOP1500 de ce joueur :
				$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
				list($datadate2) = $db->sql_fetch_row($result1);
				
				// Si trouvé récupérer les valeurs
				if ($datadate2!=NULL)
				{
					
					// dbg("Max date trouvé : $maxdate");
					$request = " SELECT rank FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player' and datadate=$datadate2";
					$result1 = $db->sql_query($request);
					list($rank) = $db->sql_fetch_row($result1);
					$delta = abs($datadate - $datadate2);
				}
				else
				{
					$datadate2="";
					$rank="----";
					$delta = "-";
				}
				
				
				$dd = strftime("%d %b %Y %Hh", $datadate);		
				if ($pub_show_top1500)
				{
					if ($datadate2 != '') 
						$dd2 = strftime("%d %b %Y %Hh", $datadate2);
					else
					{
						$dd2 = '-- --- ---- ---';
					}
					$delta = prec_time((int)$delta);
				}
				$blank = "                                                      ";
				// Rang sur 4 caractères
				$disprang = substr($blank,1,4-strlen($rang)).$rang;
				
				// Joueur sur 20 caractères
				$dispplayer = substr($player,0,19);
				$dispplayer = substr($blank,1,(20-strlen($dispplayer))/2).$dispplayer.substr($blank,1,(20-strlen($dispplayer))/2);
				if (strlen($dispplayer)<20) $dispplayer .=" ";
				
				// Alliance sur 15 caractères
				$dispally = substr($ally,0,14);
				$dispally = substr($blank,1,(15-strlen($dispally))/2).$dispally.substr($blank,1,(15-strlen($dispally))/2);
				if (strlen($dispally)<15) $dispally .=" ";

				//Points sur 8 caractères
				$disppoints = substr($blank,1,8-strlen($points)).number_format($points,0,'','.');
				
				// Rang dans le classement général sur 4 caractères
				$disprank = substr($blank,1,4-strlen($rank)).$rank;
				
				if (!isset($bb)) {$bb="";}
				$bb .= "[ $disprang | $dispplayer | $dispally | $disppoints | $dd ]";				

				$bb .= "[ $disprank | $dd2 ]";
				
				$bb .= "\n<br>";
			}	
			$bb = str_replace(" ","&nbsp;",$bb);
			echo $bb;
			echo "</td></tr></table>";
			//dbg ("($sortmode,$pub_date) - $request<br>");
			
		}
		else if (($sortmode == "sorted")&&(!$nothing_to_do))
		{
			
			// Construction de la clause CASE WHEN d'ordonnancement des allies
			$case_clause=" ,CASE a.ally";
			for($i=0;$i<count($allies);$i++)
			{
				$case_clause .= " WHEN '".mysql_real_escape_string($allies[$i])."' THEN ".$i." ";
			}
			$case_clause .= " END case_val ";
			$order_clause = " ORDER BY case_val, points DESC ";


			echo "<table width='700'>\n";

			$query = "SELECT a.player,a.ally,a.points,a.datadate ".$case_clause." FROM ".TABLE_RANK_PLAYER_POINTS." a WHERE ".$where_clause.$order_clause;
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			$lastally="";
			
			
			
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{		
									
				if ($lastally != $ally)
				{
					if ($lastally <>"")
						echo "</td></tr></table>";
					echo "<table><tr><td class='f'><font face='courier new'>";
					ARtable_header_tabtxt($ally);
					$lastally = $ally;
					$rang = 0;
				}

				$rang++;	
			
				// Chercher si un eventuel classement général pour ce joueur
				// Date du dernier classement TOP1500 de ce joueur :
				$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
				list($datadate2) = $db->sql_fetch_row($result1);
				
				// Si trouvé récupérer les valeurs
				if ($datadate2!=NULL)
				{
					// dbg("Max date trouvé : $maxdate");
					$request = " SELECT rank FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player' and datadate=$datadate2";
					$result1 = $db->sql_query($request);
					list($rank) = $db->sql_fetch_row($result1);
					$delta = abs($datadate - $datadate2);
				}
				else
				{
					//$datadate2="-- --- ---- ---";
					$rank="----";	
				}
				

				$dd = strftime("%d %b %Y %Hh", $datadate);		
				
				if ($datadate2 != '')  
						{$dd2 = strftime("%d %b %Y %Hh", $datadate2);}
					else
						{$dd2 = '-- --- ---- ---';}
					
					$delta = prec_time((int)$delta);	
				
				
				// Rang sur 4 caractères
				$disprang = substr($blank,1,4-strlen($rang)).$rang;
				
				// Joueur sur 20 caractères
				$dispplayer = substr($player,0,19);
				$dispplayer = substr($blank,1,(20-strlen($dispplayer))/2).$dispplayer.substr($blank,1,(20-strlen($dispplayer))/2);
				if (strlen($dispplayer)<20) $dispplayer .=" ";
				
				// Alliance sur 15 caractères
				$dispally = substr($ally,0,14);
				$dispally = substr($blank,1,(15-strlen($dispally))/2).$dispally.substr($blank,1,(15-strlen($dispally))/2);
				if (strlen($dispally)<15) $dispally .=" ";

				//Points sur 8 caractères
				$disppoints = substr($blank,1,8-strlen($points)).$points;
				
				// Rang sur 4 caractères
				$disprank = substr($blank,1,4-strlen($rank)).$rank;
				if (!isset($bb)) {$bb="";}
				$bb .= "[ $disprang | $dispplayer | $dispally | $disppoints | $dd ]";				
				if ($pub_show_top1500){
					$bb .= "[ $disprank | $dd2 ]";
				}
				
				$bb .= "\n<br>";
				$bb = str_replace(" ","&nbsp;",$bb);
				echo $bb;
				$bb ="";
			}
			
			echo "</td></tr></table>";
		}
		
	}
	else
	{
		?>
		<table width='700'>
			<tr><td class="c">Visualisation des résultats impossible</td></tr>
			<tr>
			<th><br>Le module n'est pas configuré : La liste des alliances est vide.<br>&nbsp;</th>
			</tr>
		</table>
		<?php
	}
}
	/**
	 * 'main()'
	 */

	/**
	 * Fichier de fonctions du module allyRanking
	 */	 
	require_once("mod/allyranking/ARinclude.php");

	if (!defined('IN_SPYOGAME')) {
		die("Hacking attempt");
	}

	if (!isset($pub_mode)) $pub_mode="fusion";
	if (!isset($pub_date))	$pub_date = "lastreport";
	$pub_show_bbcode = (bool) $pub_show_bbcode;	

// 	$order_by = $pub_order_by;
// 	$interval = $pub_interval;
// 	$member = $pub_member;

	//------------------------------------
	// Affichage des boutons de navigation
	buttons_bar($pub_subaction);

	//------------------------------------
	// Construction de la page de classmt
	if (isset($pub_show_tabtext))
		ARgalaxy_show_ranking_members_tabtxt($pub_mode);
	else
		ARgalaxy_show_ranking_members($pub_mode);
		
	//------------------------
	// Pieds de page mod/ogspy
	page_footer();
	require_once("views/page_tail.php");
?>
