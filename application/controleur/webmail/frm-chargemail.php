<?php

require_once $pathModel ."fonctions_mail.php";
	
$mailbox = "{pop.orange-business.com:110/pop3}";

$mbox=lireFlux($_SESSION["mailbox"],$_SESSION['user']["email_address"],$_SESSION['user']["email_password"]);
$id=$_POST['idMail'];

$struct = imap_fetchstructure($mbox, $id);
$info=imap_headerinfo($mbox,$id);
$email=lireEmail($mbox,$id,0,$struct,0,0);
$body=$email['message'];

$from=decodeMime($info->fromaddress);
$to=decodeMime($info->toaddress);
$subject=decodeMime($info->Subject);
$date=date("d/m/Y \&#224 H:i", strtotime($info->date));
$fichiers = $email["fichiers"];
