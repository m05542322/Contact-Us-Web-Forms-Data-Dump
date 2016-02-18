<?php
date_default_timezone_set('America/Los_Angeles');
require_once 'PHPExcel/Classes/PHPExcel.php';
require_once 'phpQuery/phpQuery/phpQuery.php';
require_once 'functions.php';
require_once('DB/db.php');

$debug = false;

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbname);

/*sql scripts*/
$sqlArray = array(
    'product_registration' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `form_name` like '%productRegistration%'",
    'i_reviewed_rosewill' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `form_name` like '%i-reviewed-rosewill%'",
    'media_contact' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Media Contact\"%'",
    'request_to_return_merchandise' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Request to Return Merchandise\"%'",
    'request_to_review_product' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Request to Review Product\"%'",
    'sponsorship_request' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Sponsorship Request\"%'",
    'tech_support' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Tech Support\"%'",
    'vendor_or_business_contact' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Vendor or Business Contact\"%'",
    'other' => "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Other\"%' "
);

$fileList = array(
    "Product_Registration",
    "I_Reviewed_Rosewill",
    "Media_Contact",
    "Request_to_Return_Merchandise",
    "Request_to_Review_Product",
    "Sponsorship_Request",
    "Tech_Support",
    "Vendor_or_Business_Contact",
    "Other");

$fileDir = 'Data/';

$now = date('Y-m-d');
$fileListWithDate = array();
foreach($fileList as $file){
    $file = $fileDir . $file . '_' . $now . '.xls';
    $fileListWithDate[] = $file;
}

$i=0;
$arrayToExcel = array();
$dataForExcel = array();
foreach ($sqlArray as $each) {
    $arrayToExcel = null;
    $dataForExcel = null;
    $result = mysql_query($each) or die('MySQL query error');
    while ($row = mysql_fetch_array($result)) {
        /*$row['form_name'], $row['ctime'], $row['value']*/
        $row['value'] = preg_replace('/\\\\\\\"/', ' ', $row['value']);
        $row['value'] = preg_replace('/","/', "\n", $row['value']);
        $row['value'] = preg_replace('/[{}"]/', '', $row['value']);
        $row['value'] = preg_replace('/\\\\\\\\\//', '/', $row['value']);

        $dataForExcel = array(
            "Form Type" => $row['form_name'],
            "Created Time" => $row['ctime'],
            "Content" => $row['value']
        );
        $arrayToExcel[] = $dataForExcel;
    }
    exportArrayToXlsx($arrayToExcel, array(
        "filename" => $fileListWithDate[$i],
        "title" => "Sheet 1"
    ));
    $i++;
}

sendMailWithDownloadUrl('Contact Us Web Form Data', $fileListWithDate);
