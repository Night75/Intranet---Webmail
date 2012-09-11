<?php

require_once $pathModel."fonctions_user.php";

if(isset($_POST['username']))
{
	
	//-------- Importation des donnees necessaire de l'utilisateur de la base de donnees donnnes
	$user = user_get($users_library,$_POST["username"],$_POST["password"]);

	// Si l'email a ete trouve dans la table user, le code suivant va s'executer
	if(!empty($user))
	{	
		//-------- Assignation des parametres de session de l'utilisateur
		$_SESSION['user']["username"]=$_POST['username'];
		$_SESSION['user']['password']= $user["password"];
		//$_SESSION['user']["id"] = $user["id"];
		$_SESSION['user']['name'] = $user["nom"];
		$_SESSION['user']['first-name'] = $user["prenom"];
		$_SESSION['user']["email_address"] = $user["email"];
		$_SESSION['user']["email_password"]= $_POST['password'];
		$_SESSION['log']='logged';	
		header('location:index.php');
		exit;
	}
	else
	{
		$_SESSION["message"]= 'Mot de passe incorrect ou identifiant non valide';
		header('location:index.php?page=user_login&login=wrong');
		exit;
	}
}
