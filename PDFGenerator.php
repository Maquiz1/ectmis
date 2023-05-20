<?php
// require_once 'php/core/init.php';
// require_once 'dompdf/autoload.inc.php';
require_once 'pdf.php';

$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$checkBatch = $override->getData('batch');
$title = $checkBatch[0]['name'];
// use Dompdf\Dompdf;

// Create a new instance of Dompdf
// $pdf = new Dompdf();
$pdf = new Pdf();

$file_name = $title . '.pdf';
// $file_name = 'Order.pdf';


// Load HTML content into dompdf
foreach ($checkBatch as $row) {
    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="2" align="center" style="font-size: 18px">
                        <b>Invoice</b>
                        <b>'.$row['id'].'</b>
                    </td>
                </tr>
            </table>   
                ';
}

// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
