<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$noE = 0;
$noC = 0;
$noD = 0;
$numRec = 5;
$users = $override->getData('user');
$today = date('Y-m-d');
$todayPlus30 = date('Y-m-d', strtotime($today . ' + 30 days'));
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
    }
} else {
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Dashboard | e-CTMIS </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="dashboard.php">Dashboard</a> <span class="divider"></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <div class="workplace">

                <?php include "header.php"; ?>

                <div class="dr"><span></span></div>
                <div class="row">
                    <div class="col-md-12">
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

                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>INVENTORY STATUS SUMMARY</h1>
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
                            <table id='inventory_report1' cellpadding="0" cellspacing="0" width="100%" class="table">
                                <thead>
                                    <tr>

                                        <th width="25%">Generic</th>
                                        <th width="3">Balance</th>
                                        <th width="3%"> EmKits</th>
                                        <th width="3%"> AmbKits</th>
                                        <th width="3%"> ECRm</th>
                                        <th width="3%"> DRm</th>
                                        <th width="3%"> ScRm</th>
                                        <th width="3%"> VSrm</th>
                                        <th width="3%"> Exam Rms</th>
                                        <th width="3%"> Ward</th>
                                        <th width="3%"> CTMr</th>
                                        <th width="3%"> Pharmacy</th>
                                        <th width="3%"> Other</th>
                                        <th width="3%">Check</th>
                                        <th width="3%">Validity</th>
                                        <th width="8%">Entries</th>
                                        <th width="8%">Checks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $amnt = 0;
                                    $pagNum = $override->getCount('generic', 'status', 1);
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    foreach ($override->getWithLimit('generic', 'status', 1, $page, $numRec) as $bDiscription) {
                                        $generic = $bDiscription['name'];
                                        $generic_id = $bDiscription['id'];
                                        $brand_id = $override->getNews('batch', 'brand_id', $generic_id, 'status', 1)[0]['brand_id'];
                                        $batch_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                        $batch_no = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                        $category = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['category'];
                                        $study_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['study_id'];
                                        $useCase = $override->get('use_case', 'id', $bDiscription['use_case'])[0]['name'];
                                        $useGroup = $override->get('use_group', 'id', $bDiscription['use_group'])[0]['name'];
                                        $form = $override->get('drug_cat', 'id', $bDiscription['category_id'])[0]['name'];
                                        $EmKits = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 1)[0];
                                        $EmKitsSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $EmKits['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $AmKits = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 2)[0];
                                        $AmKitsSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $AmKits['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $ECRm = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 3)[0];
                                        $ECRmSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $ECRm['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $DRm = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 4)[0];
                                        $DRmSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $DRm['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $ScRm = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 5)[0];
                                        $ScRmSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $ScRm['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $VSrm = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 6)[0];
                                        $VSrmSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $VSrm['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $ExamRms = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 7)[0];
                                        $ExamRmsSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $ExamRms['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $Ward = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 8)[0];
                                        $WardSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $Ward['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $CTMr = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 9)[0];
                                        $CTMrSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $CTMr['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $Pharmacy = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 10)[0];
                                        $PharmacySumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $Pharmacy['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $Other = $override->getNews('assigned_batch', 'generic_id', $bDiscription['id'], 'location_id', 96)[0];
                                        $OtherSumLoc = $override->getSumD3('assigned_batch', 'balance', 'generic_id', $bDiscription['id'],'location_id', $Other['location_id'], 'status', 1)[0]['SUM(balance)'];   

                                        $sumLoctn = $override->getSumD1('assigned_batch', 'balance', 'generic_id', $bDiscription['id'])[0]['SUM(balance)'];
                                        $sumNotify = $override->getSumD1('assigned_batch', 'notify_quantity', 'generic_id', $bDiscription['id'])[0]['SUM(notify_quantity)'];
                                        $Notify = $bDiscription['notify_quantity'];
                                        $balance = $bDiscription['balance'];
                                        $batchBalance = $override->getSumD2('batch', 'balance', 'generic_id', $bDiscription['id'], 'status', 1)[0]['SUM(balance)'];    

                                        $check = 0;
                                        $check1 = 0;
                                        foreach ($override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1) as $batch2) {
                                            $nextCheck = $batch2['next_check'];
                                            $expireDate = $batch2['expire_date'];
                                            if ($nextCheck <= date('Y-m-d')) {
                                                $check = 1;
                                            }

                                            if ($expireDate <= date('Y-m-d')) {
                                                $check1 = 1;
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td><a href="data.php?id=7&did=<?= $bDiscription['id'] ?>"><?= $generic ?></a></td>
                                            <td>
                                                <?php if ($batchBalance <= $Notify && $batchBalance > 0) { ?>
                                                    <a href="#" role="button" class="btn btn-warning"><?= $balance; ?></a>
                                                <?php } elseif ($batchBalance == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger"><?= $balance; ?></a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success"><?= $balance; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td><?php if ($EmKitsSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($EmKitsSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=1&gid=<?= $bDiscription['id'] ?>&lbid=<?= $EmKits['id'] ?>" role="button" class="btn btn-danger"><?= $EmKitsSumLoc; ?></a>
                                                    <?php } elseif ($EmKits['notify_quantity'] >= $EmKitsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=1&gid=<?= $bDiscription['id'] ?>&lbid=<?= $EmKits['id'] ?>" role="button" class="btn btn-warning"><?= $EmKitsSumLoc; ?></a>
                                                    <?php  } elseif ($EmKits['notify_quantity'] < $EmKitsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=1&gid=<?= $bDiscription['id'] ?>&lbid=<?= $EmKits['id'] ?>" role="button" class="btn btn-info"><?= $EmKitsSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($AmKitsSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                <?php if ($AmKitsSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=2&gid=<?= $bDiscription['id'] ?>&lbid=<?= $AmKits['id'] ?>" role="button" class="btn btn-danger"><?= $AmKitsSumLoc; ?></a>
                                                    <?php } elseif ($AmKits['notify_quantity'] >= $AmKitsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=2&gid=<?= $bDiscription['id'] ?>&lbid=<?= $AmKits['id'] ?>" role="button" class="btn btn-warning"><?= $AmKitsSumLoc; ?></a>
                                                    <?php  } elseif ($AmKits['notify_quantity'] < $AmKitsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=2&gid=<?= $bDiscription['id'] ?>&lbid=<?= $AmKits['id'] ?>" role="button" class="btn btn-info"><?= $AmKitsSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ECRmSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($ECRmSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=3&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ECRm['id'] ?>" role="button" class="btn btn-danger"><?= $ECRmSumLoc; ?></a>
                                                    <?php } elseif ($ECRm['notify_quantity'] >= $ECRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=3&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ECRm['id'] ?>" role="button" class="btn btn-warning"><?= $ECRmSumLoc; ?></a>
                                                    <?php  } elseif ($ECRm['notify_quantity'] < $ECRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=3&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ECRm['id'] ?>" role="button" class="btn btn-info"><?= $ECRmSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($DRmSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($DRmSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=4&gid=<?= $bDiscription['id'] ?>&lbid=<?= $DRm['id'] ?>" role="button" class="btn btn-danger"><?= $DRmSumLoc; ?></a>
                                                    <?php } elseif ($DRm['notify_quantity'] >= $DRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=4&gid=<?= $bDiscription['id'] ?>&lbid=<?= $DRm['id'] ?>" role="button" class="btn btn-warning"><?= $DRmSumLoc; ?></a>
                                                    <?php  } elseif ($DRm['notify_quantity'] < $DRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=4&gid=<?= $bDiscription['id'] ?>&lbid=<?= $DRm['id'] ?>" role="button" class="btn btn-info"><?= $DRmSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ScRmSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($ScRmSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=5&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ScRm['id'] ?>" role="button" class="btn btn-danger"><?= $ScRmSumLoc; ?></a>
                                                    <?php } elseif ($ScRm['notify_quantity'] >= $ScRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=5&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ScRm['id'] ?>" role="button" class="btn btn-warning"><?= $ScRmSumLoc; ?></a>
                                                    <?php  } elseif ($ScRm['notify_quantity'] < $ScRmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=5&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ScRm['id'] ?>" role="button" class="btn btn-info"><?= $ScRmSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($VSrmSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($VSrmSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=6&gid=<?= $bDiscription['id'] ?>&lbid=<?= $VSrm['id'] ?>" role="button" class="btn btn-danger"><?= $VSrmSumLoc; ?></a>
                                                    <?php } elseif ($VSrm['notify_quantity'] >= $VSrmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=6&gid=<?= $bDiscription['id'] ?>&lbid=<?= $VSrm['id'] ?>" role="button" class="btn btn-warning"><?= $VSrmSumLoc; ?></a>
                                                    <?php  } elseif ($VSrm['notify_quantity'] < $VSrmSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=6&gid=<?= $bDiscription['id'] ?>&lbid=<?= $VSrm['id'] ?>" role="button" class="btn btn-info"><?= $VSrmSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ExamRmsSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($ExamRmsSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=7&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ExamRms['id'] ?>" role="button" class="btn btn-danger"><?= $ExamRmsSumLoc; ?></a>
                                                    <?php } elseif ($ExamRms['notify_quantity'] >= $ExamRmsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=7&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ExamRms['id'] ?>" role="button" class="btn btn-warning"><?= $ExamRmsSumLoc; ?></a>
                                                    <?php  } elseif ($ExamRms['notify_quantity'] < $ExamRmsSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=7&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ExamRms['id'] ?>" role="button" class="btn btn-info"><?= $ExamRmsSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($WardSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                   <?php if ($WardSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=8&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-danger"><?= $WardSumLoc; ?></a>
                                                    <?php } elseif ($Ward['notify_quantity'] >= $WardSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=8&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-warning"><?= $WardSumLoc; ?></a>
                                                    <?php  } elseif ($Ward['notify_quantity'] < $WardSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=8&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-info"><?= $WardSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($CTMrSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($CTMrSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=9&gid=<?= $bDiscription['id'] ?>&lbid=<?= $CTMr['id'] ?>" role="button" class="btn btn-danger"><?= $CTMrSumLoc; ?></a>
                                                    <?php } elseif ($CTMr['notify_quantity'] >= $CTMrSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=9&gid=<?= $bDiscription['id'] ?>&lbid=<?= $CTMr['id'] ?>" role="button" class="btn btn-warning"><?= $CTMrSumLoc; ?></a>
                                                    <?php  } elseif ($CTMr['notify_quantity'] < $CTMrSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=9&gid=<?= $bDiscription['id'] ?>&lbid=<?= $CTMr['id'] ?>" role="button" class="btn btn-info"><?= $CTMrSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($PharmacySumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($PharmacySumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=10&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Pharmacy['id'] ?>" role="button" class="btn btn-danger"><?= $PharmacySumLoc; ?></a>
                                                    <?php } elseif ($Pharmacy['notify_quantity'] >= $PharmacySumLoc) { ?>
                                                        <a href="data.php?id=13&lid=10&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Pharmacy['id'] ?>" role="button" class="btn btn-warning"><?= $PharmacySumLoc; ?></a>
                                                    <?php  } elseif ($Pharmacy['notify_quantity'] < $PharmacySumLoc) { ?>
                                                        <a href="data.php?id=13&lid=10&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Pharmacy['id'] ?>" role="button" class="btn btn-info"><?= $PharmacySumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>

                                            <td><?php if ($OtherSumLoc == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($OtherSumLoc == 0) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=96&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Other['id'] ?>" role="button" class="btn btn-danger"><?= $OtherSumLoc; ?></a>
                                                    <?php } elseif ($Other['notify_quantity'] >= $OtherSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=96&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Other['id'] ?>" role="button" class="btn btn-warning"><?= $OtherSumLoc; ?></a>
                                                    <?php  } elseif ($Other['notify_quantity'] < $OtherSumLoc) { ?>
                                                        <a href="data.php?id=13&lid=96&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Other['id'] ?>" role="button" class="btn btn-info"><?= $OtherSumLoc; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td>
                                                <?php if ($check) { ?>
                                                    <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning btn-sm check" check_id="<?= $bDiscription['id'] ?>" data-toggle="modal" id="check">Not Checked!</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success btn-sm check" data-toggle="modal" id="check">OK!</a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($check1) { ?>
                                                    <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Expired</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success" data-toggle="modal">OK!</a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="data.php?id=11&gid=<?= $bDiscription['id'] ?>" class="btn btn-default">View</a>
                                            </td>
                                            <td>
                                                <a href="data.php?id=8&gid=<?= $bDiscription['id'] ?>" class="btn btn-default">View</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                    <div class="btn-group">
                        <a href="dashboard.php?page=<?php if (($_GET['page'] - 1) > 0) {
                                                        echo $_GET['page'] - 1;
                                                    } else {
                                                        echo 1;
                                                    } ?>" class="btn btn-default">
                            < </a>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <a href="dashboard.php?page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                echo 'active';
                                                                                                                            } ?>"><?= $i ?></a>
                                <?php } ?>
                                <a href="dashboard.php?page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                echo $_GET['page'] + 1;
                                                            } else {
                                                                echo $i - 1;
                                                            } ?>" class="btn btn-default"> > </a>
                    </div>
                </div>
                <div class="row">

                </div>

                <div class="dr"><span></span></div>
            </div>

        </div>
    </div>

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

            $(document).on('click', '.update', function() {
                var getUid = $(this).attr('update-data');
                $.ajax({
                    url: "process.php?content=update_generic_name",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#update_generic_id').val(data.update_generic_id);
                        // $('#fl_wait').hide();
                    }
                });
            })

            $('document').on('click', '.update', function() {
                var getUid = $(this).attr('update-data');
                // $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=update_generic_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        console.log(data);
                        $('#update_brand_id').html(data);
                        // $('#fl_wait').hide();
                    }
                });

            });


            $('#update_brand_id').change(function() {
                var getUid = $(this).val();
                // $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=update_brand_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#update_batch_id').html(data);
                        // $('#fl_wait').hide();
                    }
                });

            });

            $('#update_batch_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=update_batch_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#update_batch_no').val(data.batch_no);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#update_batch_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=update_batch_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#update_category_id').val(data.category);
                        $('#fl_wait').hide();
                    }
                });

            });

        });
    </script>
</body>

</html>