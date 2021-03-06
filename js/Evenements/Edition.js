function testStatus(a)
{
	var x = a.selectedIndex;
	if(x==2)
	{
		document.getElementById("statusAnnule").style.visibility = "visible";
		document.getElementById("reservationFieldSet").classList.add("middleErrorMessage");
		document.getElementById("reserverLieu").checked = false;
		document.getElementById("reserverLieu").disabled = true;
		newReservation();
	}
	else
	{
		document.getElementById("statusAnnule").style.visibility = "hidden";
		document.getElementById("reservationFieldSet").classList.remove("middleErrorMessage");
		document.getElementById("reserverLieu").disabled = false;
	}
}

function newTimes()
{
	var x = document.getElementById("remplacerHoraires");
	if(x.checked)
		document.getElementById("warnRemplacement").style.display = "block";
	else
		document.getElementById("warnRemplacement").style.display = "none";
}

function testRecurrence(a)
{
	var x = a.selectedIndex;
	if(x!=0)
		document.getElementById("recurrenceEnd").style.visibility = "visible";
	else
		document.getElementById("recurrenceEnd").style.visibility = "hidden";
}

function daysInMonth(month, year)
{
    return new Date(year, month, 0).getDate();
}

function newReservation()
{
	var x = document.getElementById("reserverLieu");
	if(x.checked)
		document.getElementById("reservationLieu").style.display = "block";
	else
		document.getElementById("reservationLieu").style.display = "none";
}

function updateLieux()
{
	$("#lieuxSpan").load("lieuDataList.php?eID="+document.getElementById("espaceSelection").value);
	document.getElementById("inputLieuSelection").value="";
}

function updateDayManifestation()
{
	var maxD = daysInMonth(document.getElementById("manifMonthSelection").value, document.getElementById("manifYearSelection").value);
	var daysList = document.getElementById("manifDaySelection").getElementsByTagName("option");
	for (var i = 0; i < daysList.length; i++)
	{
		(daysList[i].value > maxD) 
		? daysList[i].disabled = true 
		: daysList[i].disabled = false;
	}
	if(document.getElementById("manifDaySelection").value > maxD)
		document.getElementById("manifDaySelection").value = (maxD);
}

function updateDayRecurrence()
{
	var maxD = daysInMonth(document.getElementById("recurMonthSelection").value, document.getElementById("recurYearSelection").value);
	var daysList = document.getElementById("recurDaySelection").getElementsByTagName("option");
	for (var i = 0; i < daysList.length; i++)
	{
		(daysList[i].value > maxD) 
		? daysList[i].disabled = true 
		: daysList[i].disabled = false;
	}
	if(document.getElementById("recurDaySelection").value > maxD)
		document.getElementById("recurDaySelection").value = (maxD);
}

function deleteManif(manifID)
{
	if(typeof manifID === "undefined")
	{
		alert("Undefined manifID");
	}
	else
	{
		if(confirm("Souhaitez-vous vraiment supprimer cette manifestation?\n(Supprimera tous les horaires et toutes les réservations associées.)"))
		{
			delurl="deleteManifestation.php";
			$.ajax
			({
				type: 'POST',
				url: delurl,
				data: {mID:manifID},
				async: false,
				cache: false,
				timeout: 30000,
				success: function(data)
				{
					window.location.href = "/";
				},
				error: function(xhr, status, error)
				{
					document.getElementById("manifEditMessg").innerHTML = (xhr.responseText);
					document.getElementById("manifEditMessg").style.color = "#FF0000";
					//alert(xhr.responseText);
				}
			});
		}
	}
}

function getResponsableValue()
{
	for (var i=0; i<document.getElementById("responsablesSelection").options.length; i++)
	{ 
		if (document.getElementById("responsablesSelection").options[i].value == document.getElementById("inputResponsablesSelection").value)
		{
			return document.getElementById("responsablesSelection").options[i].getAttribute("data-value");
		}
	}
	return null;
}

function getLieuValue()
{
	for (var i=0; i<document.getElementById("lieuSelection").options.length; i++)
	{ 
		if (document.getElementById("lieuSelection").options[i].value == document.getElementById("inputLieuSelection").value)
		{
			return document.getElementById("lieuSelection").options[i].getAttribute("data-value");
		}
	}
	return null;
}

function updateManif(manifID)
{
	document.getElementById("manifEditMessg").innerHTML = "";
	var respo=document.getElementById("responsaBlesSelection").value;
	if(respo==null)
	{
		document.getElementById("manifEditMessg").innerHTML = "Reponsable Manquant.";
		document.getElementById("manifEditMessg").style.color = "#FF0000";
		return;
	}

	var mD=-1;
	var rD=-1;
	var newLieuID = -1;

	if(document.getElementById("remplacerHoraires").checked)
	{
		if(document.getElementById("reserverLieu").checked)
		{
			newLieuID=getLieuValue();
			if(newLieuID==null)
			{
				document.getElementById("manifEditMessg").innerHTML = "Veuillez choisir un lieu réservé dans la liste.";
				document.getElementById("manifEditMessg").style.color = "#FF0000";
				return;
			}
		}
	}

	if(typeof manifID === "undefined")
	{
		alert("Undefined manifID");
	}
	else
	{
		if(confirm("Remplacera les informations actuelles."))
		{
			eddurl="updateManifestation.php";
			$.ajax
			({
				type: 'POST',
				url: eddurl,
				data:
				{
					mID:manifID,
					intitule:document.getElementById("intituleEntry").value,
					type:document.getElementById("typeSelection").value,
					status:document.getElementById("statusSelection").value,
					responsable: respo,
					description:document.getElementById("descriptionText").value,
					observations:document.getElementById("observationsText").value,

					manifDate:document.getElementById("manifDate").value.replace(/-/g,''),
					manifStart:document.getElementById("manifStartTime").value.replace(':',''),
					manifEnd:document.getElementById("manifEndTime").value.replace(':',''),

					lieuID:newLieuID,
					reservStart:document.getElementById("reservStartTime").value.replace(':',''),
					reservEnd:document.getElementById("reservEndTime").value.replace(':',''),

					recurenceID:document.getElementById("recurrenceSelection").value,
					endRecurence:document.getElementById("recurDateEnd").value.replace(/-/g,'')
				},
				async: false,
				cache: false,
				timeout: 30000,
				success: function(data)
				{
					window.location.href = "?menu=evenement&section=detail&eventID="+manifID;
				},
				error: function(xhr, status, error)
				{
					document.getElementById("manifEditMessg").innerHTML = (xhr.responseText);
					document.getElementById("manifEditMessg").style.color = "#FF0000";
				}
			});
		}
	}
}

function upManifStart()
{
	document.getElementById("manifStartTime").stepUp(1);
	upReservStart();
	while(document.getElementById("manifStartTime").value >= document.getElementById("manifEndTime").value)
		document.getElementById("manifEndTime").stepUp(1);
	while(document.getElementById("manifStartTime").value > document.getElementById("reservStartTime").value)
		upReservStart();
}

function downManifStart()
{
	document.getElementById("manifStartTime").stepDown(1);
	downReservStart();
}

function upManifEnd()
{
	document.getElementById("manifEndTime").stepUp(1);
	upReservEnd();
}

function downManifEnd()
{
	document.getElementById("manifEndTime").stepDown(1);
	downReservEnd();
	while(document.getElementById("manifStartTime").value >= document.getElementById("manifEndTime").value)
		document.getElementById("manifStartTime").stepDown(1);
	while(document.getElementById("manifEndTime").value < document.getElementById("reservEndTime").value)
		downReservEnd();
}

function upReservStart()
{
	document.getElementById("reservStartTime").stepUp(1);
	while(document.getElementById("reservStartTime").value >= document.getElementById("reservEndTime").value)
		upReservEnd();
}

function downReservStart()
{
	document.getElementById("reservStartTime").stepDown(1);
	while(document.getElementById("manifStartTime").value > document.getElementById("reservStartTime").value)
		document.getElementById("manifStartTime").stepDown(1);
}

function upReservEnd()
{
	document.getElementById("reservEndTime").stepUp(1);
	while(document.getElementById("manifEndTime").value < document.getElementById("reservEndTime").value)
		document.getElementById("manifEndTime").stepUp(1);
}

function downReservEnd()
{
	document.getElementById("reservEndTime").stepDown(1);
	while(document.getElementById("reservStartTime").value >= document.getElementById("reservEndTime").value)
		downReservStart();
}

