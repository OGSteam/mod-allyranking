<?php

/**
 *	ARinclude.php Fichier d'include du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus
 */


global $table_prefix;

//================================================================================
//================================================================================
//									DEFINES
//================================================================================
//================================================================================
define("MODULE_NAME","allyranking");
define("MODULE_ACTION","allyranking");
define("MODULE_DIR","allyranking");
define("MODULE_VERSION","0.4");
define("MENU_ICON","<img align=\"absmiddle\" src=\"./mod/allyranking/images/graph_icon16.gif\">");


//================================================================================
//================================================================================
//								Liste des fonctions
//================================================================================
//================================================================================

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
	// Récupère la liste des alliances définies ds la table config.
	$allies = mod_get_option('tagRanking'); 

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

function get_allies_for_where_sql_clause(){
	
	$allies = get_allies();
	if( $allies != false){
		$where_allies = " (ally='".mysql_real_escape_string($allies[0])."' ";
		for ($i=1;$i<count($allies);$i++)
			$where_allies .= " OR ally='".mysql_real_escape_string($allies[$i])."' "; 
		$where_allies .=") ";
		
	}else{
		$where_allies = false;	
	}
	return $where_allies;
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
	echo "<br/><B>allyRanking v$version</B> - Jibus&copy;2006-2013<br/>";
	echo '<B><div>Remise à jour pour OGSpy 3.1.2 </B> - DarkNoon</div>';
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
		echo '<th width="150" style="vertical-align:middle;">'.$detail_img.'<a>Comparer</a></th>'."\n";
	else
	{
		echo '<td class="c" width="150" onclick="window.location = \'index.php?action=allyranking&subaction=detail\';">'."\n";
		echo '<a style="cursor:pointer">'.$detail_img.'<font color="lime">Comparer</font></a>'."\n";
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

?>
