function route(id) {
    if(id=='home')
    home_controller();
    else if(id=='vacant')
    vacant_seat_controller();
    else if(id=='booking')
    booking_controller({'from':'router'});
}
$(window).on("hashchange",function() {
    route(window.location.hash.substr(1));
})
window.location.hash="";
window.location.hash="#home";

function openNav() {
    document.getElementById("mySidenav").style.width = "100%";
}
  
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
function render(element) {
    if(element==undefined) return;
    $(".main").html("");
    $(".main").append(element);
}
function capitalize_first_letter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
  
/************************* create ***************************/
function booking_controller(__data) {
    var cols=['name','phone','email','address','guardian','guardian_phone','institution','seat','blood_group','date'];
    if(__data['from']=='router') {
        render_booking_view({'cols':cols});
    }
    else if(__data['from']=='view') {
        render(__data['div']);
        if(__data['post']!=undefined) {
            var query="";
            cols.forEach(i=>{
                query+=i+"="+document.getElementById(i).value+"&";
            });
            $.get("api.php?op=booking&"+query,function(recv,status) {
                alert(recv);
            });
        }
    }
}
function render_booking_view(__data) {
    var div=document.createElement("div");
    raw_html="";
    __data['cols'].forEach(element=>{
        if(element=='date')
            raw_html+=element+" <input type='date' id='"+element+"'/><br/>";
        else
            raw_html+=element+" <input type='text' id='"+element+"'/><br/>";
    });
    btn=document.createElement("button");
    btn.innerText="book";
    btn.onclick=function() {
        booking_controller({'from':'view','post':1});
    }
    div.innerHTML=raw_html;
    div.appendChild(btn);
    booking_controller({'from':'view','div':div});
}
/*************************** dashboard *****************************/
function home_controller(__data) {

    $.get("dashboard.php",function(recv,status) {

        json=JSON.parse(recv);
        render_dashboard_view(json);
    });
}

function render_dashboard_view(controller_data) {
    var div=document.createElement("div");
    var h="";
    controller_data.forEach(element=> {
        for(key in element) {
            h+=key+"="+element[key]+"<br>";
        }
    });
    $(".main").html(h);
}
/************************* due ****************************************/
function vacant_seat_controller(__data) {
    if(__data['from']=='router') {
        $.get("due.php",function(recv,status) {
            json=JSON.parse(recv);
            render_vacant_seat_view(json);
        });
    }
    else if(__data['from']=='view') {
        render(__data['div']);
    }
}

function render_vacant_seat_view(controller_data) {
    var div=document.createElement("div");
    var table=document.createElement("table");
    var tr=document.createElement("tr");
    for(key in controller_data[0]) {
        var th=document.createElement("th");
        th.innerText=key;
        tr.appendChild(th);
    }
    table.appendChild(tr);
    controller_data.forEach(element=> {
        var tr=document.createElement("tr");
        for(key in element) {
            var td=document.createElement("td");
            td.innerText=element[key];
            tr.appendChild(td);
        }
        table.append(tr);
    });

    div.appendChild(table);
    vacant_seat_controller({'from':'view','div':div});
}