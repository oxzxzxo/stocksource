<?php

if (isset($_REQUEST["id"]) && isset($_REQUEST["password"])) {
  $succVal = "";
  $ip  = $_SERVER["REMOTE_ADDR"];

  if (($_REQUEST["password"] == "thfrh" || $_REQUEST["password"] == "솔고") && preg_match("/^[0-9A-Z가-힣][0-9A-Z가-힣_-]{2,15}$/i", $_REQUEST["id"])) {
    $_SESSION["id"] = $_REQUEST["id"];
    $succVal = "성공";
  }
  else if ($_REQUEST["password"] == "thfrh") {
    $_SESSION["E"] = "아이디는 한글, 영문, 숫자, -, _로 2~15글자만 됩니다. 잘 쓰세요.";
    $succVal = "이상한 아이디";
  }
  else {
    $_SESSION["E"] = "비밀번호가 다-릅니다. ";
    $succVal = "비밀번호 다름";
  }

  $sql = "";
  $sql.= "insert into log_login (jid, jentervalue, insert_id, insert_date, insert_ip) values ";
  $sql.= "('".$_REQUEST["id"]."', '".$_REQUEST["password"]."|".$succVal."', '".$_REQUEST["id"]."', now(), '".$ip."') ";
  exeQ($sql);
}
else {
  $_SESSION["E"] = "아이디와 비밀번호를 입력해주세요. ";
}

header("Location: /");
exit();

?>