<div class="contenu_mail hide panel" id="<?php echo $id?>_mail_content">
	
	<?php
		/*echo "<pre>"; 
		var_dump($struct);
		echo '</pre>';*/
	?>
	<div class="entete">

		<div class="entete_gauche">	
			<p>De: <span class="description from"><?php echo $from ?></span></p>
			<p>A: <span class="description to"><?php echo $to ?></span></p>
			<p>Objet: <span class="description subject"><?php echo $subject ?></span></p>
		</div>

		<div class="entete_droite">
			<p>Date: <span class="description date"><?php echo $date ?></span></p>
				<ul>
				<!-- S'il existe une ou des pièces jointes, une liste des noms des pièces est générée' -->
				<?php if (isset($fichiers)) { ?>					
					<li class="desciption"><?php echo count($fichiers); ?> Pièce(s) jointe(s): </li>
					<?php foreach($fichiers as $fichier) {	?>
						<li>
							<img height="22px" width="22px" src="<?php echo $pathImage ?>webmail/fichier.png"/>
							<a><?php echo $fichier["nom"] ?></a>
							<br/>	
							<a class="link_dl">Telecharger
								<input type="hidden" name="link_dl_id" value="<?php echo 'id='.$id ?>"><!-- L'ID du mail est variable, on le separe du reste pour faciliter sa modification via javascript-->
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
		<?php echo $body ?>
	</div>

</div> <!-- fin de la classe contenu mail-->
