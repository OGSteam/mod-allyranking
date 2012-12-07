<?php

/**
 *	ARinclude.php Fichier d'include du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus
 */


global $table_prefix;
define("DEBUG",false);
//================================================================================
//================================================================================
//									DEFINES
//================================================================================
//================================================================================
define("TABLE_RANK_MEMBERS",$table_prefix."rank_members");
define("MODULE_NAME","allyranking");
define("MODULE_ACTION","allyranking");
define("MODULE_DIR","allyranking");
define("MODULE_VERSION","0.4f");
define("MENU_ICON","<img align=\"absmiddle\" src=\"./mod/allyranking/images/graph_icon16.gif\">");


//================================================================================
//================================================================================
//								Liste des fonctions
//================================================================================
//================================================================================

/**
 * Fonction de débogage
 */
function dbg($message){

	if (DEBUG) echo "<table Width='100%'><tr><th>$message</th></tr></table>";
}

function membersList()
{
	global $db;
	$list="";
	$query = "SELECT DISTINCT player FROM ".TABLE_RANK_MEMBERS." ORDER BY player";
	$result = $db->sql_query($query);

	while ( list($player) = $db->sql_fetch_row())
	{
		$list .= "\t\t\t\t\t\t<option value='".$player."'>".$player."</option>\n";
	}
	return $list;
}

function deleteOlder($nbdays)
{
	global $db;
	$nbsec = $nbdays * 24 * 3600;
	$today = getdate();
	$olderdate = $today[0] - $nbsec;
	$query = "delete from ".TABLE_RANK_MEMBERS." WHERE datadate < $olderdate";

	$db->sql_query($query);
}

function deleteMemberRanking($name1)
{
	global $db;
	$query = "DELETE FROM ".TABLE_RANK_MEMBERS." WHERE PLAYER='$name1'";
	$db->sql_query($query);
}

function renameMember($name1,$name2)
{
	global $db;
	$query = "UPDATE ".TABLE_RANK_MEMBERS." SET player='$name2' WHERE PLAYER='$name1'";
	$db->sql_query($query);
}

function changeAlly($name,$ally)
{
	global $db;
	$query = "UPDATE ".TABLE_RANK_MEMBERS." SET ally='$ally' WHERE PLAYER='$name'";
	$db->sql_query($query);
}

function getMenuStatus()
{
	global $db;

	$query = "SELECT menu FROM ".TABLE_MOD." WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

	list($menu)=$db->sql_fetch_row($result);

	// Chercher si l'image ou le titre sont affichés

	if (preg_match('/<img[^>]*>/i',$menu)!=false)
		$icon = 1;
	else
		$icon = 0;
	if (preg_match('/'.MODULE_NAME.'$/i',$menu)!=false)
		$title = 1;
	else
		$title = 0;

	$menuStatus = array($icon,$title);

	return $menuStatus;

}

function setMenuDefaults()
{
	global $db;

	$query = "UPDATE ".TABLE_MOD." SET menu = '".MENU_ICON."&nbsp;".MODULE_NAME."' WHERE TITLE='".MODULE_NAME."'";
	$db->sql_query($query);
}

function addMenuIcon()
{
	global $db;

	$query = "SELECT menu FROM ".TABLE_MOD." WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

	list($menu)=$db->sql_fetch_row($result);

	if (preg_match('/<img[^>]*>/i',$menu)==false)
	{
		$menu = MENU_ICON . "&nbsp;" . $menu;
	}

	$query = "UPDATE ".TABLE_MOD." SET menu = '$menu' WHERE TITLE='".MODULE_NAME."'";

	$result = $db->sql_query($query);

}

function delMenuIcon()
{
	global $db;

	$query = "SELECT menu FROM ".TABLE_MOD." WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);
	list($menu)=$db->sql_fetch_row($result);

	$menu = preg_replace('`<img[^>]*>&nbsp;`','',$menu);

	$query = "UPDATE ".TABLE_MOD." SET menu = '$menu' WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

}

function addMenuTitle()
{
	global $db;

	$query = "SELECT menu FROM ".TABLE_MOD." WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

	list($menu)=$db->sql_fetch_row($result);

	if (preg_match('/&nbsp;/i',$menu)==false)
	{
		$menu = $menu . "&nbsp;" . MODULE_NAME ;
	}

	$query = "UPDATE ".TABLE_MOD." SET menu = '$menu' WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

}

function delMenuTitle()
{
	global $db;

	$query = "SELECT menu FROM ".TABLE_MOD." WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);
	list($menu)=$db->sql_fetch_row($result);

	$menu = preg_replace('`>&nbsp;[^>]*$`','>',$menu);

	$query = "UPDATE ".TABLE_MOD." SET menu = '$menu' WHERE TITLE='".MODULE_NAME."'";
	$result = $db->sql_query($query);

}

/**
 * get_allies Retourne la liste des alliances choisies pour le module allyRanking
 * @return array Tableau de string des alliances ou FALSE si aucune alliance n'est trouvée
 * @global integer connect_id de l'instance ogspy de classe sql_db
 */
function get_allies()
{
	global $db;

	// Récupère la liste des alliances définies ds la table config.
	$result = $db->sql_query("SELECT config_value FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'");
	list($allies) = $db->sql_fetch_row($result);

	if ($allies=="")
		return false;
	else
	{
		//$all = str_replace(" ","",$allies);

        $all = str_replace(" ,",",",$allies);
        $all = str_replace(", ",",",$allies);

		$allies_array = explode(",",$all);
		return $allies_array;
	}
}
//================================================================================
//================================================================================
function prec_time($sec){
//-------------------------------
// Converti un nombre de seconde
// en Jour/Heure

	$prec = strval((int)($sec/(24*60*60)))."j ".strval((((int)$sec)%(24*60*60))/(60*60))."h";
	return $prec;
}
//================================================================================
//================================================================================
function page_footer()
{
	global $db;

	//Récupérer le numéro de version de la base
	$request = "SELECT version from ".TABLE_MOD." WHERE title='allyranking'";
	$result = $db->sql_query($request,false);
	list($version)=$db->sql_fetch_row($result);
	echo "<br/><B>allyRanking v$version</B> - Jibus&copy;2006-2008<br/>";
	echo '<B><div>Remise à jour pour OGSpy 3.0.7 </B> - Shad</div>';
}
//================================================================================
//================================================================================
function buttons_bar($subaction,$width=700)
{
	global $user_auth;
	global $user_data;

	//-------------------------------------------------
	// Code pour les png transparents...:( Vive IE ! :(
	$report_img  = '<img SRC="mod/allyranking/images/folder.png"  name="report"  align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">&nbsp;&nbsp;';
	$detail_img  = '<img SRC="mod/allyranking/images/detail.png"  name="detail"  align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">&nbsp;&nbsp;';
	$ranking_img = '<img SRC="mod/allyranking/images/ranking.png" name="ranking" align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">&nbsp;&nbsp;';
	$config_img  = '<img SRC="mod/allyranking/images/config.png"  name="config"  align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">&nbsp;&nbsp;';



	echo "<table border='1' width='$width'>";
	echo "<tr align='center'>";
	echo "<td class='c'>&nbsp;</td>";
	// ------------------------
	// BOUTON "NOUVEAU RAPPORT"

	if ($user_auth["server_set_ranking"] == 1 || $user_data["user_admin"] == 1)
	{
		if ($subaction == "report")
			echo '<th width="150" style="vertical-align:middle;">'.$report_img.'<a>Nouveau rapport</a></th>'."\n";
		else
		{
			echo '<td class="c" width="150" onclick="window.location = \'index.php?action=allyranking&subaction=report\';">'."\n";
			echo '<a style="cursor:pointer">'.$report_img.'<font color="lime">Nouveau rapport</font></a>'."\n";
			echo '</td>'."\n";
		}
	}

	// ------------------------
	//    BOUTON "CLASSEMENT"
	if (($subaction == "ranking")||(!isset($subaction)))
		echo '<th width="150" style="vertical-align:middle;">'.$ranking_img.'<a>Classement</a></th>'."\n";
	else
	{
		echo '<td class="c" width="150" onclick="window.location = \'index.php?action=allyranking&subaction=ranking\';">'."\n";
		echo '<a style="cursor:pointer">'.$ranking_img.'<font color="lime">Classement</font></a>'."\n";
		echo '</td>'."\n";
	}

	// ------------------------
	//     BOUTON "DETAILS"
	if ($subaction == "detail")
		echo '<th width="150" style="vertical-align:middle;">'.$detail_img.'<a>Détails</a></th>'."\n";
	else
	{
		echo '<td class="c" width="150" onclick="window.location = \'index.php?action=allyranking&subaction=detail\';">'."\n";
		echo '<a style="cursor:pointer">'.$detail_img.'<font color="lime">Détails</font></a>'."\n";
		echo '</td>'."\n";
	}

	// ------------------------
	//  BOUTON "CONFIGURATION"
	if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1)
	{
		if ($subaction == "config")
			echo '<th width="150" style="vertical-align:middle;">'.$config_img.'<a>Configuration</a></th>'."\n";
		else
		{
			echo '<td class="c" width="150" onclick="window.location = \'index.php?action=allyranking&subaction=config\';">'."\n";
			echo '<a style="cursor:pointer">'.$config_img.'<font color="lime">Configuration</font></a>'."\n";
			echo '</td>'."\n";
		}
	}
	echo "<td class='c'>&nbsp;</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

}

//================================================================================
//================================================================================
function galaxy_getranking_members($lines,$ally,$attempt=0) {
//-------------------------------------------------------------
// Importation du classement interne de l'alliance et insertion
// des enregistrements dans la table de classement des membres
// TABLE_RANK_MEMBERS.

	global $db;
	global $user_data;
	global $server_config;

	if ($attempt == 5)
	{
		// Cinq fois la tentative d'insertion(appel récursif). Le timestamp reste a zéro. Il y a un pb
		die("L'opération d'insertion ne peut aboutir. Timestamp = 0");
	}

	$time=0;
	$time = time()-60*4;
	if ($time > mktime(0,0,0) && $time < mktime(8,0,0)) $timestamp = mktime(0,0,0);
	if ($time > mktime(8,0,0) && $time < mktime(16,0,0)) $timestamp = mktime(8,0,0);
	if ($time > mktime(16,0,0) && $time < (mktime(0,0,0)+60*60*24)) $timestamp = mktime(16,0,0);

	$files = array();
	$OK = false;
	$last_position = 0;
	$index = 0;

	for ($i=0 ; $i<sizeof($lines) ; $i++)
	{
		$line = trim($lines[$i]);


		// Compatibilité Firefox.
		//
		// Le format des rapports varie entre IE et Firefox...
		// Je remplace les tabs de firefox par des espaces
		// et j'enlève les "Écrire un message"
		$line = str_replace(".","",$line);
		$line = str_replace("\t"," ",$line);
		$line = str_replace("Écrire un message","Écrireunmessage",$line);


		//Recherche de la ligne 0 du tableau

		if (preg_match("#^Nom\s\sRang\sPlace\sCoords\sAdhésion\sOnline$#",$line))
		{
			$OK = true;
			continue;
		}

		if ($OK)
		{
			$array_res = split("Écrireunmessage",$line);
			$num_name = split(" ",$array_res[0]);
			$name=$num_name[1];
			// Si des espaces dans le nom...
			for ($j=2;$j<count($num_name)-1;$j++)
				$name .= " ".$num_name[$j];
			$res = split(" ",substr($array_res[1],1,strpos($array_res[1],":")-3));
			$num = count($res)-1;
			$points = $res[count($res)-2];
			if (intval($timestamp)!=0)
			{
				if(!is_numeric($num_name[0]))
				{
					$OK = false;
				}
				else
				{
					$request = "insert ignore ".TABLE_RANK_MEMBERS;
					$request .= " (datadate, player, points, ally, sender_id)";
					$request .= " values (".intval($timestamp).", '".mysql_real_escape_string($name)."', ".intval($points).", '".mysql_real_escape_string($ally)."',  ".$user_data["user_id"].")";
					if ($name != "")
						$db->sql_query($request,false);
				}
			}
			else
				//Nouvelle tentative
				galaxy_getranking_members($lines,$ally,$attempt=0,$attempt++);
		}
	}

	if ($server_config["debug_log"] == "1")
	{
		// Sauvegarde données tranmises
		$nomfichier = PATH_LOG_TODAY.date("ymd_His")."_ID".$user_data["user_id"]."_ranking_".$datatype.".txt";
		write_file($nomfichier, "w", $files);
	}
	redirection("index.php?action=".MODULE_ACTION."&subaction=ranking");
}
?>
