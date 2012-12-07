<?php

/**
 *	detail.php Page du graphique d'évolution détaillé
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */

/**
 * Fichier de fonctions du module allyRanking
 */
 ?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function checkAll()
{
var x=document.getElementById("userlist");
for (var i=0;i<x.length;i++)
  {
  if (x.elements[i].type == "checkbox")
     {
         x.elements[i].checked = true;
     }
  }
}

function uncheckAll()
{
var x=document.getElementById("userlist");
for (var i=0;i<x.length;i++)
  {
  if (x.elements[i].type == "checkbox")
     {
         x.elements[i].checked = false;
     }
  }
}
//  End -->
</script>
<?php
 
require_once("mod/allyranking/ARinclude.php");
//define("DEBUG",true);

	global $in_list;
	if (!defined('IN_SPYOGAME')) die("Hacking attempt");
if (!isset($pub_memberslist)) $pub_memberslist = array();
	if (count($pub_memberslist))
	{	
		$mblist = implode(",",$pub_memberslist);
	}

	// Affichage des boutons de navigation
	buttons_bar($pub_subaction,810);

	$allies = get_allies();

	if ($allies != false)
	{			
		//---------------------------------------------
		// Préparer une clause where pour les alliances

		$where_allies = " WHERE (ally='".mysql_real_escape_string($allies[0])."' ";
		for ($i=1;$i<count($allies);$i++)
			$where_allies .= " OR ally='".mysql_real_escape_string($allies[$i])."' "; 
		$where_allies .=") ";
	}

	

	$detail48_img  = '<img width="48" height="48" SRC="mod/allyranking/images/detail48.png" name="detail48" align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">'; 

	echo "<BR/><table width='810'>\n";
	echo "\t<tr><td class='c' width='50'>".$detail48_img."</td><td class='c' width='750'>Evolution générale - Détail par membre</td></tr>\n";
	echo "\t<tr><th colspan='2'>";
	if (count($pub_memberslist))
		echo "<img src='index.php?action=allyranking&subaction=graphicglobal&mblist=$mblist'/>";
	else
		echo "Choisissez des joueurs dont vous voulez visualiser la progression";
	echo "</th> \n";
	echo "</tr>\n </table>\n";
	
	$result = $db->sql_query("SELECT COUNT(DISTINCT player),ally FROM ".TABLE_RANK_MEMBERS." ".$where_allies." GROUP BY ally");
	
	
	//--------------------------------
	// Tableau des options d'affichage
	$nb_max_col = 1;
	$nb_col = 0;
	echo "<form id='userlist' style='margin:0px;padding:0px;' action='' method='POST'>";
	echo "<input type='hidden' name='action' value='allyranking'>";
	echo "<input type='hidden' name='subaction' value='detail'>";
	echo "<table width='810'>\n";
	// Bouton CheckAll et UnCheckAll
    echo "\t<tr>\n";
	echo "\t\t<th><input type='button' name='CheckAll' value='Tous' onClick='checkAll()'>\n";
	echo "\t\t</th>\n";
	echo "\t\t<th><input type='button' name='UnCheckAll' value='Aucun' onClick='uncheckAll()'>\n";
	echo "\t\t</th>\n";
	echo "\t</tr>\n";
	// ----------------------------
    echo "<tr><th colspan='2'>\n";
	echo "<table width='800'>\n";
	echo "<tr><td class='c' colspan='100'>Options d'affichage</td></tr>";
	
	// Pour chaque alliance suivie, construire la liste des membres
	$color = 0;
	$tabColors = array ('red','blue','green','yellow','cyan','magenta','orange','pink','purple');
	
	for ($i = 0; $i < count($allies); $i++)
	{
		echo "\t<tr>\n";
		echo "\t\t<th width='100'>$allies[$i]</th>\n";
		echo "\t\t<th align='left' valign='top' width='150'>\n";
		$query = "SELECT DISTINCT player FROM ".TABLE_RANK_MEMBERS." WHERE ally='".mysql_real_escape_string($allies[$i])."' ORDER BY player";
		//dbg($query);
		$result = $db->sql_query($query);
		$j=1;

		while(list($player)=$db->sql_fetch_row($result))
		{
			echo "\t\t\t<table width='100%' cellpadding='0' cellspacing='1'>\n\t\t\t\t<tr><td>\n";
			echo "\t\t\t\t\t<input type='checkbox' value='$player' name='memberslist[]' id='$player'";
			if (count($pub_memberslist)!=0)
				echo (array_search($player,$pub_memberslist)!==false?" checked ":"");
			echo " >\n";
			echo "\t\t\t\t</td>\n"
				."\t\t\t\t";

			echo "<td><table width='20' heigth='10'><tr><td border='1' ";
			if (count($pub_memberslist)!=0)
			{
				if (array_search($player,$pub_memberslist)!==false)
				{
					$indice = $color % (sizeof($tabColors)-1) ;
					echo "bgcolor=\"".$tabColors[$indice]."\"";
					$color++;
				}
			}

			echo ">&nbsp;</td></tr></table></td>\n\t\t\t\t<td width='100%'>\n";
			echo "\t\t\t\t\t<label for='$player'>$player";				
			echo "</label>\n"."\t\t\t\t</td></tr>\n\t\t\t</table>\n";
			if (!($j++%6))
			{
				echo ("\t\t</th>\n\t\t<th align='left' valign='top' width='150'>\n");
			}			
		}
		echo "\t\t</th>\n";
		echo "\t</tr>\n";


		
		echo "\t<tr><td class='c' colspan='100'>&nbsp;</td></tr>\n";
	}
	echo "</table>\n";
	
	echo "</th></tr>";
	echo "<tr><th colspan='2'><input type='submit' value='Appliquer'></th></tr>";
	echo"</table>\n";

	echo "</form>";

	page_footer();
	require_once("views/page_tail.php");
?>


