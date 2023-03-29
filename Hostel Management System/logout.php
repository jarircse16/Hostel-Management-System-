<?php
session_start();
require_once 'database.inc.php';
require_once 'functions.inc.php';

if(isset($_SESSION['loggedin'])) {
    unset($_SESSION['loggedin']);
    unset($_SESSION['id']);
    echo 'logged out';
}
else {
    echo 'not logged in';
}