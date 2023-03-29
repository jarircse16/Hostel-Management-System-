<?php

$optional_vars['student']=['email','guardian','guardian_phone','blood_group','date'];
$create_vars['student']=['name','phone','address','seat','institution'];

$optional_vars['meal']=[''];
$create_vars['meal']=['seat','breakfast','lunch','dinner','date'];

$optional_vars['stuff']=[''];
$create_vars['stuff']=['name','phone','address','salary'];

$optional_vars['seat']=[''];
$create_vars['seat']=['rent','status','attached_bath','attached_balcony'];

$optional_vars['login']=[''];
$create_vars['login']=['username','password'];

$optional_vars['income']=[''];
$create_vars['income']=['seat','amount','date'];

$optional_vars['expense']=[''];
$create_vars['expense']=['amount','type','date'];

$int = ['seat','amount','salary','rent','status','attached_bath','attached_balcony',
    'breakfast','lunch','dinner'
];

$public = [
    'booking_temp_create',
    'booking_temp_head'
];