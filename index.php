<?php
  session_start();
  include_once("config.static.php");?>
<!DOCTYPE html><html lang='hu'><head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<?php
 include_once("config.php");
 // ------------- CONFIG END ---------------
 $pagerefresh_sec = $pagerefresh_sec * 1000;
 $returnhome_sec  = $returnhome_sec * 1000;
 $tabnum = $_REQUEST['tab'];
 if (!isset($tabnum)) { $tabnum = 0; }
 $watchdev = $disp[$tabnum]['devs'];
 if (count($watchdev)<1) { // if no mqtt devices do not start javascript mqtt
  $serverwport = 0;
 }

 echo '<script type="text/javascript">';
 echo 'var hostname = "'. $serverip .'";';
 echo 'var dport = '. $serverdport . ';';
 echo 'var wport = '. $serverwport . ';';
 echo 'var username = "' . $username . '";';
 echo 'var password = "' . $password . '";';
 echo 'var channelin = "' . $mqtt_topic1 . '";';
 echo 'var channelout = "' . $mqtt_topic2 . '";';
 echo 'var watcheddevs = [';
 for ($i=0;$i< (count($watchdev)-1); $i++) {
  echo $watchdev[$i].",";
 }
 echo $watchdev[count($watchdev)-1]."];";
 echo '</script>';
?>
        <script type="text/javascript" src="mqttws31.min.js"></script>
        <script src="db_mqtt.js" type="text/javascript"></script>
        <script src="db_ui.js" type="text/javascript" defer></script>
        <link href="dboard.css" rel="stylesheet">
	<title><?php echo $progid;?></title>
<script type="text/javascript">

function pad(num, size) {
    var s = "000000000" + num;
    return s.substr(s.length-size);
}

function clock()
    {
      var d = new Date();
      var date = d.getDate();
 
      var month = d.getMonth();
      var montharr =["jan","febr","már","ápr","máj","jún","júl","aug","szep","okt","nov","dec"];
      month=montharr[month];
      
      var year = d.getFullYear();
      
      var day = d.getDay();
      var dayarr =["Vasárnap","Hétfő","Kedd","Szerda","Csütörtök","Péntek","Szombat"];
      var wday=dayarr[day];
      
      var hour =d.getHours();
      var min = d.getMinutes();
//      var sec = d.getSeconds();
    
      document.getElementById("clock").innerHTML='<h1 class="clock">'+pad(hour,2)+':'+pad(min,2)+'</h1><h4 class="date">'+ year+". "+month+". "+ date + '</h4><h4 class="weekday">'+ wday + '</h4>';
//      document.getElementById("time").innerHTML=hour+":"+min+":"+sec;
    }

function refreshpage() {
  window.location.reload(true);
 }

function gotohome() {
  window.location.href = 'index.php';
 }

</script>
</head>
<?php

function makeSw($idx,$name,$image,$state) {
 if (($state == 1) or ($state == '1') or ($state == 'On') or ($state == 'ON')) {
  $status = "on";
 } else { $status = "off"; }
 $retstr = '<div data-id="' . $idx . '" id="'. $idx . '" class="mh transbg col-xs-4 hover '. $status .  '" onclick="switchDevice(this)">';
 $retstr .= '<div class="col-xs-4 col-icon"><img src="img/'. $image .'.png" class="icon"></div><div class="col-xs-8 col-data"><strong class="title">'. $name . '</strong></div></div>';
 return $retstr;
}

function makeSens($idx,$name,$image,$data,$unit) {
 $retstr = '<div data-id="' . $idx . '" class="mh transbg col-xs-4 hover" data-toggle="modal">';
 $retstr .= '<div class="col-xs-4 col-icon"><img height=30 src="img/'. $image . '.png" class="icon"></div><div class="col-xs-8 col-data">';
 $retstr .= '<strong class="title" id="d'. $idx . '">' . $data . '</strong>'. $unit .'<br><span class="value">'. $name . '</span></div></div>';
 return $retstr;
}

function makeSel($idx,$name,$image,$state,$levelnames){
$retstr = '<div data-id="'. $idx . '" class="mh transbg col-xs-4"><div class="col-xs-4 col-icon"><img height=30 src="img/'. $image . '.png" class="icon"></div>';
$retstr .= '<div class="col-xs-8 col-data" style="width: calc(100% - 50px);"><strong class="title">'. $name .'</strong><br>';
$retstr .= '<div class="btn-group" data-toggle="buttons"><label class="btn btn-default active"><select id="s'. $idx .'" name="s'. $idx  .'" onchange="selectorchanged(this)">';
$lnames = explode("|",$levelnames);
for ($i=0;$i<count($lnames);$i++) {
 $retstr .= '<option value="'. ($i*10) . '"';
 if (($i*10) == $state) {
  $retstr .= ' selected';
 }
 $retstr .= '>'. $lnames[$i] .'</option>';
}
$retstr .= '</select></div></div></div>';
return $retstr;
}

 $json_string = $serveraddr.'/json.htm?type=devices&filter=all&used=true&order=Name';
 $jsondata = file_get_contents($json_string);
 $obj = (array) json_decode($jsondata,true);

 if (isset($obj['Sunrise'])) {
  $sunrise = $obj['Sunrise'];
  $sunset = $obj['Sunset'];
 }

 $bgpic = $disp[$tabnum]['background'];
 echo "<body style='background-image: url(". '"img/'. $bgpic  .'"' . ");background-size:cover;'>";

//echo $disp[$tabnum]['name'];
?>
<div class="screen">
<?php

 $maxtabs = count($disp);
 $bw = 6;
 if ($maxtabs > 6) {
  $bw = 1;  
 } else {
  if ($maxtabs > 4) {
   $bw = 2;
  } else {
   switch($maxtabs) {
    case 4: $bw = 3; break;
    case 3: $bw = 4; break;
   }
  }
 }
 echo('<div class="col-sm-12 col-xs-12 sortable">');
 for ($i=0;$i<$maxtabs;$i++) {
  $retstr = '<div class="col-xs-'. $bw .' hover transbg buttons-UNKNOWN" data-id="buttons.UNKNOWN" ' . 'onclick="' . "location.href='index.php?tab=" . $i . "'" . ';">';
  $retstr .= '<div class="col-data"><strong class="title"';
  if ($tabnum == $i) { $retstr .= ' style="color:blue;"'; }
  $retstr .= '>' . $disp[$i]['name'] . '</strong></div></div>';
  echo $retstr;
 }
 echo('</div>');

 if ($tabnum == 0) { // main screen start
  $returnhome_sec = 0;
  $cl_str = "";
  if ($clock_width) {
   $cl_str = '<div data-id="clock" id="clock" name="clock" class="transbg block_clock col-xs-'. $clock_width .' text-center">';
   $cl_str .= '</div>';
  }

  $s_str = "";
  if (($sunrise_width) and ($sunrise)) {
   $s_str = '<div data-id="sunrise" class="block_sunrise col-xs-' . $sunrise_width . ' transbg text-center sunriseholder">';
   $s_str .= '<span class="wi-sunrise"><img src="wuimg/sunrise_s.png"></span> <span class="sunrise">' . $sunrise . '</span> &nbsp; ';
   $s_str .= ' &nbsp; <span class="wi-sunset"><img src="wuimg/sunset_s.png"></span> <span class="sunset">' . $sunset . '</span></div>';
  }

  $wn_str = "";
  $w_str = "";
  if ($wuapi) { // WU start
   $cachedmemok  = false;
   $cachedfileok = false; 
   $fname = 'wunderground.json';
   $json_string = "";
   if (isset($_SESSION['wudate'])) {
    $diff = (time()-$_SESSION['wudate'])*1000;
//    echo('MDiff '.$diff);
    if ($diff<=$pagerefresh_sec) {
//       echo('Using mem cached weather data');
       $json_string = $_SESSION['wudata'];
       $cachedmemok = true;
    }
   }
   if ($cachedmemok == false) {
    if (file_exists($fname)) {
     $diff = (time()-filemtime($fname))*1000;
//     echo('FDiff '.$diff);
     if ($diff<=$pagerefresh_sec) {
//      echo('Using disk cached weather data');
      $json_string = file_get_contents($fname);
      if ($json_string !== false) 
       { 
         $cachedfileok = true;
         $_SESSION['wudata'] = $json_string;
         $_SESSION['wudate'] = filemtime($fname);
       }
     }
    }
   }
   if (($cachedmemok == false) and ($cachedfileok == false)) {
//    echo('Downloading weather data');
    $default_socket_timeout = ini_get('default_socket_timeout');
    ini_set('default_socket_timeout', 5);
    $wurl = "http://api.wunderground.com/api/". $wuapi ."/conditions/astronomy/hourly/lang:".$wulang ."/q/". $wucountry ."/". $wulocation . ".json";
    $json_string = file_get_contents($wurl);
    ini_set('default_socket_timeout', $default_socket_timeout);
    if ( ($json_string !== false) and (strlen($json_string) > 4000))  {
     $_SESSION['wudata'] = $json_string;
     $_SESSION['wudate'] = time();
     file_put_contents($fname,$json_string);
    } else {
     $pagerefresh_sec = 60000;
    }
   }
   $wu_parsed = (array) json_decode($json_string,true);

    $wn_str = '<div data-id="currentweather_big" class="mh transbg big block_currentweather_big col-xs-'. $currentweather_width . ' containsweather"><div class="col-xs-3"><div class="weather wi" id="weather">';
    $wn_str .= '<img src="wuimg/' . $wu_parsed['current_observation']['icon'] . '.png"></div></div>';
    $wn_str .= '<div class="col-xs-9" style="line-height: 73px;"><span class="title weatherdegrees" id="weatherdegrees"><strong>'. $wu_parsed['current_observation']['temp_c'] . '°C </strong> <span class="weatherloc" id="weatherloc">';
    $wn_str .= $wu_parsed['current_observation']['display_location']['city'] . '</span></div></div>';

   $hoursavailable = 0;
   if (($weather_nexthours) and (count($weather_nexthours) > 0)) {
     $hoursavailable = count($wu_parsed['hourly_forecast']);
     $wfw = (count($weather_nexthours)*$weather_width);
     if ($wfw > 12) { $wfw = 12; }
     if ($wfw < 1) { $wfw = 1; }
     $w_str = '<div class="weatherfull col-xs-'. $wfw .'">';
     for ($i=0;$i<count($weather_nexthours);$i++) {
       if ( ($weather_nexthours[$i] >= 0) and ($weather_nexthours[$i] < $hoursavailable) ) {
           $o = $weather_nexthours[$i];
	   $w_str .= '<div class="col-xs-'. $weather_width .' transbg"><div class="day">'. $wu_parsed['hourly_forecast'][$o]['FCTTIME']['mon_abbrev'] . " " . $wu_parsed['hourly_forecast'][$o]['FCTTIME']['mday'] . " " . $wu_parsed['hourly_forecast'][$o]['FCTTIME']['weekday_name_abbrev'];
	   $w_str .= "<br>".$wu_parsed['hourly_forecast'][$o]['FCTTIME']['hour_padded'] . ":". $wu_parsed['hourly_forecast'][$o]['FCTTIME']['min']."</div>";
	   $w_str .= '<div class="icon wi"><img src="wuimg/' .$wu_parsed['hourly_forecast'][$o]['icon'] . '.png"></div>';
	   $w_str .= '<div class="temp"><span class="dayT">'.$wu_parsed['hourly_forecast'][$o]['temp']['metric']. '°C</span></div></div>';
       }
     }
     $w_str .= '</div>';
   }

//$weather_nexthours

  } // WU end

 for ($i=0;$i<count($mainscreen_order);$i++) {
  switch($mainscreen_order[$i]) {
   case 'CL': echo($cl_str);
    break;
   case 'WN': echo($wn_str);
    break;
   case 'S': echo($s_str);
    break;
   case 'W': echo($w_str);
    break;
  }
 }


 } else {  // main screen end

 }

  $outstrsw = ""; $outstrss = ""; $outstrte = ""; // Domoticz Devicelist
  foreach ($obj['result'] as $k=>$v) 
  {
   if ( ($v['Type'] !== 'Group') and ($v['Type'] !== 'Scene') and ($v['Used'] == '1') ) {
    if (in_array($v['idx'],$watchdev)) {
//     echo "<br>I:".$v['idx']." N:".$v['Name']." D:".$v['Data']; //debug only
     if ($v['CustomImage'] !== 0) {
      $imgt = $v['Image'];
     } else {
      $imgt = $v['TypeImg'];
     }
//     echo " IMG:".$imgt;

     if (isset($v['SwitchType'])) { 
//      echo " SW:".$v['SwitchType'];
      if ($v['SwitchType'] == 'Selector') {
       $outstrss .= makeSel($v['idx'],$v['Name'],$imgt,$v['Level'],$v['LevelNames']);  //display selector switch
//       echo " L:".$v['Level']." LN:".$v['LevelNames'];
      }
      if ($v['SwitchType'] == 'On/Off') {
       $outstrsw .= makeSw($v['idx'],$v['Name'],$imgt,$v['Data']); // display switch
      }
     } else { // nem kapcsolo
//      echo " T:".$v['Type'];
      if ( strpos($v['Type'],'Temp') !== false ) {
       $temp = $v['Temp']; $hum ='';
       $outstrte .= makeSens($v['idx'],$v['Name'],$imgt,$temp,'°C'); // display temperature
       if ( isset($v['Humidity'] ) ) {
         $hum = $v['Humidity']; 
        }
//       echo " T:".$temp." H:".$hum;
      }


     }


    }
   }
  }
 echo $outstrsw;
 echo $outstrss;
 echo $outstrte;
?>
</div><script type="text/javascript"><?php
 if ($tabnum == 0) {
  echo 'clock(); setInterval(clock,60000);';
 } else {
  if ( ($returnhome_sec) and ($returnhome_sec > 0) ) {
   echo 'setInterval(gotohome,'. $returnhome_sec .');';
  }
 }
 if ( ($pagerefresh_sec) and ($pagerefresh_sec > 0) ) {
  echo 'setInterval(refreshpage,'. $pagerefresh_sec .');';
 }
?></script></body></html>