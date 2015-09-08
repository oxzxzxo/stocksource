<?php 
$sql = "select (select count(*) from data_chart_d) c1, (select count(*) from data_sub_d) c2 from dual ";
$rs = rs($sql);
$beforeC = 0;
$afterC = 0;
$patchC = 0;

while ($row = mysqli_fetch_array($rs)) {
  $beforeC = (int)$row["c2"];
  $afterC = (int)$row["c1"];
  $patchC = $afterC - $beforeC;
}

if ($patchC > 0) { exeQ("call updateSub"); }

$sql = "SELECT DISTINCT jstockcode FROM data_sub_d ";
$rs = rs($sql);

$jstockcode = array();
$jdate = array();
$chartData = array();
$subsql = array();
$sqlCount = array();

$i = 0;

while ($row = mysqli_fetch_array($rs)) {
  $jstockcode[$i] = $row["jstockcode"];
  $i++;
}

for ($i = 0; $i < count($jstockcode); $i++) {
  $sql = "SELECT t1.jdate, t1.jlastprice, t2.avg5, t2.avg20, t2.avg60, t2.bbup, t2.bbdown ";
  $sql.= "FROM data_chart_d t1 LEFT OUTER JOIN data_sub_d t2 ON t1.jdate = t2.jdate AND t1.jstockcode = t2.jstockcode ";
  $sql.= "WHERE t1.jstockcode = '".$jstockcode[$i]."' ORDER BY t1.jdate DESC ";
  $rs = rs($sql);
  $rs_count = mysqli_num_rows($rs);

  $j = 0;
  while ($row = mysqli_fetch_array($rs)) {
    $jdate[$j] = $row["jdate"];
    $chartData[$j]["jlastprice"] = $row["jlastprice"];
    $chartData[$j]["avg5"] = $row["avg5"];
    $chartData[$j]["avg20"] = $row["avg20"];
    $chartData[$j]["avg60"] = $row["avg60"];
    $chartData[$j]["bbup"] = $row["bbup"];
    $chartData[$j]["bbdown"] = $row["bbdown"];
    $j++;
  }
  
  $subsql["avg5"] = "update data_sub_d set avg5 = case ";
  $subsql["avg20"] = "update data_sub_d set avg20 = case ";
  $subsql["avg60"] = "update data_sub_d set avg60 = case ";
  $subsql["bbup"] = "update data_sub_d set bbup = case ";
  $subsql["bbdown"] = "update data_sub_d set bbdown = case ";
  $sqlCount["avg5"] = 0;
  $sqlCount["avg20"] = 0;
  $sqlCount["avg60"] = 0;
  $sqlCount["bb"] = 0;

  for ($j = 0; $j < count($jdate); $j++) {
    if ($chartData[$j]["avg5"] == 0 && $j < (count($jdate) - 5)) {
      for ($k = 0; $k < 5; $k++) { $chartData[$j]["avg5"] += $chartData[$j + $k]["jlastprice"]; }
      $chartData[$j]["avg5"] = $chartData[$j]["avg5"] / 5;
      $subsql["avg5"].= "when jdate = '".$jdate[$j]."' then '".$chartData[$j]["avg5"]."' ";
      $sqlCount["avg5"]++;
    }

    if ($chartData[$j]["avg20"] == 0 && $j < (count($jdate) - 20)) {
      for ($k = 0; $k < 20; $k++) { $chartData[$j]["avg20"] += $chartData[$j + $k]["jlastprice"]; }
      $chartData[$j]["avg20"] = $chartData[$j]["avg20"] / 20;
      $subsql["avg20"].= "when jdate = '".$jdate[$j]."' then '".$chartData[$j]["avg20"]."' ";
      $sqlCount["avg20"]++;
    }

    if ($chartData[$j]["avg60"] == 0 && $j < (count($jdate) - 60)) {
      for ($k = 0; $k < 60; $k++) { $chartData[$j]["avg60"] += $chartData[$j + $k]["jlastprice"]; }
      $chartData[$j]["avg60"] = $chartData[$j]["avg60"] / 60;
      $subsql["avg60"].= "when jdate = '".$jdate[$j]."' then '".$chartData[$j]["avg60"]."' ";
      $sqlCount["avg60"]++;
    }

    try {
      if (($chartData[$j]["bbup"] == 0 || $chartData[$j]["bbdown"] == 0) && $chartData[$j]["avg20"] > 0 && $j < (count($jdate) - 20)) {
        $bbV = 0;
        $bbTemp = 0;
        for ($k = 0; $k < 20; $k++) { 
          $bbTemp = $chartData[$j + $k]["jlastprice"] - $chartData[$j]["avg20"];
          $bbTemp = $bbTemp * $bbTemp;
          $bbV += $bbTemp; 
        }
        $bbV = $bbV / 20;
        $bbV = sqrt($bbV);

        $chartData[$j]["bbup"] = $chartData[$j]["avg20"] + $bbV * 2;
        $chartData[$j]["bbdown"] = $chartData[$j]["avg20"] - $bbV * 2;

        $subsql["bbup"].= "when jdate = '".$jdate[$j]."' then '".$chartData[$j]["bbup"]."' ";
        $subsql["bbdown"].= "when jdate = '".$jdate[$j]."' then '".$chartData[$j]["bbdown"]."' ";
        $sqlCount["bb"]++;
      }
    }
    catch (exception $E) {
    }
  }

  $subsql["avg5"].= "else '0' end where jstockcode = '".$jstockcode[$i]."' and avg5 = 0 ";
  $subsql["avg20"].= "else '0' end where jstockcode = '".$jstockcode[$i]."' and avg20 = 0 ";
  $subsql["avg60"].= "else '0' end where jstockcode = '".$jstockcode[$i]."' and avg60 = 0 ";
  $subsql["bbup"].= "else '0' end where jstockcode = '".$jstockcode[$i]."' and bbup = 0 ";
  $subsql["bbdown"].= "else '0' end where jstockcode = '".$jstockcode[$i]."' and bbdown = 0 ";

  if ($sqlCount["avg5"] > 0) { exeQ($subsql["avg5"]); }
  if ($sqlCount["avg20"] > 0) { exeQ($subsql["avg20"]); }
  if ($sqlCount["avg60"] > 0) { exeQ($subsql["avg60"]); }
  if ($sqlCount["bb"] > 0) { exeQ($subsql["bbup"]); exeQ($subsql["bbdown"]); }
}

?>
<result>

  <beforeC><?php echo $beforeC; ?></beforeC>
  <afterC><?php echo $afterC; ?></afterC>
  <patchC><?php echo $patchC; ?></patchC>
  
  <subItem>
    <itemName>avg5</itemName>
    <patchCount><?php echo $sqlCount["avg5"]; ?></patchCount>
  </subItem>
  <subItem>
    <itemName>avg20</itemName>
    <patchCount><?php echo $sqlCount["avg20"]; ?></patchCount>
  </subItem>
  <subItem>
    <itemName>avg60</itemName>
    <patchCount><?php echo $sqlCount["avg60"]; ?></patchCount>
  </subItem>
  <subItem>
    <itemName>bb</itemName>
    <patchCount><?php echo $sqlCount["bb"]; ?></patchCount>
  </subItem>

</result>

