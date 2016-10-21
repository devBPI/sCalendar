<?php
	$allHeaders = getallheaders();
	if(!array_key_exists("AuthUser", $allHeaders))
	{
		echo
			'<div class="middleParts">
				<div class="middlePart">
					<fieldset class="middlePart middleErrorMessage">
						<legend>Authentification Error</legend>'.
						'Access Forbidden'
					.'</fieldset>
				</div>
			</div>'
		;
	}
	else
	{
	$defaultDate = null;
	$defaultDate = getdate();
	if(empty($_GET['date']) || !is_numeric($_GET['date']) || $_GET['date']<=19700000 || $_GET['date']>=20360000)
	{
		$today = getdate();
		$defaultDate = $today[year].(($today[mon]<10)?'0':'').$today[mon].(($today[mday]<10)?'0':'').$today[mday];
	}
	else
		$defaultDate = $_GET['date'];
	$servername = "127.0.0.1";
	$username = "root";
	$password = "password";
	$dbname = "bpi_calendar";

	$conn = mysql_connect($servername, $username, $password, $dbname);
	if ($conn->connect_error)
	{
		echo
			'<div class="middleParts">
				<div class="middlePart">
					<fieldset class="middlePart middleErrorMessage" style="height: 120px;">
						<legend>logs ERROR!</legend>'.
						'Connection failed: ' . $conn->connect_error
					.'</fieldset>
				</div>
			</div>'
		;
	}
	else
	{
		mysql_select_db($dbname, $conn);
		mysql_query('SET character_set_results = "UTF8", character_set_client = "UTF8", character_set_connection = "UTF8", character_set_database = "UTF8", character_set_server = "UTF8"');
		echo
			'<div class="middleParts">
				<div class="middlePart" style="text-align: right;">
					<fieldset>
						<legend>Commandes</legend>
						<table style="width: 100%; border: none; background-color: inherit; border-collapse: collapse;">
							<tr style="border: none; background-color: inherit;">
								<td style="width: 100%; border: none; background-color: inherit;"><span id="manifEditMessg" style="width: 100%; text-align: left; display: inline-block;"></span></td>
								<td style="border: none; background-color: inherit;""><button id="savebutton" type="button" style="margin-left: 2px; margin-right: 2px;" onclick="addManif()">Enregistrer</button></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>'
		;
		echo
			'<div class="middleParts">
				<div class="middlePart">
					<fieldset>
						<legend>Manifestation</legend>'.
						'<label for="intituleEntry">Intitué: </label>'.
						'<input id="intituleEntry" type="text" name="nom" size="40" maxlength="255" /><br />'
		;



		$sql ='SELECT * FROM type_manifestation';

		$req = mysql_query($sql);
		if(!$req)
		{
			echo 'No status found :' . (mysql_errno($conn) . ' : ' . mysql_error($conn));
		}
		else
		{
			echo
						'<label for="typeSelection">Type: </label>'.
						'<select name="typeSelection" id="typeSelection">'
			;

			while($type = mysql_fetch_assoc($req))
			{
				echo
							'<option value="' . $type['ID'] . '">' . $type['type'] . '</option>'
				;
			}

			echo
						'</select><br />'
			;
		}

		$sql ='SELECT * FROM status_manifestation';

		$req = mysql_query($sql);
		if(!$req)
		{
			echo 'No status found :' . (mysql_errno($conn) . ' : ' . mysql_error($conn));
		}
		else
		{
			echo
						'<label for="statusSelection">Status: </label>'.
						'<select onchange="testStatus(this)" name="statusSelection" id="statusSelection">'
			;

			while($status = mysql_fetch_assoc($req))
			{
				if($status['status'] != 'Annulée')
					echo	'<option value="'.$status['ID'].'">' . $status['status'] . '</option>';

			
			}

			echo 		'</select>';
		}

		echo
					'</fieldset>
				</div>'
		;
		echo
				'<div class="middlePart">
					<fieldset class="middlePart">
						<legend>Reponsable</legend>'
		;

		echo			'<label for="intituleEntry">Email: </label>'.
					'<input id="responsaBlesSelection" type="text" name="email" value="'.$allHeaders[AuthUser].'" disabled /><br />'
		;

		/*$sql ='SELECT * FROM responsable ORDER BY nom';

		$req = mysql_query($sql);
		if(!$req)
		{
			echo 'No responsable found :' . (mysql_errno($conn) . ' : ' . mysql_error($conn));
		}
		else
		{
			echo		'<datalist id="responsablesSelection">';

			while($respo = mysql_fetch_assoc($req))
			{
				echo		'<option data-value="'.$respo['ID'].'" value="'.$respo['nom'].'" label="'.$respo['nom'].'">' . $respo['nom'] . '</option>';
			}

			echo
						'</datalist>'.
						'<label for="responsablesSelection">Responsable: </label>'.
						'<input id="inputResponsablesSelection" onchange="getResponsableValue();" list="responsablesSelection" />'
			;
		}*/

		echo
					'</fieldset>
				</div>
			</div>'
		;

		/*$curDate = $defaultDate;
		$curDay = $curDate[mday];
		$curMonth = $curDate[mon];
		$curYear = $curDate[year];*/
		$curDay = substr($defaultDate, 6, 2);
		$curMonth = substr($defaultDate, 4, 2);
		$curYear = substr($defaultDate, 0, 4);
		echo'<div class="middleParts">';
		echo
				'<div class="middlePart">
					<fieldset>
						<legend>Nouveaux Horaires et Reservations</legend>'
		;

		echo
						'<label for="manifDaySelection"> Le: </label>'.
						'<select name="manifDaySelection" id="manifDaySelection">'
		;

		for($i=1; $i<=31; $i++)
		{
			echo
							'<option value="'.$i.'"'. (($i==$curDay)? ' selected' : '') .'>' . $i . '</option>'
			;
		}

		echo			'</select>';
		echo
						'<label for="manifMonthSelection"> - </label>'.
						'<select onchange="updateDayManifestation()" name="manifMonthSelection" id="manifMonthSelection">'
		;

		for($i=1; $i<=12; $i++)
		{
			echo
							'<option value="'.$i.'"'. (($i==$curMonth)? ' selected' : '') .'>' . $i . '</option>'
			;
		}

		echo
						'</select>'
		;
		echo
						'<label for="manifYearSelection"> - </label>'.
						'<select onchange="updateDayManifestation()" name="manifYearSelection" id="manifYearSelection">'
		;

		for($i=(date("Y")-10); $i<=(date("Y")+20)&&$i<=2037; $i++)
		{
			echo
							'<option value="'.$i.'"'. (($i==$curYear)? ' selected' : '') .'>' . $i . '</option>'
			;
		}

		echo
						'</select>'
		;

		echo
						'<label for="manifTimeStartSelection"> De: </label>'.
						'<select name="manifTimeStartSelection" id="manifTimeStartSelection">'
		;

		for($h=0; $h<=23; $h++)
		{
			for($m=0; $m<60; $m+=30)
			{
				echo
							'<option value="'.sprintf('%02d%02d', $h, $m).'"'. (($h==8&&$m==0)? ' selected' : '') .'>' . sprintf('%2d', $h) . 'h' . sprintf('%02d', $m) . '</option>'
				;
			}
		}

		echo
						'</select>'
		;

		echo
						'<label for="manifTimeEndSelection"> à </label>'.
						'<select name="manifTimeEndSelection" id="manifTimeEndSelection">'
		;

		for($h=0; $h<=23; $h++)
		{
			for($m=0; $m<60; $m+=30)
			{
				echo
							'<option value="'.sprintf('%02d%02d', $h, $m).'"'. (($h==8&&$m==0)? ' selected' : '') .'>' . sprintf('%2d', $h) . 'h' . sprintf('%02d', $m) . '</option>'
				;
			}
		}
		echo
							'<option value="2400"'. (($h==8&&$m==0)? ' selected' : '') .'>' . '24h00' . '</option>'
		;

		echo
						'</select>'
		;

		echo			'<br />';

		echo
						'<input onclick="newReservation(this);" type="checkbox" id="reserverLieu" name="reserverLieu" />
						<label for="reserverLieu">Ajouter une reservation.</label>'
		;
		echo
						'<div id="reservationLieu" style="display: none;">'
		;
		$sql ='SELECT * FROM espace';

		$req = mysql_query($sql);
		if(!$req)
		{
			echo 'No espace found :' . (mysql_errno($conn) . ' : ' . mysql_error($conn));
		}
		else
		{
			echo
							'<label for="espaceSelection"> Dans l\'espace: </label>'.
							'<select onchange="updateLieux()" name="espaceSelection" id="espaceSelection">'
			;

			while($espace = mysql_fetch_assoc($req))
			{
				echo
								'<option value="'.$espace['ID'].'">' . $espace['espace'] . '</option>'
				;
			}

			echo
							'</select>'
			;


			echo
							'<datalist id="lieuSelection">'
			;
			echo 			'<span id="lieuxSpan">';
			$eID=1;
			include('lieuDataList.php');
			echo 			'</span>';
			echo
							'</datalist>'.
							'<label for="lieuSelection"> Dans le lieu: </label>'.
							'<input id="inputLieuSelection" onchange="getLieuValue();" list="lieuSelection" />'
			;

			echo
							'<label for="reservTimeStartSelection"> De: </label>'.
							'<select name="reservTimeStartSelection" id="reservTimeStartSelection">'
			;

			for($h=0; $h<=23; $h++)
			{
				for($m=0; $m<60; $m+=30)
				{
					echo
								'<option value="'.sprintf('%02d%02d', $h, $m).'"'. (($h==8&&$m==0)? ' selected' : '') .'>' . sprintf('%2d', $h) . 'h' . sprintf('%02d', $m) . '</option>'
					;
				}
			}

			echo
							'</select>'
			;

			echo
							'<label for="reservTimeEndSelection"> à </label>'.
							'<select name="reservTimeEndSelection" id="reservTimeEndSelection">'
			;

			for($h=0; $h<=23; $h++)
			{
				for($m=0; $m<60; $m+=30)
				{
					echo
								'<option value="'.sprintf('%02d%02d', $h, $m).'"'. (($h==8&&$m==0)? ' selected' : '') .'>' . sprintf('%2d', $h) . 'h' . sprintf('%02d', $m) . '</option>'
					;
				}
			}
			echo
								'<option value="2400"'. (($h==8&&$m==0)? ' selected' : '') .'>' . '24h00' . '</option>'
			;

			echo
							'</select>'
			;

		
		}
		echo
						'</div>'
		;

		echo			'<div>';

		$sql ='SELECT * FROM recurrence_manifestation';

		$req = mysql_query($sql);
		if(!$req)
		{
			echo 'No recurrence found :' . (mysql_errno($conn) . ' : ' . mysql_error($conn));
		}
		else
		{
			echo
							'<label for="statusSelection">Recurrence: </label>'.
							'<select onchange="testRecurrence(this)" name="recurrenceSelection" id="recurrenceSelection">'
			;

			while($recurrence = mysql_fetch_assoc($req))
			{
				echo
								'<option value="'.$recurrence['ID'].'">' . $recurrence['recurrence'] . '</option>'
				;
			}

			echo
							'</select>'.
							'<span id="recurrenceEnd" style="visibility:hidden;">'
			;
			echo
								'<label for="recurDaySelection"> Jusqu\'au: </label>'.
								'<select name="recurDaySelection" id="recurDaySelection">'
			;

			for($i=1; $i<=31; $i++)
			{
				echo
									'<option value="'.$i.'"'. (($i==$curDay)? ' selected' : '') .'>' . $i . '</option>'
				;
			}

			echo
								'</select>'
			;
			echo
								'<label for="recurMonthSelection"> - </label>'.
								'<select onchange="updateDayRecurrence()" name="recurMonthSelection" id="recurMonthSelection">'
			;

			for($i=1; $i<=12; $i++)
			{
				echo
									'<option value="'.$i.'"'. (($i==$curMonth)? ' selected' : '') .'>' . $i . '</option>'
				;
			}

			echo
								'</select>'
			;
			echo
								'<label for="recurYearSelection"> - </label>'.
								'<select onchange="updateDayRecurrence()" name="recurYearSelection" id="recurYearSelection">'
			;

			for($i=(date("Y")-10); $i<=(date("Y")+20)&&$i<=2037; $i++)
			{
				echo
									'<option value="'.$i.'"'. (($i==$curYear)? ' selected' : '') .'>' . $i . '</option>'
				;
			}

			echo
								'</select>'
			;
			echo
							'</span>'
			;
		}
		echo			'</div>';

		echo
					'</fieldset>
				</div>'
		;
		echo
			'</div>'
		;




		echo
			'<div class="middleParts">'
		;
		echo
				'<div class="middlePart" style="width:50%;">
					<fieldset>
						<legend>Description</legend>
						<textarea name="description" id="descriptionText" rows="5" cols="40" style="width:100%; min-height: 120px; resize: vertical;"></textarea>
					</fieldset>
				</div>'
		;
		echo
				'<div class="middlePart" style="width:50%;">
					<fieldset>
						<legend>Observations</legend>
						<textarea name="observations" id="observationsText" rows="5" cols="40" style="width:100%; min-height: 120px; resize: vertical;"></textarea>
					</fieldset>
				</div>'
		;
		echo
			'</div>'
		;
	}
	}
?>