<?php

//$pathModel = "../../modele/";
require_once $pathModel ."class.phpmailer.php";

//****************************** ********************************** **********************************
//************************  *******  PARAMETRES DE CONNECTION ET ENVOI DE MAIL ******* ************************
//****************************** ********************************** **********************************

//************************ Parametres de connection utilisateur ************************

$login = $_SESSION["user"]["email_address"];
$password = $_SESSION["user"]["email_password"];

//************************ Parametres d'envoi de mail ************************

//Adresse mail du destinataire
$to=$_POST["address"];	 //addresse mail du destinataire
$to=(strpos($to,",")!==false)? explode(",",$to) : array($to);

 //Sujet du message
$subject = (!empty($_POST['subject'])) ? $_POST["subject"] : ""; 	

//Nom du domaine igeoblg.com
$domaine=$_SERVER['SERVER_NAME'];

//Pieces jointes

$files = array();
/*
$index = 0;
var_dump($_FILES);
foreach($_FILES as $file){
	if($file["size"] > $max_file_size){
		break; // On a tente de contourner la limite dans javascript 
	}
	move_uploaded_file($file["tmp_name"], $temp_folder.$file["name"]);
	$files[$index]["path"] = $temp_folder.$file["name"];
	$files[$index]["name"] = $file["name"];
	$files[$index]["type"] = $file["type"];
	$index++;
	var_dump($file);
}
*/	
//************************ Envoi du mail ************************
$mail = new PHPMailer();

$mail->IsSMTP(); 
$mail->IsHTML(true);
$mail->Host = "{$smtp_serveur}:{$port}"; // Definies dans le fichier de config
$mail->SMTPAuth = 'true';
$mail->Username = $login;
$mail->Password = $password;

$mail->From       = $_SESSION["user"]["email_address"];
$mail->FromName   = $_SESSION["user"]["name"] ." " .$_SESSION["user"]["first-name"];

$mail->Subject    = $subject;

$mail->MsgHTML($_POST["messageHtml"]);
$mail->AltBody = mb_convert_encoding($_POST["messageText"],"ISO-8859-1","UTF-8"); //Encodage du message en ISO-8859

for($i=0; $i<count($to); $i++){
	$mail->AddAddress($to[$i]); //Ajout du/des destinataire(s)
}

for($i=0; $i<count($files); $i++){
	$mail->AddAttachment($temp_folder.$files[$i]["name"]); //Ajout du/des piece(s) jointe(s)
}

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
	echo "OK"; // Message transmis par requete AJAX pour informer si tout s'est bien deroule
	exit;
}

?>
