$(document).ready(function(){

// ========================================= ========================================= =========================================
//*************** --------------  ___________  EVENEMENTS DE LA PAGE REGISTER
// ========================================= ========================================= =========================================
	
	//--------------------_____________ CONFIGURATION DU FORMULAIRE (necessite jquery.validate.js)____________------------------------>	
	$("#form-register").validate({
		// --------------- Validation de champs a l'evenement onfocusout
		//onfocusout: true, // active par defaut
		
		// --------------- Fonction appelee en cas de soumission d'un formulaire invalide
		//invalidHandler: function(form,validator){},
		
		// --------------- Fonction appelee en cas de soumission d'un formulaire valide
		//submitHandler: function(form){},
		
		// --------------- Classe d'un element valide
		success: "valid", 
		
		// --------------- Classe d'un element invalide et du message d'erreur associe
		errorClass: "error", 
					
		// --------------- Criteres de validation
		rules:{
			name: "required",
			firstName: "required",
			email: {
				email: true,
				required: true
			},
			password: {
				required: true,
				minlength: 5
			},
			password_confirm: {
				required: true,
				equalTo: "#password"
			}
		},
		// --------------- Messages correspondant aux erreurs de validation
		messages:{
			name: "Veuillez entrez votre nom",
			firstName: "Veuillez entrer votre prenom",
			email:{
				required: "Veuillez entre une addresse email",
				email: "Veuillez entrer une addresse email valide"
			},
			password:{
				required: "Veuillez entrer un mot de passe",
				minlength: "Le mot de passe doit comporter au moins {0} caracteres"
			},
			password_confirm:{
				required: "Veuillez entrer un mot de passe",
				equalTo: "Les mots de passe doivent se correspondre"
			}
		}
	});
})
