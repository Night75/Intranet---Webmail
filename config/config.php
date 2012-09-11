<?php
//--------------------_____________ PARAMETRES DE LA BASE DE DONNEES ___________------------------------>	
$dsn = "mysql:dbname=mabdd;host=localhost";
$bdd_user = "root";
$bdd_password = "igeoblg";

//--------------------_____________ PARAMETRES POUR LA BASE DE DONNEES VERSION FICHIER TXT ___________------------------------>
$users_library = "config/users.txt";

//--------------------_____________ CHEMIN DES DOSSIERS PRINCIPAUX ____________------------------------>	
$pathIndexFrm= "/intranet/index-frmset.php";
$pathController= "application/controleur/";
$pathView= "application/vue/";
$pathModel="application/modele/";
$pathCss= "/intranet/public/css/";
$pathScript= "/intranet/public/javascript/";
$pathImage="/intranet/public/images/";
$pathDownload="download.php";

//--------------------_____________  ATTRIBUTIONS DES SOURCES PUBLIQUES (css, javascript,img) ____________------------------------>
//Format attendu : : "dossier/" => 'source1.css'
//Ou en cas de fichier particulier, on peut preciser une source qui lui est uniquement lie : "dossier/fichier.php" => "source1.css"

//------------------------ FICHIERS CSS
$filesCss = array("general/" => "webmail.css"."styles.css",
				"user/" => "user.css",
				"webmail/" => "webmail.css"."styles.css",
				"webmail/frm-contenu.php" => "jquery.cleditor.css");
				
//------------------------ FICHIERS JS
$filesScript = array("general/" => "intranet.js",
					"user/" => "jquery.js"."jquery.validate.js"."user.js",
					"webmail/" => "intranet.js",
					"webmail/frm-contenu.php" =>"jquery.js". "jquery.cleditor.js"."webmail.js",
					"webmail/frm-menu.php" => "jquery.js");
					
					
//--------------------_____________  PARAMETRES DU WEBMAIL ____________------------------------>
$mailbox = "{pop.maibox.com:110/pop3}";		 //Adresse du serveur de messagerie mail
$smtp_serveur='smtp.auth.mailbox.com';		 //Adresse du serveur d'envoi de mail
$port=587;
$limite_mails_par_page = 10; 				//Nombre max de mails dans la liste des mails
$nb_contenus_a_charger_max = 10;
$temp_folder = "temp/";
$max_file_size = 3000;
$max_files_total_size = 10000; 


//--------------------_____________ SECURITE___________------------------------>	
$secret_key = "123456789";					//Cle d'inscription secrete

//------------------------ Pages autorisees a un utlisateur anonyme
$pagesAllowed = array(
						"user/login.php",
						"user/ctr-checklogin.php",
						"user/register.php",
						"user/register-confirmed.php"
					); 	// format attendu : dossier/fichier.php

