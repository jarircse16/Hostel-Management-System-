<?php

function debug($x) {
     return;
    echo '<pre><strong>debug: '.$x.'</strong></pre><br/>';
}

function is_logged_in() {
    if(isset($_SESSION['loggedin'])) return true;
    return false;
}

function purify($conn) {
    foreach($_GET as $var=>$val)
    $_GET[$var]=strip_tags($conn->real_escape_string($_GET[$var]));
}