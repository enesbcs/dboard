/*
 * MQTT WebSocket unit
 */

function UpdateData(ioname, displayData){
    var cell = document.getElementById(ioname);
    if (cell){
       cell.innerHTML=displayData;
     }
    }

function UpdateSelector(ioname, data){
    var cname = 's'+ioname;
    var cell = document.getElementById(cname);
    if (cell){
       cell.value=data;
//       cell.options[(data/10)].selected = 'selected';
//       console.log(data);
     }
    }

function UpdateSwitch(ioname, data) {
    var cell = document.getElementById(ioname);
    data2 = data;
    if (cell){
      if (data == -1) { // invert current status
       if (cell.classList.contains('on')) {
        currentstat = 1; data2 = 0;
       } else { currentstat = 0; data2 = 1;}
      }
      if (data2 == 1) { //set on
       cell.classList.remove('off');
       cell.classList.add('on');
      } else { // set off
       cell.classList.remove('on');
       cell.classList.add('off');
      }
    }
}


function switchDevice(e) { // invert switch state
   commandurl = 'http://'+hostname+':'+dport+'/json.htm?type=command&param=switchlight&idx=' + e.id +'&switchcmd=Toggle';
//   console.log(commandurl);
   var xmlHttp = new XMLHttpRequest();
   xmlHttp.open("GET", commandurl, true); // true for asynchronous
   xmlHttp.send(null);

}

function selectorchanged(select){
   commandurl = 'http://'+hostname+':'+dport+'/json.htm?type=command&param=switchlight&idx=' + select.name.substring(1) +'&switchcmd=Set%20Level&level='+select.value;
//   console.log(commandurl);
   var xmlHttp = new XMLHttpRequest();
   xmlHttp.open("GET", commandurl, true); // true for asynchronous
   xmlHttp.send(null);
}

function clock()
    {
      var d = new Date();
      var date = d.getDate();
 
      var month = d.getMonth();
      var montharr =["Jan","Febr","Már","Ápr","Máj","Jún","Júl","Aug","Szep","Okt","Nov","Dec"];
      month=montharr[month];
      
      var year = d.getFullYear();
      
      var day = d.getDay();
      var dayarr =["Vas","Hét","Ke","Sze","Cs","Pé","Szo"];
      var wday=dayarr[day];
      
      var hour =d.getHours();
      var min = d.getMinutes();
//      var sec = d.getSeconds();

      document.getElementById("clock").innerHTML='<h1 class="clock">'+hour+':'+min+'</h1><h4 class="weekday">'+ wday + '</h4><h4 class="date">'+ year+"-"+month+"-"+ day + '</h4>';
    }
