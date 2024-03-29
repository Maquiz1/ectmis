<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
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
        } elseif (Input::get('add_staff_study')) {
            try {
                $study_stuffs = 0;
                foreach ($override->getNews('staff_study', 'study_id', Input::get('study'), 'staff_id', Input::get('id')) as $study_stuff) {
                    if ($study_stuff) {
                        $study_stuffs = 1;
                    }
                }

                if (!$study_stuffs) {
                    $user->createRecord('staff_study', array(
                        'staff_id' => Input::get('id'),
                        'study_id' => Input::get('study'),
                        'status' => 1,
                        'create_on' => date('Y-m-d'),
                    ));

                    $successMessage = 'Stuff Added Successful to a Study';
                } else {
                    $errorMessage = 'Stuff Already Registered to a Study';
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } elseif (Input::get('remove_staff_study')) {
            try {
                $study_stuffs = 0;
                foreach ($override->getNews('staff_study', 'study_id', Input::get('study'), 'staff_id', Input::get('id')) as $study_stuff) {
                    if ($study_stuff) {
                        $study_stuffs = 1;
                    }
                }

                if ($study_stuffs) {
                    $user->createRecord('staff_study', array(
                        'staff_id' => Input::get('id'),
                        'study_id' => Input::get('study'),
                        'status' => 0,
                        'create_on' => date('Y-m-d'),
                    ));

                    $successMessage = 'Stuff Removed Successful to a Study';
                } else {
                    $errorMessage = 'Stuff Not Registered to a Study';
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } elseif (Input::get('add_study_group')) {
            try {
                $study_groups = 0;
                foreach ($override->getNews('study_group', 'group_id', Input::get('use_group_id'), 'staff_id', Input::get('id')) as $study_group) {
                    if ($study_group) {
                        $study_groups = 1;
                    }
                }

                if (!$study_groups) {
                    $user->createRecord('study_group', array(
                        'staff_id' => Input::get('id'),
                        'group_id' => Input::get('use_group_id'),
                        'status' => 1,
                        'create_on' => date('Y-m-d'),
                    ));

                    $successMessage = 'Group Added Successful to a Study';
                } else {
                    $user->updateRecord('study_group', array(
                        'group_id' => Input::get('use_group_id'),
                        'status' => 1,
                        'create_on' => date('Y-m-d'),
                    ), Input::get('id'));
                    $successMessage = 'Group Updated Successful to a Study';
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } elseif (Input::get('update_user_group')) {
            try {
                $study_groups = 0;
                foreach ($override->getNews('study_group', 'group_id', Input::get('use_group_id'), 'staff_id', Input::get('id')) as $study_group) {
                    if ($study_group) {
                        $study_groups = 1;
                    }
                }

                if ($study_groups) {
                    $user->updateRecord('study_group', array(
                        'staff_id' => Input::get('id'),
                        'group_id' => Input::get('use_group_id'),
                        'status' => 1,
                        'create_on' => date('Y-m-d'),
                    ));

                    $successMessage = 'Group Update Successful to a Study';
                } else {
                    $errorMessage = 'Group Already Registered to a Study';
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } elseif (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
        } elseif (Input::get('reactivate_user')) {
            $user->updateRecord('user', array(
                'count' => 0,
            ), Input::get('id'));
            $successMessage = 'User Re-activated Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('edit_generic')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('generic', array(
                        'name' => Input::get('name'),
                        'notify_quantity' => Input::get('notify_quantity'),
                        'maintainance' => Input::get('maintainance'),
                    ), Input::get('id'));

                    foreach ($override->get('batch', 'generic_id', Input::get('id')) as $batch) {
                        if ($batch) {
                            $user->updateRecord('batch', array(
                                'maintainance' => Input::get('maintainance'),
                            ), $batch['id']);
                        }
                    }

                    $successMessage = 'Generic Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
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
        } elseif (Input::get('delete_generic')) {
            $user->updateRecord('generic', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Generic Deleted Successful';
        } elseif (Input::get('delete_batch')) {
            $user->updateRecord('batch', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Batch Deleted Successful';
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
        } elseif (Input::get('add_site')) {
            try {
                $study_stuffs = 0;
                foreach ($override->getNews('study_sites', 'study_id', Input::get('id'), 'site_id', Input::get('site')) as $study_stuff) {
                    if ($study_stuff) {
                        $study_stuffs = 1;
                    }
                }

                if (!$study_stuffs) {
                    $user->createRecord('study_sites', array(
                        'site_id' => Input::get('site'),
                        'study_id' => Input::get('id'),
                        'status' => 1,
                        'create_on' => date('Y-m-d'),
                    ));

                    $successMessage = 'Site Added Successful to a Study';
                } else {
                    $errorMessage = 'Site Already Registered to a Study';
                }
            } catch (Exception $e) {
                die($e->getMessage());
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
        } elseif (Input::get('update_location')) {
            try {
                $update_location = 0;
                foreach ($override->getNews('generic_location', 'generic_id', Input::get('id'), 'location_id', Input::get('generic_location')) as $location) {
                    if ($location) {
                        $update_location = 1;
                    }
                }

                if (!$update_location) {
                    $user->createRecord('generic_location', array(
                        'generic_id' => Input::get('id'),
                        'notify_quantity' => Input::get('notify_quantity'),
                        'location_id' => Input::get('generic_location'),
                        'create_on' => date('Y-m-d'),
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                    ));
                    $successMessage = 'Location Added Successful';
                } else {
                    $errorMessage = 'Location Already Registered';
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } elseif (Input::get('remove_location')) {
            try {
                $user->updateRecord('generic_location', array(
                    'status' => 0,
                ), Input::get('id'));

                // $user->updateRecord('generic_location', array(
                //     'status' => 0,
                // ), Input::get('id'));
                $successMessage = 'Location Removed Successful';
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }


        if ($_GET['id'] == 11) {
            $data = null;
            $filename = null;
            if (Input::get('full_report')) {
                $data = $override->get('batch_records', 'status', 1);
                $filename = 'Full Stock Report' . '-' . date('Y-m-d');
            } elseif (Input::get('sufficient')) {
                $data = $override->get4('batch', 'amount', 'notify_amount', 'status', 1, 'type', 1);
                $filename = 'SUFFICIENT' . '-' . date('Y-m-d');
            } elseif (Input::get('running_low')) {
                $data = $override->get5('batch', 'amount', 'notify_amount', 'status', 1, 'type', 1);
                $filename = 'RUNNING LOW' . '-' . date('Y-m-d');
            } elseif (Input::get('out_stock')) {
                $data = $override->get6('batch', 'amount', 0, 'status', 1, 'type', 1);
                $filename = 'Out of Stock' . '-' . date('Y-m-d');
            } elseif (Input::get('expired')) {
                $data = $override->get7('batch', 'expire_date', date('Y-m-d'), 'status', 1, 'type', 1);
                $filename = 'Expired' . '-' . date('Y-m-d');
            } elseif (Input::get('not_checked')) {
                $data = $override->get4('batch', 'amount', 'notify_amount', 'status', 1, 'type', 2);
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
    <title> Info | e-CTMIS </title>
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

            <div class="workplace">

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
                                    <h1>List of Staff</h1>
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
                                                <th width="10%">Name</th>
                                                <th width="8%">Username</th>
                                                <th width="8%">Position</th>
                                                <th width="40%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->get('user', 'status', 1) as $staff) {
                                                $position = $override->get('position', 'id', $staff['position'])[0] ?>
                                                <tr>
                                                    <td> <?= $staff['firstname'] . ' ' . $staff['lastname'] ?></td>
                                                    <td><?= $staff['username'] ?></td>
                                                    <td><?= $position['name'] ?></td>
                                                    <td>
                                                        <a href="#user<?= $staff['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                        <a href="#reset<?= $staff['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Reset</a>
                                                        <a href="#reactivate_user<?= $staff['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Reactivate</a>
                                                        <a href="#delete<?= $staff['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        <a href="#add_user_study<?= $staff['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Add User Study</a>
                                                        <a href="#remove_user_study<?= $staff['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Remove User Study</a>
                                                        <a href="#add_user_group<?= $staff['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Add User to Group</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="user<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit User Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">First name:</div>
                                                                                <div class="col-md-9"><input type="text" name="firstname" value="<?= $staff['firstname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Last name:</div>
                                                                                <div class="col-md-9"><input type="text" name="lastname" value="<?= $staff['lastname'] ?>" required /></div>
                                                                            </div>
                                                                            <!--                                                                <div class="row-form clearfix">-->
                                                                            <!--                                                                    <div class="col-md-5">Select Study:</div>-->
                                                                            <!--                                                                    <div class="col-md-7">-->
                                                                            <!--                                                                        <select name="study[]" id="s2_2" style="width: 100%;" multiple="multiple" required>-->
                                                                            <!--                                                                            <option value="">choose a study...</option>-->
                                                                            <!--                                                                            --><?php //foreach ($override->getData('study') as $study){
                                                                                                                                                                ?>
                                                                            <!--                                                                                <option value="--><? //=$study['id']
                                                                                                                                                                                    ?>
                                                                            <!--">--><? //=$study['name']
                                                                                        ?>
                                                                            <!--</option>-->
                                                                            <!--                                                                            --><?php //}
                                                                                                                                                                ?>
                                                                            <!--                                                                        </select>-->
                                                                            <!--                                                                    </div>-->
                                                                            <!--                                                                </div>-->
                                                                            <!--                                                                <div class="row-form clearfix">-->
                                                                            <!--                                                                    <div class="col-md-5">Select sites:</div>-->
                                                                            <!--                                                                    <div class="col-md-7">-->
                                                                            <!--                                                                        <select name="sites[]" id="s2_1" style="width: 100%;" multiple="multiple" required>-->
                                                                            <!--                                                                            <option value="">choose a site...</option>-->
                                                                            <!--                                                                            --><?php //foreach ($override->getData('sites') as $site){
                                                                                                                                                                ?>
                                                                            <!--                                                                                <option value="--><? //=$site['id']
                                                                                                                                                                                    ?>
                                                                            <!--">--><? //=$site['name']
                                                                                        ?>
                                                                            <!--</option>-->
                                                                            <!--                                                                            --><?php //}
                                                                                                                                                                ?>
                                                                            <!--                                                                        </select>-->
                                                                            <!--                                                                    </div>-->
                                                                            <!--                                                                </div>-->
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Position</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="position" style="width: 100%;" required>
                                                                                        <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                                                        <?php foreach ($override->getData('position') as $position) { ?>
                                                                                            <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Phone Number:</div>
                                                                                <div class="col-md-9"><input value="<?= $staff['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">E-mail Address:</div>
                                                                                <div class="col-md-9"><input value="<?= $staff['email_address'] ?>" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                    <input type="submit" name="edit_staff" value="Save updates" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="reset<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Reset Password</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Are you sure you want to reset password to default (12345678)</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                    <input type="submit" name="reset_pass" value="Reset" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="reactivate_user<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Re-activate User Acount</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Are you sure you want to re-activate This User Acount</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                    <input type="submit" name="reactivate_user" value="Reactivate User" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="delete<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Delete User</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this user</p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                    <input type="submit" name="delete_staff" value="Delete" class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- <div class="modal fade" id="add_user_study<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit User Study Info </h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">First name:</div>
                                                                                <div class="col-md-9"><input type="text" name="firstname" value="<?= $staff['firstname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Last name:</div>
                                                                                <div class="col-md-9"><input type="text" name="lastname" value="<?= $staff['lastname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-5">Select Study:</div>
                                                                                <div class="col-md-7">
                                                                                    <select name="study" id="study_id" style="width: 100%;" required>
                                                                                        <option value="">choose a Study...</option>
                                                                                        <?php foreach ($override->getData('study') as $site) {
                                                                                        ?>
                                                                                            <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                                                        <?php }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="add_staff_study" value="Save updates" class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="remove_user_study<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit User Study Info </h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">First name:</div>
                                                                                <div class="col-md-9"><input type="text" name="firstname" value="<?= $staff['firstname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Last name:</div>
                                                                                <div class="col-md-9"><input type="text" name="lastname" value="<?= $staff['lastname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-5">Select sites:</div>
                                                                                <div class="col-md-7">
                                                                                    <select name="study" id="s2_1" style="width: 100%;" required>
                                                                                        <option value="">choose a Study...</option>
                                                                                        <?php foreach ($override->getData('study') as $site) {
                                                                                        ?>
                                                                                            <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                                                        <?php }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="remove_staff_study" value="Save updates" class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                        </form>
                                                    </div>
                                                </div> -->
                                                <div class="modal fade" id="add_user_group<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit User Study Info </h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">First name:</div>
                                                                                <div class="col-md-9"><input type="text" name="firstname" value="<?= $staff['firstname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Last name:</div>
                                                                                <div class="col-md-9"><input type="text" name="lastname" value="<?= $staff['lastname'] ?>" required /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-5">Select Study:</div>
                                                                                <div class="col-md-7">
                                                                                    <select name="use_group_id" id="use_group_id" style="width: 100%;" required>
                                                                                        <option value="">choose a Study...</option>
                                                                                        <?php foreach ($override->getData('use_group') as $site) {
                                                                                        ?>
                                                                                            <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                                                        <?php }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dr"><span></span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="add_study_group" value="Save updates" class="btn btn-warning">
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
                            <div class="col-md-6">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>List of Positions</h1>
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
                                                <th width="25%">Name</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->getData('position') as $position) { ?>
                                                <tr>
                                                    <td> <?= $position['name'] ?></td>
                                                    <td><a href="#position<?= $position['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                    <!-- EOF Bootrstrap modal form -->
                                                </tr>
                                                <div class="modal fade" id="position<?= $position['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Position Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9"><input type="text" name="name" value="<?= $position['name'] ?>" required /></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $position['id'] ?>">
                                                                    <input type="submit" name="edit_position" class="btn btn-warning" value="Save updates">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>List of Sites</h1>
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
                                                <th width="25%">Name</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->getData('sites') as $site) { ?>
                                                <tr>
                                                    <td> <?= $site['name'] ?></td>
                                                    <td><a href="#site<?= $site['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                    <!-- EOF Bootrstrap modal form -->
                                                </tr>
                                                <div class="modal fade" id="site<?= $site['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Site Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9"><input type="text" name="name" value="<?= $site['name'] ?>" required /></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $site['id'] ?>">
                                                                    <input type="submit" name="edit_site" class="btn btn-warning" value="Save updates">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>List of Categories</h1>
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
                                                <th width="25%">Name</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                <tr>
                                                    <td> <?= $dCat['name'] ?></td>
                                                    <td><a href="#site<?= $dCat['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                    <!-- EOF Bootrstrap modal form -->
                                                </tr>
                                                <div class="modal fade" id="site<?= $dCat['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Category Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9"><input type="text" name="name" value="<?= $dCat['name'] ?>" required /></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $dCat['id'] ?>">
                                                                    <input type="submit" name="edit_drug_cat" class="btn btn-warning" value="Save updates">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } elseif ($_GET['id'] == 3) { ?>
                            <div class="col-md-12">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>Generic List</h1>
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
                                    <input class="form-control" id="myInput" type="text" placeholder="Search..">
                                    <br>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">Date</th>
                                                <th width="15%">Generic</th>
                                                <th width="35%">Manage</th>
                                                <th width="20%">Action</th>
                                                <th width="10%">Entries</th>
                                                <th width="10%">Checks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            <?php $amnt = 0;
                                            foreach ($override->get('generic', 'status', 1) as $batch) {

                                            ?>
                                                <tr>
                                                    <td><?= $batch['create_on'] ?></td>
                                                    <td><?= $batch['name'] ?></td>

                                                    <td>
                                                        <!-- <a href="info.php?id=5&bt=<?= $batch['id'] ?>" class="btn btn-default">View</a> -->
                                                        <a href="info.php?id=13&loc_id=<?= $batch['id'] ?>" class="btn btn-default">View Locations</a>

                                                        <a href="#updateGeneric<?= $batch['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                        <a href="#delete<?= $batch['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    </td>
                                                    <td>
                                                        <a href="#updateLocation<?= $batch['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update Locations Of Inventory</a>

                                                    </td>

                                                    <td>
                                                        <a href="data.php?id=11&gid=<?= $batch['id'] ?>" class="btn btn-default">View Entries</a>
                                                    </td>
                                                    <td>
                                                        <a href="data.php?id=8&gid=<?= $batch['id'] ?>" class="btn btn-default">View Checks</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="updateGeneric<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Generic Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Name:</label>
                                                                                <input value="<?= $batch['name'] ?>" class="validate[required]" type="text" name="name" id="name" required />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Notify Quantity:</label>
                                                                                <input value="<?= $batch['notify_quantity'] ?>" class="validate[required]" type="text" name="notify_quantity" id="notify_quantity11" required />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Maintanace Type:</label>
                                                                                <select name="maintainance" id="maintainance" style="width: 100%;" required>
                                                                                    <option value="<?= $batch['maintainance'] ?>"><?php if ($batch) {
                                                                                                                                        if ($batch['maintainance'] == 1) {
                                                                                                                                            echo 'Scheduled Verification (check date)';
                                                                                                                                        } elseif ($batch['maintainance'] == 2) {
                                                                                                                                            echo 'Expiration date';
                                                                                                                                        } elseif ($batch['maintainance'] == 3) {
                                                                                                                                            echo 'Calibration / Services date';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?>
                                                                                    </option>
                                                                                    <option value="1">Scheduled Verification (check date)</option>
                                                                                    <option value="2">Expiration date</option>
                                                                                    <option value="3">Calibration / Services date</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                    <input type="submit" name="edit_generic" value="Save updates" class="btn btn-warning">
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
                                                                    <h4>Delete Generic List</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this Generic List</p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                    <input type="submit" name="delete_generic" value="Delete" class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="updateLocation<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Generic Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">

                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Generic Name::</label>
                                                                                    <input value="<?= $batch['name'] ?>" class="validate[required]" type="text" name="name" id="name11" disabled />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Notify Quantity:</label>
                                                                                    <input value="<?= $batch['notify_quantity'] ?>" class="validate[required]" type="text" name="notify_quantity" id="notify_quantity11" required />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Generic Name Location:</label>
                                                                                    <select name="generic_location" id="generic_location" style="width: 100%;" required>
                                                                                        <option value="">Select Location</option>
                                                                                        <?php foreach ($override->getData('location') as $cat) { ?>
                                                                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                        <input type="submit" name="update_location" value="Save updates" class="btn btn-warning">
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
                        <?php } elseif ($_GET['id'] == 4) { ?>
                            <div class="col-md-12">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>List of Studies</h1>
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
                                                <th width="10%">Name</th>
                                                <th width="10%">PI</th>
                                                <th width="10%">Coordinator</th>
                                                <th width="10%">Start Date</th>
                                                <th width="10%">End Date</th>
                                                <th width="20%">Details</th>
                                                <th width="5%">status</th>
                                                <th width="15%">Sites</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->getData('study') as $study) {
                                                $pi = $override->get('user', 'id', $study['pi_id'])[0];
                                                $co = $override->get('user', 'id', $study['co_id'])[0] ?>
                                                <tr>
                                                    <td><input type="checkbox" name="checkbox" /></td>
                                                    <td> <?= $study['name'] ?></td>
                                                    <td><?= $pi['firstname'] . ' ' . $pi['lastname'] ?></td>
                                                    <td><?= $co['firstname'] . ' ' . $co['lastname'] ?></td>
                                                    <td> <?= $study['start_date'] ?></td>
                                                    <td> <?= $study['end_date'] ?></td>
                                                    <td> <?= $study['details'] ?></td>
                                                    <td>
                                                        <?php if ($study['status'] == 1) { ?>
                                                            <a href="#" role="button" class="btn btn-success" data-toggle="modal">Active</a>
                                                        <?php } else { ?>
                                                            <a href="#" role="button" class="btn btn-danger" data-toggle="modal">End</a>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php foreach ($override->get('study_sites', 'study_id', $study['id']) as $site) {
                                                            $site_name = $override->get('sites', 'id', $site['site_id'])[0];
                                                            if ($site_name) {
                                                                echo $site_name['name'] . ' , ';
                                                            }
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <a href="#study<?= $study['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                        <a href="#delete<?= $study['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        <a href="#add_site<?= $study['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Add Site To A study</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="study<?= $study['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Study Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $study['name'] ?>" class="validate[required]" type="text" name="name" id="name" required />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">PI</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="pi" style="width: 100%;" required>
                                                                                        <option value="<?= $pi['id'] ?>"><?= $pi['firstname'] . ' ' . $pi['lastname'] ?></option>
                                                                                        <?php foreach ($override->getData('user') as $staff) { ?>
                                                                                            <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Coordinator</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="coordinator" style="width: 100%;" required>
                                                                                        <option value="<?= $co['id'] ?>"><?= $co['firstname'] . ' ' . $co['lastname'] ?></option>
                                                                                        <?php foreach ($override->getData('user') as $staff) { ?>
                                                                                            <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Start Date:</div>
                                                                                <div class="col-md-9"><input type="text" name="start_date" value="<?= $study['start_date'] ?>" required /> <span>Example: 2012-01-01</span></div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">End Date:</div>
                                                                                <div class="col-md-9"><input type="text" name="end_date" value="<?= $study['end_date'] ?>" required /> <span>Example: 2012-12-31</span></div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Study details:</div>
                                                                                <div class="col-md-9"><textarea name="details" rows="4" required><?= $study['details'] ?></textarea></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $study['id'] ?>">
                                                                    <input type="submit" name="edit_file" value="Save updates" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="add_site<?= $study['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Site Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $study['name'] ?>" class="validate[required]" type="text" name="name" id="name" required />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">PI</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="site" style="width: 100%;" required>
                                                                                        <option value=" ">Select Site</option>
                                                                                        <?php foreach ($override->getData('sites') as $staff) { ?>
                                                                                            <option value="<?= $staff['id'] ?>"><?= $staff['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $study['id'] ?>">
                                                                    <input type="submit" name="add_site" value="Save updates" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="delete<?= $study['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Delete User</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this Study</p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $study['id'] ?>">
                                                                    <input type="submit" name="delete_file" value="Delete" class="btn btn-danger">
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
                        <?php } elseif ($_GET['id'] == 5) { ?>
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
                                                <th width="25%">Date</th>
                                                <th width="25%">Generic</th>
                                                <th width="5%">Used</th>
                                                <th width="5%">Balance</th>
                                                <th width="20%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $amnt = 0;
                                            foreach ($override->get('generic', 'status', 1) as $batchDesc) {
                                                $amnt = $batchDesc['quantity'] - $batchDesc['assigned'] ?>
                                                <tr>
                                                    <td> <?= $batchDesc['create_on'] ?></td>
                                                    <td> <?= $batchDesc['name'] ?></td>
                                                    <td> <?= $batchDesc['assigned'] ?></td>
                                                    <td> <?= $batchDesc['balance'] ?></td>
                                                    <!-- <td> <?= number_format($batchDesc['quantity'] - $batchDesc['assigned']) ?></td> -->

                                                    <td>
                                                        <a href="#study1<?= $batchDesc['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">View</a>
                                                        <!-- <a href="#delete<?= $batchDesc['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a> -->
                                                        <a href="#updateGeneric<?= $batchDesc['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                    </td>
                                                </tr>
                                                <div class="modal fade" id="updateGeneric<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Batch Description Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Batch</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $override->get('batch', 'id', $batchDesc['batch_id'])[0]['name'] ?>" type="text" id="name" disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['name'] ?>" class="validate[required]" type="text" name="name" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Category</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="category" style="width: 100%;" required>
                                                                                        <option value="<?= $batchDesc['cat_id'] ?>"><?= $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0]['name'] ?></option>
                                                                                        <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Quantity:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['quantity'] ?>" class="validate[required]" type="number" name="quantity" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Notification Amount: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['notify_amount'] ?>" class="validate[required]" type="text" name="notify_amount" required />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="batch" value="<?= $batchDesc['batch_id'] ?>">
                                                                    <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                    <input type="submit" name="edit_batch_desc" value="Save updates" class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="study<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Batch Description Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Batch</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $override->get('batch', 'id', $batchDesc['batch_id'])[0]['name'] ?>" type="text" id="name" disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['name'] ?>" class="validate[required]" type="text" name="name" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Category</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="category" style="width: 100%;" required>
                                                                                        <option value="<?= $batchDesc['cat_id'] ?>"><?= $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0]['name'] ?></option>
                                                                                        <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Quantity:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['quantity'] ?>" class="validate[required]" type="number" name="quantity" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Notification Amount: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['notify_amount'] ?>" class="validate[required]" type="text" name="notify_amount" required />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="batch" value="<?= $batchDesc['batch_id'] ?>">
                                                                    <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                    <input type="submit" name="edit_batch_desc" value="Save updates" class="btn btn-warning">
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
                                    </table>
                                </div>
                            </div>
                        <?php } elseif ($_GET['id'] == 6) { ?>
                            <div class="col-md-12">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>Assigned Stock</h1>
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
                                                <th width="25%">Name</th>
                                                <th width="25%">Study</th>
                                                <th width="25%">Manufacturer</th>
                                                <th width="10%">Amount</th>
                                                <th width="25%">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $type = $_GET['type'];
                                            foreach ($override->getNews('batch', 'status', 1, 'type', $type) as $batch) {
                                                $study = $override->get('study', 'id', $batch['study_id'])[0] ?>
                                                <tr>
                                                    <td><input type="checkbox" name="checkbox" /></td>
                                                    <td> <a href="info.php?id=7&bt=<?= $batch['id'] ?>"><?= $batch['name'] ?></a></td>
                                                    <td><?= $study['name'] ?></td>
                                                    <td><?= $batch['manufacturer'] ?></td>
                                                    <td><?= $batch['amount'] ?></td>
                                                    <td>
                                                        <a href="info.php?id=7&bt=<?= $batch['id'] ?>" class="btn btn-default">View</a>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } elseif ($_GET['id'] == 7) { ?>
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
                                                <th width="35%">Product Name</th>
                                                <th width="15%">Batch No</th>
                                                <th width="10%">Drug Category</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Assigned</th>
                                                <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->get('batch_description', 'batch_id', $_GET['bt']) as $batchDesc) {
                                                $batch_no = $override->get('batch', 'id', $batchDesc['batch_id'])[0];
                                                $dCat = $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0] ?>
                                                <tr>
                                                    <td><input type="checkbox" name="checkbox" /></td>
                                                    <td><a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>"><?= $batchDesc['name'] ?></a></td>
                                                    <td><?= $batch_no['batch_no'] ?></td>
                                                    <td><?= $dCat['name'] ?></td>
                                                    <td> <?= $batchDesc['quantity'] ?></td>
                                                    <td> <?= $batchDesc['assigned'] ?></td>
                                                    <td>
                                                        <a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>" class="btn btn-info">Details</a>
                                                    </td>
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
                                    <h1>Product Assignment</h1>
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
                                                <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($override->get('assigned_stock', 'drug_id', $_GET['dsc']) as $batchDesc) {
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
                                                    <td>
                                                        <a href="info.php?id=9&dsc=<?= $_GET['dsc'] ?>" class="btn btn-default">Assigned History</a>
                                                    </td>
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
                        <?php } elseif ($_GET['id'] == 10) { ?>
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
                                                <th width="20%">Product Name</th>
                                                <th width="10%">Batch No</th>
                                                <th width="10%">Drug Category</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Assigned</th>
                                                <th width="10%">Remained</th>
                                                <th width="15%">Status</th>
                                                <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $amnt = 0;
                                            foreach ($override->get('batch_description', 'status', 1) as $batchDesc) {
                                                $batch_no = $override->get('batch', 'id', $batchDesc['batch_id'])[0];
                                                $dCat = $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0];
                                                $amnt = $batchDesc['quantity'] - $batchDesc['assigned'] ?>
                                                <tr>
                                                    <td><input type="checkbox" name="checkbox" /></td>
                                                    <td> <?= $batchDesc['name'] ?></td>
                                                    <td><?= $batch_no['batch_no'] ?></td>
                                                    <td><?= $dCat['name'] ?></td>
                                                    <td> <?= $batchDesc['quantity'] ?></td>
                                                    <td> <?= $batchDesc['assigned'] ?></td>
                                                    <td> <?= number_format($batchDesc['quantity'] - $batchDesc['assigned']) ?></td>
                                                    <td>
                                                        <?php if ($amnt <= $batchDesc['notify_amount'] && $amnt > 0) { ?>
                                                            <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Insufficient</a>
                                                        <?php } elseif ($amnt == 0) { ?>
                                                            <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Finished</a>
                                                        <?php } else { ?>
                                                            <a href="#" role="button" class="btn btn-success" data-toggle="modal">Sufficient</a>
                                                        <?php } ?>
                                                    </td>

                                                    <td>
                                                        <a href="#study<?= $batchDesc['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                        <a href="#delete<?= $batchDesc['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="study<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Batch Description Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Batch</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $override->get('batch', 'id', $batchDesc['batch_id'])[0]['name'] ?>" type="text" id="name" disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['name'] ?>" class="validate[required]" type="text" name="name" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Category</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="category" style="width: 100%;" required>
                                                                                        <option value="<?= $batchDesc['cat_id'] ?>"><?= $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0]['name'] ?></option>
                                                                                        <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Quantity:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['quantity'] ?>" class="validate[required]" type="number" name="quantity" id="name" />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Notification Amount: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batchDesc['notify_amount'] ?>" class="validate[required]" type="text" name="notify_amount" required />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="batch" value="<?= $batchDesc['batch_id'] ?>">
                                                                    <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                    <input type="submit" name="edit_batch_desc" value="Save updates" class="btn btn-warning">
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
                                    </table>
                                </div>
                            </div>
                        <?php } elseif ($_GET['id'] == 11) { ?>
                            <div class="col-md-6">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>Download Report</h1>
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
                                                <th width="5%">#</th>
                                                <th width="25%">Name</th>
                                                <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>FULL REPORT</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="full_report" value="Download Full Report"></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>SUFFICIENT</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="sufficient" value="Download Sufficient"></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>RUNNING LOW</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="running_low" value="Download Running Low"></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Out Of Stock</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="out_stock" value="Download out of Stock"></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Expired</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="expired" value="Download Expired"></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>NOT CHECKED</td>
                                                <td>
                                                    <form method="post"><input type="submit" name="not_checked" value="Download Not Checked"></form>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } elseif ($_GET['id'] == 12) { ?>
                            <div class="col-md-12">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>Batch List</h1>
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
                                    <h2>Filterable</h2>
                                    <input class="form-control" id="myInput" type="text" placeholder="Search..">
                                    <br>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">Name</th>
                                                <th width="10%">Study</th>
                                                <th width="10%">Amount</th>
                                                <th width="10%">Exp Date</th>
                                                <th width="5%">Status</th>
                                                <th width="20%">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            <?php $amnt = 0;
                                            $type = $_GET['type'];
                                            foreach ($override->getNews('batch', 'status', 1, 'type', $type) as $batch) {
                                                $study = $override->get('study', 'id', $batch['study_id'])[0];
                                                $batchItems = $override->getSumD1('batch_description', 'assigned', 'batch_id', $batch['id']);
                                                // print_r($batchItems[0]['SUM(assigned)']);
                                                $amnt = $batch['amount'] - $batchItems[0]['SUM(assigned)']; ?>
                                                <tr>
                                                    <td> <a href="info.php?id=5&bt=<?= $batch['id'] ?>"><?= $batch['name'] ?></a></td>
                                                    <td><?= $study['name'] ?></td>
                                                    <td><?= $batch['amount'] ?></td>
                                                    <td><?= $batch['expire_date'] ?></td>
                                                    <td>
                                                        <?php if ($amnt <= $batch['notify_amount'] && $amnt > 0) { ?>
                                                            <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                        <?php } elseif ($amnt == 0) { ?>
                                                            <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                        <?php } else { ?>
                                                            <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <a href="info.php?id=5&bt=<?= $batch['id'] ?>" class="btn btn-default">View</a>
                                                        <a href="#user<?= $batch['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                        <a href="#delete<?= $batch['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="user<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Edit Batch Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Name: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batch['name'] ?>" class="validate[required]" type="text" name="name" id="name" required />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Batch No: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batch['batch_no'] ?>" class="validate[required]" type="text" name="batch_no" id="name" required />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Study</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="study" style="width: 100%;" required>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                        <?php foreach ($override->getData('study') as $study) { ?>
                                                                                            <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Amount: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batch['amount'] ?>" class="validate[required]" type="text" name="amount" id="name" required />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Manufacturer:</div>
                                                                                <div class="col-md-9"><input type="text" value="<?= $batch['manufacturer'] ?>" name="manufacturer" id="manufacturer" /></div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Manufactured Date:</div>
                                                                                <div class="col-md-9"><input type="date" name="manufactured_date" value="<?= $batch['manufactured_date'] ?>" required /> </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Expire Date:</div>
                                                                                <div class="col-md-9"><input type="date" name="expire_date" value="<?= $batch['expire_date'] ?>" required /> </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Notification Amount: </div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $batch['notify_amount'] ?>" class="validate[required]" type="text" name="notify_amount" id="name" required />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Details: </div>
                                                                                <div class="col-md-9">
                                                                                    <textarea class="" name="details" id="details" rows="4"><?= $batch['details'] ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                    <input type="submit" name="edit_batch" value="Save updates" class="btn btn-warning">
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

                        <?php } elseif ($_GET['id'] == 13) { ?>
                            <div class="col-md-12">
                                <div class="head clearfix">
                                    <div class="isw-grid"></div>
                                    <h1>Location list </h1>
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
                                                <th width="5%">No.</th>
                                                <th width="40%">Generic Name</th>
                                                <th width="30%">Location Name</th>
                                                <th width="25%">Action</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $amnt = 1;
                                            foreach ($override->getNews('generic_location', 'generic_id', $_GET['loc_id'], 'status', 1) as $loc_id) {
                                                $location = $override->get('location', 'id', $loc_id['location_id'])[0];
                                                $generic = $override->get('generic', 'id', $_GET['loc_id'])[0];

                                            ?>
                                                <tr>
                                                    <td><?= $amnt ?></td>
                                                    <td><?= $generic['name'] ?></td>
                                                    <td><?= $location['name'] ?></td>
                                                    <td>
                                                        <a href="#removeLocation<?= $loc_id['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete Location</a>
                                                    </td>
                                                </tr>
                                                <div class="modal fade" id="removeLocation<?= $loc_id['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4>Delete Generic Location List</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this Generic Location List</p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $loc_id['id'] ?>">
                                                                    <input type="submit" name="remove_location" value="Delete Location" class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php

                                                $amnt += 1;
                                            }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
    $(document).ready(function() {
        $('#wait_ds').hide();
        $('#region').change(function() {
            var getUid = $(this).val();
            $('#wait_ds').show();
            $.ajax({
                url: "process.php?content=region",
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
                url: "process.php?content=district",
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
                url: "process.php?content=download",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {

                }
            });

        });

        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    });
</script>

</html>