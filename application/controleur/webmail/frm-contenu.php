<?php

try{
	//------------- ---------------- ---------------- ----------------
	//------------- CHARGEMENT DES PARAMETRES GENERAUX ---------------------------
	//------------- ---------------- ---------------- ----------------
	require_once $pathModel ."fonctions_mail.php";
	
	$mbox = lireFlux($_SESSION["mailbox"],$_SESSION['user']["email_address"],$_SESSION['user']["email_password"]);
	$nombre_mails = imap_num_msg($mbox);  //nombre total de mails
		
	$nb_messages = imap_check($mbox)->Nmsgs;
	
	//$nb_contenus_a_charger_max = $limite_mails_par_page; //nombre de contenus de mails a charger (ceci est different des entetes de mails qui sont
	$nb_contenus_a_charger = ($nb_messages < $nb_contenus_a_charger_max) ? $nb_messages : $nb_contenus_a_charger_max;

	//------------- ---------------- ---------------- ----------------
	//------------- CONTROLES DE LA LISTE DES MAILS ---------------------------
	//------------- ---------------- ---------------- ----------------

	// ----------------- =================== PAGINATION 
	// ----------------- Determination du nombre de pages total necessaire pour recouvrir l'affichage de tous les mails
	$nb_pages = 0; //nombres de pages recouvrant la totalité des listes de mails initialisé à 0
	$compteur = $nombre_mails; //compteur servant à déterminer nb_pages
	do{
		$nb_pages++;
		$compteur -= $limite_mails_par_page;
	}
	while($compteur > 0);

	// ----------------- Extraction du numero de page et determination du mail en fin de liste
	if(empty($_GET['numpage'])){
		$fin_liste = $nombre_mails;
		$numpage = 1;
	}
	else{
		$fin_liste = $nombre_mails-$limite_mails_par_page*($_GET['numpage']-1);
		$numpage = $_GET['numpage'];
	}
	
	//Les variable fin_liste_atteint et debut_liste_atteint nous serviront pour la pagination
	$fin_liste_atteint = ($numpage == 1) ? true : false; 
	
	// ---------------- Determination du mail en debut de liste
	// ======== $_GET["lastmail"] est defini si les emails sont deja charges.
	//  l'utilisateur a clique sur le bouton de rafraichissement des mails. Une requete ajax est envoyee.
	if(empty($_GET["lastmail"])){
		if($fin_liste - $limite_mails_par_page<0) {
			$debut_liste = 1;
			$debut_liste_atteint = true;
		}
		else{
			$debut_liste = $fin_liste - $limite_mails_par_page+1;
			$debut_liste_atteint = false;
		}
	}
	else{
		if($_GET["lastmail"] == $fin_liste){
			echo "OK";  //La boite de messagerie est deja a jour. Pas la peine de rafraichir la page. 
			exit;
		}
		$debut_liste =  $_GET["lastmail"]+1;
	}
	
	
	// ----------------- =================== EXTRACTION DES ENTETES DE MAILS 
	//On extrait tous les email entre le mail de debut et fin de liste 
	$overview = imap_fetch_overview($mbox,"$debut_liste:$fin_liste");
	$overview = array_reverse($overview);

	$idTab = array();
	$from = array();
	$subject = array();
	$date = array();
	
	for($i=0; $i < count($overview); $i++)
	{
		$idTab[$i] = $overview[$i]->msgno;
		$from[$i] = decodeMime($overview[$i]->from);
		$date[$i] = date("d/m/Y", strtotime($overview[$i]->date));
		//$attachment[$i]= true;
		if(!empty($overview[$i]->subject)){	
			$subject[$i] = decodeMime($overview[$i]->subject);
		}
		else{
			$subject[$i] = "sans objet";
		}
	}					


	//------------- ---------------- ---------------- ----------------
	//------------- CONTROLES DES CONTENUS DE MAILS ---------------------------
	//------------- ---------------- ---------------- ----------------
	$struct = array();
	$info = array();
	$email = array();
	$body = array();
	$to = array();
	$fichiers = array();
	$indice = 0;

	$start = (!empty($_GET["lastmail"])) ? ($_GET["lastmail"]+1) : ($fin_liste-$nb_contenus_a_charger_max);

	for($id=$fin_liste; $id>=$start; $id--)
	{	
		/*$struct = imap_fetchstructure($mbox, $id);
		$info = imap_headerinfo($mbox,$id);
		$email = lireEmail($mbox,$id,0,$struct,0,0,true);*/
		$struct = imap_fetchstructure($mbox, $id);
		$info = imap_headerinfo($mbox,$id);
		$email = lireEmail($mbox,$id,0,$struct,0,0,true);
		$body[$indice] = $email['message'];
		//$from[$indice] = decodeMime($info->fromaddress);
		$to[$indice] = decodeMime($info->toaddress);
		//$subject[$indice] = (!empty($info->Subject)) ? decodeMime($info->Subject) : "sans objet";
		//$date[$indice] = date("d/m/Y \&#224 H:i", strtotime($info->date));
		$fichiers[$indice] = (isset($email["fichiers"])) ? $email["fichiers"] :null ;
	
		$indice++;
	}

	/*
	echo "numpage {$numpage}";
	echo "debut liste = {$debut_liste}";
	echo "<br/>";
	echo "fin liste = {$fin_liste}";
	echo "<br/>";
	echo "nombre mail {$nombre_mails}";
	echo "<br/>";
	echo "numpage {$numpage}";
	*/
}
catch(Exception $e)
{
	var_dump($e);
}
?>
