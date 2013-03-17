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
	mod_set_option('tagRanking',$pub_tagRanking);
}

$tagRanking = mod_get_option('tagRanking');

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
		<tr>
			<td colspan="2" class="c" align="center"><input name="submitbt" type="submit" value="Envoyer"/></td>
		</tr>
</form>
<form style='margin:0px;padding:0px;' action='' enctype='multipart/form-data'  method='POST'>
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
</form>

</table>
<?php
	page_footer();
	require_once("views/page_tail.php");
?>
