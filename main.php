<?php
date_default_timezone_set('America/Los_Angeles');
require_once 'PHPExcel/Classes/PHPExcel.php';
require_once 'phpQuery/phpQuery/phpQuery.php';
require_once 'functions.php';

$debug = false;

$now = date('Y-m-d');
$fileList = array(
    "Reviewed_Rosewill",
    "Media_Contact",
    "Request_to_Return_Merchandise",
    "Request_to_Review_Product",
    "Sponsorship_Request",
    "Tech_Support",
    "Vendor_or_Business_Contact",
    "Other");

$fileDir = 'Data/';

$fileListWithDate = array();

foreach($fileList as $file){
    $file = $fileDir . $file . '_' . $now;
    $fileListWithDate[] = $file;
}



$arrayToExcel = array();
$result = array();
foreach ($fileListWithDate as $file){
    $result = null;
    $arrayToExcel = null;
    $handle = fopen($file.'.txt', "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // data[0] = No, data[1] = Form Type, data[2] = User Data, data[3] = Modify Time, data[4] = Created Time,
            $data = preg_split('/\t+/', $line);
            $data[2] = preg_replace('/\\\\\\\"/', ' ', $data[2]);
            $data[2] = preg_replace('/","/', "\n", $data[2]);
            $data[2] = preg_replace('/[{}"]/', '', $data[2]);
            $data[2] = preg_replace('/\\\\\\\\\//', '/', $data[2]);
            //echo $data[2];
            //die();
            $result = array(
                "Form Type" => $data[1],
                "Content" => $data[2]
            );
            $arrayToExcel[] = $result;
        }
        fclose($handle);
    } else {
        // error opening the file.
    }
    //var_dump($arrayToExcel);
    exportArrayToXlsx($arrayToExcel, array(
        "filename" => $file.'.xls',
        "title" => "Sheet 1"
    ));
}

sendMailWithDownloadUrl('Contact Us Web Form Data', $fileListWithDate);
