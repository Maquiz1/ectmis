<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $data_count = 0;
        $startDate = date('Y-m-d'); // Start date
        $numberOfDays = 30; // Number of days to add

        $newDate = date('Y-m-d', strtotime($startDate . ' + ' . $numberOfDays . ' days'));
        switch (Input::get('report')) {
            case 1:
                $data = $override->getNewsASC1('batch', 'status', 1, 'expire_date', $newDate, 'batch_no');
                $data_count = $override->getNewsASC1Count('batch', 'status', 1, 'expire_date', $newDate, 'batch_no');
                break;
            case 2:
                $data = $override->getNewsASC1G('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                $data_count = $override->getNewsASC1CountG('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                break;
            case 3:
                $data = $override->getNewsASC1G('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                $data_count = $override->getNewsASC1CountG('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                break;
            case 4:
                $data = $override->getNewsASC1G('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                $data_count = $override->getNewsASC1CountG('batch', 'status', 1, 'expire_date', $newDate, 'use_group', $_GET['group'], 'batch_no');
                break;
        }
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$span0 = 14;
$span1 = 7;
$span2 = 7;


$data_count1 = 0;
$data_count3 = 0;
$data_count4 = 0;
if ($_GET['group'] == 1) {
    $data_count1 = $data_count;
    $title =
        $quantity . ' Inventory ( Medicines )';
} elseif ($_GET['group'] == 2) {
    $data_count = 0;
    $title =
        $quantity . 'To Expire Inventory (Medical Equipments)';
    $span0 = 14;
    $span1 = 7;
    $span2 = 7;
} elseif ($_GET['group'] == 3) {
    $data_count3 = $data_count;

    $title =
        $quantity .  'To Expire Inventory ( Accessories )';
} elseif ($_GET['group'] == 4) {
    $data_count4 = $data_count;

    $title =
        $quantity . 'To Expire  Inventory ( Supplies )';
} else {
    $data_count = $data_count1 + $data_count3 + $data_count4;

    $title =
        $quantity . 'To  Expire' . ' Total Inventory ';
}

$pdf = new Pdf();

$file_name = $title . ' - ' . date('Y-m-d') .  '.pdf';

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
            <div class="period">' . date('Y-m-d') . '</div>
        </header>
        <footer>
            <div>SOP CODE IHIBAG-CLN_031_V01: <span class="page"></span></div>
            <div class="reviewed">Reviewed By  .................( INITIALS )</div>
        </footer>

';

$output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                <b>' . $title . ':  Total ( ' . $data_count . ' )</b>
            </td>
        </tr>
    
        <tr>
            <th colspan="2">No.</th>
            <th colspan="2">Generic Name No.</th>
            <th colspan="2">Batch No.</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Categry</th>
            <th colspan="2">Date To Expire</th>
            <th colspan="2">Status</th>
        </tr>
    
     ';

// Load HTML content into dompdf
$x = 1;
$status = '';
$balance_status = '';

foreach ($data as $row) {
    $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
    $gen_name = $override->get('generic', 'id', $row['generic_id'])[0]['name'];

    if ($row['expire_date'] <= date('Y-m-d')) {
        $status = 'Expired';
    } else {
        $status = 'Valid';
    }

    if ($row['balance'] <= 0) {
        $balance_status = 'Out of Stock';
        $quantity = 'Out of Stock';
    } elseif ($row['balance'] > 0 && $row['balance'] < $row['notify_quantity']) {
        $balance_status = 'Running Low';
        $quantity = 'Running Low';
    } else {
        $balance_status = 'Sufficient';
        $quantity = 'Sufficient';
    }

    if ($row['use_group'] == 1 || $row['use_group'] == 3 || $row['use_group'] == 4) {

        $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $gen_name . '</td>
            <td colspan="2">' . $row['batch_no'] . '</td>
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $category_name . '</td>
            <td colspan="2">' . $row['expire_date'] . '</td>
            <td colspan="2">' . $status . '</td>
        </tr>
        ';

        $x += 1;
    }
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
