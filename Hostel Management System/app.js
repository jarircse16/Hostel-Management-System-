function route(id) {
    var crud_id=['student','seat','meal','stuff','income','expense','booking'];
    if(crud_id.includes(id))
    read_controller({"table":id,'from':'router'});
    else if(id=='dashboard')
    dashboard_controller();
    else if(id=='due')
    due_controller({'from':'router'});
}
$(window).on("hashchange",function() {
    route(window.location.hash.substr(1));
})
window.location.hash="";
window.location.hash="#dashboard";

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

/******************************************** read **********************************************/
function render_read_view(controller_data) {
    var __table=controller_data['table']
    var __data=controller_data['data'];

    var div=document.createElement("div");

    var btn=document.createElement("button");
    btn.onclick=function() { create_controller({'table':__table,'from':'router'}); }
    btn.innerText="Add";
    
    var table=document.createElement("table");
    var tr=document.createElement("tr");
    for(var key in __data[0]) {
        var th=document.createElement("th");
        th.innerText=key;
        tr.appendChild(th);
    }
    var th=document.createElement("th");
    th.innerText="action";
    tr.append(th);

    table.appendChild(tr);

    __data.forEach(element => {
        var tr=document.createElement("tr");
        for(var key in element) {
            var td=document.createElement("td");
            td.innerText=element[key];
            tr.appendChild(td);
        }
        var td=document.createElement("td");
        var del_btn=document.createElement("button");
        del_btn.onclick=function() {
            delete_controller({'table':__table,'from':'router','id':element['id']});
        }
        del_btn.innerText="delete";
        var edit_btn=document.createElement("button");
        edit_btn.onclick=function() {
            update_controller({'table':__table,'from':'router','id':element['id']});
        }
        edit_btn.innerText="edit";
        td.appendChild(del_btn);
        td.appendChild(edit_btn);
        tr.appendChild(td);
        table.appendChild(tr);
    });
    
    div.appendChild(btn);
    div.appendChild(table);

    read_controller({'from':'view','div':div});
}
function read_controller(data) {
    var table=data['table'];
    if(data['from']=='router') {

        $.get("crud_api.php?op=view&table="+table, function(recv, status){
            var x=JSON.parse(recv);
            render_read_view({'table':table,'data':x});
        });
    }
    else if(data['from']=='view') {

        var element=data['div'];
        $(".main").html("");
        $(".main").append(element);
    }
}

/***************************************** delete ******************************************/
function render_delete_view(controller_data) {
    var table=controller_data['table']
    var id=controller_data['id'];
    
    var div=document.createElement("div");

    read_controller({'from':'view','div':div});
}
function delete_controller(data) {
    var table=data['table'];
    var id=data['id'];
    if(data['from']=='router') {
       // nothing to render, delete just
       $.get("crud_api.php?op=delete&table="+table+"&id="+id,function(recv,status) {
           alert(recv);
           if(recv=="success")
           route(table);
       });
    }
    else if(data['from']=='view') {
        render(data['div']);
    }
}
/************************* create ***************************/
function create_controller(data) {
    var table=data['table'];
    if(data['from']=='router') {

        $.get("crud_api.php?op=head&table="+table, function(recv, status){
            var x=JSON.parse(recv);
            //alert(recv);
            render_create_view({'table':table,'data':x});
        });
    }
    else if(data['from']=='view'){

        var element=data['div'];
        if(element!=undefined) {
            $(".main").html("");
            $(".main").append(element);
        }
        if(data['post']!=undefined) {
            $.get("crud_api.php?op=head&table="+table, function(recv, status){
                var x=JSON.parse(recv);
                var query="&";
                x.forEach(element=>{
                    key=element['column_name'];
                    query+=key+"="+document.getElementById(key).value+"&";
                });
                $.get("crud_api.php?op=create&table="+table+query,function(recv1,status) {
                    //alert(recv1);
                    //alert(query);
                    if(recv1=="success")
                    route(table);
                });
                
            });
        }
    }
}
function render_create_view(controller_data) {
    var __table=controller_data['table'];
    var __data=controller_data['data'];

    var div=document.createElement("div");

    raw_html="<div>";
    __data.forEach(element=>{
        if(element['column_name']=='date')
            raw_html+=element['column_name']+": <input type='date' id='"+element['column_name']+"'/><br/>";
        else
            raw_html+=element['column_name']+": <input type='text' id='"+element['column_name']+"'/><br/>";
    });

    raw_html+="</div>";
    html_wrapper=document.createElement("div");
    html_wrapper.innerHTML=raw_html;

    btn=document.createElement("button");
    btn.innerText="create";
    btn.onclick=function() {
        create_controller({'table':__table,'from':'view','post':1});
    }
    
    div.appendChild(html_wrapper);
    div.appendChild(btn);

    create_controller({'div':div,'from':'view'});
}
/**************************** update *********************************/
function update_controller(data) {
    var table=data['table'];
    var __id=data['id'];
    if(data['from']=='router') {

        $.get("crud_api.php?op=read&table="+table+"&id="+__id, function(recv, status){
            var x=JSON.parse(recv);
            alert(recv);
            render_update_view({'table':table,'data':x});
        });
    }
    else if(data['from']=='view'){

        var element=data['div'];
        if(element!=undefined) {
            $(".main").html("");
            $(".main").append(element);
        }
        if(data['post']!=undefined) {
            $.get("crud_api.php?op=head&table="+table, function(recv, status){
                var x=JSON.parse(recv);
                var query="&";
                x.forEach(element=>{
                    key=element['column_name'];
                    query+=key+"="+document.getElementById(key).value+"&";
                });
                alert(query);
                $.get("crud_api.php?op=update&table="+table+query,function(recv1,status) {
                    alert(recv1);
                    
                    if(recv1=="success")
                    route(table);
                });
                
            });
        }
    }
}
function render_update_view(controller_data) {
    var __table=controller_data['table'];
    var __data=controller_data['data'];

    var div=document.createElement("div");

    raw_html="<div>";
    
    for(key in __data[0]) {
        if(key=='date')
            raw_html+=key+": <input type='date' id='"+key+"' value='"+__data[0][key]+"'/><br/>";
        else
            raw_html+=key+": <input type='text' id='"+key+"' value='"+__data[0][key]+"'/><br/>";
    }

    raw_html+="</div>";
    html_wrapper=document.createElement("div");
    html_wrapper.innerHTML=raw_html;

    btn=document.createElement("button");
    btn.innerText="create";
    btn.onclick=function() {
        update_controller({'table':__table,'from':'view','post':1});
    }
    
    div.appendChild(html_wrapper);
    div.appendChild(btn);

    update_controller({'div':div,'from':'view'});
}
/*************************** dashboard *****************************/
function dashboard_controller(__data) {

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
function due_controller(__data) {
    if(__data['from']=='router') {
        $.get("due.php",function(recv,status) {
            json=JSON.parse(recv);
            render_due_view(json);
        });
    }
    else if(__data['from']=='view') {
        render(__data['div']);
    }
}

function render_due_view(controller_data) {
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
    due_controller({'from':'view','div':div});
}