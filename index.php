<?php
session_start();
//A FAIRE
//INTERDIRE LES ACCES aux dossiers lorsque l'on souhaite y acceder par l'URL

//------------- ---------------- ---------------- ---------------- ----------------
//------------- CHARGEMENT DES PARAMETRES POUR LA PAGE DEMANDEE ---------------------
//------------- ---------------- ---------------- ---------------- ----------------

//Variables de comfiguration
require_once "config/config.php";

//Redirection vers l accueil par defaut(pas de page en GET)
if(!isset($_GET['page']))
{
	$pageDir="webmail/";
	$page="frmset-webmail";
}

//On determine la page demandee si elle est en GET
//Pour renseigner une page,se situant general/webmail/frm-page.php, on introduira dans l'url page=general_webmail_frm-page
if(isset($_GET['page']) && !empty($_GET['page']))
{
	$path = explode("_",$_GET['page']);
	$pageDir = "";
	
	if(in_array("..", $path)){
		// On a tente d'acceder a des fichiers anterieurs a la racine
		header("location:/intranet/index.php?page=user_login&login=error");
	}
	for($i=0;$i<count($path)-1;$i++){
		$pageDir.= $path[$i] ."/";
	}
	$page=$path[count($path)-1];
}
$pathPage = $pageDir .$page .".php";

//------------------- VERIFICATION DU DROIT D'ACCES A LA PAGE
if(!isset($_SESSION['log']) || $_SESSION['log']!='logged')
{
	// On autorise seulement l'acces a la page de login a un utilisateur anonyme
	if(!in_array($pathPage,$pagesAllowed)){
		header("location:/intranet/index.php?page=user_login&login=error");
	}
}

//Definition des variables de session
$_SESSION["mailbox"] = $mailbox;

//Determination des templates des headers et footers a inclure
if(strstr($page,"frmset-")){
	$pathHeader = "template/header-frm.php";
	$pathFooter = "template/footer-frm.php";
}
else{
	$pathHeader = "template/header.php";
	$pathFooter = "template/footer.php";
}

//Determination des fichiers css et javascript a inclure pour la page demandee
$pageCss = (isset($filesCss[$pageDir])) ? explode(".css", $filesCss[$pageDir]) : array();
$pageScript = (isset($filesScript[$pageDir])) ? explode(".js", $filesScript[$pageDir]) : array();
array_pop($pageCss);
array_pop($pageScript);

if(!empty($filesCss[$pathPage]))
{
	$pageCssTemp= explode(".css",$filesCss[$pathPage]);
	$indexCss = count($pageCss);
	for($i=0; $i<count($pageCssTemp)-1;$i++)
	{
		$pageCss[$indexCss]	= $pageCssTemp[$i];
		$indexCss++;
	}
}

if(!empty($filesScript[$pathPage]))
{
	$pageScriptTemp= explode(".js",$filesScript[$pathPage]);
	$indexScript = count($pageScript); 
	for($i=0; $i<count($pageScriptTemp)-1;$i++)
	{
		$pageScript[$indexScript] = $pageScriptTemp[$i];
		$indexScript++;
	}
}

//------------- ---------------- ---------------- ----------------
//------------- CONSTRUCTION DE LA PAGE ---------------------------
//------------- ---------------- ---------------- ----------------

//CONTROLEUR
require_once $pathController .$pathPage;

//HEADER
require_once $pathView .$pathHeader;

//VUE
require_once $pathView . $pathPage;

//FOOTER
require_once $pathView . $pathFooter;

?>

