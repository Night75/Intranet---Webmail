<?php

try
{
require_once $pathModel ."fonctions_mail.php";

//****************************** ********************************** **********************************
//************************  *******  PARAMETRES DE CONNECTION ET ENVOI DE MAIL ******* ************************
//****************************** ********************************** **********************************

//************************ Parametres de connection utilisateur ************************

$login = $_SESSION["user"]["email_address"];
$password = $_SESSION["user"]["email_password"];
//Encodage des infos de connection utilisateur en base 64
$login=base64_encode($login);
$password=base64_encode($password);

//************************ Parametres d'envoi de mail ************************

//Adresse mail du destinataire
$to=$_POST["address"];	 //addresse mail du destinataire
//$to = "jeanfrancois.lai@i-geo-blg.com,jfrancois.lai@gmail.com";
$to=(strpos($to,",")!==false)? explode(",",$to) : array($to);
$to_name = $to;

//Adresse mail de l'expediteur MartinoPerez@igeoblg.com
$from= $_SESSION["user"]["email_address"];
$from_name = $_SESSION["user"]["name"] ." " .$_SESSION["user"]["first-name"];

//Message ecrit par l'expediteur
$message_txt = $_POST["messageText"];
$message_txt=mb_convert_encoding($message_txt,"ISO-8859-1","UTF-8"); //Encodage du message en ISO-8859
$message_html =  $_POST["messageHtml"];

//$message_txt ="message NEW";
//$message_html = "messagehtml New";

 //Sujet du message
$subject = (!empty($_POST['subject'])) ? $_POST["subject"] : ""; 	

//Nom du domaine igeoblg.com
$domaine=$_SERVER['SERVER_NAME'];

//Mise en forme des parametres pour les faire passer dans notre fonction formaterMail
$to_tab = array("to" => $to, "to_name" => $to_name);
$from_tab = array("from" => $from, "from_name" => $from_name);
$message_tab = array($message_txt,$message_html);

//Email a envoyer (entetes et corps)
$email = formaterMail($from_tab,$to_tab,$message_tab,$domaine,$subject); //Ca n'est qu'une chaine de texte formatee en MIME

/* -----DEBUG
echo "<pre>";
var_dump($email);
echo "</pre>";
*/

//****************************** ********************************** **********************************
//************************   VERIFICATION DE LA VALIDITE DE L'ADRESSE MAIL **************************
//****************************** ********************************** **********************************

/*
// the email to validate  
$email = array('joe@gmail.com');  

// instantiate the class  
$SMTP_Valid = new SMTP_validateEmail();  
// do the validation  
$result = $SMTP_Valid->validate($email, $from);  
// view results  
var_dump($result);  
echo $email.' is '.($result ? 'valid' : 'invalid')."\n";  
*/

//****************************** ********************************** **********************************
//************************  *************** CONNECTION SMTP ***************  ************************
//****************************** ********************************** **********************************

//Initialisation de la connection SMTP
$connection=connect_SMTP($smtp_serveur,$port,$time_out);

//Commande EHLO qui permet de s'introduire au serveur SMTP,  
execCommande($connection,'EHLO '.$_SERVER['SERVER_NAME'],250,"Impossible d'initier la connection SMTP");	

//Commande AUTH LOGIN qui demande au serveur de s'authentifier en AUTH LOGIN'
execCommande($connection,'AUTH LOGIN',334,"Erreur lors de la demande d'authentification vers le serveur");

//Ecriture du mot du login et du mot de passe
execCommande($connection,$login,334,"1-Erreur lors de l'authentification vers le serveur");
execCommande($connection,$password,235,"2-Erreur lors de l'authentification vers le serveur");
	
//
execCommande($connection,'RSET',250,"Impossible d'envoyer le mail");

//Commande MAIL FROM qui annonce l'expediteur du mail
execCommande($connection,'MAIL FROM:<'.$from.'>',250,"Erreur lors de la declaration de l'expediteur du mail vers le serveur");

//Commande RCP TO qui precise le(s) destinataire(s) du mail
//execCommande($connection,'RCPT TO:<'.$to.'>',250,"Erreur lors de la declaration du destinataire du mail vers le serveur");
foreach($to as $dest){
	execCommande($connection,'RCPT TO:<'.$dest.'>',250,"Erreur lors de la declaration du destinataire du mail vers le serveur");
}

//Commande DATA qui indique que l'on va envoyer des donnees
$var=execCommande($connection,'DATA',354,"Erreur lors de la demande d'envoi du mail");

//Insetion de notre Email
fputs($connection,$email);
fputs($connection,"\r\n.\r\n");

//Insetion de notre Email
fputs($connection,$email);
fputs($connection,"\r\n.\r\n");

//Retour a la liste des mails;
echo "OK"; // Message transmis par requete AJAX pour informer si tout s'est bien deroule
exit;
}
catch(Exception $e)
{
	echo $e->getMessage();
}

?>
