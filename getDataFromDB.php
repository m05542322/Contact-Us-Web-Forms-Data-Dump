<?php
require_once('DB/db.php');
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbname);

//$sql = "SELECT COUNT(*) as total FROM `FileList` WHERE `class` = 0;";
$sql_i_reviewed_rosewill = "SELECT form_name, ctime, value FROM `custom_form` WHERE `form_name` like \'%i-reviewed-rosewill%'";
$sql_media_contact = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Media Contact\"%' ";
$sql_request_to_return_merchandise = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Request to Return Merchandise\"%' ";
$sql_request_to_review_product = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Request to Review Product\"%' ";
$sql_sponsorship_request = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Sponsorship Request\"%' ";
$sql_tech_support = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Tech Support\"%'";
$sql_vendor_or_business_contact = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Vendor or Business Contact\"%' ";
$sql_other = "SELECT form_name, ctime, value FROM `custom_form` WHERE `value` like '%\"Purpose for Contact\":\"Other\"%' ";

$sqlArray = array(
    $sql_i_reviewed_rosewill,
    $sql_media_contact,
    $sql_request_to_return_merchandise,
    $sql_request_to_review_product,
    $sql_sponsorship_request,
    $sql_tech_support,
    $sql_vendor_or_business_contact,
    $sql_other
);

foreach ($sqlArray as $each) {
    $result = mysql_query($sql) or die('MySQL query error');
    while ($row = mysql_fetch_array($result)) {
        echo $row['name'];
    }
}