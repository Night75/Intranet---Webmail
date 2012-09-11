<?php
$message = "";
if(!empty($_SESSION['message'])){
	$message = $_SESSION['message'];
	$_SESSION['message'] = "";
}

if(isset($_GET['login'])&& $_GET['login']=="error")
{
	$message = 'Accès non autorisé. Veuillez vous identifier';
}
?>
