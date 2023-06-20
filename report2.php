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

$span0 = 7;
$span1 = 4;
$span2 = 3;

if ($_GET['group'] == 1) {
    $title = 'Medicines';
} elseif ($_GET['group'] == 2) {
    $title = 'Medical Equipments';
    $span0 = 11;
    $span1 = 5;
    $span2 = 6;
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
            footer { position: fixed; bottom: -40px; left: 0px; right: 0px; height: 50px; }

            .page-number {
                position: fixed;
                right: 20px;
                top: -30px;
             }
            .page-number1 {
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
            <div class="page-number">IFAKARA HEALTH INSTITUTE</div>
            <div class="page-number1">Period ' . $_GET['start'] . ' to ' . $_GET['end'] . '</div>
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
            <th colspan="1">No.</th>
            <th colspan="2">Date</th>
            <th colspan="2">Generic Name</th>
            <th colspan="2">Brand Name</th>   
            ';
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
        
        ';
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
$canvas->page_text(700, 550, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));


// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
