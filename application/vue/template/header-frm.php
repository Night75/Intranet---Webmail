<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name='google-site-verification' content='aw4ANMIzMN6fmw2cOYCOlALWfz39f3ic1-mIOYrcvGU' />
	
	<!-- ICONE -->
	<link rel="shortcut icon" href="<?php echo $pathImage;?>generique/favicon.ico" />
	
	<!-- FICHIERS CSS-->
	<?php for($i=0;$i<count($pageCss)-1;$i++){?>
		<link rel="stylesheet" type="text/css" href="<?php echo $pathCss .$pageCss[$i] .'.css'; ?>"/>
	<?php } ?>
	
	<!-- FICHIERS JAVASCRIPT-->
	<?php for($i=0;$i<count($pageScript)-1;$i++){?> 
		<script type="text/javascript" src=" <?php echo $pathScript .$pageScript[$i] .'.js'; ?>"></script>
	<?php } ?>
	
</head>

