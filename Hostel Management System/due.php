<?php
session_start();
require_once 'database.inc.php';
require_once 'functions.inc.php';
require_once 'config.inc.php';

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