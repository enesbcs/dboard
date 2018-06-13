<?php
 $serverip    = '192.168.1.2'; // Domoticz server IP
 $serverdport = '8081'; // Domoticz API port
 $serverwport = '9001'; // MQTT WebSocket port
 $serveraddr  = "http://".$serverip.":".$serverdport;
 $username    = 'user';
 $password    = 'secret';
 $mqtt_topic1 = "domoticz/in";
 $mqtt_topic2 = "domoticz/out";
 $wuapi       = "xxxxxxxxxxxxxxxx";
 $wulang      = "HU";
 $wucountr    = "HU";
 $wulocation  = "Bekescsaba";

 $disp        = array(
   0 => 
    array(
     'name' => 'Főképernyő',
     'background' => 'bg_noon1.jpg',
     'devs' => array()
    ),
   1 => 
    array(
     'name' => 'Kapcsolók',
     'background' => 'bg_switch.jpg',
     'devs' => array(1,2,3,4,46,5,7,8,10,82,9,11)
    ),
   2 => 
    array(
     'name' => 'Vezérlések',
     'background' => 'bg_switch.jpg',
     'devs' => array(38,93,62,71,85)
    ),
   3 => 
    array(
     'name' => 'Hőmérők',
     'background' => 'bg_thermo.jpg',
     'devs' => array(12,22,39,59,90,53)
    )
 );

 $weather_nexthours = array(3,6,10,15,24,34);
 $clock_width = 6;
 $currentweather_width = 6;
 $sunrise_width = 5;
 $weather_width = 2;
 $mainscreen_order = array('CL','S','WN','W');
 $pagerefresh_sec = 1800; // refreshing every 1/2 hour
 $returnhome_sec = 120;  // return to main page after x sec
?>
