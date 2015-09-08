<?php
// DB 연결 부분을 작성하는 곳입니다.

define("DB_HOST", "localhost");
define("DB_USER", "stockuser");
define("DB_PASSWORD", "stocka!q2w3e4r");
define("DB_NAME", "stockdb");

function rs($sql) {
  $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("DB ERROR: ".mysqli_error());
  if (!$connect->set_charset("utf8")) die($connect->error); 
  $rs = mysqli_query($connect, $sql);
  mysqli_close($connect);
  return $rs;
}

function exeQ($sql) {
  $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("DB ERROR: ".mysqli_error());
  if (!$connect->set_charset("utf8")) die($connect->error); 
  mysqli_query($connect, $sql);
  mysqli_close($connect);
}

?>