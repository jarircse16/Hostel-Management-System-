<?php
session_start();
require_once 'database.inc.php';
require_once 'functions.inc.php';
require_once 'settings.inc.php';
purify($conn);

function format_value($var) {
    if(in_array($var,$GLOBALS['int'])) return $var;
    return '\''.$var.'\'';
}
function get_table_headers() {
   
    $sql="select column_name from information_schema.columns where table_name='".$_GET['table']."'";
    debug($sql);

    $result=$GLOBALS['conn']->query($sql);
    if($result) {
        $rows = array();
        while($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
        print json_encode($rows);
    }
}
function create() {
    
    $cols='';
    $vals='';
    foreach($GLOBALS['create_vars'][$_GET['table']] as $var) {
        if(!isset($_GET[$var])) die('please fill in the required values');
        $cols.=$var.',';
        $vals.=format_value($_GET[$var]).',';
    }
    foreach($GLOBALS['optional_vars'][$_GET['table']] as $var) {
        if(!isset($_GET[$var])) continue;
        $cols.=$var.',';
        $vals.=format_value($_GET[$var]).',';  
    }
    $cols=substr($cols,0,-1);
    $vals=substr($vals,0,-1);
    
    $sql='insert into '.$_GET['table'].' ('.$cols.') values ('.$vals.');';
    debug($sql);
    $result=$GLOBALS['conn']->query($sql);
    
    if($result) {
        echo 'success';
        debug($GLOBALS['conn']->affected_rows.' rows affected');
    }
    else {
        echo 'failed';
    }

    if($_GET['table']=='student') {
        $sql='update seat set status=1 where id='.$_GET['seat'];
        $GLOBALS['conn']->query($sql);
        $sql='select rent from seat where id='.$_GET['seat'];
        $taka=$GLOBALS['conn']->query($sql)->fetch_assoc()['rent'];
        $sql="insert into income (seat,amount,date) values (".$_GET['seat'].",".$taka.",'".$_GET['date']."')";
        $GLOBALS['conn']->query($sql);
        //echo $sql;
    }
}
function read() {
    if(!isset($_GET['id'])) die('id is needed');
    $sql='select * from '.$_GET['table'].' where id='.$_GET['id'];
    $result=$GLOBALS['conn']->query($sql);
    debug($sql);
    if($result) {
        $rows = array();
        while($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
        print json_encode($rows);
    }
    else {
        echo 'failed';
    }
}
function update() {
    
    if(!isset($_GET['id'])) die('id is needed');
    $set='';
    foreach($GLOBALS['create_vars'][$_GET['table']] as $var) {
        if(!isset($_GET[$var])) die('please fill in the required values');
        $set.=$var.'='.format_value($_GET[$var]).',';
    }
    foreach($GLOBALS['optional_vars'][$_GET['table']] as $var) {
        if(!isset($_GET[$var])) continue;
        $set.=$var.'='.format_value($_GET[$var]).',';
    }
    $set=substr($set,0,-1);
    $sql='update '.$_GET['table'].' set '.$set.' where id='.$_GET['id'];
    $result=$GLOBALS['conn']->query($sql);
    debug($sql);

    if($result) {
        echo 'success';
    }
    else {
        echo 'failed';
    }
}
function delete() {
    
    if(!isset($_GET['id'])) die('id is needed');
    $sql='delete from '.$_GET['table'].' where id='.$_GET['id'];
    $result=$GLOBALS['conn']->query($sql);
    debug($sql);
    if($result) {
        echo 'success';
    }
    else {
        echo 'failed';
    }
}
function view() {
   
    $sql='select * from '.$_GET['table'];
    $result=$GLOBALS['conn']->query($sql);
    debug($sql);
    if($result) {
        $rows = array();
        while($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
        print json_encode($rows);
    }
    else {
        echo 'failed';
    }
}

if(isset($_GET['op']) && isset($_GET['table'])) {
    $op=$_GET['op'];
    if(in_array($_GET['table'],$GLOBALS['public']) or in_array($_GET['table'].'_'.$_GET['op'],$GLOBALS['public']) or is_logged_in()) {
        if($op=='create') create();
        else if($op=='read') read();
        else if($op=='update') update();
        else if($op=='delete') delete();
        else if($op=='view') view();
        else if($op=='head') get_table_headers();
    }
    else {
        echo 'access denied';
    }

}
