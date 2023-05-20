<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        switch (Input::get('report')) {
            case 1:
                $data = $override->searchBtnDate3('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                $data_count = $override->getCountReport('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                break;
            case 2:
                $data = $override->searchBtnDate3('check_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                $data_count = $override->getCountReport('check_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                break;
            case 3:
                $data = $override->searchBtnDate3('batch_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                $data_count = $override->getCountReport('batch_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
                break;
        }
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

if ($_GET['group'] == 1) {
    $title = 'Medicines';
} elseif ($_GET['group'] == 2) {
    $title = 'Medical Equipments';
} elseif ($_GET['group'] == 3) {
    $title = 'Accessories';
} elseif ($_GET['group'] == 4) {
    $title = 'Supplies';
}

$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

if ($_GET['group'] == 2) {

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
    
        <tr>
            <td colspan="11" align="center" style="font-size: 18px">
                <b>IFAKARA HEALTH INSTITUTE ( e-CTMIS Report)</b>
            </td>
        </tr>
        <tr>
            <td colspan="11" align="center" style="font-size: 18px">
                <b>Report FOR ' . $title . ':  Total ( '. $data_count .' )</b>
            </td>
        </tr>
    
        <tr>
        <td colspan="11" align="center" style="font-size: 18px">
            <b>For Period ' . $_GET['start'] . ' to ' .$_GET['end'].'</b>
        </td>
        </tr>
    
        <tr>
            <th colspan="1">No.</th>
            <th colspan="2">Date</th>
            <th colspan="2">Generic Name</th>
            <th colspan="2">Brand Name</th>        
            <th colspan="2">Quantity</th>
            <th colspan="2">Units</th>
        </tr>
    
     ';
    
    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
        $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        $staff = $override->get('user', 'id', $row['staff_id'])[0];
        $batch_no = $row['batch_no'];
    
    
        $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $row['create_on'] . '</td>
            <td colspan="2">' . $generic_name . '</td>
            <td colspan="2">' . $brand_name . '</td>
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $category_name . '</td>
        </tr>
        ';
    
        $x +=1;
    
    }

    $output .= '
    <tr>
        <td colspan="5" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">----'.$user->data()->firstname. ' '.$user->data()->lastname.'-----<br />Printed By</p>
            <br />
            <br />
            <br />
        </td>

        <td colspan="6" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">-----'.date('Y-m-d').'-------<br />Date Printed</p>
            <br />
            <br />
            <br />
        </td>
    </tr>
        </table>  
    ';  

} else{

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">

        <tr>
            <td colspan="15" align="center" style="font-size: 18px">
                <b>IFAKARA HEALTH INSTITUTE ( e-CTMIS Report)</b>
            </td>
        </tr>
        <tr>
            <td colspan="15" align="center" style="font-size: 18px">
                <b>Report FOR ' . $title . ':  Total ( '. $data_count .' )</b>
            </td>
        </tr>

        <tr>
        <td colspan="15" align="center" style="font-size: 18px">
            <b>For Period ' . $_GET['start'] . ' to ' .$_GET['end'].'</b>
        </td>
        </tr>

        <tr>
            <th colspan="1">No.</th>
            <th colspan="2">Date</th>
            <th colspan="2">Generic Name</th>
            <th colspan="2">Brand Name</th>        
            <th colspan="2">Batch No</th>
            <th colspan="2">Quantity</th>
            <th colspan="2">Units</th>
            <th colspan="2">Expire Date</th>
        </tr>

    ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
        $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        $staff = $override->get('user', 'id', $row['staff_id'])[0];
        $batch_no = $row['batch_no'];


        $output .= '
        <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $row['create_on'] . '</td>
            <td colspan="2">' . $generic_name . '</td>
            <td colspan="2">' . $brand_name . '</td>
            <td colspan="2">' . $batch_no . '</td>
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $category_name . '</td>
            <td colspan="2">' . $row['expire_date'] . '</td>
        </tr>
        ';

        $x +=1;

    }

    $output .= '
    <tr>
        <td colspan="7" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">----'.$user->data()->firstname. ' '.$user->data()->lastname.'-----<br />Printed By</p>
            <br />
            <br />
            <br />
        </td>

        <td colspan="8" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">-----'.date('Y-m-d').'-------<br />Date Printed</p>
            <br />
            <br />
            <br />
        </td>
    </tr>
        </table>  
    ';  

}


   

// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
