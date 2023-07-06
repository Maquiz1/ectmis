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
$numRec = 15;
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
    <style>
        .hover-container {
            position: relative;
            display: inline-block;
        }

        .hover-container .hover-text {
            visibility: hidden;
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .hover-container:hover .hover-text {
            visibility: visible;
        }
    </style>

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

                        <input class="form-control" id="myInput" type="text" placeholder="Search..">

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

                                        <th width="20%">Generic</th>
                                        <th width="10">Required</th>
                                        <th width="10">Balance</th>
                                        <th width="20%">Check</th>
                                        <th width="20%">Validity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $userid = $user->data()->id;
                                    $amnt = 0;
                                    $data1 = $override->get('study_group', 'staff_id', $userid);
                                    foreach ($data1 as $data2) {
                                        $data = $override->getNews('generic', 'status', 1, 'use_group', $data2['group_id']);
                                        $pagNum = $override->countData('generic', 'status', 1, 'use_group', $data2['group_id']);
                                    }
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    foreach ($override->get('study_group', 'staff_id', $user->data()->id) as $datas) {
                                        $data = $override->getWithLimit1('generic', 'status', 1, 'use_group', $datas['group_id'], $page, $numRec);
                                        foreach ($data as $bDiscription) {

                                            $generic = $bDiscription['name'];
                                            $generic_id = $bDiscription['id'];
                                            $notify_quantity = $bDiscription['notify_quantity'];
                                            $brand_id = $override->getNews('batch', 'brand_id', $generic_id, 'status', 1)[0]['brand_id'];
                                            $batch_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                            $batch_no = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                            $category = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['category'];
                                            $study_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['study_id'];
                                            $useCase = $override->get('use_case', 'id', $bDiscription['use_case'])[0]['name'];
                                            $useGroup = $override->get('use_group', 'id', $bDiscription['use_group'])[0]['name'];
                                            $form = $override->get('drug_cat', 'id', $bDiscription['category_id'])[0]['name'];

                                            $sumLoctn = 0;
                                            $sumNotify = 0;
                                            if ($override->getSumD2('batch', 'balance', 'generic_id', $bDiscription['id'], 'status', 1)[0]['SUM(balance)'] > 0) {
                                                $sumLoctn = $override->getSumD2('batch', 'balance', 'generic_id', $bDiscription['id'], 'status', 1)[0]['SUM(balance)'];
                                            }

                                            $Notify = $bDiscription['notify_quantity'];
                                            $balance = $bDiscription['balance'];
                                            $batchBalance = $override->getSumD2('batch', 'balance', 'generic_id', $bDiscription['id'], 'status', 1)[0]['SUM(balance)'];

                                            $check = 0;
                                            $check1 = 0;
                                            // foreach ($override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1) as $batch2) {
                                            //     $nextCheck = $batch2['next_check'];
                                            //     $expireDate = $batch2['expire_date'];
                                            //     $group = $batch2['use_group'];

                                            //     if ($nextCheck <= date('Y-m-d')) {
                                            //         $check = 1;
                                            //     }

                                            //     // if (!$group = 2) {
                                            //     //     if ($expireDate <= date('Y-m-d')) {
                                            //     //         $check1 = 1;
                                            //     //     }
                                            //     // }
                                            // }

                                            $found1 = false;
                                            foreach ($override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1) as $value1) {
                                                $nextCheck = $value1['next_check'];
                                                if ($nextCheck <= date('Y-m-d')) {
                                                    $found1 = true;
                                                    break;
                                                }
                                            }

                                            if ($found1) {
                                                $check = 1;
                                            } else {
                                                $check = 0;
                                            }


                                            // $values = array(15, 27, 39, 42, 56);
                                            // $targetValue = 50;
                                            $found = false;

                                            foreach ($override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1) as $value) {
                                                $expireDate = $value['expire_date'];
                                                $group = $value['use_group'];

                                                if ($expireDate <= date('Y-m-d') && $group != 2) {
                                                    $found = true;
                                                    break;
                                                }
                                            }

                                            if ($found) {
                                                $check1 = 1;
                                            } else {
                                                $check1 = 0;
                                            }





                                    ?>
                                            <tr>
                                                <td><a href="data.php?id=7&did=<?= $bDiscription['id'] ?>"><?= $generic ?></a></td>
                                                <td><?= $notify_quantity ?></td>
                                                <td>
                                                    <?php if ($batchBalance <= $Notify && $batchBalance > 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="add.php?id=11" role="button" class="btn btn-warning"><?= $balance; ?>
                                                                <div class="hover-text"><?php echo 'Receive New Batch'; ?></div>
                                                            </a>

                                                        </div>
                                                    <?php } elseif ($batchBalance == 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="add.php?id=11" role="button" class="btn btn-danger"><?= $balance; ?>

                                                                <div class="hover-text"><?php echo 'Receive New Batch'; ?></div>
                                                            </a>


                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="hover-container">
                                                            <a href="add.php?id=11" role="button" class="btn btn-success"><?= $balance; ?>

                                                                <div class="hover-text"><?php echo 'Receive New Batch'; ?></div>
                                                            </a>

                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($sumLoctn <= 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Out of Stock</a>

                                                            <div class="hover-text"><?php echo 'Increase this Stock'; ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } else if ($check == 1) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Not Checked!</a>

                                                            <div class="hover-text"><?php echo 'Please Check this Stock'; ?></div>
                                                            </a>
                                                        </div>

                                                    <?php } else if ($sumLoctn < $Notify && $sumLoctn > 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Low!</a>

                                                            <div class="hover-text"><?php echo 'Increase this Stock'; ?></div>
                                                            </a>
                                                        </div>

                                                    <?php } else { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Checked!</a>

                                                            <div class="hover-text"><?php echo 'Please Check this Stock'; ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($sumLoctn <= 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Out of Stock</a>

                                                            <div class="hover-text"><?php echo 'Increase this Stock'; ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } else if ($check1 == 1) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Expired!</a>

                                                            <div class="hover-text"><?php echo 'Please Quarantine this Stock'; ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } else if ($sumLoctn < $Notify && $sumLoctn > 0) { ?>
                                                        <div class="hover-container">
                                                            <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Low!</a>

                                                            <div class="hover-text"><?php echo 'Increase this Stock'; ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } else { ?>
                                                        <a href="data.php?id=1&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Valid!</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
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

            $(document).ready(function() {
                $("#myInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#inventory_report1 tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });

        });
    </script>
</body>

</html>