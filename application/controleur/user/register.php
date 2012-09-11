<?php

require_once $pathModel."fonctions_user.php";

//--------------------_____________ OPTIONS DE FILTRAGES DES DONNNEES____________------------------------>	

//-------------------- Association   Parametre => Filtre
$options = array(
				"name" => array(
								"filter" => FILTER_CALLBACK,
								"options" => "sanitize_string"
							),
				"firstname" => array(
								"filter" => FILTER_CALLBACK,
								"options" => "sanitize_string"
							),
				"email" => FILTER_VALIDATE_EMAIL,		
				"password" => FILTER_UNSAFE_RAW,
				"password_confirm" => FILTER_UNSAFE_RAW,
				"secret_key" => array(
									"filter" => FILTER_CALLBACK,
									"options" => "validate_key"	
								)
			);

//-------------------- Association  Parametre => Message d'erreur
$messages_erreurs = array(
							"email" => "L'email inscrit est invalide",
							"secret_key" => "La clé de registration est invalide"
						);
						
function sanitize_string($chaine){
	return trim(filter_var($chaine,FILTER_SANITIZE_STRING));
}

function validate_key($key){
	return ($key == $GLOBALS["secret_key"]) ? true : false;		
}

$pile_messages_erreurs = array(); // Initialisation	du tableau de messages d'erreurs


//--------------------_____________ TRAITEMENT DES DONNNEES____________------------------------>	

$data = filter_input_array(INPUT_POST,$options);

if($data != null) // Le formulaire a ete poste
{						
	
	foreach($data as $key => $value){
		if($value === ""){
			$pile_messages_erreurs[] = "Veuillez remplir le champ {$key}";
		}
		elseif($value === false){
			$pile_messages_erreurs[] = $messages_erreurs[$key];
		}
	}
	
	if(empty($pile_messages_erreurs)){
		//----- Le formulaire est valide!
		user_create($users_library,$data["name"],$data["firstname"],$data["email"],$data["password"]);		
		
		header("location:index.php?page=user_register-confirmed");
		exit;
	}
}
else{
	$keys = array_keys($options);
	$data = array_fill_keys($keys,"");
}


?>
