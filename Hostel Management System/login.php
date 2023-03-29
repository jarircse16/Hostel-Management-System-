<?php
session_start();
require_once 'database.inc.php';
require_once 'functions.inc.php';

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
