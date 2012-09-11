<?php

require_once($pathModel ."fonctions_mail.php");

if(isset($_POST['idMail']))
{
	$id_tab=explode("-", $_POST['idMail']);
	$mbox=lireFlux($_SESSION["mailbox"],$_SESSION["user"]["email_address"],$_SESSION["user"]["email_password"]);
	foreach($id_tab as $id)
	{
		imap_delete($mbox,$id);
	}
	imap_expunge($mbox);
}
echo "OK"; // Message transmis par requete AJAX pour informer que tout s'est bien deroule
exit;
?>
