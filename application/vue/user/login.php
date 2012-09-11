<div id="panel-login">
	<form method="post" action="index.php?page=user_ctr-checklogin">
		<h1>Connexion a l'intranet</h1>
		<p class="bk-error"><?php echo $message;?></p>
		<p><label for="username">Identifiant (email)<input type="text" name="username"></label></p>
		<p><label for="password">Mot de passe<input name="password" type="password"></label></p>
		<input type="submit" value="Se connecter"> 
	</form>

	<a class="bt-register" href="index.php?page=user_register">Créér un compte</a>
</div>
