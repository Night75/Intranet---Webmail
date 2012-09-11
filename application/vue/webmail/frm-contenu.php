<div id="container">
<!-- ================================= *********************** ===============================================
- ---------------------------				LISTE DES MAILS 			---------------------------
======================================= ********************** =========================================== -->
<div class="info" style="display:none;"><span>Chargement</span></div>
<div class="liste_mails panel">
	<form action="delete_message.php" method="post">
		<div class="boutons_liste_mails">
			<div>
				<!--<a><input type="button" value="Tout cocher" id="toutCocher"/></a>-->
				<a class="bt bt-blue refresh"><img src="<?php echo $pathImage?>webmail/Refresh.png"/>Rafraichir</a><!--
				--><a class="bt bt-red mail_suppr_group"><img src="<?php echo $pathImage?>webmail/cross.png"/>Supprimer</a>
			</div>
			<!-- ***** ***************** Liens vers les autres listes de mails ***************** ***** -->
			<div class="droite" >
				<a>
		  			<input class="bt-navigation-list" type="button" value="<" disabled />
				</a>
				<a>
		 		  <input class="bt-navigation-list" type="button" value=">" <?php if($debut_liste_atteint): ?> disabled <?php else: ?> id="2_next_page" <?php endif ?> />
				</a>	
			</div>
		</div>	  
	
		<!--------------------_____________ LISTE DES MESSAGES ____________------------------------>	
		<table>
			<tbody>
			
				<!-- ***** ***************** Headers du tableau des messages ***************** ***** -->
				<tr>
					<th class="column1">Sélectionné</th>
					<th class="column2">Message de</th>
					<th class="column3">Objet</th>
					<th	class="column4">Date</th>
					<!--<th class="column4">Taille</th>-->
				</tr>
				
				<!-- ***** ***************** Liste des messages ***************** ***** -->
				<?php for($i=0;$i<count($idTab);$i++){ ?>
				
					<tr class="mail_row" id="<?php echo $idTab[$i]; ?>_mail_header">
						<td>
							<!-- *****  Checkbox  ***** -->
							<a class="ls-case">
								<label for="case"><input type="checkbox" name="supprimer[]" value="<?php echo $idTab[$i]; ?>"/>
								</label>
							<a>
						</td>
						<td>	
							<!-- *****  Expediteur du message ***** -->
							<a class="ls-sender">
								 <?php echo $from[$i] ?>
							</a>
						</td>
						<td>
							<!-- *****  Sujet du message ***** -->
							<a class="ls-subject">	
								<?php echo $subject[$i]; ?>
								<!-- *****  Icone de piece jointe ***** -->			
								<span class="attachment">
								<?php if(!empty($fichiers[$i])) { ?> 
									<img src ="<?php echo $pathImage ?>webmail/mail-attachment.png"/> 
								<?php }	?>
								</span>		
							</a>		
						</td>
						<td>
							<!-- *****  Date du message ***** -->
							<a class="ls-date"> <?php echo "Le {$date[$i]}" ?> </a>			
						</td>
					</tr>
					
				<?php } ?>
				
			</tbody>
		</table>
		
	</form>
</div>



<!-- ================================= *********************** ===============================================
- ---------------------------			CONTENUS DES MAILS 			---------------------------
======================================= ********************** =========================================== -->

<div class="wrapper_options hide panel">
	<form class="options_mail">
			<a class="bt bt-orange mail_answer"><img src="<?php echo $pathImage?>webmail/Reply.png"/>Repondre</a><!--
			--><a class="bt bt-orange mail_answer_all"><img src="<?php echo $pathImage?>webmail/Reply.png"/>Repondre a tous</a><!--
			--><a class="bt bt-purple mail_transfer"><img src="<?php echo $pathImage?>webmail/Transfer.png"/>Transferer</a><!--
			--><a class="bt bt-red mail_suppr"><img src="<?php echo $pathImage?>webmail/cross.png"/>Supprimer</a>
	</form>
</div>

<div class="liste_contenu_mails">
	<?php for($i=0; $i<count($idTab); $i++){?>

	<div class="contenu_mail hide panel" id="<?php echo $idTab[$i]?>_mail_content">
	
		<div class="entete">

			<div class="entete_gauche">	
				<p><b>De:</b> <span class="description from"><?php echo $from[$i] ?></span></p>
				<p><b>A:</b> <span class="description to"><?php echo $to[$i] ?></span></p>
				<p><b>Objet:</b> <span class="description subject"><?php echo $subject[$i] ?></span></p>
			</div>
	
			<div class="entete_droite">
				<p><b>Date:</b> <span class="description date"><?php echo $date[$i] ?></span></p>
					<ul>
					<!-- S'il existe une ou des pièces jointes, une liste des noms des pièces est générée' -->
					<?php if (isset($fichiers[$i])) { ?>					
						<li class="desciption"><?php echo count($fichiers[$i]); ?> Pièce(s) jointe(s): </li>
						<?php foreach($fichiers[$i] as $fichier) {	?>
							<li>
								<img height="22px" width="22px" src="<?php echo $pathImage ?>webmail/fichier.png"/>
								<a><?php echo $fichier["nom"] ?></a>
								<br/>	
								<a class="link_dl">Telecharger
									<input type="hidden" name="link_dl_id" value="<?php echo 'id='.$idTab[$i] ?>"><!-- L'ID du mail est variable, on le separe du reste pour faciliter sa modification via javascript-->
									<input type="hidden" name="link_dl_params" value="<?php echo '&nom=' .$fichier['nom'].'&partie='.$fichier['numero_partie'] .'&encodage=' .$fichier['encodage'] .'&taille=' .$fichier['taille'].'&type=' .$fichier['type'] .'&subtype=' .$fichier['subtype']?>">
								</a>
							
							</li>
					<?php	}
						}
					?>
				</ul>
			</div>
		</div> <!-- fin de la classe en tete-->

		<div class ="contenu">	
			<?php echo $body[$i] ?>
		</div>
	
	</div> <!-- fin de la classe contenu mail-->
	
	<?php } //Fin de la boucle?>
</div>


<!-- ================================= *********************** ===============================================
- ---------------------------			ECRITURE DE MAILS 			---------------------------
======================================= ********************** =========================================== -->
<div class="envoi_mail hide panel">
	<form id="mail" name="mail" action="" method="post" ENCTYPE="multipart/form-data" >	
		
		<div class="envoimail_top">	
			<p><label for="address">À</label><input type="text" name="address" class="dataTosend"></p>
			<p><label for="subject">Sujet</label><input type="text" name="subject" class="dataTosend"></p>
			<input type="file" name="fichier_1">
		</div>
	
		<div id="wrapper_message">
			<div id="mail-container">
				<textarea id="input_cle" name="input"></textarea>
			</div>
		</div>
		
		<div class="envoimail_bot">
			<a class="bt bt-green mail-envoi"><img src="<?php echo $pathImage?>webmail/Send.png"/>Envoyer</a>
		</div>
			
	</form>
</div>
</div> <!-- Fin de #container-->
