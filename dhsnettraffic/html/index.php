<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");
// include config file
date_default_timezone_set("Asia/Ho_Chi_Minh");
include_once "config.php";
// Traffic theo nam (yearly stats)
$yearly  = $conn_web->query("SELECT DATE_FORMAT(timestamp, '%Y'), sum(tx), sum(rx), sum(tx)+sum(rx) FROM traffic GROUP BY DATE_FORMAT(timestamp, '%Y') ORDER BY timestamp DESC");
$monthly = $conn_web->query("SELECT DATE_FORMAT(timestamp, '%m-%Y'), sum(tx), sum(rx), sum(tx)+sum(rx) FROM traffic GROUP BY DATE_FORMAT(timestamp, '%m-%Y') ORDER BY timestamp DESC LIMIT 12");
$daily   = $conn_web->query("SELECT DATE_FORMAT(timestamp, '%d-%m-%Y'), sum(tx), sum(rx), sum(tx)+sum(rx) FROM traffic GROUP BY DATE_FORMAT(timestamp, '%d-%m-%Y') ORDER BY timestamp DESC LIMIT 7");
$id      = 1;

//get data for chart
// $getTraffic = $conn_web->query("SELECT timestamp, tx, rx FROM traffic WHERE device_id = '".$id."' ORDER BY timestamp DESC LIMIT 6");
$getTraffic = $conn_web->query("SELECT timestamp, sum(tx) as tx, sum(rx) as rx FROM traffic WHERE timestamp >= DATE_SUB(NOW(),INTERVAL 24 HOUR) GROUP BY DATE_FORMAT(timestamp, '%H %d-%m-%Y') ORDER BY timestamp");
$chartData  = "";
while ($res = $getTraffic->fetch_array(MYSQLI_ASSOC)) {
    if (!isset($res["timestamp"])) {
        continue;
    }
    //set to Google Chart data format
    $chartData .= "['" . date("H", strtotime($res["timestamp"])) . "'," . round($res["rx"] / 1024 / 1024 / 1024, 2) . "," . round($res["tx"] / 1024 / 1024 / 1024, 2) . "],";
}

//$results->finalize();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Traffic Stats - Thống kê lưu lượng</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="icon" type="image/png" href="/traffic/favicon16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="/traffic/favicon32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/traffic/favicon96x96.png" sizes="96x96">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawChart);
    
        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ['Hour', 'Rx (GB)', 'Tx (GB)'],
            <?php echo $chartData; ?>
         ]);

          var options = {
            chart: {
              title: '24h gần đây'
            }, 
            colors: ['#e0440e', '#42b6f5']
          };

          var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

          chart.draw(data, google.charts.Bar.convertOptions(options));
        }
        $(window).resize(function(){
          drawChart();
        });
      </script>
  <style>
        
      .title {
          font-size: 30px;
          font-weight: 600;
          text-align:center;
          color:blue;
          margin: 50px 0 50px 0;
      }
      .table-title {
          font-size: 20px;
          font-weight: 600;
          color:red;
      }
      .table{
        margin-bottom: 50px;
      }
      .footer{
        font-size:12px;
        font-weight:500;
        text-align:center;
        margin: 0px 0 20px 0;
      }
     .chart {
          width: 100%; 
          min-height: 300px;
        }
        .row {
          margin:0 !important;
        }
  </style>
</head>
<body>
        <div class="container">
            <br/>
            <?php
$getDevice = $conn_web->query("SELECT * FROM devices WHERE id = '" . $id . "'");

$device = $getDevice->fetch_array(MYSQLI_ASSOC);
echo "<strong>Route: DHs - Device Serial: " . $device["sn"] . "</strong><br/>";
echo "Last check time: " . $device["last_check"] . " <br/>";
echo "<br/>";
?>
           <div class="row">
                <div class="col-md-12">
                    <div id="columnchart_material" class="chart"></div>
                </div>
            </div>
                      
            <div class="table-title"><?php
echo mysqli_num_rows($daily);
?> ngày gần đây</div>
            <table class="table table-striped">
                    <thead>
                        <th>Ngày</th>
                        <th>Tải xuống (Download)</th>
                        <th>Tải lên (Upload)</th>
                        <th>Tổng lưu lượng</th>
                    </thead>
                    <tbody>
                <?php
while ($row = $daily->fetch_array(MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td width=\"15%\">" . $row["DATE_FORMAT(timestamp, '%d-%m-%Y')"] . "</td>";
    echo "<td>" . number_format($row["sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)+sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "</tr>";
}
?>
               </tbody>
            </table>
            <div class="table-title"><?php
echo mysqli_num_rows($monthly);
?> tháng gần đây</div>
            <table class="table table-striped">
                    <thead>
                        <th>Tháng</th>
                        <th>Tải xuống (Download)</th>
                        <th>Tải lên (Upload)</th>
                        <th>Tổng lưu lượng</th>
                    </thead>
                    <tbody>
                    <?php
while ($row = $monthly->fetch_array(MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td width=\"15%\">" . $row["DATE_FORMAT(timestamp, '%m-%Y')"] . "</td>";
    echo "<td>" . number_format($row["sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)+sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "</tr>";
}
?>
                   </tbody>
                </table>
            <div class="table-title"><?php
echo mysqli_num_rows($yearly);
?> năm gần đây</div>
            <table class="table table-striped">
                <thead>
                    <th>Năm</th>
                    <th>Tải xuống (Download)</th>
                    <th>Tải lên (Upload)</th>
                    <th>Tổng lưu lượng</th>
                </thead>
                <tbody>
                <?php
while ($row = $yearly->fetch_array(MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td width=\"15%\">" . $row["DATE_FORMAT(timestamp, '%Y')"] . "</td>";
    echo "<td>" . number_format($row["sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "<td>" . number_format($row["sum(tx)+sum(rx)"] / 1024 / 1024 / 1024, 2, ".", " ") . " GB </td>";
    echo "</tr>";
}
?>
               </tbody>
           </table>
        </div>
</body>
<footer>
    <div class="footer">DHs<br/></div>
</footer>
</html>