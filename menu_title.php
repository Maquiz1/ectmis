<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();

?>

<!-- menu.php -->
<!-- <div id="menu">
    <table width="100%" border="1" cellpadding="5" cellspacing="0"> -->

<!-- Your menu HTML code here -->
<!-- <tr>
        <td colspan="'.$span0.'" align="center" style="font-size: 18px">
            <b>' . $title . ': Total ( ' . $data_count . ' )</b>
        </td>
    </tr> -->

<!-- <tr>
            <th colspan="2">No.</th>
            <th colspan="2">Generic Name</th>
            <th colspan="2">Required Quantity</th>
            <th colspan="2">Available Quantity</th>
            <th colspan="2">Status</th>
            <th colspan="2">Status</th>
            <th colspan="2">Status</th>
            <th colspan="2">Expiry Date</th>
            <th colspan="2">Expiry Date</th>
            <th colspan="2">Expiry Date</th>
        </tr>

    </table>

</div> -->


<!-- $output .= ' -->
<table width="100%" border="1" cellpadding="5" cellspacing="0">

    <tr>
        <th colspan="1">No.</th>
        <th colspan="2">Generic Name</th>
        <th colspan="2">Req.</th>
        <th colspan="2">Ava.</th>
        <th colspan="2">Batch No.</th>
        <th colspan="2">Last Check</th>
        <th colspan="2">Next Check</th>
        <!-- '; -->

        <!-- if ($_GET['group'] == 1 || $_GET['group'] == 3 || $_GET['group'] == 4) { -->

        <!-- $output .= ' -->
        <th colspan="2">Expiry Date</th>
        ';
        <!-- } -->
        <!-- $output .= ' -->
        <th colspan="2">Status</th>
        <th colspan="2">Remarks</th>
    </tr>
</table>