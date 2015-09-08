<?php
if (isset($_SESSION["id"])) {

/* 접속자 수 체크하는 경우임
  $dir = opendir("/tmp/stock_session"); 
  $onSession = 0; 

  while (($read=readdir($dir)) !== false) { 

     $when_read = explode("_",$read); 
     $read0 = $when_read[0]; 

     if ($read0 == "sess") { 

        $fh = fopen("/tmp/stock_session/".$read, "r"); 

        while (!feof($fh)) { $vContent = fread($fh,2098); } 

        fclose($fh); 

        if (0 < strlen($vContent)) { $onSession++; } 
     } 
  } 

  $탑구절 = $_SESSION["id"]."로 접속했습니다. 현재 접속자 수 : ".$onSession;

*/
  $sql = "SELECT DATE_FORMAT(update_date, '%Y-%m-%d %H시 %i분') as jlast FROM data_chart_d ORDER BY update_date DESC LIMIT 1 ";
  $rs = rs($sql);
  $last = "업데이트 기록이 없습니다. ";
  while ($row = mysqli_fetch_array($rs)) {
    $last = $row["jlast"];
  }

  $탑구절 = $_SESSION["id"]."로 접속했습니다. 마지막 업데이트 : ".$last." ";

  if (isset($_REQUEST["A"])) {
    $내용 = $_REQUEST["A"];
  }
  else {
    $내용 = "StockMain";
  }
}
else {
  if (isset($_POST["id"])) {
    $내용 = "LoginProc";
  }
  else
  {
    $탑구절 = "로그인 해주세요. ";
    $내용 = "Login";
  }
}

if (isset($_SESSION["E"])) { $탑구절 = $_SESSION["E"]; unset($_SESSION["E"]); }

?>


<nav class="top-bar">
  <div class="left" >
    <ul>
      <span class="white" ><?php echo $탑구절; ?></span>
    </ul>
  </div>
<?php if (isset($_SESSION["id"])) : ?>
  <div class="right">
    <ul>
      <a href="/?A=Logout"><span class="white" >로그아웃</span></a>
    </ul>
  </div>
<?php endif ?>
</nav>

<?php
require_once("./뼈대/".$내용.".php");
?>