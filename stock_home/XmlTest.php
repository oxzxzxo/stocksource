<?php 
if (isset($_REQUEST["A"])) {
  $내용 = $_REQUEST["A"];
}
require_once("./뼈대/MainDatabase.php");
require_once("./xml/".$내용.".php");
?>

