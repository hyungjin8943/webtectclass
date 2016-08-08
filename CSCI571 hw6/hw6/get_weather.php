<html>
<head>
<title> Hyung-Jin Kim CSCI571 Homework4 </title>
<body>

<script type="text/javascript">
function checkInput(){
	var location = document.forms["myform"]["LOC"].value;
	var type = document.forms["myform"]["types"].value;
	var temp = document.forms["myform"]["temperature"].value;
	if (!location){
		
		alert ("Please enter a location");
		return false; 
	}
	else{
	// validation for zipcode 
	if(type=="city"){
	 var re=/^[a-zA-Z0-9_\s\-\,]*$/;
		if(re.test(location))
		{
			return true;
		}
		else {
			alert("invlalid city name format");
			return false;
		}
	
	}
	if(type=="zipcode"){
		var re=/^\d{5}([\-]\d{4})?$/;
		if(re.test(location))
		{
			return true;
		}
		else {
			alert("invlalid zipcode");
			return false;
		}

	}


	}
	return true;
}
</script>


</head>


<center>
Weather Search
<form style = "padding:5px;text-align:left; width:350px; height:120; border:5px double;" name="myform" method="post" onsubmit ="return checkInput()" action ="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" >
Location: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
<input style = "margin:5px;" type="text" name="LOC" maxlength="155" size="25" /><br>
Location Type:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<select style = "margin:5px;"name="types">
<option value ="city" selected>City 
<option value ="zipcode">Zipcode 
</select><br>
Temperature Unit:  
<input style = "margin:5px;"type="radio" value = "fah" name="temperature" checked="checked" >Fahrenheit  
<input style = "margin:5px;"type="radio" value ="cel" name="temperature">Celsius <br>
<center>
<input style = "margin:5px;"type="submit" name="submit"  value="Search"  />
</center>
</form>
</center>

<?php
if(isset($_POST['submit']))
{	
	$validnum=0;
	$num=0;
	$okay=false;
	$citynum=false;
	$html_text="";

	$myArr = array();
	 $URL ="";
	 $temp="";
	$location= $_POST['LOC'];
	$type = $_POST['types'];
	$tempt= $_POST['temperature'];
	
	//echo"location is $location <br> ";
	//echo"type = $type <br>";
	//echo"$tempt <br>";

	if($tempt =="fah")
	$temp="f";
	else if($tempt=="cel")
	$temp="c";
	
	
	
	
	
	
	if(strcmp("$type","zipcode")==0){
	
	$URL= "http://where.yahooapis.com/v1/concordance/usps/".$location."?appid=FlWxI__V34EDPbccO_k3nU2xOsSNNwKTD_t_7tA8sYSPrzzg7ZU44LnQKKyZGcQ";
	//echo "$URL <br>";
	$xmlfile = simplexml_load_file(urlencode("$URL")); 
	
	$woe=$xmlfile->woeid;
	//echo "$woe <br>";
	$myArr[]=$woe;
	}
	
	
	
	else if($type =="city"){
	//$location = urlencode($location);
	$URL = "http://where.yahooapis.com/v1/places\$and(.q('".$location."'),.type(7));start=0;count=5?appid=FlWxI__V34EDPbccO_k3nU2xOsSNNwKTD_t_7tA8sYSPrzzg7ZU44LnQKKyZGcQ";
	//echo"$URL<br> ";
	
	$xmlfile = simplexml_load_file(urlencode("$URL")); 
	
 // var_dump($xmlfile);

	foreach($xmlfile->place as $place){
		$woe= $place->woeid;
		$myArr[]=$woe;
		}
	}
		$arrlength=count($myArr);
		
		//checking valid woeid
		if($arrlength!=0){
		
		for($x=0;$x<$arrlength;$x++){ 
		
			$URL="http://weather.yahooapis.com/forecastrss?w=".$myArr[$x]."&u=".$temp;
			$xmlfile = simplexml_load_file(urlencode("$URL")); 
			$validcity=$xmlfile->channel->title;
			if(strcmp("$validcity","Yahoo! Weather - Error")==0 ){
			}
			else {
			$validnum++;
				if(!$citynum){
				$city=$xmlfile->channel->children('yweather', true)->location->attributes()->city ;
				$citynum=true;
				}
			}
			}
			
			if($validnum>0){
			$okay=true;
			
			}

}			
		
		if ($okay){
		for($x=0;$x<$arrlength;$x++){  // need to change 1 to arrlength
		//	echo $myArr[$x];
		//	echo "<br>";
			$URL="http://weather.yahooapis.com/forecastrss?w=".$myArr[$x]."&u=".$temp;
			//echo "$URL <br>";
			$xmlfile = simplexml_load_file(urlencode("$URL")); 
		//print_r($xmlfile);
		
		$validcity=$xmlfile->channel->title;
			if(strcmp("$validcity","Yahoo! Weather - Error")==0 ){
			//echo "no city info<br>" ;
			
			}	
		
		else{	
			if($num==0){			
?>
			<script type='text/javascript'>
			var valnum= "<?php echo $validnum;?>";
			var city= "<?php echo $city;?>";
			var type= "<?php echo $type;?>";
  var html_text="<center>";		
		if (type=="city")
		html_text+= valnum+" result(s) for City "+city+"<br>"; 
		else 
		html_text+= valnum+" result(s) for Zip Code "+city+"<br>";
			 html_text+="<p><table border='2' style = text-align:center;";
			
             html_text+="<tr> <th>Weather</th><th>Temperature</th><th>City</th><th>Region</th>";
			html_text+="<th>Country</th><th>Latitude</th><th>Longitude</th><th>Links to Details</th></tr>";
			
			</script>
<?php			
	$num++;
			}
			
			$ititle = $xmlfile->channel->item->children('yweather', true)->attributes()->text;
			$tvalue= $xmlfile->channel->item->children('yweather', true)->attributes()->temp;
			$tunit= $xmlfile->channel->children('yweather', true)->units->attributes()->temperature ;
			$city=$xmlfile->channel->children('yweather', true)->location->attributes()->city ;
			$region=$xmlfile->channel->children('yweather', true)->location->attributes()->region ;
			$country=$xmlfile->channel->children('yweather', true)->location->attributes()->country ;
			$lat=$xmlfile->channel->item->children('geo', true)->lat;
			$long=$xmlfile->channel->item->children('geo', true)->long;
			$detail=$xmlfile->channel->link;
			
			//parsing image src
			$descript= $xmlfile->channel->item->description;
			$imgp = '/src="(.*?)"/i';
			preg_match($imgp, $descript, $matches);
			$weather= $matches[1];
			
			
			
			
			
			
			?>
			<script type='text/javascript'>	 //getting the wether icon to js
				var icon= "<?php echo $weather;?>";
				var itext= "<?php echo $ititle;?>";
				var tvalue= "<?php echo $tvalue;?>";
				var tunit= "<?php echo $tunit;?>";
				var iurl= "<?php echo $URL;?>";
				var city= "<?php echo $city;?>";
				var region= "<?php echo $region;?>";
				var country= "<?php echo $country;?>";
				var lat= "<?php echo $lat;?>";
				var longt= "<?php echo $long;?>";
				var detail= "<?php echo $detail;?>";
				
				if(!icon){
				icon="N/A";
				}
				if(!itext){
				itext="N/A";
				}
				if(!tvalue){
				tvalue="N/A";
				}
				if(!tunit){
				tunit="N/A";
				}
				if(!iurl){
				iurl="N/A";
				}
				if(!city){
				city="N/A";
				}
				if(!region){
				region="N/A";
				}
				if(!country){
				country="N/A";
				}if(!lat){
				lat="N/A";
				}
				if(!longt){
				longt="N/A";
				}
				
				
				
				
				
				html_text+="<tr>"
				html_text+="<td><a href='"+iurl+"'><img src='"+icon+"' alt='"+itext+"' title='"+itext+"' width='70' height='50'></a></td>";
				html_text+="<td>"+itext+" "+tvalue+"&deg; "+tunit+"</td>";
				html_text+="<td>"+city+"</td>";
				html_text+="<td>"+region+"</td>";
				html_text+="<td>"+country+"</td>";
				html_text+="<td>"+lat+"</td>";
				html_text+="<td>"+longt+"</td>";
				if(!detail)
				html_text+="<td>N/A</td>";
				else 
				html_text+="<td><a href='"+detail+"'>Details</a></td>";
				html_text+="</tr>";
			</script>	
			
			
<?php
			
			
		}


		}

		}
		// no results found for woeid
	
		else {
		
		echo "<center><b>Zero results found!!!</b></center>";
		 
		
		}
		if($okay){
		
		?>
			<script type='text/javascript'>	
			html_text+="</table></p></center>";
			</script>
			<?php
			
					echo "<script type='text/javascript'>
			document.write(html_text);
			</script>";
			
			$num=0;
	
		}
}


?>

<noscript>





</body>
</html>
