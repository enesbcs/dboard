/*
* MQTT-WebClient
*/
var clientId = "dboard";
clientId += new Date().getUTCMilliseconds();;
var subscription = "#";

if ((wport !== 0) && (wport !== '0')) {
 mqttClient = new Paho.MQTT.Client(hostname, wport, clientId);
 mqttClient.onMessageArrived = MessageArrived;
 mqttClient.onConnectionLost = ConnectionLost;
 Connect();
}

/*Initiates a connection to the MQTT broker*/
function Connect(){
    mqttClient.connect({
    onSuccess: Connected,
    onFailure: ConnectionFailed,
    keepAliveInterval: 10,
    userName: username,
    useSSL: false,
    password: password});
}

/*Callback for successful MQTT connection */
function Connected() {
    console.log("Connected");
    mqttClient.subscribe(subscription);
}

/*Callback for failed connection*/
function ConnectionFailed(res) {
    console.log("Connect failed:" + res.errorMessage);
}

/*Callback for lost connection*/
function ConnectionLost(res) {
    if (res.errorCode !== 0) {
	console.log("Connection lost:" + res.errorMessage);
	Connect();
    }
}

/*Callback for incoming message processing */
function MessageArrived(message) {
   devstr = ""
   if ((message.destinationName == channelin) || (message.destinationName == channelout)) {
    if (message.payloadString.indexOf("{") >= 0) {
     var mqttmsg = JSON.parse(message.payloadString);
     if (watcheddevs.indexOf(mqttmsg.idx) > -1) { // if idx in list!!
      if (message.destinationName == channelout) { // watch for switching
       console.log(mqttmsg.idx+ " "+ mqttmsg.name);
       if (mqttmsg.switchType == "Selector") {
        devstr = "Selector ";
        devstr += " I:"+mqttmsg.idx+" L:"+(mqttmsg.svalue1);
        UpdateSelector(mqttmsg.idx,mqttmsg.svalue1);
       } else {
        if (mqttmsg.switchType == "On/Off") {
         devstr = "Switch ";
         devstr += " I:"+mqttmsg.idx+" S:"+mqttmsg.nvalue;
         UpdateSwitch(mqttmsg.idx, mqttmsg.nvalue);
        }

        if (devstr !== "") {
         console.log(devstr);
        }

       }
      } else { //sensors - channelin
       if (mqttmsg.svalue) {
         if (mqttmsg.svalue.indexOf(";") >= 0) {
          devstr = "Temperature";
         } else {
          devstr = "Sensor";
         }         
         devstr += " I:"+mqttmsg.idx+" N:"+mqttmsg.nvalue+" S:"+mqttmsg.svalue;
       } else {
        UpdateData("d"+mqttmsg.idx, mqttmsg.nvalue);
       }
         if (devstr !== "") {
          console.log(devstr);
          if (mqttmsg.svalue.indexOf(";") >= 0) {
           rval = mqttmsg.svalue.split(";");
           for (i=0;i<rval.length;i++) {
            UpdateData("d"+mqttmsg.idx+"_"+(i+1), rval[i]);
           }
          } else { UpdateData("d"+mqttmsg.idx, mqttmsg.svalue);}

         }


      } // sensor end
     } // if idx in list end!!
    } // JSON decode end
   }


}