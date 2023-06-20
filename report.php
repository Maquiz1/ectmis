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
                $data = $override->searchBtnDate4('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group'], 'maintainance', 2);
                $data_count = $override->getCountReport1('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group'], 'maintainance', 2);
                break;
            case 2:
                $data = $override->searchBtnDate5('batch', 'last_check', $_GET['start'], 'last_check', $_GET['end'], 'use_group', $_GET['group'] ,'maintainance',1, 'maintainance', 3);
                $data_count = $override->getCountReport2('batch', 'last_check', $_GET['start'], 'last_check', $_GET['end'], 'use_group', $_GET['group'], 'maintainance', 1, 'maintainance', 3);
        }
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$span0 = 16;
$span1 = 8;
$span2 = 8;

if ($_GET['group'] == 1) {
    $title = 'Medicines';
} elseif ($_GET['group'] == 2) {
    $title = 'Medical Equipments';
    $span0 = 16;
    $span1 = 8;
    $span2 = 8;
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
            @page { margin: 60px; }
            header { position: fixed; top: -30px; left: 0px; right: 0px; height: 50px; }
            footer { position: fixed; bottom: -55px; left: 0px; right: 0px; height: 50px; }

            .tittle {
                position: fixed;
                right: 20px;
                top: -30px;
             }
            .period {
                position: fixed;
                right: 400px;
                top: -30px;
                color: blue;
             }
            .reviewed {
                position: fixed;
                right: 350px;
                top: -1px;
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
            <th colspan="2">Generic Name</th>
            ';
if (Input::get('report') == 1) {

    $output .= '
   
            <th colspan="2">Required Quantity</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Units</th>
            <th colspan="2">Expire Date</th>   
            <th colspan="2">Status</th>   
            <th colspan="2">Remarks</th>   


            ';
}

if (Input::get('report') == 2) {

    $output .= '
            <th colspan="2">Required Quantity</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Last Check</th>  
            <th colspan="2">Status</th>   
            <th colspan="2">Remarks</th>   
            <th colspan="2">Next Check</th>   
            ';
}

'  
        </tr>
    
     ';

$x = 1;
$status = '';
$balance_status = '';

foreach ($data as $row) {
    $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
    $generic_balance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['notify_quantity'];
    $maintainance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['maintainance'];
    $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];

    if ($row['expire_date'] <= date('Y-m-d')) {
        $status = 'Expired';
    } else {
        $status = 'Valid';
    }

    if ($row['balance'] <= 0) {
        $balance_status = 'Out of Stock';
    } elseif ($row['balance'] > 0 && $row['balance'] < $generic_balance) {
        $balance_status = 'Running Low';
    } else {
        $balance_status = 'Sufficient';
    }

    if ($row['last_check'] = '') {
        $check_status = 'Not Checked1' . ' - ' . $balance_status;
    } else {
        $check_status = 'Checked!' . ' - ' . $balance_status;
    }




    $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $generic_name . '</td>   
            <td colspan="2">' . $generic_balance . '</td>
     
        ';
    if (Input::get('report') == 1) {

        $output .= '
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $category_name .  '</td>
            <td colspan="2">' . $row['expire_date'] . '</td>
            <td colspan="2">' . $status .  '</td>
            <td colspan="2">' . $balance_status .  '</td>
        ';
    }

    if (Input::get('report') == 2) {

        $output .= '
 
            <td colspan="2">' . $row['balance'] . '</td>
            <td colspan="2">' . $row['last_check1'] . '</td>
            <td colspan="2">' . $check_status .  '</td>
            <td colspan="2">' . $row['check_remarks'] . '</td>
            <td colspan="2">' . $row['next_check'] . '</td>

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
