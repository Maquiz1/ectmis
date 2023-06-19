<?php
require_once 'php/core/init.php';
require_once 'pdf.php';

$pdf = new Pdf();

$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$checkBatch = $override->getData('batch');

foreach ($checkBatch as $row) {
    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="2" align="center" style="font-size: 18px">
                        <b>Invoice</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <table width="100%" cellpadding="5">
                        <tr>
                            <td width="65%">
                                To,<br />
                                    <b>RECEIVER (BILL TO)</b><br />
                                    Name : ' . $row["id"] . '<br />
                                    Billing Address : ' . $row["id"] . '<br />
                            </td>
                            <td width="35%">
                                Reverse Charge<br />
                                Invoice No : ' . $row["id"] . '<br />
                                Invoice Date : ' . $row["create_on"] . '<br />
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                    <tr>
                        <th rowspan="2">Sr No.</th>
                        <th rowspan="2">Product</th>
                        <th rowspan="2">Quantity</th>
                        <th rowspan="2">Price</th>
                        <th rowspan="2">Actual Amt.</th>
                        <th colspan="2">Tax (%)</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>Rate</th>
                        <th>Amt.</th>
                    </tr>
               ';

    $product_datatch = $override->getData('batch')[0];

    $count = 0;
    $total = 0;
    $total_actual_amount = 0;
    $total_tax_amount = 0;
    foreach ($product_data as $sub_row) {
        $count = $count + 1;
        $actual_amount = $sub_row["id"] * $sub_row["id"];
        $tax_amount = ($actual_amount * $sub_row["id"]) / 100;
        $total_product_amount = $actual_amount + $tax_amount;
        $total_actual_amount = $total_actual_amount + $actual_amount;
        $total_tax_amount = $total_tax_amount + $tax_amount;
        $total = $total + $total_product_amount;

        $output .= '
                <tr>
                    <td>' . $count . '</td>
                    <td>' . $product_data['id'] . '</td>
                    <td>' . $sub_row["id"] . '</td>
                    <td align="right">' . $sub_row['id'] . '</td>
                    <td align="right">' . number_format($actual_amount, 2) . '</td>
                    <td align="right">' . $sub_row['id'] . '%</td>
                    <td align="right">' . number_format($tax_amount, 2) . '</td>
                    <td align="right">' . number_format($total_product_amount, 2) . '</td>
                </tr>
            ';
    }

    $output .= '
        <tr>
            <td align="right" colspan="4"><b>Total</b></td>
            <td align="right"><b>' . number_format($total_actual_amount, 2) . '</b></td>
            <td>&nbsp;</td>
            <td align="right"><b>' . number_format($total_tax_amount, 2) . '</b></td>
            <td align="right"><b>' . number_format($total, 2) . '</b></td>
        </tr>
        ';

    $output .= '
            </table>
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <p align="right">---------------------------------------<br />Receiver Signature</p>
                <br />
                <br />
                <br />
            </td>
        </tr>
    </table>    
';
}

spl_autoload_register('DOMPDF_autoload');

$file_name = 'Order-' . $row["id"] . '.pdf';
$pdf->loadHtml($output);
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
?>