<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 50;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('search')) {
            $validate = $validate->check($_POST, array(
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
                'report' => array(
                    'required' => true,
                ),
                'use_group' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    Redirect::to('report.php?report=' . Input::get('report') . '&start=' . Input::get('start_date') . '&end=' . Input::get('end_date') . '&group=' . Input::get('use_group'));
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }

        if (Input::get('report') == 11) {
            $data = null;
            $filename = null;
            if (Input::get('full_report')) {
                $data = $override->getFull('batch_product', 'quantity', 'notify_quantity', 'status', 1);
                $filename = 'Full Report' . '-' . date('Y-m-d');
            } elseif (Input::get('sufficient')) {
                $data = $override->get4('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 1);
                $filename = 'SUFFICIENT' . '-' . date('Y-m-d');
            } elseif (Input::get('running_low')) {
                $data = $override->get5('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 1);
                $filename = 'RUNNING LOW' . '-' . date('Y-m-d');
            } elseif (Input::get('out_stock')) {
                $data = $override->get6('batch_product', 'quantity', 0, 'status', 1, 'type', 1);
                $filename = 'Out of Stock' . '-' . date('Y-m-d');
            } elseif (Input::get('expired')) {
                $data = $override->get7('batch_product', 'expire_date', date('Y-m-d'), 'status', 1, 'use_group', 1);
                $filename = 'Expired' . '-' . date('Y-m-d');
            } elseif (Input::get('not_checked')) {
                $data = $override->get4('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 2);
                $filename = 'NOT CHECKED' . '-' . date('Y-m-d');
            }
            $user->exportData($data, $filename);
        }
    }
} else {
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Report - e-ctmis </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Report</a> <span class="divider">></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <div class="workplace">
                <?php if ($errorMessage) { ?>
                    <div class="alert alert-danger">
                        <h4>Error!</h4>
                        <?= $errorMessage ?>
                    </div>
                <?php } elseif ($pageError) { ?>
                    <div class="alert alert-danger">
                        <h4>Error!</h4>
                        <?php foreach ($pageError as $error) {
                            echo $error . ' , ';
                        } ?>
                    </div>
                <?php } elseif ($successMessage) { ?>
                    <div class="alert alert-success">
                        <h4>Success!</h4>
                        <?= $successMessage ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <?php if ($_GET['id'] == 1) { ?>
                        <div class="col-md-offset-1 col-md-10">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Search Report</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation3" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-1">Start Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="start_date" id="start_date3" />
                                            <span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">End Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="end_date" id="end_date3" />
                                            <span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">Type</div>
                                        <div class="col-md-2">
                                            <select name="report" style="width: 100%;" required>
                                                <option value="">Select Report</option>
                                                <option value="1">Validity Report</option>
                                                <option value="2">Verification / Check Report</option>
                                                <!-- <option value="3">Quantity Report</option> -->
                                            </select>
                                        </div>
                                        <div class="col-md-1">Group</div>
                                        <div class="col-md-2">
                                            <select name="use_group" style="width: 100%;" required>
                                                <option value="">Select Group</option>
                                                <option value="1">Medicine</option>
                                                <option value="2">Medical Equipment</option>
                                                <option value="3">Accesssories</option>
                                                <option value="4">Supplies</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" id="submit3" name="search" value="Search Report" class="btn btn-info">
                                        </div>
                                    </div>

                                    <div class="footer tar">

                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Report</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                            <li><a href="#"><span class="isw-delete"></span> Delete</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <?php if ($user->data()->power == 1) {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch_records');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch_records');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } ?>
                                <?php if ($_POST && Input::get('report') == 1) { ?>
                                    <table id="status" cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="8%">DATE</th>
                                                <th width="10%">GENERIC</th>
                                                <th width="10%">BRAND</th>
                                                <th width="8%">BATCH</th>
                                                <th width="5%">BALANCE</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $sum_balance = $override->getSumD2('batch', 'balance', 'generic_id', $records['generic_id'], 'use_group', Input::get('use_group'))[0]['SUM(balance)'];
                                                $received = $records['quantity'];
                                                $used = $records['assigned'];
                                                $balance = $records['balance'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $generic = $override->get('generic', 'id', $records['generic_id'])[0]['name'];
                                                $brand = $override->get('brand', 'id', $records['brand_id'])[0]['name'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $generic ?></td>
                                                    <td><?= $brand ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $sum_balance ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>

                                <?php } ?>

                            </div>
                            <?php if (!$_POST) { ?>
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <a href="report.php?id=1&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                            echo $_GET['page'] - 1;
                                                                        } else {
                                                                            echo 1;
                                                                        } ?>" class="btn btn-default">
                                            < </a>
                                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                    <a href="report.php?id=1&page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                                echo 'active';
                                                                                                                                            } ?>"><?= $i ?></a>
                                                <?php } ?>
                                                <a href="report.php?id=1&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                    echo $_GET['page'] + 1;
                                                                                } else {
                                                                                    echo $i - 1;
                                                                                } ?>" class="btn btn-default"> > </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="dr"><span></span></div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>


<script>
    <?php if ($user->data()->pswd == 0) { ?>
        $(window).on('load', function() {
            $("#change_password_n").modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');
        });
    <?php } ?>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function() {

        var report_start;
        if ($('#start_date3').val() != '') {
            report_start = $('#start_date3').val();
        }
        // alert(report_start);

        var currentDate = new Date()
        var day = currentDate.getDate()
        var month = currentDate.getMonth() + 1
        var year = currentDate.getFullYear()

        var d = day + "-" + month + "-" + year;

        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, row, column, node) {
                        // Strip $ from salary column to make it numeric
                        return column === 5 ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                }
            }
        };
    });
</script>

</html>