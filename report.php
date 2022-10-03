<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 10;
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
            ));
            if ($validate->passed()) {
                // print_r($_POST);
                try {
                    switch (Input::get('report')) {
                        case 1:
                            $data = $override->searchBtnDate2('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'));
                            break;
                            // case 2:
                            //     $data = $override->searchBtnDateSufficient('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'notify_amount', 'amount', 'type', 1, 'status', 1);
                            //     break;
                            // case 3:
                            //     $data = $override->searchBtnDateLow('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'notify_amount', 'amount', 'type', 1, 'status', 1);
                            //     break;
                            // case 4:
                            //     $data = $override->searchBtnDateOutStock('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'amount',0, 'type', 1, 'status', 1);
                            //     break;
                            // case 5:
                            //     $data = $override->searchBtnDateExpired('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'expire_date', date('Y-m-d'), 'type', 1, 'status', 1);
                            //     break;
                            // case 6:
                            //     $data = $override->searchBtnDateNotChecked('batch_description', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'next_check', date('Y-m-d'), 'type', 2, 'status', 1);
                            //     break;
                    }
                    $successMessage = 'Report Successful Created';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }
    }
} else {
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Report - TanCov </title>
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
                    <?php if ($_GET['id'] == 1 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Search Report</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-1">Start Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="start_date" id="start_date" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">End Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="end_date" id="end_date" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">Type</div>
                                        <div class="col-md-2">
                                            <select name="report" style="width: 100%;" required>
                                                <option value="">Select Report</option>
                                                <option value="1">Stock Report</option>
                                                <option value="2">Check Report</option>
                                                <!-- <option value="3">Running Low Medicine</option>
                                                <option value="4">Out of Stock Medicine</option>
                                                <option value="5">Expired Medicine</option>
                                                <option value="6">Unchecked Devices</option> -->
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" name="search" value="Search Report" class="btn btn-info">
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
                                    $pagNum = $override->getNo('batch');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } ?>
                                <?php if ($_POST && Input::get('report') == 1) { ?>
                                    <table id="FullReport" cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%"></th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $balance = $override->get('batch_description', 'batch_id', $records['id'])[0]['quantity'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $balance ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <a href="data.php?id=10&report_id=<?= $records['id'] ?>" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 2) { ?>
                                    <table id='FullReport' cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="15%">Generic</th>
                                                <th width="15%">Brand</th>
                                                <th width="5%">Last Check</th>
                                                <th width="5%">Status</th>
                                                <th width="5%">Next Check</th>
                                                <th width="5%">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $amnt = 0;
                                            $pagNum = $override->getCount('batch', 'status', 1);
                                            $pages = ceil($pagNum / $numRec);
                                            if (!$_GET['page'] || $_GET['page'] == 1) {
                                                $page = 0;
                                            } else {
                                                $page = ($_GET['page'] * $numRec) - $numRec;
                                            }
                                            $type = Input::get('report');
                                            foreach ($override->getNews('batch', 'status', 1, 'type', $type) as $batch) {
                                                // foreach ($override->getWithLimit('batch', 'status', 1, $page, $numRec) as $batch) {
                                                $study = $override->get('study', 'id', $batch['study_id'])[0];
                                                $name = $override->get('batch_description', 'assigned', 'batch_id', $batch['id']);
                                                $batchItems = $override->getSumD1('batch_description', 'assigned', 'batch_id', $batch['id']);
                                                $currentAmount = $override->get('batch_description', 'batch_id', $batch['id'])[0]['quantity'];
                                                $notifyAmount = $override->get('batch_description', 'batch_id', $batch['id'])[0]['quantity'];
                                                $batchDescId = $override->get('check_records', 'batch_desc_id', $batch['id'])[0]['batch_desc_id'];
                                                $maintainance_type = $override->get('check_records', 'batch_desc_id', $batch['id'])[0]['check_type'];
                                                $lastStatus2 = $override->lastRow2('check_records', 'batch_desc_id', $batchDescId, 'id')[0]['status'];
                                                $checkDate = $override->lastRow2('check_records', 'batch_desc_id', $batchDescId, 'id')[0]['check_date'];
                                                $nextCheck = $override->get('batch_description', 'batch_id', $batch['id'])[0]['next_check'];
                                                $status = $override->get('batch_description', 'batch_id', $batch['id'])[0]['check_status'];
                                                $batchId = $override->get('batch_description', 'batch_id', $batch['id'])[0]['batch_id'];
                                                // $nextCheck2 = $override->get('batch_description', 'batch_id', $batch['id'])[0]['next_check'];

                                                $amnt = $batch['amount'] - $batchItems[0]['SUM(assigned)'];
                                                // print_r($nextCheck);
                                            ?>
                                                <tr>
                                                    <td> <a href="info.php?id=5&bt=<?= $batch['id'] ?>"><?= $batch['name'] ?></a></td>
                                                    <td><?= $batch['name'] ?></td>
                                                    <td><?= $batch['last_check'] ?></td>
                                                    <td>
                                                        <?php if ($batch['next_check'] == date('Y-m-d')) { ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Check Date!</a>
                                                        <?php } elseif ($batch['next_check'] < date('Y-m-d')) { ?>
                                                            <a href="#" role="button" class="btn btn-danger">NOT CHECKED!</a>
                                                        <?php } else { ?>
                                                            <a href="#" role="button" class="btn btn-success">OK!</a>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= $batch['next_check'] ?></td>
                                                    <td>
                                                        <a href="data.php?id=8&updateId=<?= $batch['id'] ?>" class="btn btn-default">View</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="desc<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Batch Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">

                                                                    <div class="row">

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>NAME:</label>
                                                                                    <div class="col-md-9"><input type="text" name="name" value='<?= $batch['name'] ?>' readonly /> <span></span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Maintainance Status:</label>
                                                                                    <select name="maintainance_status" style="width: 100%;" required>
                                                                                        <option value="">Select Type</option>
                                                                                        <?php foreach ($override->getData('maintainance_status') as $study) { ?>
                                                                                            <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Check Date:</label>
                                                                                    <div class="col-md-9"><input type="date" name="check_date" id="check_date" /> <span></span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Next Check Date:</label>
                                                                                    <div class="col-md-9"><input type="date" name="next_check" id="next_check" /> <span></span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                    <input type="hidden" name="batch_id" value="<?= $batchId ?>">
                                                                    <input type="submit" name="update_check" value="Save updates" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="delete<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Delete Batch</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this Batch</p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                    <input type="submit" name="delete_batch" value="Delete" class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 3) { ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $remained ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 4) { ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $remained ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 5) { ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%"></th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $remained ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <a href="#" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 6) { ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%"></th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $balance = $override->get('batch_description', 'batch_id', $records['id'])[0]['quantity'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $balance ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <a href="data.php?id=10&report_id=<?= $records['id'] ?>" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 7) { ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">NAME</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRRE</th>
                                                <th width="10%">INITIAL</th>
                                                <th width="10%">STATUS</th>
                                                <th width="10%"></th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $used = $override->get('batch_description', 'batch_id', $records['id'])[0]['assigned'];
                                                $remained = $records['amount'] - $used;
                                                $notify = $records['notify_amount'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $balance = $override->get('batch_description', 'batch_id', $records['id'])[0]['quantity'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $records['name'] ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['amount'] ?></td>
                                                    <td><?php if ($used > 0) {
                                                            echo $used;
                                                        } else {
                                                            echo 0;
                                                        } ?></td>
                                                    <td><?= $balance ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?php if ($remained <= 0) {; ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Out of stock</a>
                                                        <?php
                                                        } elseif ($remained > $notify) {; ?>
                                                            <a href="#" role="button" class="btn btn-info btn-sm">Sufficent</a>
                                                        <?php
                                                        } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger btn-sm">Running Low</a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <a href="data.php?id=10&report_id=<?= $records['id'] ?>" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td>
                                                    <td><?= $records['details'] ?></td>
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

<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->

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
        $('#FullReport').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Report Searched</h1><div><span></span></div></div>"
            },


            dom: 'Bfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'REPORT',
                    className: 'btn-primary',
                    // displayFormat: 'dddd D MMMM YYYY',
                    // wireFormat: 'YYYY-MM-DD',
                    // columnDefs: [{
                    // targets: [6],
                    // render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    // }],
                },
                {
                    extend: 'pdfHtml5',
                    title: 'REPORT',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },
                // {
                //     extend: 'csvHtml5',
                //     title: 'REPORT',
                //     className: 'btn-primary'
                // },
                // {
                //     extend: 'copyHtml5',
                //     title: 'VISITS',
                //     className: 'btn-primary'
                // },
                //     {
                //         extend: 'print',
                //         // name: 'printButton'
                //         title: 'VISITS'
                //     }
            ],

            // paging: true,
            // scrollY: 10
        });
    });
</script>

</html>