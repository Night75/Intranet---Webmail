<?php

//--------------------______________ USERS STOCKES DANS FICHIER TXT____________------------------------>	
/**
 * Cree un utilisateur. On enregistre ses parametres dans un fichier .txt
 * 
 * @param string $source. Path du fichier vers lequel enregistrer les infos
 * @param string $name. Nom de l'utilisateur
 * @param string $firstname. Prenom de l'utilisateur
 * @param string $email. Email
 * @param string $password. Mot de passe
 */
function user_create($source,$name,$firstname,$email,$password)
{
	$handle = fopen($source,"a");
	$password_encrypted = md5($email.$password);
	$data = $name ."\t" .$firstname ."\t" .$email ."\t" .$password_encrypted ."\n";
	fwrite($handle,$data);
	fclose($handle);
}

/**
 * Obtient les infos d'utilisateur. On les extrait a partir du fichier .txt
 * 
 * @param string $source. Path du fichier vers lequel enregistrer les infos
 * @param string $email. Email
 * @param string $password. Mot de passe
 *
 * @return array $user
 */
function user_get($source,$email,$password)
{
	$password_encrypted = md5($email.$password);
	
	$handle = fopen($source,"r");
	if($handle){
		while(!feof($handle)){
			$data = explode("\t",fgets($handle));	
			if(count($data) <= 1){ return "";} //------ La fin du fichier est atteint

			if($data[2] == $email && trim($data[3]) == $password_encrypted){
				$user_keys = array("nom","prenom","email","password");
				$user = array_combine($user_keys,$data);
				return $user;
			}
		}
	}
	return "";
}


//--------------------_____________ USERS STOCKES DANS BDD ____________------------------------>	

//------------------ Initialisation :	$bdd = new PDO($dsn,$bdd_user,$bdd_password);
//										$bdd->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

/*

function user_create($source,$name,$firstname,$email,$password)
{
	$password_encrypted = md5($email.$password);
	$request = $source->prepare("INSERT INTO user(nom,prenom,email,password) VALUES(:nom,:prenom,:email,:password)");
	$request->execute(array(
				":nom" => $name,
				":prenom" => $firstname,
				":email" => $email,
				":password" => $password_encrypted
				));
}

function user_search()
{

}

function user_check($source,$password)
{
	$request = $source->prepare("SELECT id,nom,prenom,email,password FROM user WHERE email=:email;");
	$request->execute(array(":email" => $_POST["username"]));
	$user = $request->fetch(PDO::FETCH_ASSOC);
	return $user;
}
*/

?>
