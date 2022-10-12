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
        $validate = new validate();
        if (Input::get('update_stock_guide')) {
            $validate = $validate->check($_POST, array(
                'added' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $total_quantity = 0;
                if (Input::get('added') > 0) {
                    $total_quantity = Input::get('quantity_db') + Input::get('added');
                    try {
                        $user->updateRecord('generic', array(
                            'quantity' => $total_quantity,
                        ), Input::get('id'));

                        $user->createRecord('batch_records', array(
                            'generic_id' => Input::get('id'),
                            'brand_id' => Input::get('brand_id'),
                            'batch_id' => Input::get('batch_id'),
                            'batch_no' => Input::get('batch_no'),
                            'quantity' => 0,
                            'assigned' => 0,
                            'added' => Input::get('added'),
                            'balance' => $total_quantity,
                            'create_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'study_id' => Input::get('study_id'),
                            'last_check' => Input::get('last_check'),
                            'next_check' => Input::get('next_check'),
                            'category' => Input::get('category'),
                            'use_group' => Input::get('use_group'),
                            'maintainance' => Input::get('maintainance'),
                            'use_case' => Input::get('use_case'),
                            'remarks' => Input::get('remarks'),
                            'expire_date' => Input::get('expire_date'),
                        ));

                        $successMessage = 'Stock guied Successful Updated';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $errorMessage = 'Amount added Must Be Greater Than 0';
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
                    <li><a href="#">Dashboard</a> <span class="divider"></span></li>
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
                                        <th width="3%">Quantity</th>
                                        <th width="20%">Action</th>
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
                                        $EmKits = ($override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 1)[0]['quantity']);
                                        $AmKits = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 2)[0]['quantity'];
                                        $ECRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 3)[0]['quantity'];
                                        $DRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 4)[0]['quantity'];
                                        $ScRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 5)[0]['quantity'];
                                        $VSrm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 6)[0]['quantity'];
                                        $ExamRms = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 7)[0]['quantity'];
                                        $Ward = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 8)[0]['quantity'];
                                        $CTMr = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 9)[0]['quantity'];
                                        $Pharmacy = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 10)[0]['quantity'];
                                        $Other = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 96)[0]['quantity'];
                                        $sumLoctn = $override->getSumD1('generic_guide', 'quantity', 'generic_id', $bDiscription['id'])[0]['SUM(quantity)'];
                                        $sumNotify = $override->getSumD1('generic_guide', 'notify_quantity', 'generic_id', $bDiscription['id'])[0]['SUM(notify_quantity)'];
                                        $Notify = $bDiscription['notify_quantity'];
                                        $balance = $bDiscription['balance'];
                                        // $batchBalance = $override->getSumD1('batch', 'balance', 'generic_id', $bDiscription['id'])[0]['SUM(balance)'];

                                        $check = 0;
                                        $check1 = 0;
                                        foreach ($override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1) as $batch2) {
                                            $nextCheck = $batch2['next_check'];
                                            $expireDate = $batch2['expire_date'];
                                            // $Quantity = $batch2['balance'];
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
                                            <td><?php if ($EmKits) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $EmKits; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($AmKits) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $AmKits; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($ECRm) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $ECRm; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($DRm) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $DRm; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($ScRm) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $ScRm; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($VSrm) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $VSrm; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($ExamRms) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $ExamRms; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($Ward) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $Ward; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($CTMr) {
                                                ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $CTMr; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($Pharmacy) { ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $Pharmacy; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td><?php if ($Other) { ?>
                                                    <a href="#" role="button" class="btn btn-info"><?= $Other; ?></a>
                                                <?php
                                                } else {
                                                    echo 'NA';
                                                } ?>
                                            </td>
                                            <td>
                                                <?php if ($check) { ?>
                                                    <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning btn-sm check" check_id="<?= $bDiscription['id'] ?>" data-toggle="modal" id="check">Not Checked!</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success btn-sm check" check_id="<?= $bDiscription['id'] ?>" data-toggle="modal" id="check">OK!</a>
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
                                                <?php if ($balance <= $Notify && $balance > 0) { ?>
                                                    <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                <?php } elseif ($balance == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="data.php?id=7&did=<?= $bDiscription['id'] ?>" class="btn btn-info">View</a>
                                                <a href="#edit_stock_guide_id<?= $bDiscription['id'] ?>" role="button" class="btn btn-success update" gen_id="<?= $bDiscription['id'] ?>" data-toggle="modal">Update</a>
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

                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <!-- select -->
                                                                        <div class="form-group">
                                                                            <label>Generic Name:</label>
                                                                            <input value="<?= $bDiscription['name'] ?>" type="text" id="name" name="name" disabled />

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <!-- select -->
                                                                        <div class="form-group">
                                                                            <label>Brand Name</label>
                                                                            <select name="brand_id" id="brand_id" style="width: 100%;" required>
                                                                                <option value="">Select brand</option>
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
                                                                            <label>Batch No:</label>
                                                                            <select name="batch_id" id="batch_id" style="width: 100%;" required>
                                                                                <option value="">Select Batch</option>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-4">
                                                                    <div class="row-form clearfix">
                                                                        <!-- select -->
                                                                        <div class="form-group">
                                                                            <label>Current Quantity::</label>
                                                                            <input value="<?= $bDiscription['quantity'] ?>" class="validate[required]" type="number" name="quantity" id="name" disabled />

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="row-form clearfix">
                                                                        <!-- select -->
                                                                        <div class="form-group">
                                                                            <label>Quantity to Add:</label>
                                                                            <input value=" " class="validate[required]" type="number" name="added" id="added" />

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" value="<?= $bDiscription['id'] ?>">
                                                            <input type="hidden" name="study_id" value="<?= $bDiscription['study_id'] ?>">
                                                            <input type="hidden" name="notify_quantity" value="<?= $bDiscription['notify_quantity'] ?>">
                                                            <input type="hidden" name="use_group" value="<?= $bDiscription['use_group'] ?>">
                                                            <input type="hidden" name="use_case" value="<?= $bDiscription['use_case'] ?>">
                                                            <input type="hidden" name="maintainance" value="<?= $bDiscription['maintainance'] ?>">
                                                            <input type="hidden" name="category" value="<?= $bDiscription['category'] ?>">
                                                            <input type="hidden" name="quantity_db" value="<?= $bDiscription['quantity'] ?>">
                                                            <input type="hidden" name="batch_no" value="" id="batch_no">
                                                            <input type="hidden" name="last_check" value="" id="last_check">
                                                            <input type="hidden" name="next_check" value="" id="next_check">
                                                            <input type="hidden" name="expire_date" value="" id="expire_date">
                                                            <input type="submit" name="update_stock_guide" value="Save updates" class="btn btn-warning">
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
                var getUid = $(this).attr('gen_id');
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=gen",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#brand_id').html(data);
                        $('#fl_wait').hide();
                    }
                });
            })

            $('#brand_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=bat",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#batch_id').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#batch_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=batch_id_update",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#batch_no').val(data.batch_no);
                        $('#fl_wait').hide();
                    }
                });

            });


            $(document).on('click', '.update', function() {
                var getUid = $(this).attr('gen_id');
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=bat3",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#gen_name').val(data.gen_name);
                        $('#category').val(data.category);
                        $('#last_check').val(data.last_check);
                        $('#next_check').val(data.next_check);
                        $('#expire_date').val(data.expire_date);
                        $('#fl_wait').hide();
                    }
                });
            })

            $(document).on('click', '.check', function() {
                var getUid = $(this).attr('check_id');
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=gen",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#brand_id2').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#brand_id2').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=bat",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#batch_id2').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#batch_id2').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=batch_id_check",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#batch_no2').val(data.batch_no);
                        $('#fl_wait').hide();
                    }
                });

            });

            $(document).on('click', '.check', function() {
                var getUid = $(this).attr('check_id');
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=bat3",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#category2').val(data.category);
                        $('#last_check2').val(data.last_check);
                        $('#next_check2').val(data.next_check);
                        $('#expire_date2').val(data.expire_date);
                        $('#fl_wait').hide();
                    }
                });
            })

            // $('#batch_id').change(function() {
            //     var getUid = $(this).val();
            //     $('#fl_wait').show();
            //     $.ajax({
            //         url: "process.php?content=batch_id_check",
            //         method: "GET",
            //         data: {
            //             getUid: getUid
            //         },
            //         dataType: "json",
            //         success: function(data) {
            //             $('#batch_no2').val(data.batch_no);
            //             $('#fl_wait').hide();
            //         }
            //     });

            // });


            // $(document).on('click', '.check', function() {
            //     var getUid = $(this).attr('check_id');
            //     $('#fl_wait').show();
            //     $.ajax({
            //         url: "process.php?content=gen2",
            //         method: "GET",
            //         data: {
            //             getUid: getUid
            //         },
            //         dataType: "json",
            //         success: function(data) {
            //             $('#maintainance2').val(data.maintainance);
            //             $('#category2').val(data.category);
            //             $('#fl_wait').hide();
            //         }
            //     });

            // });

        });
    </script>
</body>

</html>