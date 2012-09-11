<?php

require_once ($pathModel."fonctions_mail.php");

$id_msg=$_GET["id"];
$partie=$_GET["partie"];
$encodage=$_GET["encodage"];
$taille = $_GET["taille"];
$nom = $_GET["nom"];

$mbox=lireFlux($_SESSION["mailbox"],$_SESSION['user']["email_address"],$_SESSION['user']["email_password"]);
$content = recupererFichier($mbox,$id_msg, $partie, $encodage);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream' );
header('Content-Disposition: attachment; filename='.$nom);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $taille);
ob_clean();
flush();
echo $content;
exit;
