<?php

/**
 * Ouvre un flux vers le serveur de messagerie
 * 
 * @param string $mailbox. Nom de la boite de messagerie. Exemple: "{" nom_systeme_distant [":" port] [flags] "}" [nom_mailbox]
 * @param string $email. Adresse email
 * @param string $password. Mot de passe de l'email
 *
 * @return resource
 */
function lireFlux($mailbox,$email,$password)
{
	$flux=imap_open($mailbox,$email,$password);
	return $flux;
}

/**
 * Lit un email, ou la partie souhaitee de l'email 
 * 
 * @param resource $mbox. Flux vers la boite de messagerie
 * @param string $id_msg. L'id du message 
 * @param string $num_partie. Section du message a extraire (present dans le cas de contenu de type MULTIPART)
 * @param object $partie. Instance d'un objet cree par imap_fetchstructure
 * @param integer $niveau. Niveau de la partie lue. 0 correspond a l'element parent
 * @param boolean $reset. Reinitialisation
 *
 * @return resource
 */
function lireEmail($mbox,$id_msg, $num_partie, $partie, $niveau, $alternative=0, $reset=false )
{
	static $retour = Array('message' => '');
	
	if($reset)
	{
		$retour = Array('message' => '');
	}
	
	if ($partie->ifdisposition && strtolower($partie->disposition) == "attachment" )
	 {
		 //on récupère le nom et quelques infos
		 //qu'on ajoute au tableau de résultats.
		 //on suppose la fonction trouverNom qui parourt
		 //le tableau parameters jusqu'à trouver le paramètre
		 //name, et en retourner la valeur
		 if ($partie->ifdparameters) 
		 {
			 $fichier = array( 'nom' => trouverNom($partie->dparameters),
			 'numero_partie' => $num_partie,
			 'encodage' => $partie->encoding,
			 'taille' => $partie->bytes,
			 'type' => $partie->type,
			 'subtype' => $partie->subtype);
		 }
		 elseif ($partie->ifparameters) 
		 {
			 $fichier = array( 'nom' => trouverNom($partie->parameters),
			 'numero_partie' => $num_partie,
			 'encodage' => $partie->encoding,
			 'taille' => $partie->bytes,
			 'type' => $partie->type,
			 'subtype' => $partie->subtype);
		 }
		 
		 $retour['fichiers'][] = $fichier;
	}
	else{
		switch ($partie->type)
		{
			case 0: // si c'est du texte TYPETEXT
		
				if ( $alternative) // et que c'est un texte avec une alternative
				{
					 //echo $partie->subtype;echo'<br/>';
					 if ($partie->subtype == "PLAIN" ) // on fait un test du format à afficher
					 {
						 $texte= imap_fetchbody( $mbox, $id_msg, $num_partie);
						 $retour['message'] = "".nl2br(decoder($texte,$partie->encoding))."";
						 /*echo 'retour incrémente pour le subtype PLAIN' ;echo'<br/>';
						 $retour['numero']=$num_partie;
						 echo 'numero de la partie(interieur):'.$retour['numero'];*/
					 }
					  elseif ($partie->subtype =="HTML") // on fait un test du format à afficher
					 {
						 $texte= imap_fetchbody( $mbox, $id_msg, $num_partie);
						 $retour['message'] = "".decoder($texte,$partie->encoding)."";
						 /*echo 'retour incrémenté pour le subtype HTML' ;echo'<br/>';
						 $retour['numero']=$num_partie;
						 echo 'numero de la partie(interieur):'.$retour['numero'];*/
					 }
	
				}
				else // si c'est pas alternatif, on affiche
				{
					 //if( !empty($partie->parts) )
					 //if ($niveau !=0)
					 if ($num_partie<>0)
					 {
						 // on lit le texte à la partie qui nous interesse.
						 $texte = imap_fetchbody( $mbox, $id_msg, $num_partie);
						 $retour['message'] = "".decoder($texte,$partie->encoding)."";
					 }
					 else
					 {
						 if ($partie->subtype == 'PLAIN') // on lit le texte PLAIN
						 {
							 $texte = imap_body( $mbox, $id_msg);
							 $retour['message'] = "".nl2br(decoder($texte,$partie->encoding))."";
						 }
						 else if($partie->subtype == 'HTML') // on le texte HTML
						 {
							 $texte = imap_body( $mobx, $id_msg);
							 $retour['message'] = "".decoder($texte,$partie->encoding)."";
						 }
					 }
				}
			break;
			
			case 1:  //TYPEMULTIPART
			
				if ($partie->subtype == "ALTERNATIVE")
				{
					$alternative = 1;
				}
				//on lit chaque sous parts.
				for ( $i=0;$i<count($partie->parts); $i++)
				{
					if ($niveau != 0 )
					{
						$pos = strrpos($num_partie, ".");
						if ($pos === false) 
						{ // si on trouve pas de point, on en ajoute un sinon pas : pas de double point
							$num_partie = $num_partie.".";
						}
					}
					else 
					{
						$num_partie = "";
					}
					lireEmail($mbox,$id_msg, $num_partie.($i + 1), $partie->parts[$i], 1, $alternative);
				}
			break;
		
			case 2://TYPEMESSAGE:
			case 3://TYPEAPPLICATION:
			case 4://TYPEAUDIO:
			case 5://TYPEIMAGE:
			case 6://TYPEVIDEO:
			case 7://TYPEMODEL:
			default:
				if($partie->ifdparameters) 
				{
					$fichier = array( 'nom' => trouverNom($partie->dparameters),
					'numero_partie' => $num_partie,
					'encodage' => $partie->encoding,
					'taille' => $partie->bytes);
					$retour['fichiers'][] = $fichier;
				}
				elseif ($partie->ifparameters) 
				{
					$fichier = array( 'nom' => trouverNom($partie->parameters),
					'numero_partie' => $num_partie,
					'encodage' => $partie->encoding,
					'taille' => $partie->bytes);
					$retour['fichiers'][] = $fichier;		
				}
			break;
		
		}//fin du switch
	}// Fin de la condition else
	return $retour;
}

/**
 * Extrait le nom de fichier correspondant a un objet cree par la propriete dparameters d'un objet de imap_fetchstructure()
 * 
 * @param array $mailbox. Tableau d'objets ayant une propriete attribute 
 *
 * @return string.
 */
function trouverNom($dparam)
{
	 $nomFichier = "";
	 for ($i=0;$i<sizeof($dparam);$i++)
	 {
		 if ( ($dparam[$i]->attribute == "filename") || ($dparam[$i]->attribute =="name") 
		   || ($dparam[$i]->attribute == "FILENAME") || ($dparam[$i]->attribute =="NAME") 	
		 )
		 {
		 	$nomFichier = $dparam[$i]->value;
		 }
	 }
	 return $nomFichier;
}

/**
 * Decode un champ d'en tete mime en chain UTF 8
 * 
 * @param string $texte. Chaine a decoder
 *
 * @return string.
 */
function decodeMime($texte)
{
	return iconv_mime_decode($texte,0,'UTF-8');
}

/**
 * Recupere un fichier contenu dans un mail de type MULTIPART.
 * 
 * @param reource $mbox. Flux vers la boite de messagerie 
 * @param integer $id_msg. L'id du message dans lequel extraire le fichier
 * @param integer $parts. La section du message dans lequel se trouve le fichier
 * @param integer $encodage. L'encodage du fichier
 *
 * @return bytes.
 */
function recupererFichier($mbox,$id_msg, $parts, $encodage)
{
	$file = imap_fetchbody($mbox, $id_msg, $parts);
	$ret = decoder($file, $encodage);
	
	return $ret;
}

/**
 * Decode une chaine encodee et la retourne en format UTF-8.
 * 
 * @param string $texte. Donnees a decoder
 * @param integer $encodage. Encodage des donnees
 *
 * @return bytes.
 */
function decoder( $texte, $encodage)
{
	 switch ($encodage)
	 {
	 case 4:
	 	$ret = utf8_encode(imap_qprint($texte));
	 break;
	 case 3:
	 	$ret = imap_base64($texte);
	 break;
	 default:
		 $ret = $texte;
	 break;
	 }
	 
	 return $ret;
}

/**
 * Se connecte a un serveur SMTP
 * 
 * @param string $smtp_serveur. Serveur SMTP
 * @param integer $port. Port du serveur
 * &@param integer $num_erreur. Numero d'erreur retourne
 * &@param integer $message_erreur. Message d'erreur retourne
 * @param integer $time_out. Time out 
 *
 * @return resource.
 */
function connect_SMTP($smtp_serveur,$port,$time_out)
{
	$connection=fsockopen($smtp_serveur,$port,$num_erreur,$message_erreur,$time_out);
	if(!$connection)
	{
		throw new Exception("numero de l'erreur:".$num_erreur. "message d'erreur:". $message_erreur);
	}
	else
	{
	//Si la connection est effectuée on lit la réponse du serveur pour placer le pointeur à la ligne suivante 
	//Ainsi la recuperation des donnees futures ne sera pas troublee
		$answer = fgets($connection,515);
		//------DEBUG
		//var_dump($answer); 
		return $connection;
	}
	
}

/**
 * Extrait les donnees d'un flux SMTP
 * 
 * @param resource $connection. Flux vers le serveur SMTP
 *
 * @return string $answer
 */
function get_smtp_data($connection)
{
	$data="";
	while($donnees=fgets($connection,515))
	{
		$data.=$donnees;
		if(substr($donnees,3,1)==' ')
		{
			break;
		}
	}
	$answer['code']=substr($donnees,0,3);
	$answer['message']=$data;
	
	return $answer;
}

/**
 * Execute une commmande SMTP
 * 
 * @param resource $connection. Flux vers le serveur SMTP
 * @param string $commande. Commande a executer
 * @param integer $code_valide. Code de retour attendu apres la commande
 * @param string $connection. Message d'erreur a envoyer si la commande echoue
 *
 * @return string $answer
 */
function execCommande($connection,$commande,$code_valide,$message_erreur)
{
	fputs($connection,$commande."\n");
	$answer=get_smtp_data($connection);
	/* -----DEBUG
	echo "<pre>";
	echo $commande;
	var_dump($answer);
	echo "</pre>";
	*/
	if($answer["code"]==$code_valide)
	{return $answer;}
	else
	{throw new Exception($message_erreur);}
}

/**
 * Formate les entetes et le corps d'un email
 * 
 * @param array $from_tab. Contient le nom et l'adresse mail de l'expediteur
 * @param array $to_tab. Contient le(s) nom(s) et le(s) adresse(s) mail(s) de(s) destinataire(s)
 * @param array $message_tab. Contient le message a envoyer sous format .txt et html
 * @param string $domaine. Nom de domaine de l'expediteur
 * @param string $subject. Sujet du message
 *
 * @return string $email
 */
function formaterMail(array $from_tab ,array $to_tab,array $message_tab,$domaine,$subject)
{
			
//************************  *******  MISE EN FORME DES PARAMETRES ******* ************************
	
	//$from est l'adresse mail de l'expediteur ex:MartinoPerez@igeoblg.com et $from_name le nom de l'expediteur ex: Martino Perez'
	$from = encodeHeader($from_tab["from"]);
	$from_name = $from_tab["from_name"]!="" ? encodeHeader($from_tab["from_name"]) : $from; //

	//Adresse mail du destinataire
	for($i=0; $i<count($to_tab["to"]); $i++){
		$to[] = encodeHeader($to_tab["to"][$i]);
		$to_name[] = $to_tab["to_name"][$i]!="" ? encodeHeader($to_tab["to_name"][$i]) : $to[$i];
	}
	//echo "to_name = {$to_name}";
	
	//Message au format plain et html
	$message_txt = $message_tab[0];
	$message_html = $message_tab[1]!="" ? $message_tab[1] : null;
	
	//Determination du symbole de saut de ligne en fonction du serveur mail de destination
	if(!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}#",$to[0]))
	{ $br="\r\n"; }
	else
	{ $br="\n"; }
	
	//Creation de la boundary qui pour le serveur est le repere de separation de chaque partie de notres message// ======
	$boundary = "-----=".md5(rand());
	
//************************  *******  PARAMETRES DE CONNECTION ET ENVOI DE MAIL ******* ************************
	
	//----------------- *************** Création de l'entete de l'email
	$header ="From: \"" .$from_name. "\" <" .$from. ">".$br;  //Adresse de l'expediteur?
	$header .=sprintf("Message-ID: <%s@%s>%s", md5(uniqid(time())), $domaine, $br); //
	//$header .='X-FID: FLAVOR00-NONE-0000-0000-000000000000'.$br;
	$header .="To:".$to[0];  //Le To correspond au nom du destinataire (parametre independant a l'envoi du mail)
	for($i=1; $i<count($to); $i++){
		$header .="," .$to[$i];
	}
	$header .=$br;
	$header .="Reply-to:\"".$from_name. "\"<" .$from. ">";
	//$header ="Reply-to:\"jf.lai@it-si.fr\"<jf.lai@it-si.fr>";
	/*for($i=1; $i<count($to); $i++){
		$header .=",\"" .$to_name[$i]. "\"<" .$to[$i]. ">";
	}*/
	$header .=$br;
	$header .="MIME-Version: 1.0".$br;
	
	if(!empty($subject))
	{
		$header .="Subject:".$subject.$br;
	}
	$header .="Content-Type: multipart/alternative;".$br;
	
	//$header .='Return-Path: <'.from.">\n";
	//Notre boundary qui pour le serveur est le repere de separation de chaque partie de notres message// ======
	$header.=" boundary=\"".$boundary."\"".$br;
	
	//----------------- *************** Création du message
	$message=$br."--".$boundary.$br;
	
	///-----------------Message au format txt
	$message.="Content-type: text/plain; charset=\"ISO-8859-1\"".$br;
	$message.="Content-Transfer-Encoding: 8bit".$br;
	$message.=$br.$message_txt.$br;
	//=====
	$message.=$br."--".$boundary.$br;
	///-----------------Message au format HTML
	$message.="Content-type: text/html; charset=\"ISO-8859-1\"".$br;
	$message.="Content-Transfer-Encoding: 8bit".$br;
	$message.=$br.$message_html.$br;
	//=====
	//=====
	$message.=$br."--".$boundary."--".$br;
	$message.=$br."--".$boundary."--".$br;
	
	$email = $header .$message;
	
	//echo "domaine = {$domaine}";
	return $email;
}

/**
 * Encode une chaine UTF-8 en un entete MIME 
 * 
 * @param string $chaine_UTF8. Chaine UTF-8
 *
 * @return string 
 */
function encodeHeader($chaine_UTF8)
{
	return mb_encode_mimeheader(mb_convert_encoding($chaine_UTF8,"ISO-8859-1","UTF-8")); 
}

?>
