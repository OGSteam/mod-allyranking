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
			<th>Affichage des r�sultats des alliances :</th>
			<th>
				<INPUT TYPE="radio" name="mode" value="fusion" onclick="this.form.submit();" <?php if (!isset($pub_mode)||$pub_mode=="fusion") echo "CHECKED";?> > Fusionner les classements 				
				<INPUT TYPE="radio" name="mode" value="sorted" onclick="this.form.submit();" <?php if ($pub_mode=="sorted") echo "CHECKED";?>> S�parer les classements
			</th>
		</tr>
		<tr>
			<th>Afficher le classement en tableau texte</th>
      <th><input type='checkbox' name='show_tabtext' value='true' onclick='this.form.submit();' <?php if ($pub_show_tabtext==true) echo "CHECKED";?> ></th>
		</tr>
		<tr>
			<th>Afficher le classement au top 1500</th>
      <th><input type='checkbox' name='show_top1500' value='true' onclick='this.form.submit();' <?php if ($pub_show_top1500==true) echo "CHECKED";?> ></th>
		</tr>
		<?php 
		if ($pub_show_top1500)
		{
		?>	
		<tr>
			<th>Afficher la pr�cision des rapports</th>
      <th><input type='checkbox' name='show_delta' value='true' onclick='this.form.submit();'<?php if ($pub_show_delta==true) echo "CHECKED";?> ></th>
		</tr>
		<?php
		}
		?>
	<?php
}

/**
 * G�n�re le bloc de selection de dates de la page Classement et inclu le bloc d'options de classement.
 * @param string $wa Clause WHERE pour la s�lection des alliances.
 */
function ARpage_top_block($wa)
{
	global $db, $pub_date, $user_data;
	
	$request = "select datadate , count( distinct ally ) AS nb from ".TABLE_RANK_MEMBERS." ".$wa." group by datadate order by datadate desc";
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
					   <td>
					   <?php
						//--------------------------------------------
						// Droit de suppression d'un ancien classement
						if ($user_data["user_admin"] == 1 || $user_data["management_server"] == 1 || $user_data["management_ranking"] == 1)
						  {
							if ($pub_date != 'lastreport')
							 {
						  ?>
								<form style='margin:0px;padding:0px;' method="POST" action="" onsubmit="return confirm('Etes-vous s�r de vouloir supprimer ce classement');">
									<input type="hidden" name="action" value="<?php echo MODULE_ACTION; ?>">
									<input type="hidden" name="subaction" value="dropranking">
									<input type="hidden" name="datadate" value="<?php echo $pub_date;?>">
									<input type="image" src="images/drop.png" title="Supprimer le classement du <?php echo $sel_date;?>">
								</form>
						<?php
							}
						}
					?>
					</td></tr>
					</table>
				</th>
			</tr>
			</form>
		</table>

	<?php
}


/**																	
 * G�n�re les ent�tes du tableau de classement				
 * @param string $ally nom de l'alliance pour cas "sorted	"	
 */
function ARtable_header($ally=null){

	global $pub_show_top1500;
	global $pub_show_delta;
	//--------------------------------
	// Entete du tableau de classement
	
	//--------
	// Ligne 1
	
	if (isset($ally)) echo "<tr><td class='c'colspan='".strval(6+intval($pub_show_top1500)*2+intval($pub_show_delta))."'>Alliance <font color='lime'>$ally</font></td></tr> ";
	echo "<tr><td class='c' colspan='5'>Classement interne</td>";
	if ($pub_show_top1500)	echo "<td class='c' colspan='2'>TOP 1500</td>";
	if ($pub_show_delta) 	echo "<td class='c'>&nbsp;</td>";
	echo "<td class='c'>&nbsp;</td></tr>";
	
	//--------
	// Ligne 2
	
	echo "<tr><td class='c'>Rang</td><td class='c'>Joueur</td><td class='c'>Alliance</td><td class='c'>Points</td><td class='c'>Date rapport</td>";
	if ($pub_show_top1500)	echo "<td class='c'>Rang</td><td class='c'>Date rapport</td>";
	if ($pub_show_delta)	echo "<td class='c'>Pr�cision</td>";
	echo "<td class='c'>Graph</td></tr>";
}

/**																	
 * G�n�re les ent�tes du tableau de classement en BBCode				
 * @param string $ally nom de l'alliance pour cas "sorted	"	
 */
function ARtable_header_tabtxt($ally=null){

	global $pub_show_top1500;
	global $pub_show_delta;
	//--------------------------------
	// Entete du tableau de classement
	
	//--------
	// Ligne 1
	
	if (isset($ally)) {$bb = "[    Alliance    $ally    ]\n<br>";}else{ $bb="";}
	$bb .= "[                              Classement interne                              ]";
	if ($pub_show_top1500)	$bb .= "[        TOP 1500        ]";
	$bb .="\n<br>";
	//--------
	// Ligne 2
	
	$bb .= "[ Rang |        Joueur        |    Alliance     |   Points   |   Date rapport  ]";
	if ($pub_show_top1500)	$bb .= "[ Rang |   Date rapport  ]";
	$bb .= "\n<br>";
	$bb = str_replace(" ","&nbsp;",$bb);

	$bb .= "[------------------------------------------------------------------------------]";
	if ($pub_show_top1500)	$bb .= "[------------------------]";
	$bb .="\n<br>";

	echo $bb;
}


/**																			
 * G�n�re le bloc d'affichage du graphique d'�volution de membre	
 */	
function ARevo_member(){

	global $db;
	global $pub_member;
	
	//--------------------------------------------
	// D�terminer les bornes de date pour le graph
	
	$query = "SELECT MIN(datadate), MAX(datadate) FROM ".TABLE_RANK_MEMBERS." WHERE player='".mysql_real_escape_string($pub_member)."' GROUP BY player";
	$result = $db->sql_query($query);
    list($min,$max)=$db->sql_fetch_row($result);
	
	//----------------------------------                                     
	// Affichage du graphique en 700x350
	
	if (isset($pub_member))
	{
		echo "<table width='700'>\n";
		echo "\t<tr><td class='c' colspan='5'>Evolution du joueur $pub_member</td></tr>\n";
		echo "\t<tr><th colspan='5'><img src='index.php?action=allyranking&subaction=graphic&player=".$pub_member."&player_comp=&start=".$min."&end=".$max."&graph=members_points&titre=".$pub_member."' alt='pas de graphique disponible' /></th></tr>\n";
		echo "</table>\n";
	}
}


/**																		
 * G�n�re l'ensemble de la page de classement en fn� du mode		
 * @param string $sortmode accepte deux valeurs : "fusion","sorted" 	
 */
function ARgalaxy_show_ranking_members($sortmode){

	global $db, $pub_show_delta, $pub_show_top1500, $pub_date;

	$pub_show_top1500 = (bool) $pub_show_top1500;
	$pub_show_delta   = (bool) $pub_show_delta;

	//dbg("galaxy_show_ranking_members($sortmode,$pub_date)<br>");
	
	//---------------------------------
	// R�cup�rer la liste des alliances
	
	$allies = get_allies();
	$case_clause = "";
	$order_clause = "";
	
	if ($allies != false)
	{			
		//---------------------------------------------
		// Pr�parer une clause where pour les alliances

		$where_allies = " WHERE (ally='".mysql_real_escape_string($allies[0])."' ";
		for ($i=1;$i<count($allies);$i++)
			$where_allies .= " OR ally='".mysql_real_escape_string($allies[$i])."' "; 
		$where_allies .=") ";
		ARpage_top_block($where_allies);

		$nothing_to_do = false;
		$where_clause=" 0=1 ";
		if ($pub_date == 'lastreport')
		{
			//-----------------------------------------------------
			// L'utilisateur veut les derniers rapports d'alliances
	
			//-------------------------------------------------
			// Trouver la date max de chaque rapport d'alliance
			// et g�n�rer la requete par morceaux la requete de
			// classement des alliances
			
			$request = "SELECT ally,MAX(datadate) AS maxdate FROM ".TABLE_RANK_MEMBERS.$where_allies." GROUP BY ally"; 
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
				// On d�gage le 1er OR
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
			// L'utilisateur veut les rapports d'une date donn�e
			
			$where_clause = " a.datadate = ".$pub_date." "; 			
		}
		
		//-------------------------------------------------
		if (($sortmode == "fusion")&&(!$nothing_to_do))
		{
			// Classement g�n�ral trans alliances
			$order_clause = " ORDER BY points DESC ";
					
			// Remplacer la requete pour compatibilit� MySQL<4.1
			// Trouver la liste des joueurs de TABLE_RANK_MEMBERS pour la where_clause d�finie

			echo "<table width='700'>\n";				
			ARtable_header();

			$query = "SELECT a.player,a.ally,a.points,a.datadate FROM ".TABLE_RANK_MEMBERS." a WHERE ".$where_clause.$order_clause;
			//dbg($query);
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{
				$rang++;	
				
				if ($pub_show_top1500)
				{
					// Chercher si un eventuel classement g�n�ral pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouv� r�cup�rer les valeurs
					if ($datadate2!=NULL)
					{
						
						// dbg("Max date trouv� : $maxdate");
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
				if ($pub_show_top1500)
				{
					if ($datadate2 != '') 
						$dd2 = strftime("%d %b %Y %Hh", $datadate2);
					else
						$dd2 = '&nbsp;';
					
					$delta = prec_time((int)$delta);
				}
					
				echo "<tr>\n";
				echo "\t<th>$rang</th>"
					."<th><a href='index.php?action=search&type_search=player&string_search=$player&strict=on'>$player</a></th>"
					."<th><a href='index.php?action=search&type_search=ally&string_search=$ally&strict=on'>$ally</a></th>"
					."<th>".number_format("$points",0,'','.')."</th>"
					."<th>$dd</th>\n";
				
				if ($pub_show_top1500)
					echo "<th>$rank</th><th>$dd2</th>\n";
				
				if ($pub_show_delta)
					echo "<th>$delta</th>\n";
				
				echo "\t<th>\n"
					."\t\t<a href='index.php?action=".MODULE_ACTION."&subaction=ranking&date=$datadate&member=$player'>\n"
					."\t\t\t<img src='mod/".MODULE_DIR."/images/graph_icon16.gif'>\n"
					."\t\t</a>\n"
					."\t</th>\n";		
				echo "</tr>\n";	
			}			

			echo "<tr><td class='c' colspan='".strval(6+intval($pub_show_top1500)*2+intval($pub_show_delta))."'>&nbsp;</td></tr>";
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

			$query = "SELECT a.player,a.ally,a.points,a.datadate ".$case_clause." FROM ".TABLE_RANK_MEMBERS." a WHERE ".$where_clause.$order_clause;
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
					// Chercher si un eventuel classement g�n�ral pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouv� r�cup�rer les valeurs
					if ($datadate2!=NULL)
					{
						
						// dbg("Max date trouv� : $maxdate");
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
				if ($pub_show_top1500)
				{
					if ($datadate2 != '') 
						$dd2 = strftime("%d %b %Y %Hh", $datadate2);
					else
						$dd2 = '&nbsp;';
					
					$delta = prec_time((int)$delta);	
				}	
				echo "<tr>\n";
				echo "\t<th>$rang</th>"
					."<th><a href='index.php?action=search&type_search=player&string_search=$player&strict=on'>$player</a></th>"
					."<th><a href='index.php?action=search&type_search=ally&string_search=$ally&strict=on'>$ally</a></th>"
					."<th>$points</th>"
					."<th>$dd</th>\n";
				
				if ($pub_show_top1500)
					echo "<th>$rank</th><th>$dd2</th>\n";
				
				if ($pub_show_delta)
					echo "<th>$delta</th>\n";
				
				echo "\t<th>\n"
					."\t\t<a href='index.php?action=".MODULE_ACTION."&subaction=ranking&date=$datadate&mode=$sortmode&member=$player'>\n"
					."\t\t\t<img src='mod/".MODULE_DIR."/images/graph_icon16.gif'>\n"
					."\t\t</a>\n"
					."\t</th>\n";		
				echo "</tr>\n";			
			}
			echo "<tr><td class='c' colspan='".strval(6+intval($pub_show_top1500)*2+intval($pub_show_delta))."'>&nbsp;</td></tr>";
			echo "</table>";
		}
	}
	else
	{
		?>
		<table width='700'>
			<tr><td class="c">Visualisation des r�sultats impossible</td></tr>
			<tr>
			<th><br>Le module n'est pas configur� : La liste des alliances est vide.<br>&nbsp;</th>
			</tr>
		</table>
		<?php
	}
	ARevo_member();
}

/**																		
 * G�n�re l'ensemble de la page de classement en BBCode en fn� du mode		
 * @param string $sortmode accepte deux valeurs : "fusion","sorted" 	
 */
function ARgalaxy_show_ranking_members_tabtxt($sortmode){

	global $db, $pub_show_delta, $pub_show_top1500, $pub_date, $rank;

	$pub_show_top1500 = (bool) $pub_show_top1500;
	$pub_show_delta   = (bool) $pub_show_delta;

	//dbg("galaxy_show_ranking_members($sortmode,$pub_date)<br>");
	
	//---------------------------------
	// R�cup�rer la liste des alliances
	
	$allies = get_allies();
	$case_clause = "";
	$order_clause = "";
	$blank = "                                                      ";

	if ($allies != false)
	{			
		//---------------------------------------------
		// Pr�parer une clause where pour les alliances

		$where_allies = " WHERE (ally='".mysql_real_escape_string($allies[0])."' ";
		for ($i=1;$i<count($allies);$i++)
			$where_allies .= " OR ally='".mysql_real_escape_string($allies[$i])."' "; 
		$where_allies .=") ";
		ARpage_top_block($where_allies);
		$nothing_to_do = false;
		$where_clause=" 0=1 ";
		if ($pub_date == 'lastreport')
		{
			//-----------------------------------------------------
			// L'utilisateur veut les derniers rapports d'alliances
	
			//-------------------------------------------------
			// Trouver la date max de chaque rapport d'alliance
			// et g�n�rer la requete par morceaux la requete de
			// classement des alliances
			
			$request = "SELECT ally,MAX(datadate) AS maxdate FROM ".TABLE_RANK_MEMBERS.$where_allies." GROUP BY ally"; 
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
				// On d�gage le 1er OR
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
			// L'utilisateur veut les rapports d'une date donn�e
			
			$where_clause = " a.datadate = ".$pub_date." "; 			
		}
		
		//-------------------------------------------------
		if (($sortmode == "fusion")&&(!$nothing_to_do))
		{
			// Classement g�n�ral trans alliances
			$order_clause = " ORDER BY points DESC ";
					
			// Remplacer la requete pour compatibilit� MySQL<4.1
			// Trouver la liste des joueurs de TABLE_RANK_MEMBERS pour la where_clause d�finie

			echo "<table><tr><td class='f'><font face='courier new'>";		
			ARtable_header_tabtxt();

			$query = "SELECT a.player,a.ally,a.points,a.datadate FROM ".TABLE_RANK_MEMBERS." a WHERE ".$where_clause.$order_clause;
			//dbg($query);
			$result = $db->sql_query($query,false,false);

			// Corps du tableau
			$rang = 0;
			while(list($player,$ally,$points,$datadate) = $db->sql_fetch_row($result))
			{
				$rang++;	
				
				if ($pub_show_top1500)
				{
					// Chercher si un eventuel classement g�n�ral pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouv� r�cup�rer les valeurs
					if ($datadate2!=NULL)
					{
						
						// dbg("Max date trouv� : $maxdate");
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
				// Rang sur 4 caract�res
				$disprang = substr($blank,1,4-strlen($rang)).$rang;
				
				// Joueur sur 20 caract�res
				$dispplayer = substr($player,0,19);
				$dispplayer = substr($blank,1,(20-strlen($dispplayer))/2).$dispplayer.substr($blank,1,(20-strlen($dispplayer))/2);
				if (strlen($dispplayer)<20) $dispplayer .=" ";
				
				// Alliance sur 15 caract�res
				$dispally = substr($ally,0,14);
				$dispally = substr($blank,1,(15-strlen($dispally))/2).$dispally.substr($blank,1,(15-strlen($dispally))/2);
				if (strlen($dispally)<15) $dispally .=" ";

				//Points sur 8 caract�res
				$disppoints = substr($blank,1,8-strlen($points)).number_format($points,0,'','.');
				
				// Rang dans le classement g�n�ral sur 4 caract�res
				$disprank = substr($blank,1,4-strlen($rank)).$rank;
				
				if (!isset($bb)) {$bb="";}
				$bb .= "[ $disprang | $dispplayer | $dispally | $disppoints | $dd ]";				
				if ($pub_show_top1500){
					$bb .= "[ $disprank | $dd2 ]";
				}
				
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

			$query = "SELECT a.player,a.ally,a.points,a.datadate ".$case_clause." FROM ".TABLE_RANK_MEMBERS." a WHERE ".$where_clause.$order_clause;
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
			
				if ($pub_show_top1500)
				{
					// Chercher si un eventuel classement g�n�ral pour ce joueur
					// Date du dernier classement TOP1500 de ce joueur :
					$result1 = $db->sql_query("SELECT MAX(datadate) FROM ".TABLE_RANK_PLAYER_POINTS." WHERE player='$player'");
					list($datadate2) = $db->sql_fetch_row($result1);
					
					// Si trouv� r�cup�rer les valeurs
					if ($datadate2!=NULL)
					{
						// dbg("Max date trouv� : $maxdate");
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
				}	

				$dd = strftime("%d %b %Y %Hh", $datadate);		
				if ($pub_show_top1500)
				{
					if ($datadate2 != '')  
						{$dd2 = strftime("%d %b %Y %Hh", $datadate2);}
					else
						{$dd2 = '-- --- ---- ---';}
					
					$delta = prec_time((int)$delta);	
				}	
				
				// Rang sur 4 caract�res
				$disprang = substr($blank,1,4-strlen($rang)).$rang;
				
				// Joueur sur 20 caract�res
				$dispplayer = substr($player,0,19);
				$dispplayer = substr($blank,1,(20-strlen($dispplayer))/2).$dispplayer.substr($blank,1,(20-strlen($dispplayer))/2);
				if (strlen($dispplayer)<20) $dispplayer .=" ";
				
				// Alliance sur 15 caract�res
				$dispally = substr($ally,0,14);
				$dispally = substr($blank,1,(15-strlen($dispally))/2).$dispally.substr($blank,1,(15-strlen($dispally))/2);
				if (strlen($dispally)<15) $dispally .=" ";

				//Points sur 8 caract�res
				$disppoints = substr($blank,1,8-strlen($points)).$points;
				
				// Rang sur 4 caract�res
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
			<tr><td class="c">Visualisation des r�sultats impossible</td></tr>
			<tr>
			<th><br>Le module n'est pas configur� : La liste des alliances est vide.<br>&nbsp;</th>
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
