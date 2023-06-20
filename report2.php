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

$span0 = 18;
$span1 = 9;
$span2 = 9;

if ($_GET['group'] == 1) {
    $title = 'Medicines';
} elseif ($_GET['group'] == 2) {
    $title = 'Medical Equipments';
    $span0 = 14;
    $span1 = 7;
    $span2 = 7;
} elseif ($_GET['group'] == 3) {
    $title = 'Accessories';
    
} elseif ($_GET['group'] == 4) {
    $title = 'Supplies';
}

$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

$output .= '
<html>
    <head>
        <style>
            @page { margin: 50px; }
            header { position: fixed; top: -30px; left: 0px; right: 0px; height: 50px; }
            footer { position: fixed; bottom: -50px; left: 0px; right: 0px; height: 50px; }

            .tittle {
                position: fixed;
                right: 20px;
                top: -30px;
             }
            .period {
                position: fixed;
                right: 470px;
                top: -30px;
                color: blue;
             }
        </style>
    </head>
    <body>
        <header>
            <div><span class="page"> e-CTMIS Report </span></div>
            <div class="tittle">IFAKARA HEALTH INSTITUTE</div>
            <div class="period">Period ' . $_GET['start'] . ' to ' . $_GET['end'] . '</div>
        </header>
        <footer>
            <div>SOP CODE 1: <span class="page"></span></div>
        </footer>

';

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="'.$span0.'" align="center" style="font-size: 18px">
                <b>' . $title . ':  Total ( ' . $data_count . ' )</b>
            </td>
        </tr>
    
        <tr>
            <th colspan="2">No.</th>
            <th colspan="2">Date Entered</th>
            <th colspan="2">Generic Name</th>
            ';
            if ($_GET['group'] == 1) {

    $output .= '
   
            <th colspan="2">Required Quantity</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Units</th>
            <th colspan="2">Expire Date</th>   
            <th colspan="2">Status</th>   
            <th colspan="2">Remarks</th>   


            ';
            }

            if ($_GET['group'] == 2) {

    $output .= '
   
            <th colspan="2">Quantity</th>
            <th colspan="2">Units</th>

            ';
            }

            '  
        </tr>
    
     ';

    // Load HTML content into dompdf
    $x = 1;
    $status = '';
    $balance_status = '';

    foreach ($data as $row) {
        $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        $generic_balance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['notify_quantity'];

        $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
        $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        $staff = $override->get('user', 'id', $row['staff_id'])[0];
        $batch_no = $row['batch_no'];


        if ($row['expire_date'] <= date('Y-m-d')) {
            $status = 'Expired';        
        } else{
            $status = 'Valid';        
        }

        if ($row['balance'] <= 0) {
            $balance_status = 'Out of Stock';
        } elseif($row['balance'] > 0 && $row['balance'] < $generic_balance){
            $balance_status = 'Running Low';
        } else {
            $balance_status = 'Sufficient';
        }




        $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $row['create_on'] . '</td>
            <td colspan="2">' . $generic_name . '</td>   
            <td colspan="2">' . $generic_balance . '</td>
            <td colspan="2">' . $row['balance'] . '</td>
     
        ';
        if ($_GET['group'] == 1) {

    $output .= '
 
            <td colspan="2">' . $category_name .  '</td>
            <td colspan="2">' . $row['expire_date'] . '</td>
            <td colspan="2">' . $status .  '</td>
            <td colspan="2">' . $balance_status .  '</td>
        ';
        }

        if ($_GET['group'] == 2) {

        $output .= '
 
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $category_name .  '</td>

        ';
         }

        ' 
        </tr>
        ';

        $x += 1;
    }

    $output .= '
    <tr>
        <td colspan="' . $span1 . '" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <p align="right">----' . $user->data()->firstname . ' ' . $user->data()->lastname . '-----<br />Printed By</p>
            <br />
            <br />
            <br />
        </td>

        <td colspan="' . $span2 . '" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <p align="right">-----' . date('Y-m-d') . '-------<br />Date Printed</p>
            <br />
            <br />
            <br />
        </td>
    </tr>
</table>  
    ';

$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();


$canvas = $pdf->getCanvas();
$canvas->page_text(700, 560, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));


// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
