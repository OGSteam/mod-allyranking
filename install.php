<?php

/**
 *	install.php Fichier d'installation du module allyRanking
 *	@package	allyRanking
 *	@author		Jibus 
 */

	if (!defined('IN_SPYOGAME')) {
		die("Hacking attempt");
	}
	
//Insertion du champs pour la declaration du module dans OGSpy
	$is_ok = false;
	$mod_folder = "allyranking";
	$is_ok = install_mod($mod_folder);
if ($is_ok == true)
	{
		//Nothing to do
	}
else
  {
  echo  "<script>alert('D�sol�, un probl�me a eu lieu pendant l'installation, corrigez les probl�mes survenue et r�essayez.');</script>";
  }
?>
