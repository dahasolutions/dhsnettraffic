<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");
// include config file
date_default_timezone_set("Asia/Ho_Chi_Minh");
include_once "config.php";
$verify_token = "AbcdAnhem";
$time = time();
$last_check = date("Y-m-d H:i:s", $time);
if (
    isset($_GET["sn"])
        ? $_GET["sn"]
        : ("" and
            isset($_GET["token"]) and
            $_GET["token"] == $verify_token and
            isset($_GET["tx"]) and
            is_numeric($_GET["tx"]) and
            isset($_GET["rx"]) and
            is_numeric($_GET["rx"]))
) {
    $device_serial = substr($_GET["sn"], 0, 12);
} else {
    echo "fail";
    exit();
}
// Check if device exists
$getDevice = $conn_web->query(
    'SELECT * FROM devices WHERE sn="' . $device_serial . '"'
);
$device = $getDevice->fetch_array(MYSQLI_ASSOC);
if (empty($device)) {
    //Add new device
    $addDevice = $conn_web->query(
        "INSERT INTO devices (sn, last_check, last_tx, last_rx)
	VALUES ('" .
            $_GET["sn"] .
            "', '" .
            $last_check .
            "', '" .
            $_GET["tx"] .
            "', '" .
            $_GET["rx"] .
            "')"
    );
    if (!$addDevice) {
        die("Could not insert data: " . mysqli_error());
    } else {
        echo "Updated Successfully" . "</br>";
    }
} else {
    //Update last received data
    //$data_up_rx = $device["last_rx"] + $_GET["rx"];
    //$data_up_tx = $device["last_tx"] + $_GET["tx"];
    $data_up_rx = $_GET["rx"];
    $data_up_tx = $_GET["tx"];
    $updateData = $conn_web->query(
        "UPDATE devices SET last_check='" .
            $last_check .
            "', last_tx='" .
            $data_up_tx .
            "', last_rx='" .
            $data_up_rx .
            "' WHERE id='" .
            $device["id"] .
            "'"
    );
    if (!$updateData) {
        die("Could not update data: " . mysqli_error());
    } else {
        echo "Updated Successfully" . "</br>";
    }
    //Update traffic data

    // Check if device exists
    $getHour = $conn_web->query(
        "SELECT * FROM traffic WHERE DATE_FORMAT(timestamp, '%H %d-%m-%Y') = DATE_FORMAT(NOW(), '%H %d-%m-%Y') ORDER BY timestamp DESC LIMIT 1"
    );
    $hour = $getHour->fetch_array(MYSQLI_ASSOC);

    if (empty($hour)) {
        //Add new row
        $updateTraffic = $conn_web->query(
            "INSERT INTO traffic (device_id, timestamp, tx, rx)
		VALUES ('" .
                $device["id"] .
                "', '" .
                $last_check .
                "', '" .
                $_GET["tx"] .
                "', '" .
                $_GET["rx"] .
                "')"
        );
        if (!$updateTraffic) {
            die("Could not Add Traffic data: " . mysqli_error());
        } else {
            echo $hour;
        }
    } else {
        //update hour
        //Update last received data
        $data_up_rx = $hour["rx"] + $_GET["rx"];
        $data_up_tx = $hour["tx"] + $_GET["tx"];
        $updateData = $conn_web->query(
            "UPDATE traffic SET tx='" .
                $data_up_tx .
                "', rx='" .
                $data_up_rx .
                "' WHERE id='" .
                $hour["id"] .
                "'"
        );
        if (!$updateData) {
            die("Could not update data: " . mysqli_error());
        } else {
            echo "Updated Successfully" . "</br>";
        }
    }
}
?>
