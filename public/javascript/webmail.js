// ========================================= ========================================= =========================================
//*************** --------------  ___________  VARIABLES GLOBALES
// ========================================= ========================================= =========================================
glob = new Array();
glob['boxChecked'] = false;
glob['screen'] = "list";
glob['mailcontentId'] = "";
glob['currentPage'] = 1;
glob["pages_loaded"]= new Array(); glob["pages_loaded"][0] = 1;
glob['limite_mails_par_page'] = 10;

//******* CLES de glob[]: 
//	glob['boxChecked'] = true OR false, si une checkbox a ete cochee ou non
//  glob['screen'] = "list" OR "content" OR "edit" en fonction de l'ecran affiche
//  glob['mailcontentId'] = insteger ,qui est le dernier id de message a avoir ete selectionne

indexAjax = "index.php";
classTab = new Array(".liste_mails", ".wrapper_options", ".envoi_mail");
groupeMails = 5 //Nombre d'emails charges a chaque sequence de rechargement
intervalleRefresh = 2*60*1000; //Intervalle de temps entre chaque rechargment automatique d'emails 
hashEnabled = true;


 // ========================================= ========================================= =========================================
//*************** --------------  ___________  UTILISATION DE LA CLASSE DetectBrowser 
// ========================================= ========================================= =========================================

// Cette classe permet de renvoyer le nom du navigateur utilise, cela nous permettra notamment d'activer la navigation par ancre 
// pour firefox


//--------------------_____________ DEFINITION DE LA CLASSE____________------------------------>	

var DetectBrowser = {  
 init: function () {  
  this.browser = this.searchString(this.dataBrowser) || "An unknown browser";  
  this.version = this.searchVersion(navigator.userAgent)  
   || this.searchVersion(navigator.appVersion)  
   || "an unknown version";  
    
 },  
 searchString: function (data) {  
  for (var i=0;i<data.length;i++) {  
   var dataString = data[i].string; 
   var dataProp = data[i].prop;  
   this.versionSearchString = data[i].versionSearch || data[i].identity;  
   if (dataString) {  
    if (dataString.indexOf(data[i].subString) != -1)  
     return data[i].identity;  
   }  
   else if (dataProp)  
    return data[i].identity;  
  }  
 },  
 searchVersion: function (dataString) {  
  var index = dataString.indexOf(this.versionSearchString);  
  if (index == -1) return;  
  return parseFloat(dataString.substring(index+this.versionSearchString.length+1));  
 },  
 dataBrowser: [  
  {  
   string: navigator.userAgent,  
   subString: "Chrome",  
   identity: "Chrome"  
  },  
  {  string: navigator.userAgent,  
   subString: "OmniWeb",  
   versionSearch: "OmniWeb/",  
   identity: "OmniWeb"  
  },  
  {  
   string: navigator.vendor,  
   subString: "Apple",  
   identity: "Safari",  
   versionSearch: "Version"  
  },  
  {  
   prop: window.opera,  
   identity: "Opera"  
  },  
  {  
   string: navigator.vendor,  
   subString: "iCab",  
   identity: "iCab"  
  },  
  {  
   string: navigator.vendor,  
   subString: "KDE",  
   identity: "Konqueror"  
  },  
  {  
   string: navigator.userAgent,  
   subString: "Firefox",  
   identity: "Firefox"  
  },  
  {  
   string: navigator.vendor,  
   subString: "Camino",  
   identity: "Camino"  
  },  
  {  // for newer Netscapes (6+)  
   string: navigator.userAgent,  
   subString: "Netscape",  
   identity: "Netscape"  
  },  
  {  
   string: navigator.userAgent,  
   subString: "MSIE",  
   identity: "Internet Explorer",  
   versionSearch: "MSIE"  
  },  
  {  
   string: navigator.userAgent,  
   subString: "Gecko",  
   identity: "Mozilla",  
   versionSearch: "rv"  
  },  
  {   // for older Netscapes (4-)  
   string: navigator.userAgent,  
   subString: "Mozilla",  
   identity: "Netscape",  
   versionSearch: "Mozilla"  
  }  
 ]  
   
};  

//--------------------_____________ ACTIVATION(ou non) de la navigation par ancres____________------------------------>	
//==== La navigation en se servant des ancres va nous permettre d'utiliser les boutons precedent et suivant des navigateurs
DetectBrowser.init();
if(DetectBrowser.browser == "Firefox"){

	$(parent).on('hashchange', function(event) {
		event.preventDefault();
		var actionData = parent.location.hash.substring(1,parent.location.hash.length);
		var action = extractMixedData(actionData);
	
		//alert("prefix" + action["prefix"])
		switch(action["prefix"])
		{
			case "inbox":
				setTimeout('showPanel(".liste_mails")',100);
				break;
			case "compose":
				setTimeout('showPanel(".envoi_mail")',100);
				break;
			case "content":
				glob['mailcontentId'] = "#" + action["suffix"] + "_mail_content"
				setTimeout('showContent()',100);
				break;
			default:
				console.log("event");
				setTimeout('showPanel(".liste_mails")',100);
				break;
		}
	
	});
		
	function replaceHash(hash)
	{
		var basePage = top.location.host + top.location.pathname;
		top.location.hash = hash;
	}
}
else{
	function replaceHash(hash) {return;} //Desactivation de la fonction
}


function extractMixedData(data)
{
	var tabData = new Array()
	var isMixed = (data.indexOf("_") != -1) ? true : false;	
	tabData["prefix"] = (isMixed) ? data.substring(0, data.indexOf("_")) : data ;
	tabData["suffix"] = (isMixed) ? data.substring(data.indexOf("_")+1, data.length) : 0;
	return tabData;
}


// ========================================= ========================================= =========================================
//*************** --------------  ___________  EVENEMENTS AU DEMARRAGE POUR LA FRAME DROITE
// ========================================= ========================================= =========================================


if(window.name == "frm-droite")
{
	$(document).ready(function()
	{

		//--------------------- --------------------- --------------------- --------------------- ---------------------
		//--------------------_____________ EVENEMENTS DE LA LISTE DES MAILS ____________------------------------>	
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		
		//--------------------_____________ CLIQUE D'UNE CHECKBOX DE LA LISTE ____________------------------------>	
		$(".ls-case").click(function(){
			glob['boxChecked'] = true //Variable boxChecked qui va servir d'echappement a l'evement$(".mail_row").click
		})
		
		//--------------------_____________ CLIQUE D'UNE LIGNE DE LA LISTE' ____________------------------------>
		$(".liste_mails").on("click","tr",function(event){
			
			if(glob['boxChecked'] == true)
			{
				glob['boxChecked'] = false;
				return;
			}
			//------------- Extraction des parametres de la ligne
			var mailRowId = $(this).attr("id")
			
			var numMail = extractId(mailRowId)
			glob['mailcontentId'] = "#" + numMail + "_mail_content"
			//glob['screen'] = "content";
			console.log(mailRowId);
			console.log(numMail);
			console.log(glob['mailcontentId']);
			
			//------------- CAS 1: L'email est deja charge => on l'affiche
			if($(glob['mailcontentId']).length > 0)
			{
				replaceHash("content_" + numMail);
				showPanel(glob['mailcontentId'],".wrapper_options")
			}
			//------------- CAS 2: L'email n'est pas charge
			else
			{
				var idMail = new Array(numMail)
				chargerContenuMail(idMail);
			}
		})
		
		
		//--------------------_____________ CLIQUE SUR BOUTON RAFRAICHIR___________------------------------>
		$(".refresh").click(function(){
			console.log("heho")
			refreshMails();
			console.log("here")
		})
		
		//--------------------_____________ CLIQUE SUR BOUTON DE NAVIGATION DE PAGES (<) ou (>)___________------------------------>
		$(".bt-navigation-list").click(function(){
			if($(this).attr("id").length != 0){
				var numPage = parseInt(extractId($(this).attr("id")));
				console.log("pages loaded=" + glob["pages_loaded"]);
				if(!in_array(numPage, glob["pages_loaded"])){
					chargerMails(numPage);
				}
				else{
					glob["currentPage"] = numPage;
					showPanel(".liste_mails");	
					refreshNavButtons(numPage);
				}
			}	
		})
		
		
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		//--------------------_____________ EVENEMENTS DE L'ENVOI DE MAILS ____________------------------------>	
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		
		//--------------------_____________ CONFIGURATION DE L'EDITEUR WYSIWYG ____________------------------------>	
		editor = $("#input_cle").cleditor
		({
			width:"100%",
			height:"100%",
			controls:     // controls to add to the toolbar
						"bold italic underline superscript | color font size " +
						"style | bullets numbering | outdent " +
						"indent | alignleft center alignright justify | undo redo | " +
						"cut copy paste ",
			colors:       // colors in the color popup
						"FFF FCC FC9 FF9 FFC 9F9 9FF CFF CCF FCF " +
						"CCC F66 F96 FF6 FF3 6F9 3FF 6FF 99F F9F " +
						"BBB F00 F90 FC6 FF0 3F3 6CC 3CF 66C C6C " +
						"999 C00 F60 FC3 FC0 3C0 0CC 36F 63F C3C " +
						"666 900 C60 C93 990 090 399 33F 60C 939 " +
						"333 600 930 963 660 060 366 009 339 636 " +
						"000 300 630 633 330 030 033 006 309 303",    
			fonts:        // font names in the font popup
						"Arial,Arial Black,Comic Sans MS,Courier New,Narrow,Garamond," +
						"Georgia,Impact,Sans Serif,Serif,Tahoma,Trebuchet MS,Verdana",
			sizes:        // sizes in the font size popup
						"1,2,3,4,5,6,7",
			styles:       // styles in the style popup
						[["Paragraph", "<p>"], ["Header 1", "<h1>"], ["Header 2", "<h2>"],
						["Header 3", "<h3>"],  ["Header 4","<h4>"],  ["Header 5","<h5>"],
						["Header 6","<h6>"]],
			useCSS:       false, // use CSS to style HTML when possible (not supported in ie)
			docType:      // Document type contained within the editor
						'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
			docCSSFile:   // CSS file used to style the document contained within the editor
						"", 
			bodyStyle:    // style to assign to document body contained within the editor
						"margin:4px; font:10pt Arial,Verdana; cursor:text"
		})[0].focus();
		
		//*************** --------------  ___________  EVENEMENT AU REDIMENSIONNEMENT DE LA FENETRE
		$(window).resize(function()
		{
			$win = $(parent.frames['frm-droite']);
			//console.log($(".envoi_mail").css("padding"));
			//alert(typeof($(window).width()));
			console.log("height panel-top" + $(".envoimail_top").outerHeight());
			console.log("height panel-bot" + $(".envoimail_bot").outerHeight());
			console.log("windows height" + $(window).outerHeight());
			var windowMargin = 10;
			var heightPanels = $(".envoimail_top").outerHeight() + $(".envoimail_bot").outerHeight();
			var leftPadding = $(".envoimail_top label").outerWidth() + 2*windowMargin;
			console.log("heightPanels" + heightPanels);
			var widthPanel = ($(window).width() > 490) ? $(window).width() : 390;
			var heightWindow = ($(window).outerHeight() > 320) ? $(window).height() : 320;
			$("#mail-container").width(widthPanel - leftPadding).height(heightWindow - (heightPanels+2*windowMargin)).offset({left:$(".envoimail_top label").outerWidth()+ windowMargin});
			editor.refresh();
		});
		
		//--------------------_____________ CLIQUE DU BOUTON ENVOYER(mail) ____________------------------------>	
		$(".mail-envoi").click(function(){
			envoiMail();
		});//Fin de l'event $(".buttonPost").click
	
	
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		//--------------------_____________ EVENEMENTS DES CONTENUS DE MAILS ____________------------------------>	
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		
		//--------------------_____________ CLIQUE DU BOUTON SUPPRIMER(dans le mail) ____________------------------------>	
		$(".mail_suppr").click(function(){
				var idMail = new Array();
				idMail[0] = extractId(glob['mailcontentId']);
				supprMail(idMail);
		})
		
		//--------------------_____________ CLIQUE DU BOUTON SUPPRIMER(dans la liste) ____________------------------------>	
		$(".mail_suppr_group").click(function(){
			var idMail = new Array();
				
			$("input[type=checkbox]:checked").each(function(){
				idMail.push($(this).attr("value"));
				console.log("checked=" + $(this).attr("value"));
			})
			//alert(idMail);
			supprMail(idMail);
		})
		
		//--------------------_____________ CLIQUE D'UN LIEN DE TELECHARGEMENT DE PIECE JOINTE ____________------------------------>	
		$(".liste_contenu_mails").on("click",".link_dl",function(){
				var queryString = $(this).find("input[name=link_dl_id]").val() + $(this).find("input[name=link_dl_params]").val()
				var pathDl = indexAjax + "?page=webmail_ctr-download&" + queryString
				console.log(pathDl)
				window.location.href = pathDl;
			}
		);//Fin de l'event $(".buttonPost").click
		
		//--------------------_____________ CLIQUE DU BOUTON REPONDRE(mail) ____________------------------------>	
		$(".mail_answer").click(
			function()
			{
				//------------- Recuperation des parametres du mail
				var mail = extractMailContent(glob['mailcontentId']);
				console.log(mail["from_address"]);
				//------------- Generation du template de reponse puis affichage de l'ecran d'envoi de mail
				var previousMsg = formatAnswer(mail["from"],mail["date"],mail["content"]);
				console.log(previousMsg);
				$("input[name='address']").val(mail["from_address"]);
				$("input[name='subject']").val("RE:" + mail["subject"]);
				showPanel(".envoi_mail");
				$(window).resize();
				$($("#input_cle").siblings("iframe")[0]).contents().find("body").html(previousMsg);
			}
		);//Fin de l'event $(".buttonPost").click
		
		
		//--------------------_____________ CLIQUE DU BOUTON REPONDRE(mail) ____________------------------------>	
		$(".mail_answer_all").click(function(){
			//------------- Recuperation des parametres du mail
			var mail = extractMailContent(glob['mailcontentId']);
			console.log(mail["from_address"]);
			//------------- Generation du template de reponse puis affichage de l'ecran d'envoi de mail
			var previousMsg = formatAnswer(mail["from"],mail["date"],mail["content"]);
			console.log(previousMsg);
			$("input[name='address']").val(mail["from_address"]);
			$("input[name='subject']").val("RE:" + mail["subject"]);
			showPanel(".envoi_mail");
			$(window).resize();
			$($("#input_cle").siblings("iframe")[0]).contents().find("body").html(previousMsg);
		})
		
		//--------------------_____________ CLIQUE DU BOUTON REPONDRE(mail) ____________------------------------>	
		$(".mail_transfer").click(function(){
			//------------- Recuperation des parametres du mail
			var mail = extractMailContent(glob['mailcontentId']);
			//------------- Generation du template de reponse puis affichage de l'ecran d'envoi de mail
			var previousMsg = formatAnswer(mail["from"],mail["date"],mail["content"]);
			$("input[name='subject']").val("Fwd:" + mail["subject"]);
			showPanel(".envoi_mail");
			$(window).resize();
			$($("#input_cle").siblings("iframe")[0]).contents().find("body").html(previousMsg);
			$("input[name='address']").focus();
		})
			
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		//--------------------_____________ EVENEMENTS DU MENU DE GAUCHE ____________------------------------>	
		//--------------------- --------------------- --------------------- --------------------- ---------------------
		
		//-------------  CLIQUE SUR Boite de reception
		parent.frames['frm-gauche'].$(".maillist").click(function(){
			replaceHash("inbox");
			showPanel(".liste_mails");
		})
		
		//-------------  CLIQUE SUR Nouveau message
		parent.frames['frm-gauche'].$(".mailnew").click(function(){
			replaceHash("compose");
			showPanel(".envoi_mail");
			$("#input_cle").cleditor()[0].clear();
			$(window).resize();
		})
		
		//-------------  CLIQUE SUR Messages envoyes
		parent.frames['frm-gauche'].$(".mailsent").click(function(){
			chargerMail("nothing");
		})

	});//Fin de $(document).ready
	 
}

// ========================================= ========================================= =========================================
//*************** --------------  ___________  FONCTIONS
// ========================================= ========================================= =========================================


/**
 * Affiche le contenu d'un mail. Cette fonction est declenchee uniquement si l'on a clique 
 * sur le bouton precedent ou suivant du navigateur
 */
function showContent()
{	
	showPanel(glob['mailcontentId'],".wrapper_options");
}

/**
 * Extraits les differentes infos d'un mail, telles que l'expediteur, la date du mail, le contenu, sujet
 *
 * @param string contentSelector. Le selecteur du mail a extraire.
 * 
 * @return Array
 */
function extractMailContent(contentSelector)
{
	var email = new Array()
	email["from"] = $(contentSelector + " .from").text()
	console.log(contentSelector)
	email["from_address"] = $(contentSelector + " .from");
	email["from_address"] = (email["from_address"].children().length == 0) ? email["from_address"].text() : email["from_address"].children()[0].tagName.toLowerCase();
	email["date"] = $(contentSelector + " .date").text()
	email["content"] = $(contentSelector + " .contenu").html();
	email["subject"] = $(contentSelector + " .subject").text();
	return email;
}

/**
 * Affiche le(s) ecran(s) souhaites et masque ceux deja affiches. 
 *
 * @param string1. Selecteur du panneau a afficher
 * @param string2 ...
 */
function showPanel(argsNbVariables)
{
	//***** L'ecran auquel on souhaite acceder est deja actuellement affiche
	if($(arguments[0]).is(":visible") && arguments[0] != ".liste_mails"){
		return;
	}
	
	//***** Masquage des ecrans visibles
	$('.panel:visible').toggleClass("hide");
	
	console.log("arguments.length" + arguments.length);
	//***** Affichage de ecrans souhaites
	for(var i=0; i<arguments.length; i++)
	{	
		console.log("isvisible first=" + $(".mail_row").eq(0).is(":visible"));
		$(arguments[i]).toggleClass("hide");
		console.log("isvisible mid=" + $(".mail_row").eq(0).is(":visible"));
		
		//---- CAS DE L'AFFICHAGE DE LA LISTE DES MAILS
		if(arguments[i] == ".liste_mails"){
		
			if($(".mail_row:visible").length != 0){
				$(".mail_row:visible").toggleClass("hide"); // Masque tous les elements de liste visibles
			}
			
			//On doit traiter chaque element de la liste individuellement
			var nb_mails_charges = $(".mail_row").length;
			var maxOffset = extractId($(".mail_row").eq(0).attr("id")) - glob['limite_mails_par_page']*(glob["currentPage"]-1);
			var minOffset = maxOffset - glob['limite_mails_par_page']+1;
			minOffset = (minOffset >= 1) ? minOffset : 1;
			
			console.log("currentPage" + glob["currentPage"]);
			console.log("highest id=" + extractId($(".mail_row").eq(0).attr("id")))
			console.log("nb_mails_charges=" + nb_mails_charges)
			console.log("maxOffset=" + maxOffset);
			console.log("minOffset=" + minOffset);
			console.log("isvisible last=" + $(".mail_row").eq(0).is(":visible"));
			
			for(var idMail=maxOffset; idMail >= minOffset; idMail--){
				//console.log("id verif=" + i)
				var listElement = $("#" + idMail + "_mail_header");
				console.log(listElement);
				console.log(listElement.is(":visible"));
				listElement.toggleClass("hide");
			}
		}
	}
}

/**
 * Extrait les mumeros pour les id de la forme #12_*******
 * 
 * @param string selectorName. Nom de l'id dont on souhaite extraire le numero 
 *
 * @return number id
 */
function extractId(selectorName) 
{
	var debut = (selectorName[0] == "#") ? 1 : 0;
	var fin = selectorName.indexOf("_");
	var id = selectorName.substring(debut,fin);
	return id;
}

/**
 * Rafraichit la boite de messagerie par requete AJAX. 
 */
function refreshMails()
{
	if($(".info").is(":visible")) {return;}	
	showMsg("Rafraichissement de la boite de messagerie");
	
	var lastMail = extractId($(".mail_row").eq(0).attr("id"));
	var dataFormatted = "numpage=" + 1 + "&" + "lastmail=" + lastMail
	var request = $.ajax({
		type: "GET",
		url: indexAjax + "?page=webmail_frm-contenu",
		data:dataFormatted
	})
	
	console.log(dataFormatted);
	request.done(function(msg) 
	{
		var data = $(msg)
		console.log("nb contenu" + data.find(".mail_row").length);
		console.log("message" + msg)
		if(msg == "OK"){
			endMsg("Boite de messagerie déja à jour",true);
			console.log("OKKK");
			//$(".info").toggle(); // La boite de mail est deja a jour
		}else{
			//***** Traitement de la liste des mails
			glob["currentPage"] = 1;
			$(".liste_mails table tbody tr:eq(0)").after(data.find(".mail_row")); //----Chargement des elements de liste de mails
			
			//$(".liste_mails").toggleClass("hide"); // Masquage de la liste des mails
			showPanel(".liste_mails"); // Affichage de la liste des mails de la page concernee
		
			//***** Traitement des contenus de mails
			$(".liste_contenu_mails").prepend(data.find(".contenu_mail"));
			refreshNavButtons(1);
			
			//**** Masquage de la boite d'info
			endMsg("Boite de messagerie mise à jour",true);	
			console.log("refreshed");
			//$(".info").toggle(); //$(".info").toggleClass("hide"); 	
		}	
	});
	
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus);
	  $(".info").toggle();
	});
}

/**
 * Charge les mails de la page souhaitee. Se declence par appui du bouton > de la liste des mails. 
 * 
 * @param number idPage. Le numero de la page de la liste a charger.(Rappel: page=1 ---> Mails les plus recents)  
 */
function chargerMails(idPage)
{	
	if($(".info").is(":visible")) {return;}	
	showMsg("Chargement de la page en cours");
	glob["pages_loaded"].push(idPage);
	
	var request = $.ajax({
		type: "GET",
		url: indexAjax + "?page=webmail_frm-contenu&numpage=" + idPage,
	})
	
	request.done(function(msg) 
	{
		var data = $(msg)
		
		//***** Traitement de la liste des mails
		glob["currentPage"] = idPage;
		$(".liste_mails table tbody").append(data.find(".mail_row")); //----Chargement des elements de liste de mails
		console.log($(".liste_mails table tbody"));
		console.log(data.find(".mail_row").eq(0));
		
		//$(".liste_mails").toggleClass("hide"); // Masquage de la liste des mails
		showPanel(".liste_mails"); // Affichage de la liste des mails de la page concernee
		
		//***** Traitement des contenus de mails
		$(".liste_contenu_mails").append(data.find(".contenu_mail"));
		refreshNavButtons(idPage);
		
		//**** Masquage de la boite d'info
		//endMsg("Chargement termine",true);
		$(".info").toggle(); //$(".info").toggleClass("hide"); 
	});
	
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus);
	  $(".info").toggle();
	});
}

/**
 * Charge le contenu du mail souhaite via AJAX.
 * 
 * @param number idMail. Numero du mail a charger
 */
function chargerContenuMail(idMail)
{
	//Si l'action est en cours d'exection on annule les actions demandees
	if($(".info").is(":visible")) {return;}		
	
	var request = $.ajax({
		type: "POST",
		url: indexAjax + "?page=webmail_frm-chargemail",
		data: "idMail=" + idMail 
	})

	showMsg("Chargement du mail en cours");

	request.done(function(msg) 
	{
		var data = $(msg)	
		if(data.find(".entete").length > 0){
			//***** Traitement des contenus de mails
			$(".liste_contenu_mails").append(data);
			glob['mailcontentId'] = "#" + idMail + "_mail_content"
			showPanel(glob['mailcontentId'],".wrapper_options")
		
			//**** Masquage de la boite d'info
			
			$(".info").toggle(); //$(".info").toggleClass("hide"); 
		}
		else{
			endMsg("Erreur innatendue lors du chargement du mail",false);
		}
	});
	
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus);
	  $(".info").toggle();
	});
}			


/**
 * Active ou desactive les boutons (<) et (>) de la liste des mails. Cette fonction doit
 * etre appelee a chaque fois que l'utilisateur bascule de page.
 * 
 * @param number numPage. Numero de la page sur laquelle l'utilisateur se trouve.
 */		
function refreshNavButtons(numPage)
{
	//----- BOUTON PRECEDENT (<)
	if(numPage !=1){
		$(".bt-navigation-list").eq(0).removeAttr("disabled");
		$(".bt-navigation-list").eq(0).attr("id",(numPage-1) + "_next_page")
	}else{
		$(".bt-navigation-list").eq(0).attr("disabled",true);
		$(".bt-navigation-list").eq(0).removeAttr("id");
	}	
		
	console.log("getlowestid")
	console.log(getLowestId(true))
	//----- BOUTON SUIVANT (>)
	if(getLowestId(true) != 1){
		$(".bt-navigation-list").eq(1).attr("id",(numPage+1) + "_next_page")
		$(".bt-navigation-list").eq(1).removeAttr("disabled");
	}else{
		console.log("disable >")
		$(".bt-navigation-list").eq(1).attr("disabled",true);
		$(".bt-navigation-list").eq(1).removeAttr("id");
	}
}


/**
 * Cherche le plus faible id des mails charges. Autrement dit l'id du mail le plus recent.
 * 
 * @param boolean visible. Si ce parametre est egal a true, alors 
 * la recherche de l'id se limitera uniquement aux mails de la page courante.
 *
 * @return number
 */
function getLowestId(visible)
{	
	if(visible){
		var firstElement = $(".mail_row:visible").eq($(".mail_row:visible").length-1)
	}else{
		var firstElement = $(".mail_row").eq($(".mail_row").length-1)
	}

	console.log($(".mail_row:visible").eq($(".mail_row:visible").length-1));
	
	return extractId(firstElement.attr("id"));
}

function supprMail(idMail)
{
	//Si l'action est en cours d'exection on annule les actions demandees
	if($(".info").is(":visible")) {return;}		
	console.log("idMail" + idMail);
	var idFormatted = idMail[0];
	//orderMails(idMail[idMail.length-1]);
	//return;
	if(idMail.length >1)
	{
		for(var i=1; i<idMail.length; i++)
		{
			idFormatted += "-" + idMail[i];
		} 
	}
	console.log(idFormatted);
	
	var request = $.ajax({
		type: "POST",
		url: indexAjax + "?page=webmail_ctr-supprimemail",
		data: "idMail=" + idFormatted 
	})
	
	showMsg("Suppression du mail en cours");
	
	request.done(function(msg) 
	{
		if(msg == "OK"){
			endMsg("Mail(s) supprime(s)",true);
			for(var i=0; i<idMail.length; i++){
				var mailHeader = "#" + idMail[i] + "_mail_header";
				var contenuMail = "#" + idMail[i] + "_mail_content";
				$(mailHeader).remove();
				$(contenuMail).remove();
			}
			//Remise a jour des numeros de mails 
			orderMails(idMail[idMail.length-1]); //L'argument est le plus petit id parmi les mails supprimes		
			showPanel(".liste_mails");
		}
		else{
			endMsg("Erreur lors de la suppression du mail",false);
			showPanel(".liste_mails");
			//alert(msg);
		}
	});
				
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus );
	  showPanel(".liste_mails");
	});
}

/**
 * Remet a jour les id des mails. Necessaire apres une suprression de mail. 
 * 
 * @param number minId. L'id le plus faible parmi le(s) mail(s) supprime(s)
 */
function orderMails(minId)
{
	var maxId = $(".mail_row:visible").eq(0).attr("id");
	var newTabId = new Array();
	var index = parseInt(minId);
	
	//-------------  Remise a jour des id pour les champs suivants
	/* -Elements de la liste de mails + checkbox inclus dans la liste
	 * -Contenu de mails
	 * -Liens vers les pieces jointes
	 */
	for(var i=parseInt(minId); i<=parseInt(maxId); i++){
		console.log($("#" + i + "_mail_header input[type=checkbox]").eq(0));
		if($("#" + i + "_mail_header").length != 0){
			$("#" + i + "_mail_header input[type=checkbox]").val(index); 
			$("#" + i + "_mail_header").attr("id",index + "_mail_header");
			if($("#" + i + "_mail_content input[name=link_dl_id]").length !=0){
				$("#" + i + "_mail_content input[name=link_dl_id]").val("id=" + index);
			};
			$("#" + i + "_mail_content").attr("id",index + "_mail_content");
			index += 1;
		}
		
	}
}

/**
 * Verifie la presence/validite des donnees saisies par l'utilisateur avant l'envoi de mail. Puis envoie le mail par AJAX.
 */
function envoiMail()
{
	var data = new Array();
	
	//Si l'action est en cours d'exection on annule les actions demandees
	if($(".info").is(":visible")) {return;}	
	
	//--------------  ___________ VERIFICATION DE LA VALIDITE DES DONNEES 
			
	//-------------  Verification de l'adresse mail
	var regExpEmail = /^([a-z0-9]+[\.\-_]?)+[a-z0-9]@([a-z0-9]+[\.\-_]?)+[a-z0-9]\.[a-z0-9]{2,4}$/;
	// Permet: adresse1@mail.com, addresse@autre.fr ... etc ,,
	var regExpGroupEmail = /^(([a-z0-9]+[\.\-_]?)+[a-z0-9]@([a-z0-9]+[\.\-_]?)+[a-z0-9]\.[a-z0-9]{2,4})(\s*,\s*(([a-z0-9]+[\.\-_]?)+[a-z0-9]@([a-z0-9]+[\.\-_]?)+[a-z0-9]\.[a-z0-9]{2,4}))*$/;
	
	data['address'] = trim($("input[name='address']").val());
	if(data['address'] == "")
	{
		alert("L'adresse email du destinaiere est vide. Veuillez renseigner votre destinataire.")
		return;
	}
	else if(regExpGroupEmail.test(data['address']) == false)
	{
		alert("L'adresse email du destinataire n'est pas valide, veuillez vous assurer que l'adresse est à un format valide.");
		return;
	}

	//-------------  Verification du sujet
	data['subject'] = trim($("input[name='subject']").val());
	if( data['subject'] == "")
	{
		if (confirm("Souhaitez vous vraiment envoyer votre message sans objet?") == false)
		{return;}
	}

	//-------------  Verification du contenu du corps du message
	var field= $("#input_cle").cleditor()[0].select();
	//On doit utiliser un setTimeout a 0 pour que le select() ci-dessus s'execute avant les instructions qui suivent
	setTimeout(function()
	{
		data["messageHtml"] = field.selectedHTML();
		data["messageText"] = field.selectedText();

		if(data["messageText"] == "")
		{
			if (confirm("Souhaitez vous vraiment envoyer votre message sans contenu de texte?") == false)
			{return;}
		}
		dataFormatted = "address=" + data['address'] + "&subject=" +  data['subject'] 
		dataFormatted += "&messageHtml=" + data["messageHtml"] + "&messageText=" + data["messageText"]
		
		
		//--------------  ___________ ENVOI DE L'EMAIL
		console.log(dataFormatted)
		console.log(indexAjax + "?page=webmail_ctr-envoimail");
		showMsg("Envoi du mail en cours");
		//-------------  Envoi de la requete ajax
		var request = $.ajax({
			type: "POST",
			url: indexAjax + "?page=webmail_ctr-envoimail",
			data: dataFormatted
		})
		
		//------------- Email envoye
		request.done(function(msg) 
		{
			console.log("end");
			if(msg == "OK"){
				endMsg("Email envoye avec succes",true);
				$("input[name='address']").val("");
				$("input[name='subject']").val("");	
				$("#input_cle").cleditor()[0].clear();
				showPanel(".liste_mails")
			}
			else{
				endMsg("Erreur lors de l'envoir de l'email",false);
				alert(msg);
			}
			
		});	
		
	},0)			
}

/**
 * Formate la chaine de reponse, si l'utilisateur desire repondre a un message.
 * 
 * @param string from. L'expediteur d'origine
 * @param string date. La date d'envoi du message d'origine. 
 * @param string content. Le contenu du message d'origine origine 
 *
 * @return string
 */
function formatAnswer(from,date,content)
{
	var previousMsg = "Le " + date + ", " + from + " a envoyé: <br>";
	previousMsg += "<i>" + content + "</i>";
	return previousMsg;
}

/**
 * Affiche la boite d'info avec le message souhaite. On appelle cette fonction suite a une requete de l'utilisateur pour l'informer
 * du success ou de l'echec de sa demande.
 * 
 * @param string msg. Le message a afficher
 * @param boolean success. Doit etre egal a true si l'on annonce un message de succes sinon false en cas message d'erreur.
 */
function endMsg(msg,success)
{	
	$(".info span").text(msg);
	var color = (success) ? "#D3FFE7" : "#FF9090";
	$(".info").css("backgroundColor", color);
	setTimeout('$(".info").slideToggle("slow")',2000);
}

/**
 * Affiche la boite d'info avec le message souhaite. Le message est juste informatif.
 * 
 * @param string msg. Le message a afficher.
 */
function showMsg(msg)
{
	$(".info span").text(msg);
	$(".info").css("backgroundColor", "#FFFEC5");
	$(".info").slideToggle("slow");
}


//*************** --------------  ___________  FONCTIONS GENERALES

/**
 * Equivalent de trim de php
 *
 * @param string myString.
 *
 * @return string
 */
function trim(myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}   

/**
 * Equivalent de in_array de php
 *
 * @param mixed val. C'est la valeur recherchee. Elle peut soit etre de type string ou number.
 * @param Array tab
 * 
 * @return Boolean
 */
function in_array(val, tab)
{
	for(var i=0; i<tab.length; i++){
		if(tab[i] == val){
			return true;
		}
	}
	return false;
}
