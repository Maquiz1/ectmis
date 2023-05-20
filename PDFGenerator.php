<?php
// require_once 'php/core/init.php';
// require_once 'dompdf/autoload.inc.php';
require_once 'pdf.php';

$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$checkBatch = $override->get('batch', 'status', 1);
// $checkBatch = $override->getData('batch');
$title = $checkBatch[0]['name'];
// use Dompdf\Dompdf;

// Create a new instance of Dompdf
// $pdf = new Dompdf();
$pdf = new Pdf();

$file_name = $title . '.pdf';
// $file_name = 'Order.pdf';

$output = ' ';

$output .= '
<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <tr>
        <td colspan="12" align="center" style="font-size: 18px">
            <b>Report</b>
        </td>
    </tr>

    <tr>
        <th colspan="2">Generic Name</th>
        <th colspan="2">Brand Name</th>
        <th colspan="2">Batch No</th>
        <th colspan="2">Balance</th>
        <th colspan="2">Units</th>
        <th colspan="2">Expire Date</th>
    </tr>

 ';

// Load HTML content into dompdf
foreach ($checkBatch as $row) {
    $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
    $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
    $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
    $batch_no = $row['batch_no'];


    $output .= '
     <tr>
        <td colspan="2">' . $generic_name . '</td>
        <td colspan="2">' . $brand_name . '</td>
        <td colspan="2">' . $batch_no . '</td>
        <td colspan="2">' . $row['balance'] . '</td>
        <td colspan="2">' . $category_name . '</td>
        <td colspan="2">' . $row['expire_date'] . '</td>
    </tr>
    ';
}

$output .= '</table> ' ;

// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4','landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
