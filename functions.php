<?php
function exportArrayToXlsx ($exportArray, $exportParam) {

    PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );

    $objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel->getProperties()->setCreator($exportParam['title'])
        ->setLastModifiedBy($exportParam['title'])
        ->setTitle($exportParam['title'])
        ->setSubject($exportParam['title'])
        ->setDescription($exportParam['title'])
        ->setKeywords($exportParam['title'])
        ->setCategory($exportParam['title']);

    // Set active sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle($exportParam['title']);

    // Set cell value
    //rows are 1-based whereas columns are 0-based, so ��A1�� becomes (0,1).
    //$objPHPExcel->setCellValueByColumnAndRow($column, $row, $value);
    //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "This is A1");
    for($row = 0; $row < count($exportArray); $row++){
        //ksort($exportArray[$row]);  // sort by key
        foreach ($exportArray[$row] AS $key => $value){
            // Find key index from first row
            $key_index = -1;
            if (array_key_exists($key, $exportArray[0])){
                $key_index = array_search($key, array_keys($exportArray[0]));
            }

            // Set key(column name)
            if($row==0){
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key_index, 1, $key);
            }

            //   var_dump($key);

            if($key_index != -1){

                switch ($key) {

                    case 'createDate' :
                    case 'mtime' :
                        if($value!=null && $value> 25569){
                            $value=(($value/86400)+25569); //  change  database  timestamp to date for excel .
                        }

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key_index, $row+2, $value);
                        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key_index, $row+2)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD);
                        //  var_dump($key.$value);
                        break;

                    default:
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key_index, $row+2, $value);
                    //    var_dump($key.$value);

                }
                // Set Value (each row)


            }else{
                // Can not find $key in $row
            }

        }
    }

    // Browser download
    if (strcmp("php://output", $exportParam['filename'])==0){
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="FixedAssets.xls"');
        header('Cache-Control: max-age=0');
    }

    // Write to file
    // If you want to output e.g. a PDF file, simply do:
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($exportParam['filename']); // Excel2007 : '.xlsx'   Excel5 : '.xls'

    echo json_encode(array('message' => 'success'));
}

function sendMailWithDownloadUrl ($action, $fileList) {
    global $debug;

    var_dump($fileList);
    die();

    if ($debug) {
        $recipient_array = array(
            'to' => array('Tim.H.Huang@newegg.com'),
            'bcc' => array('Li.L.Liu@newegg.com', 'Tim.H.Huang@newegg.com')
        );
    } else {
        $recipient_array = array(
            'to' => array('Stephanie.Y.Chang@rosewill.com'),
            'bcc' => array('Li.L.Liu@newegg.com', 'Tim.H.Huang@newegg.com')
        );
    }

    require_once 'class/Email.class.php';
    require_once 'class/EmailFactory.class.php';

    /* SMTP server name, port, user/passwd */
    $smtpInfo = array("host" => "127.0.0.1",
        "port" => "25",
        "auth" => false);
    $emailFactory = EmailFactory::getEmailFactory($smtpInfo);

    $attachments = array();
    foreach($fileList as $each){
        $fileName = $each;
        $excelFileType =  'application/vnd.ms-excel';
        $attachments[$fileName] = $excelFileType;
    }
    /* $email = class Email */
    $email = $emailFactory->getEmail($action, $recipient_array);
    $content = templateReplace($action);
    $email->setContent($content);
    $email->setAttachments($attachments);
    $email->sendMail();

    return true;
}

function templateReplace ($action) {
    require_once 'PHPExcel/Classes/PHPExcel.php';
    $content = file_get_contents('email/content/template.html');
    $doc = phpQuery::newDocumentHTML($content);

    $contentTitle = array(
        'Crawler Report' => 'NE.com and Amazon.com Daily Crawling Report'
    );
    (isset($contentTitle[$action])) ? $doc['.descriptionTitle'] = $contentTitle[$action] : $doc['.descriptionTitle'] = $action;

    $emailContent = array();
    $description = "Hi All:" . "<div>Data as attachments</div>";
    $doc['.description'] = $description;
    $doc['.logoImage']->attr('src', 'images/rosewilllogo.png');
    return $doc;
}

function currentTime () {
    $now = new DateTime(null, new DateTimeZone('UTC'));
    return $now->format('Y-m-d H:i:s');    /*MySQL datetime format*/
}
