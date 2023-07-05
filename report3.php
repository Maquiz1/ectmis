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
                $data = $override->getNewsASC0('generic', 'status',1, 'balance', 0,'name');
                $data_count = $override->getNewsASC0Count('generic', 'status', 1, 'balance', 0, 'name');
                break;
            case 2:
                $data = $override->getNewsASC1('generic', 'status', 1, 'balance', 0, 'name');
                $data_count = $override->getNewsASC1Count('generic', 'status', 1, 'balance', 0, 'name');
                break;
            case 3:
                $data = $override->getNewsASC0G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC0CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 4:
                $data = $override->getNewsASC1G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC1CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 5:
                $data = $override->getNewsASC0G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC0CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 6:
                $data = $override->getNewsASC1G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC1CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 7:
                $data = $override->getNewsASC0G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC0CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 8:
                $data = $override->getNewsASC1G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC1CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 9:
                $data = $override->getNewsASC0G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC0CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
            case 10:
                $data = $override->getNewsASC1G('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                $data_count = $override->getNewsASC1CountG('generic', 'status', 1, 'balance', 0, 'use_group', $_GET['group'], 'name');
                break;
        }
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$span0 = 10;
$span1 = 5;
$span2 = 5;

$quantity = '';

if ($_GET['report'] == 1 || $_GET['report'] == 3 || $_GET['report'] == 5 || $_GET['report'] == 7 || $_GET['report'] == 9) {
    $quantity = 'Available';

} elseif ($_GET['report'] == 2 || $_GET['report'] == 4 || $_GET['report'] == 6 || $_GET['report'] == 8 || $_GET['report'] == 10) {
    $quantity = 'Out Of Stock';

} 


if ($_GET['group'] == 1) {
    $title =
    $quantity . ' Inventory ( Medicines )';
} elseif ($_GET['group'] == 2) {
    $title =
    $quantity . ' Inventory (Medical Equipments)';
    $span0 = 10;
    $span1 = 5;
    $span2 = 5;
} elseif ($_GET['group'] == 3) {
    $title =
    $quantity .  ' Inventory ( Accessories )';
    
} elseif ($_GET['group'] == 4) {
    $title =
    $quantity . ' Inventory ( Supplies )';
}

$pdf = new Pdf();

$file_name = $title .' - '. date('Y-m-d') .  '.pdf';

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
            .reviewed {
                position: fixed;
                right: 470px;
                top: -1px;
             }
        </style>
    </head>
    <body>
        <header>
            <div><span class="page"> e-CTMIS Report </span></div>
            <div class="tittle">IFAKARA HEALTH INSTITUTE</div>
            <div class="period">'. date('Y-m-d') . '</div>
        </header>
        <footer>
            <div>SOP CODE IHIBAG-CLN_031_V01: <span class="page"></span></div>
            <div class="reviewed">Reviewed By  .................( INITIALS )</div>
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
            <th colspan="2">Generic Name</th>
            <th colspan="2">Required Quantity</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Status</th>
        </tr>
    
     ';

    // Load HTML content into dompdf
    $x = 1;
    $status = '';
    $balance_status = '';

    // print_r($data[0]['next_check']);

    foreach ($data as $row) {
        $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        $generic_balance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['notify_quantity'];
        $maintainance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['maintainance'];


        // $batch_balance = $override->getNews('batch', 'id', $row['batch_id'], 'status', 1)[0]['balance'];
        $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        // $category_name1 = $override->get('drug_cat', 'id', $row['category'])[0]['name'];

        // $staff = $override->get('user', 'id', $row['staff_id'])[0];
        // $batch_no = $row['batch_no'];

        if ($row['expire_date'] <= date('Y-m-d')) {
            $status = 'Expired';        
        } else{
            $status = 'Valid';        
        }

        if ($row['balance'] <= 0) {
            $balance_status = 'Out of Stock';
            $quantity = 'Out of Stock';

        } elseif($row['balance'] > 0 && $row['balance'] < $row['notify_quantity']){
            $balance_status = 'Running Low';
            $quantity = 'Running Low';

        } else {
            $balance_status = 'Sufficient';
            $quantity = 'Sufficient';

        }

        // if ($row['last_check'] = '') {
        //     $check_status = 'Not Checked1' .' - '. $balance_status;
        // } else {
        //     $check_status = 'Checked!' .' - '. $balance_status;
        // }




        $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $row['name'] . '</td>
            <td colspan="2">' . $row['notify_quantity'] . '</td>
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $balance_status . '</td>
        </tr>
        ';

        $x += 1;
    }

    $output .= '
    <tr>
        <td colspan="' . $span1 . '" align="center" style="font-size: 18px">
            <br />
            <p align="right">----' . $user->data()->firstname . ' ' . $user->data()->lastname . '-----<br />Printed By</p>
            <br />
        </td>

        <td colspan="' . $span2 . '" align="center" style="font-size: 18px">
            <br />
            <p align="right">-----' . date('Y-m-d') . '-------<br />Date Printed</p>
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
