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
$numRec = 13;
$users = $override->getData('user');

// $number = $nextCheck;
// $nextCheck = date('Y-m-d', strtotime($checkDate . ' + 30 days'));

$today = date('Y-m-d');
$todayPlus30 = date('Y-m-d', strtotime($today . ' + 30 days'));


if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('edit_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('position', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Position Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_staff')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                ),
                'email_address' => array(),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    //                    $staffSites=$override->get('staff_sites', 'staff_id', Input::get('id'));
                    //                    $staffStudy=$override->get('staff_study', 'staff_id', Input::get('id'));
                    $user->updateRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                    ), Input::get('id'));

                    //                    if($staffSites){
                    //                        $currentSite=array();
                    //                        foreach ($staffSites as $staffSite){
                    //                            array_push($currentSite, $staffSite['site_id']);
                    ////                            $user->deleteRecord('staff_sites','id',$staffSite['id']);
                    //                        }
                    //                        $changeSites = array_diff($currentSite,Input::get('sites'));
                    //                        if($changeSites){
                    //                            foreach ($changeSites as $changeSite){
                    //                                if(in_array($changeSite, $currentSite)){
                    ////                                    $user->deleteRecord('staff_sites','id',$changeSite);
                    //                                    print_r($changeSite);echo ' To delete, ';
                    //                                }else{
                    ////                                    $user->createRecord('staff_sites', array(
                    ////                                        'staff_id' => Input::get('id'),
                    ////                                        'site_id' => $changeSite,
                    ////                                    ));
                    //                                    print_r('New Site ,');
                    //                                }
                    //                            }
                    //                        }
                    //                    }
                    //                    foreach (Input::get('sites') as $site){
                    //                        $user->createRecord('staff_sites', array(
                    //                            'staff_id' => Input::get('id'),
                    //                            'site_id' => $site,
                    //                        ));
                    //                    }

                    $successMessage = 'Account Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('edit_batch')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'batch_no' => array(
                    'required' => true,
                ),
                'study' => array(
                    'required' => true,
                ),
                'amount' => array(
                    'required' => true,
                ),
                'manufactured_date' => array(
                    'required' => true,
                ),
                'expire_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('batch', array(
                        'name' => Input::get('name'),
                        'study_id' => Input::get('study'),
                        'batch_no' => Input::get('batch_no'),
                        'amount' => Input::get('amount'),
                        'notify_amount' => Input::get('notify_amount'),
                        'manufacturer' => Input::get('manufacturer'),
                        'manufactured_date' => Input::get('manufactured_date'),
                        'expire_date' => Input::get('expire_date'),
                        'create_on' => date('Y-m-d'),
                        'details' => Input::get('details'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ), Input::get('id'));

                    $successMessage = 'Batch Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('archive_batch')) {
            $user->updateRecord('batch', array(
                'status' => 4,
            ), Input::get('id'));

            $user->updateRecord('batch_description', array(
                'status' => 4,
            ), Input::get('id'));

            $successMessage = 'Medicine / Device Archived Successful';
        } elseif (Input::get('delete_batch')) {
            $user->updateRecord('batch', array(
                'status' => 0,
            ), Input::get('id'));

            $user->updateRecord('batch_description', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Medicine / Device Destroyed / Burned Successful';
        } elseif (Input::get('edit_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('sites', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Site Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_study')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'pi' => array(
                    'required' => true,
                ),
                'coordinator' => array(
                    'required' => true,
                ),
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('study', array(
                        'name' => Input::get('name'),
                        'pi_id' => Input::get('pi'),
                        'co_id' => Input::get('coordinator'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                        'details' => Input::get('details'),
                    ), Input::get('id'));

                    $successMessage = 'Study Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_drug_cat')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('drug_cat', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Drug Category Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_batch_desc')) {
            $validate = $validate->check($_POST, array(
                'batch' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
                'category' => array(
                    'required' => true,
                ),
                'quantity' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $descSum = 0;
                $bSum = 0;
                $dSum = 0;
                $descSum = $override->getSumD1('batch_description', 'quantity', 'batch_id', Input::get('batch'));
                $bSum = $override->get('batch', 'id', Input::get('batch'))[0];
                $dSum = $descSum[0]['SUM(quantity)'] + Input::get('quantity');
                if ($dSum <= $bSum['amount']) {
                    try {
                        $user->updateRecord('batch_description', array(
                            'name' => Input::get('name'),
                            'cat_id' => Input::get('category'),
                            'quantity' => Input::get('quantity'),
                            'notify_amount' => Input::get('notify_amount'),
                        ), Input::get('id'));
                        $successMessage = 'Batch Description Successful Updated';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $errorMessage = 'Exceeded Batch Amount, Please cross check and try again';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_group')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('use_group', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Group Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_use_case')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('use_case', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Use Case Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_location')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('location', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Location Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_check')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'check_date' => array(
                    'required' => true,
                ),
                'next_check' => array(
                    'required' => true,
                )
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('check_records', array(
                        'product_id' => Input::get('id'),
                        'last_check' => Input::get('check_date'),
                        'next_check' => Input::get('next_check'),
                        'create_on' => date('Y-m-d'),
                        'staff_id' => $user->data()->id,
                        'check_type' => Input::get('maintainance'),
                        'status' => Input::get('maintainance_status'),
                        'remark' => Input::get('remark'),
                    ));

                    $BatchLastRow1 = $override->lastRow('check_records', 'id');
                    $user->updateRecord('batch_product', array('next_check' => Input::get('next_check')), $BatchLastRow1[0]['product_id']);
                    $user->updateRecord('batch_product', array('last_check' => Input::get('check_date')), $BatchLastRow1[0]['product_id']);
                    $successMessage = 'Check Status Updated Successful';
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
    <title> Info | Pharmacy </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Info</a> <span class="divider">></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <?php include "header.php"; ?>

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
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Expired Medicine</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="20%">Generic Name</th>
                                            <th width="10%">Batch No</th>
                                            <th width="10%">Drug Category</th>
                                            <th width="10%">Currently Quantity</th>
                                            <th width="10%">Remained</th>
                                            <th width="10%">Expire Date</th>
                                            <th width="15%">Status</th>
                                            <th width="25%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'expire_date', $today, 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        $amnt = 0;
                                        foreach ($override->getWithLimitLessThanDate('batch', 'expire_date', $today, 'status', 1, $page, $numRec) as $batchDesc) {
                                            $batch_no = $override->get('batch', 'id', $batchDesc['id'])[0];
                                            $dCat = $override->get('drug_cat', 'id', $batchDesc['id'])[0];
                                            $amnt = $batchDesc['quantity'] - $batchDesc['assigned'];
                                            $used = $override->get('batch_description', 'batch_id', $batchDesc['id'])[0];
                                        ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><?= $batchDesc['name'] ?></td>
                                                <td><?= $batchDesc['batch_no'] ?></td>
                                                <td><?= $dCat['name'] ?></td>
                                                <td><?= $batchDesc['amount'] ?></td>
                                                <td><?= $used['assigned'] ?></td>
                                                <td><?= $batchDesc['expire_date'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['expire_date'] <= $today) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Expired</a>
                                                    <?php } elseif ($batchDesc['expire_date'] > $today) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Not Expired</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal">Un - Checked</a>
                                                    <?php } ?>
                                                </td>

                                                <td>
                                                    <a href="#archive<?= $batchDesc['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Archive</a>
                                                    <a href="#burn<?= $batchDesc['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Burn / Destroy</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="archive<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to Archive this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="submit" name="archive_batch" value="Archive" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="burn<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to destroy / Burn this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="submit" name="delete_batch" value="Burn / Destroy" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 2) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of medicine 30 days Before Expiration Date</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Generic Name</th>
                                            <th width="25%">Study</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%">Expire Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'expire_date', $todayPlus30, 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimitLessThan30('batch', 'expire_date', $todayPlus30, 'status', 1, $page, $numRec) as $list) {
                                            $study = $override->get('study', 'id', $list['study_id'])[0]['name'];
                                            // print_r($study);
                                        ?>
                                            <tr>
                                                <td> <a href="info.php?id=7&bt=<?= $list['id'] ?>"><?= $list['name'] ?></a></td>
                                                <td><?= $study ?></td>
                                                <td><?= $list['amount'] ?></td>
                                                <td><?= $list['expire_date'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 3) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Batch Description</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="35%">Generic Name</th>
                                            <th width="15%">Batch No</th>
                                            <th width="10%">Drug Category</th>
                                            <th width="10%">Current Quantity</th>
                                            <th width="10%">Last Check Date</th>
                                            <th width="25%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimitLessThanDate('batch_description', 'next_check_date', $today, 'status', 1, $page, $numRec) as $batchDesc) {
                                            $batch_no = $override->get('batch', 'id', $batchDesc['batch_id'])[0];
                                            $dCat = $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0] ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>"><?= $batchDesc['name'] ?></a></td>
                                                <td><?= $batch_no['batch_no'] ?></td>
                                                <td><?= $dCat['name'] ?></td>
                                                <td> <?= $batchDesc['quantity'] ?></td>
                                                <td> <?= $batchDesc['last_check_date'] ?></td>
                                                <td>
                                                    <!-- <a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>" class="btn btn-info">Details</a> -->
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 4) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Unchecked Devices / Medicines</h1>
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
                                <table id='tableId4' cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Last Check</th>
                                            <th width="5%">Status</th>
                                            <th width="10%">Next Check</th>
                                            <th width="10%">Remarks</th>
                                            <th width="20%">Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_product', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        $type = $_GET['type'];
                                        foreach ($override->getNews('batch_product', 'status', 1, 'use_group', $type) as $batch) {
                                            $generic = $override->get('generic', 'id', $batch['generic_id'])[0]['name'];
                                            $brand = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                        ?>
                                            <tr>
                                                <td> <a href="info.php?id=5&bt=<?= $batch['id'] ?>"><?= $generic ?></a></td>
                                                <td><?= $brand ?></td>
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
                                                <td><?= $batch['remark'] ?></td>
                                                <td>
                                                    <a href="data.php?id=8&updateId=<?= $batch['id'] ?>" class="btn btn-default">View</a>
                                                    <a href="#desc<?= $batch['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
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

                                                                    <div class="col-sm-12">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>NAME:</label>
                                                                                <div class="col-md-9"><input type="text" name="name" value='<?= $brand ?>' readonly /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">


                                                                    <div class="col-sm-6">
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
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Check Date:</label>
                                                                                <div class="col-md-9"><input type="date" name="check_date" id="check_date" /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">


                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Remark:</label>
                                                                                <div class="col-md-9"><input type="text" name="remark" id="remark" /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-6">
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
                                                                <input type="hidden" name="maintainance" value="<?= $batch['maintainance'] ?>">
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
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 19) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Product Assignment History</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="15%">Staff Name</th>
                                            <th width="25%">Study</th>
                                            <th width="25%">Drug</th>
                                            <th width="10%">Quantity</th>
                                            <th width="25%">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->get('assigned_stock_rec', 'drug_id', $_GET['dsc']) as $batchDesc) {
                                            $study = $override->get('study', 'id', $batchDesc['study_id'])[0];
                                            $staff = $override->get('user', 'id', $batchDesc['staff_id'])[0];
                                            $drug = $override->get('batch_description', 'id', $_GET['dsc'])[0];
                                        ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><a href="#"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></a></td>
                                                <td><?= $study['name'] ?></td>
                                                <td><?= $drug['name'] ?></td>
                                                <td> <?= $batchDesc['quantity'] ?></td>
                                                <td><?= $batchDesc['create_on'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 6) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Batch Description</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="10%">Generic</th>
                                            <th width="10%">Brand</th>
                                            <th width="5%"> Use Case</th>
                                            <th width="5%">Quantity</th>
                                            <th width="5%"> ICU</th>
                                            <th width="5%"> EmKit</th>
                                            <th width="5%"> EmBuffer</th>
                                            <th width="5%"> AmbKit</th>
                                            <th width="5%"> CTM Room</th>
                                            <th width="5%"> Exam Room</th>
                                            <th width="5%"> Pharmacy</th>
                                            <th width="5%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_description', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->get4b('batch_description', 'status', 1, $page, $numRec) as $bDiscription) {
                                            $useGroup = $override->get('use_group', 'id', $bDiscription['use_group'])[0]['name'];
                                            $useCase = $override->get('use_case', 'id', $bDiscription['use_case'])[0]['name'];
                                            $icu = ($override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 1)[0]['quantity']);
                                            $EmKit = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 2)[0]['quantity'];
                                            $EmBuffer = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 3)[0]['quantity'];
                                            $AmKit = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 4)[0]['quantity'];
                                            $CTM = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 5)[0]['quantity'];
                                            $Exam = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 6)[0]['quantity'];
                                            $Pharmacy = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 7)[0]['quantity'];
                                            $sumLoctn = $override->getSumD1('batch_guide_records', 'quantity', 'batch_description_id', $bDiscription['id'])[0]['SUM(quantity)'];

                                        ?>
                                            <tr>
                                                <td><?= $bDiscription['name'] ?></td>
                                                <td><?= $bDiscription['name'] ?></td>
                                                <td><?= $useCase ?></td>
                                                <td><?= $bDiscription['quantity'] ?></td>
                                                <td><?php if ($icu) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $icu; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($EmKit) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $EmKit; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($EmBuffer) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $EmBuffer; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($AmKit) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $AmKit; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($CTM) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $CTM; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($Exam) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $Exam; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($Pharmacy) { ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $Pharmacy; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if ($bDiscription['quantity'] <= $bDiscription['notify_amount'] && $bDiscription['quantity'] > 0) { ?>
                                                        <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                    <?php } elseif ($bDiscription['quantity'] == 0) { ?>
                                                        <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a href="info.php?id=16&gid=<?= $bDiscription['id'] ?>" class="btn btn-info">View</a>
                                                    <a href="#edit_stock_guide_id<?= $bDiscription['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                    <!-- <a href="#delete<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a> -->
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="edit_stock_guide_id<?= $bDiscription['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Update Stock Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Generic Name</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $override->get('batch', 'id', $bDiscription['batch_id'])[0]['name'] ?>" type="text" id="name" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Brand Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $bDiscription['name'] ?>" class="validate[required]" type="text" name="name" id="name" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Current Quantity:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $bDiscription['quantity'] ?>" class="validate[required]" type="number" name="quantity" id="name" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Quantity to Add:</div>
                                                                            <div class="col-md-9">
                                                                                <input value=" " class="validate[required]" type="number" name="amount" id="amount" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="batch" value="<?= $bDiscription['batch_id'] ?>">
                                                                <input type="hidden" name="id" value="<?= $bDiscription['id'] ?>">
                                                                <input type="hidden" name="quantity_db" value="<?= $bDiscription['quantity'] ?>">
                                                                <input type="submit" name="update_stock_guide" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="delete<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to delete this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="submit" name="delete_file" value="Delete" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device History Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                            <!-- <li><a href="#"><span class="isw-delete"></span> Delete</a></li> -->
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table id='tableId7' cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="10%">Date</th>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="15%">Batch</th>
                                            <th width="10%">Current Received</th>
                                            <th width="5%">Added</th>
                                            <th width="5%">Used</th>
                                            <th width="5%">Balance</th>
                                            <th width="5%">Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_records', 'generic_id', $_GET['did']);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimit('batch_records', 'generic_id', $_GET['did'], $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $brand_id = $override->get('batch', 'brand_id', $batch['brand_id'])[0]['brand_id'];
                                            $generic_id = $override->get('batch', 'generic_id', $batch['generic_id'])[0]['generic_id'];
                                            $batch_no = $override->get('batch', 'brand_id', $batch['brand_id'])[0]['batch_no'];
                                            $brand = $override->get('brand', 'id', $brand_id)[0]['name'];
                                            $generic = $override->get('generic', 'id', $generic_id)[0]['name'];
                                        ?>
                                            <tr>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $generic ?></td>
                                                <td><?= $brand ?></td>
                                                <td><?= $batch_no ?></td>
                                                <td><?= $batch['quantity'] ?></td>
                                                <td><?= $batch['added'] ?></td>
                                                <td><?= $batch['assigned'] ?></td>
                                                <td><?= $batch['balance'] ?></td>
                                                <td><?= $staff ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Use Stock Guide Description</h1>
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
                                <table id='tableId8' cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic Name</th>
                                            <th width="15%">Brand Name</th>
                                            <th width="10%">check date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Date Changed</th>
                                            <th width="10%">Remarks</th>
                                            <th width="10%">Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('check_records', 'product_id', $_GET['updateId']);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('check_records', 'product_id', $_GET['updateId'], $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $brand_id = $override->get('brand', 'id', $_GET['updateId'])[0]['generic_id'];
                                            $generic = $override->get('generic', 'id', $brand_id)[0]['name'];
                                            $brand = $override->get('brand', 'id', $_GET['updateId'])[0]['name'];
                                            if ($status == 1) {
                                                $status = 'OK!';
                                            } else {
                                                $status = 'NOT OK!';
                                            } ?>

                                            <tr>
                                                <td><?= $generic ?></td>
                                                <td><?= $brand ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td><?= $status ?></td>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $batch['remark'] ?></td>
                                                <td><?= $staff ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Archive Medicines / Devices </h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic Name</th>
                                            <th width="10%">Last check date</th>
                                            <th width="10%">Date Archived / Expired</th>
                                            <th width="10%">Staff</th>
                                            <th width="10%">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch', 'status', 4);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('batch', 'status', 4, $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $status = $override->get('maintainance_status', 'id', $batch['status'])[0]['name'];
                                            $name = $override->get('batch_description', 'id', $_GET['updateId'])[0]['name'];
                                            $last_check_date = $override->get('batch_description', 'id', $_GET['updateId'])[0]['last_check_date'];
                                            $next_check_date = $override->get('batch_description', 'batch_id', $batch['id'])[0]['next_check_date'];
                                            // print_r($last_check_date);
                                        ?>
                                            <tr>
                                                <td><?= $batch['name'] ?></td>
                                                <td><?= $last_check_date ?></td>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $staff ?></td>
                                                <td>
                                                    <a href="#destroy<?= $batch['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Burn / Destroy</a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="destroy<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to destroy / Burn this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                <input type="submit" name="delete_batch" value="Burn / Destroy" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Archive Medicines / Devices </h1>
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
                                            <th width="10%">REMARKS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $amnt = 0;
                                        foreach ($override->get('batch_description_records', 'batch_description_id', $_GET['report_id']) as $batchDesc) {
                                            $batch_no = $override->get('batch', 'id', $batchDesc['batch_description_id'])[0];
                                            $name = $override->get('batch', 'id', $batchDesc['batch_description_id'])[0]['name'];
                                            $expire = $override->get('batch', 'id', $batchDesc['batch_description_id'])[0]['expire_date'];
                                            $staff = $override->get('user', 'id', $batchDesc['staff_id'])[0]['username'];
                                            $dCat = $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0];
                                            $amnt = $batchDesc['quantity'] - $batchDesc['assigned'];
                                            $balance = $override->get('batch_description', 'batch_id', $batchDesc['batch_description_id'])[0]['quantity'];
                                        ?>
                                            <tr>
                                                <td> <?= $batchDesc['create_on'] ?></td>
                                                <td> <?= $name ?></td>
                                                <td><?= $batch_no['batch_no'] ?></td>
                                                <td> <?= $batchDesc['quantity'] ?></td>
                                                <td> <?= $batchDesc['assigned'] ?></td>
                                                <!-- <td> <?= $balance ?></td>                                                     -->
                                                <td> <?= number_format($batchDesc['quantity'] - $batchDesc['assigned']) ?></td>
                                                <td> <?= $expire ?></td>
                                                <td> <?= $staff ?></td>
                                                <td> <?= $batchDesc['details'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="pull-right">
                    <div class="btn-group">
                        <a href="data.php?page=<?php if (($_GET['page'] - 1) > 0) {
                                                    echo $_GET['page'] - 1;
                                                } else {
                                                    echo 1;
                                                } ?>" class="btn btn-default">
                            < </a>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <a href="data.php?id=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                        echo 'active';
                                                                                                                    } ?>"><?= $i ?></a>
                                <?php } ?>
                                <a href="data.php?page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                            echo $_GET['page'] + 1;
                                                        } else {
                                                            echo $i - 1;
                                                        } ?>" class="btn btn-default"> > </a>
                    </div>
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
        $('#wait_ds').hide();
        $('#region').change(function() {
            var getUid = $(this).val();
            $('#wait_ds').show();
            $.ajax({
                url: "process.php?cnt=region",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#ds_data').html(data);
                    $('#wait_ds').hide();
                }
            });

        });
        $('#wait_wd').hide();
        $('#ds_data').change(function() {
            $('#wait_wd').hide();
            var getUid = $(this).val();
            $.ajax({
                url: "process.php?cnt=district",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#wd_data').html(data);
                    $('#wait_wd').hide();
                }
            });

        });
        $('#download').change(function() {
            var getUid = $(this).val();
            $.ajax({
                url: "process.php?cnt=download",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {

                }
            });

        });

        $('#tableId4').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Any Pending Issue Today</h1><div><span></span></div></div>"
            },
            // columns: columnDefs,

            dom: 'lBfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'Check_report',
                    className: 'btn-primary'
                },

                {
                    extend: 'pdfHtml5',
                    title: 'Check_report',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },


                {
                    extend: 'csvHtml5',
                    title: 'Check_report',
                    className: 'btn-primary'
                },
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
            "pageLength": 100
        });

        $('#tableId8').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Any Pending Issue Today</h1><div><span></span></div></div>"
            },
            // columns: columnDefs,

            dom: 'lBfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'Check_report',
                    className: 'btn-primary'
                },

                {
                    extend: 'pdfHtml5',
                    title: 'Check_report',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },


                {
                    extend: 'csvHtml5',
                    title: 'Check_report',
                    className: 'btn-primary'
                },
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
            "pageLength": 100
        });

        $('#tableId7').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Any Pending Issue Today</h1><div><span></span></div></div>"
            },
            // columns: columnDefs,

            dom: 'lBfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'Inventory_status_report',
                    className: 'btn-primary'
                },

                {
                    extend: 'pdfHtml5',
                    title: 'inventory_report',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },


                {
                    extend: 'csvHtml5',
                    title: 'inventory_report',
                    className: 'btn-primary'
                },
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
            "pageLength": 100
        });
    });
</script>

</html>