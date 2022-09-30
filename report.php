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
                try {
                    switch (Input::get('report')) {
                        case 1:
                            $data = $override->searchBtnDate2('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'));
                            break;
                        case 2:
                            $data = $override->searchBtnDateSufficient('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'notify_amount', 'amount', 'type', 1, 'status', 1);
                            break;
                        case 3:
                            $data = $override->searchBtnDateLow('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'notify_amount', 'amount', 'type', 1, 'status', 1);
                            break;
                        case 4:
                            $data = $override->searchBtnDateOutStock('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'amount',0, 'type', 1, 'status', 1);
                            break;
                        case 5:
                            $data = $override->searchBtnDateExpired('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'expire_date', date('Y-m-d'), 'type', 1, 'status', 1);
                            break;
                        case 6:
                            $data = $override->searchBtnDateNotChecked('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'valid_date', date('Y-m-d'), 'type', 2, 'status', 1);
                            break;
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
                                            <input value="" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">End Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="text" name="end_date" id="end_date" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">Type</div>
                                        <div class="col-md-2">
                                            <select name="report" style="width: 100%;" required>
                                                <option value="">Select Report</option>
                                                <option value="1">Full Report</option>
                                                <option value="2">Sufficent Medicine</option>
                                                <option value="3">Running Low Medicine</option>
                                                <option value="4">Out of Stock Medicine</option>
                                                <option value="5">Expired Medicine</option>
                                                <option value="6">Unchecked Devices</option>
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
                                    $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } ?>
                                <?php if ($_POST && Input::get('report') == 1) { ?>
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
                                <?php } elseif ($_POST && Input::get('report') == 2) { ?>
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
                                <?php } else { ?>
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
</script>

</html>