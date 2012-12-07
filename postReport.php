<?php

/**
 *	postReport.php Importation d'un classement et formulaire d'importation
 *	@package	allyRanking
 *	@author		Jibus 
 *	created	: 18/08/2006   
 *	modified	: 06/09/2006
 */

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

/**
 * Fichier de fonctions du module allyRanking
 */
require_once("mod/allyranking/ARinclude.php");

$allies = get_allies();

	
if ($allies != false)
{			

	if ($user_auth["server_set_ranking"] == 1 || $user_data["user_admin"] == 1) 
	{
	
		if ($pub_subaction=="datasend")
		{
		
			$lines = array();
			$lines = explode(chr(10), $pub_importdata);
		
			galaxy_check_auth("set_ranking");
			galaxy_getranking_members($lines,$pub_ally);
					
		}
		
		//Récupérer les tag d'alliance stockés dans la table des paramètres.
		$myquery = "SELECT config_value FROM ".TABLE_CONFIG;
		$myquery.= "  WHERE config_name='tagRanking'";
		$result = $db->sql_query($myquery,false);
				
				
		list($tagRanking) = $db->sql_fetch_row($result);
			
			
		// Séparer la liste des alliances (sep virgule)
		$allys = explode(",",$tagRanking);
			
			
		//Affichage des boutons de navigation
		buttons_bar($pub_subaction);
		
		?>
		
		<BR/>
		<form style='margin:0px;padding:0px;' action="" enctype="multipart/form-data"  method="POST">
			<input type="hidden" name="action" value="allyRanking"/>
			<input type="hidden" name="subaction" value="datasend"/>
		
		
		<?php
		
			$folder48_img  = '<img width="48" height="48" SRC="mod/allyranking/images/folder48.png" name="folder48" align="absmiddle" style="behavior: url(\'mod/allyranking/images/pngbehavior.htc\');">'; 
			echo "<table width='700'>\n";
			echo "<tr><td class='c' width='50'>".$folder48_img."</td><td class='c' width='750'>Ajout de rapports d'alliance</td></tr>\n";
		
		?>
				<tr>
					<td class="c" colspan='2'>TAG d'alliance</td>
				</tr>
				<tr align="center">
					<th  colspan='2'>
						<SELECT name="ally"> 
						<?php
							for ($i = 0; $i<sizeof($allys) ; $i++)
								echo "<OPTION value='$allys[$i]'>$allys[$i]</OPTION>";	
						?>		
						</SELECT>
					</th>
				</tr>
		
				<tr>
					<td class="c" colspan='2'>Rapport d'alliance</td>
				</tr>
				<tr align="center">
					<th  colspan='2'><textarea name="importdata" rows="16"></textarea></th>
				</tr>
				<tr>
					<td class="c" align="center" colspan='2'><input type="submit" value="Envoyer"/></td>
				</tr>
			</table>
		</form>
		<?php
	}
}
else
{
	//------------------------------------
	// Affichage des boutons de navigation
	buttons_bar($pub_subaction);

	?>
	<table width='700'>
		<tr><td class="c">Visualisation des résultats impossible</td></tr>
		<tr>
		<th><br/>Le module n'est pas configuré : La liste des alliances est vide.<br/>&nbsp;</th>
		</tr>
	</table>
	<?php
}
page_footer();
require_once("views/page_tail.php");
?>
