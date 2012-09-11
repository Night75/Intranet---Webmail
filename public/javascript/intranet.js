var pathIndex = "index.php";
var pathIndexfrm = "index-frmset.php";
var frameRight= parent.frames['frm-droite'];

// ----------- Gestionnaire d'evenements -----------
/*
if(window.name == "frm-droite")
{
	window.addEventListener
	(
		"load",
		function()
		{	
			// --------- Path de l'objet document de la frame "frm-droite"
		
			
			// --------- Redimensionnement 
	
			// --------- 
			if(getPageName(frameRight)=="frm-listemail")
			{
				document.getElementById("supprimer").addEventListener
				(
					"click",
					function()
					{
						delMultipleMessages();
					},
					false
				)
			}
			
		
			// --------- Evenements pour la page envoimail
			if(getPageName(frameRight)=="frm-envoimail")
			{
				//Activation de l'ecriture sur l'iframe
				document.getElementById('message').contentWindow.document.designMode = "on";	
				
				window.addEventListener
				(
					"resize",
					function()
					{
						sizeIframe()
					},
					false
				)
				
				// --------- Boutons envoyer
				document.getElementById("buttonPost1").addEventListener
				(
					"click",
					function()
					{
						document.getElementById('mail').submit();
					},
					false
				)
			}
			
		},
		false
	)
}
*/

//--------- Dimensionne l'Iframe du message -----------
function sizeIframe()
{
	var size = document.getElementById('wrapper_message').scrollHeight;
	var size = parent.frames['frm-droite'].innerHeight
	//Taille du padding en px
	size -= 167
	if (size<150)
	{
		size=150;
	}
	document.getElementById('message').setAttribute('height',size);
	//alert(size);
}


// ----------- Soumet les divers champs du formulaire -----------
function postMail()
{
 	var x = document.getElementById('message').contentWindow.document.body.innerHTML;
 	//alert(x);
 	var form = document.getElementById('mail');
 	var texte = document.createElement('input');
 	texte.setAttribute('type','text');
 	texte.setAttribute('name','message');
 	texte.setAttribute('value',x);
 	texte.setAttribute('style','display:none');
	form.appendChild(texte);
	document.forms['mail'].submit();
}

function delMultipleMessages()
{
	var tab = document.getElementsByName("supprimer[]");

	var  j = 0;
	var message = new Array();
	
	for(var i=0; i<tab.length; i++)
	{ 
	    if (tab[i].checked == true)
	    {
			message[j] = tab[i].value;
			j++;
		}
	}
	
	var get=extractUrl(parent.frames['frm-droite']);
	var del=confirm("Voulez vous vraiment supprimer les messages cochés");
	
	if(del)
	{	
		var adresse = pathIndexfrm +"?page=general_webmail_ctr-supprimemail&numpage=" + get['numpage'] + "&message=" + message.join('-');
		window.location = adresse;
	}
	
}

function delMessage()
{
	var get=extractUrl(parent.frames['frm-droite']);
	var del=confirm("Voulez vous vraiment supprimer ce message");
	if(del)
	{	
		var adresse = pathIndexfrm +"?page=general_webmail_ctr-supprimemail&numpage=" + get['numpage'] + "&message=" + get['message'];
		window.location = adresse;
	}
	else
	{
		
	}
}

function extractUrl(frame)
{
	//url du type contenu_mail.php?page1&message=2
	var t=frame.location.search.substring(1).split('&');
	var get=new Array();
	
	for(var i=0;i<t.length;i++)
	{
		var tab=t[i].split('=');
		get[tab[0]]=tab[1];
	}
	return get; // Retourne un tableau associatif du type {'page':1;'message':2}
}


function getPageName(frame)
{
	var get = extractUrl(frame);
	var pagePath = get['page'].split('_');     
	var page = pagePath[pagePath.length-1];
    return page;
}

function open(page,data)
{
	var form = document.createElement('form');
	form.setAttribute('action',page);
	form.setAttribute('method','post');
	
	for(nameData in data)
	{
		var inputData = document.createElement('input');
		inputData.setAttribute('type','hidden');
		inputData.setAttribute('name',nameData);
		inputData.setAttribute('value',data[nameData]);
		form.appendChild(inputData);
	}
	document.body.appendChild(form);
	form.submit();
}

