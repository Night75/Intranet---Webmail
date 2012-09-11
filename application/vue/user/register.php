<div id="panel-register">
	<h1>Creation d'utilisateur</h1>
	<?php if(!empty($pile_messages_erreurs)) : ?>
	<div class="bk-error">
		<?php foreach($pile_messages_erreurs as $erreur): ?>
			<p class="error"><?php echo $erreur; ?></p>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<form id="form-register" method="post">
		<p><label for="name">Nom:</label><input type="text" id="name" name="name" value="<?php echo $data['name']?>"></p>
		<p><label for="firstname">Prénom:</label><input type="text" id="firstname" name="firstname" value="<?php echo $data['firstname']?>"></p>
		<p><label for="email">Email:</label><input type="text" id="email" name="email" class="email" value="<?php echo $data['email']?>"></p>
		<p><label for="password">Mot de passe:</label><input type="password" id="password" name="password" value="<?php echo $data['password']?>"></p>
		<p><label for="password_confirm">Confirmation du mot de passe:</label><input type="password" id="password_confirm" name="password_confirm" value="<?php echo $data['password_confirm']?>"></p>
		<p><label for="secret_key">Clé de registration:</label><input type="text" id="secret_key" name="secret_key"></p>
		<input type="submit" value="Valider">
	</form>
<form>
