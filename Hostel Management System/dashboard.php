<?php
session_start();
require_once 'database.inc.php';
require_once 'functions.inc.php';
require_once 'config.inc.php';

$public = ['login','logout'];
$op=$_GET['op'];
if(is_logged_in() or in_array($op,$public)) call_user_func($op);
purify($conn);

function format_value($var) {
    if(in_array($var,$GLOBALS['int'])) return $var;
    return '\''.$var.'\'';
}

function dashboard() {
    $sql_queries = [
        'vacant_seat_count' => 'select (count(*)-sum(status)) as vacant_seat_count from seat where status=0',
        'income' => 'select sum(amount) as income from income',
        'net_income' => 'select ((select sum(amount) from income)-(select sum(amount) from expense)) as net_income',
        'expense' => 'select sum(amount) as expense from expense '
    ];

    $rows = array();
    foreach($sql_queries as $sql) {
        $result=$GLOBALS['conn']->query($sql);
        while($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
    }
    print json_encode($rows);
}
function due() {
    $conn=$GLOBALS['conn'];
    $sql = 'select seat,date,name from student';
    $result = $conn->query($sql);
    $output = array();
    while($row=$result->fetch_assoc()) {
        $seat=$row['seat'];
        $date_begin=$row['date'];
        $name=$row['name'];

        $sql='select rent from seat where id='.$seat;
        $rent=$conn->query($sql)->fetch_assoc()['rent'];

        $sql='select sum(amount) as total from income where seat = '.$seat.' and date >= '.$date_begin;
        $payment=$conn->query($sql)->fetch_assoc()['total'];

        $exploded=explode('-',$date_begin);
        $y=$exploded[0];
        $m=$exploded[1];
        $d=$exploded[2];
        $start=$y*365+$m*30+$d;
        $end=date('Y')*365+date('m')*30+date('d');
        $total_rent=0;

        while($end - $start >= 30) {
            $start+=30;
            $total_rent+=$rent;
        }

        $sql='select sum(breakfast) as b,sum(lunch) as l,sum(dinner) as d from meal where seat='.$seat;
        $re=$conn->query($sql)->fetch_assoc();
        $meal_cost=$re['b']*global_vars::$cost_per_breakfast+$re['l']*global_vars::$cost_per_lunch+$re['b']*global_vars::$cost_per_dinner;

        $due=$total_rent-$payment+$meal_cost;
        
        $out = [
            'name' => $name,
            'due' => $due
        ];
        $output[]=$out;
    }
    print json_encode($output);
}

function login() {
    if(isset($_GET['username']) && isset($_GET['password'])) {
        $username=$conn->real_escape_string($_GET['username']);
        $password=$conn->real_escape_string($_GET['password']);
        $sql='SELECT id from login where username=\''.$username.'\' and password=\''.md5($password).'\'';
        
        debug($sql);
      
        $result=$conn->query($sql);
        if($result) {
          if($result->num_rows!=0) {
            $row=$result->fetch_assoc();
            $_SESSION['id']=$row['id'];
            $_SESSION['loggedin']=true;
            $result->free_result();
            echo 'logged in';
          }
          else {
            echo 'wrong username or password';
          }
        }
        else {
          echo 'query failed';
        }
        $conn->close();
    }
}
function logout() {
    if(isset($_SESSION['loggedin'])) {
        unset($_SESSION['loggedin']);
        unset($_SESSION['id']);
        echo 'logged out';
    }
    else {
        echo 'not logged in';
    }
}
function booking() {
    $required_cols=['name','phone','email','address','institution','seat','date'];
    $optional_cols=['guardian','guardian_phone','blood_group'];
    
    $cols='';
    $vals='';
    foreach($required_cols as $col) if(!isset($col)) die("please fill in required information");
    else {
        $col.=$col.',';
        $vals.=format_value($_GET[$col]).',';
    } 
    $sql='insert into booking_temp ('.$cols.
}