<?php

/**
 *	config.php Page de configuration du module allyRanking. Accessible uniquement par les admins
 *	@package	allyRanking
 *	@author		Jibus
 */

/**
 * Fichier de fonctions du module allyRanking
 */
require_once("mod/allyranking/ARinclude.php");

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

$adminMessage = "";

// Suppression des vieux rapports.
if (isset($pub_delRanking))
{
	if ($pub_deleteDays !="")
	{
		deleteOlder($pub_deleteDays);
		$adminMessage .="<br/>Suppression des classements de plus de $pub_deleteDays jours effectuée";
	}
}

// Connexion Xtense2
if (isset($pub_submitxt2))
  {
  // on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $ally_id = $db->sql_fetch_row($result);
  $ally_id = $ally_id[0];
  // on fait du nettoyage au cas ou
  $query = "DELETE FROM `".$table_prefix."xtense_callbacks"."` WHERE `mod_id`=".$ally_id;
  $db->sql_query($query);
  // Insert les données pour récuperer les info de la page alliance
  $query = "INSERT INTO ".$table_prefix."xtense_callbacks"." ( `mod_id` , `function` , `type` )
				VALUES ( '".$ally_id."', 'ally_list', 'ally_list')";
	$db->sql_query($query);
  }


// Renommage de membre
if (isset($pub_oldMemberName)&&isset($pub_newMemberName))
{
	if (($pub_oldMemberName!="")&&($pub_newMemberName!=""))
	{
		renameMember($pub_oldMemberName,$pub_newMemberName);
		$adminMessage .= "<br/>Renommage du membre $pub_oldMemberName en $pub_newMemberName effectué.";
	}
}

// Changement d'alliance (ex:passage Wing->Mère)
if (isset($pub_transMember)&&isset($pub_transAlliance))
{
	if (($pub_transMember!="")&&($pub_transAlliance!=""))
	{
		changeAlly($pub_transMember,$pub_transAlliance);
		$adminMessage .= "<br/>Déplacement du membre $pub_transMember dans l'alliance $pub_transAlliance effectué.";
	}
}

if (isset($pub_delMember))
{
	if ($pub_delMember!="")
	{
		deleteMemberRanking($pub_delMember);
		$adminMessage .= "<br/>Suppression des classements du membre $pub_delMember.";
	}
}

if (isset($pub_optimize))
{
	$query = "OPTIMIZE TABLE ".TABLE_RANK_MEMBERS;
	$db->sql_query($query);
	$adminMessage .= "<br/>Optimization de la table ".TABLE_RANK_MEMBERS." effectué.";
}
if (isset($pub_defaultMenu))
{
	SetMenuDefaults();
	// refresh, parce que le menu s'affiche avantl'action du module.
	redirection("index.php?action=".MODULE_ACTION."&subaction=config");
}

if (isset($pub_showtitleyes))
{
	addMenuTitle();
	// refresh, parce que le menu s'affiche avantl'action du module.
	redirection("index.php?action=".MODULE_ACTION."&subaction=config");
}

if (isset($pub_showtitleno))
{
	delMenuTitle();
	// refresh, parce que le menu s'affiche avantl'action du module.
	redirection("index.php?action=".MODULE_ACTION."&subaction=config");
}

if (isset($pub_showiconyes))
{
	addMenuIcon();
	// refresh, parce que le menu s'affiche avantl'action du module.
	redirection("index.php?action=".MODULE_ACTION."&subaction=config");
}

if (isset($pub_showiconno))
{
	delMenuIcon();
	// refresh, parce que le menu s'affiche avantl'action du module.
	redirection("index.php?action=".MODULE_ACTION."&subaction=config");
}

$res = getMenuStatus();
list($icon,$title) = $res;

if (isset($pub_submitbt) && $pub_submitbt == "Envoyer")
{
	$pub_tagRanking = mysql_real_escape_string($pub_tagRanking);
	$query = "UPDATE ".TABLE_CONFIG." SET config_value='".$pub_tagRanking."' WHERE config_name='tagRanking'";
	$db->sql_query($query);
}

$query  = "SELECT config_value FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'";
$result = $db->sql_query($query);

list($tagRanking) = $db->sql_fetch_row($result);

//Affichage des boutons de navigation
buttons_bar($pub_subaction);

?>


<br/>
<script>
<!--
function valid()
{
	if (document.form1.delMember.value!="")
		return confirm("Voulez-vous supprimer définitivement tous les classements alliance de "+document.form1.delMember.value);
	else
		return true;
}

-->
</script>

<form name="form1" style='margin:0px;padding:0px;' action="" enctype="multipart/form-data"  method="POST" onsubmit="return valid();">
	<input type="hidden" name="action" value="allyranking"/>
	<input type="hidden" name="subaction" value="config"/>

	<table width='700' border=0>
	<?php
		$config48_img  = '<img width="48" height="48" SRC="mod/allyranking/images/config48.png" name="config48" align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">';
		echo "<tr><td class='c' width='50'>".$config48_img."</td><td class='c' width='750'>Administration du module</td></tr>\n";

	?>
	</table>


	<table width="700">
		<tr>
			<td class="c" colspan="2">Paramètres du module</td>
		</tr>
		<tr>
			<td class="c" colspan="2">Gestion des alliances </td>
		</tr>
		<tr align="center">
			<th>Liste des alliances à utiliser (séparées par des virgules)</th>
			<th>
				<input type="text" name="tagRanking" rows="1" cols="10"
				<?php
					if (!empty($tagRanking))
						echo "value='$tagRanking'";
				?>
				/>
			</th>
		</tr>
		<tr align="center">
        	<th>Renommer le membre</th>
        	<th>
            <div align="left">
  			<select name="oldMemberName" id="oldMemberName">
			<option></option>
<?php
	$membersOptionsList = membersList();
	echo $membersOptionsList;
?>
			</select>
			&gt;&gt;
			<input name="newMemberName" type="text" id="newMemberName" size="15" rows="1"/>
			&nbsp;</div></th>
		</tr>
		<tr align="center">
			<th>Transf&eacute;rer un membre </th>
			<th>
				<div align="left">
					<select name="transMember" id="transMember">
						<option></option>
<?php
	echo $membersOptionsList;
?>
					</select>
					&gt;&gt;
					<select name="transAlliance" id="transAlliance">
						<option></option>
<?php

							$listAlly = explode(',',$tagRanking);
							for($i=0;$i<count($listAlly);$i++)
							{
								$ally = trim($listAlly[$i]);
								echo "\t\t\t\t\t\t<option value='".$ally."'>".$ally."</option>\n";
							}
?>
					</select>
					&nbsp;
        		</div>
        	</th>
	  	</tr>
		<tr align="center">
			<th>Supprimer les classements d'un (ex) membre </th>
			<th>
				<div align="left">
					<select name="delMember" id="delMember">
						<option></option>
<?php
	echo $membersOptionsList;
?>
					</select>
				</div>
			</th>
		</tr>

		<tr>
			<td colspan="2" class="c" align="center"><input name="submitbt" type="submit" value="Envoyer"/></td>
		</tr>
</form>
<form style='margin:0px;padding:0px;' action="" enctype="multipart/form-data"  method="POST">
	<input type="hidden" name="action" value="allyranking"/>
	<input type="hidden" name="subaction" value="config"/>

		<tr>
			<td class="c" colspan="2">Menu</td>
		</tr>
		<tr align="center">
			<th>Affichage du texte </th>
			<th>
			<?php
				if ($title==0){
			?>
				<input name="showtitleyes" type="submit" onclick="this.form.submit();" value="Afficher"/>
			<?php
				} else if ($icon!=0){
			?>
				<input name="showtitleno" type="submit" onclick="this.form.submit();" value="Masquer"/>
			<?php
				} else echo "&nbsp;";
			?>
			</th>
		</tr>
		<tr align="center">
			<th>Affichage de l'image </th>
			<th>
			<?php
				if ($icon==0){
			?>
				<input name="showiconyes" type="submit" onclick="this.form.submit();" value="Afficher"/>
			<?php
				} else if ($title!=0){
			?>
				<input name="showiconno" type="submit" onclick="this.form.submit();" value="Masquer"/>
			<?php
				} else echo "&nbsp;";
			?>
			</th>
		</tr>
		<tr align="center">
			<th>Remettre le menu par d&eacute;faut</th>
			<th><input type="submit" name="defaultMenu" value="Lancer" onclick="this.form.submit();"/></th>
		</tr>
		<tr>
			<td colspan="2" class="c" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td class="c" colspan="2">Performances</td>
		</tr>
		<tr align="center">
			<th>Optimiser la table <?php echo TABLE_RANK_MEMBERS;?></th>
			<th>
				<input type="submit" name="optimize" value="Lancer" onclick="this.form.submit();"/>
			</th>
		</tr>
</form>
<form style='margin:0px;padding:0px;' action="" enctype="multipart/form-data"  method="POST" onsubmit="return confirm('Etes-vous sûr de vouloir supprimer définitivement ces classements');">
	<input type="hidden" name="action" value="allyranking"/>
	<input type="hidden" name="subaction" value="config"/>

		<tr>
			<td class="c" colspan="2">Maintenance</td>
		</tr>


		<tr align="center">
			<th>Supprimer les classements de plus de
				<input name="deleteDays" type="text" size="2" rows="1"/> jours
			</th>
			<th>
				<input name="delRanking" type="submit" id="delRanking" value="Lancer">
			</th>
		</tr>
		<tr>
			<td colspan="2" class="c" align="center">&nbsp;</td>
		</tr>

		<?php
			if ($adminMessage!="")
			{
		?>
		<tr>
			<td class="c" colspan="2">Messages d'administration</td>
		</tr>
		<tr>
			<th colspan="2"><?php echo $adminMessage;?><br/><br/></th>
		</tr>
		<?php
			}
		?>

		<tr>
			<td class="c" colspan="2" >Etat du module</td>
		</tr>
		<tr>
			<th colspan="2">
		<?php
			// Controler l'existence de la table TABLE_RANK_MEMBERS
			$tb_ally_ranking_found = false;
			
			/* Correction Jibus Mars 2006 */
			$result = $db->sql_query("select 1 from ".TABLE_RANK_MEMBERS." LIMIT 1");
			$tb_ally_ranking_found = true;
			if (!$result)
			{
				if (mysql_errno() == 1146)
					$tb_ally_ranking_found = false;
			}

			if (!$tb_ally_ranking_found)
			{
				echo " /!\ Table ".TABLE_RANK_MEMBERS." non trouvée /!\<br />";
			}
			else
			{
				echo " Table ".TABLE_RANK_MEMBERS." -> [OK] /!\<br />";
			}

			$problem = false;

			// Controler la présence du paramètre 'tagRanking'
			$result = $db->sql_query("SELECT * FROM ".TABLE_CONFIG." WHERE config_name='tagRanking'");

			if ($db->sql_numrows() != 0)
				echo "[".TABLE_CONFIG."|allyRanking]->[OK]<BR/>";
			else
			{
				echo "[".TABLE_CONFIG."|allyRanking]->[ECHEC]<BR/>";
				$problem = true;
			}
			if (!$problem)
				echo "Base opérationelle<BR/>";
		?>
			</td>
		</tr>
		<tr>
			<td class="c" colspan="2" >Etat du module GD</td>
		</tr>
		<tr>
			<th colspan="2">
				<?php
					$mygdinfo = gd_info();
					$ttfsupport = false;
					$pngsupport = false;

					//Parcourir à la recherche des fonctions importantes
					foreach($mygdinfo as $key => $value)
					{
						if ($key == "FreeType Support")
							$ttfsupport = true;
						if ($key == "PNG Support")
							$pngsupport = true;
					}

					echo "Version ".$mygdinfo["GD Version"]."<br/>";

					if (!$ttfsupport)
						echo "Support FreeType absent<br />";
					if (!$pngsupport)
						echo "Support PNG absent<br />";
					if ($pngsupport && $ttfsupport)
						echo "Module GD fonctionnel";



				?>
			</th>
		</tr>
		</form>
		<?php
        //Connexion Xtense2
echo "<form name='xt2' style='margin:0px;padding:0px;' action='' enctype='multipart/form-data' method='post'><center>";
echo "<input type='hidden' name='action' value='allyranking'/>
	<input type='hidden' name='subaction' value='config'/>";
//echo "<table width='60%' border='0'>
echo "<tr><td class='c' colspan='2'>Xtense 2&nbsp;</td></tr>";
echo "<tr>";
//On vérifie que la table xtense_callbacks existe
if( ! mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
  {
  echo "<th colspan='2'>la barre Xtense2 semble ne pas &ecirc;tre install&eacute;e</th>";
  }
  else
  {
  // Si oui, on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='allyranking' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $ally_id = $db->sql_fetch_row($result);
  $ally_id = $ally_id[0];
  // Maintenant on vérifie que le mod est déclaré dans la table
  $query = "SELECT `id` FROM ".$table_prefix."xtense_callbacks"." WHERE `mod_id`=".$ally_id;
  $result = $db->sql_query($query);
  // On doit avoir 2 entrées dans la table : une pour les RC, une pour les RR
  if (mysql_num_rows($result) != 1)
    {
    echo "<th colspan='2'>Le module 'allyRanking' n'est pas enregistr&eacute; aupr&egrave;s de Xtense2</th>";
    echo "<tr>";
    echo "<th colspan='2'>Souhaitez vous &eacute;tablir la connexion ?</th>";
    echo "</tr>";
    echo "<tr><td class='c' align='center' colspan='2'><input name='submitxt2' type='submit' value='Connecter Xtense2' onclick='this.form.submit();' ></td></tr>";
    }else{
    echo "<th colspan='2'>Le module 'allyRanking' est correctement enregistr&eacute; aupr&egrave;s de Xtense2</th>";
    echo "<tr><td class='c' colspan='2'>&nbsp;</td>";
    }
  }
echo "</tr></table></form>";

	page_footer();
	require_once("views/page_tail.php");
?>
