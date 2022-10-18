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
                                        <th width="5%">Required</th>
                                        <th width="5%">Balance</th>
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
                                        // $guide_location_id[] = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'status', 1)[0]['location_id'];
                                        $brand_id = $override->getNews('batch', 'brand_id', $generic_id, 'status', 1)[0]['brand_id'];
                                        $batch_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                        $batch_no = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['name'];
                                        $category = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['category'];
                                        $study_id = $override->getNews('batch', 'generic_id', $bDiscription['id'], 'status', 1)[0]['study_id'];
                                        $useCase = $override->get('use_case', 'id', $bDiscription['use_case'])[0]['name'];
                                        $useGroup = $override->get('use_group', 'id', $bDiscription['use_group'])[0]['name'];
                                        $form = $override->get('drug_cat', 'id', $bDiscription['category_id'])[0]['name'];
                                        $EmKits = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 1)[0];
                                        $AmKits = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 2)[0];
                                        $ECRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 3)[0];
                                        $DRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 4)[0];
                                        $ScRm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 5)[0];
                                        $VSrm = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 6)[0];
                                        $ExamRms = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 7)[0];
                                        $Ward = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 8)[0];
                                        $CTMr = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 9)[0];
                                        $Pharmacy = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 10)[0];
                                        $Other = $override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'location_id', 96)[0];
                                        $sumLoctn = $override->getSumD1('generic_guide', 'balance', 'generic_id', $bDiscription['id'])[0]['SUM(balance)'];
                                        $sumNotify = $override->getSumD1('generic_guide', 'notify_quantity', 'generic_id', $bDiscription['id'])[0]['SUM(notify_quantity)'];
                                        $Notify = $bDiscription['notify_quantity'];
                                        $balance = $bDiscription['balance'];
                                        $batchBalance = $override->getSumD2('batch', 'balance', 'generic_id', $bDiscription['id'], 'status', 1)[0]['SUM(balance)'];

                                        // $location[] = '';
                                        // foreach ($override->getNews('generic_guide', 'generic_id', $bDiscription['id'], 'status', 1) as $batch2) {
                                        //     if ($batch2['location_id'] != '') {
                                        //         $location[] = $batch2['location_id'];
                                        //     }
                                        // }                                      


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
                                            <td><?= $bDiscription['notify_quantity']; ?></td>
                                            <td>
                                                <?php if ($batchBalance <= $Notify && $batchBalance > 0) { ?>
                                                    <a href="data.php?id=12&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-warning"><?= $balance; ?></a>
                                                <?php } elseif ($batchBalance == 0) { ?>
                                                    <a href="add.php?id=11" role="button" class="btn btn-danger"><?= $balance; ?></a>
                                                <?php } else { ?>
                                                    <a href="data.php?id=12&gid=<?= $bDiscription['id'] ?>" role="button" class="btn btn-success"><?= $balance; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td><?php if ($EmKits['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($EmKits['notify_quantity'] >= $EmKits['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=1&gid=<?= $bDiscription['id'] ?>&lbid=<?= $EmKits['id'] ?>" role="button" class="btn btn-danger"><?= $EmKits['balance']; ?></a>
                                                    <?php } elseif ($EmKits['notify_quantity'] < $EmKits['balance']) { ?>
                                                        <a href="data.php?id=13&lid=1&gid=<?= $bDiscription['id'] ?>&lbid=<?= $EmKits['id'] ?>" role="button" class="btn btn-info"><?= $EmKits['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($AmKits['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?> <?php if ($AmKits['notify_quantity'] >= $AmKits['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=2&gid=<?= $bDiscription['id'] ?>&lbid=<?= $AmKits['id'] ?>" role="button" class="btn btn-danger"><?= $AmKits['balance']; ?></a>
                                                    <?php } elseif ($AmKits['notify_quantity'] < $AmKits['balance']) { ?>
                                                        <a href="data.php?id=13&lid=2&gid=<?= $bDiscription['id'] ?>&lbid=<?= $AmKits['id'] ?>" role="button" class="btn btn-info"><?= $AmKits['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ECRm['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($ECRm['notify_quantity'] >= $ECRm['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=3&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ECRm['id'] ?>" role="button" class="btn btn-danger"><?= $ECRm['balance']; ?></a>
                                                    <?php } elseif ($ECRm['notify_quantity'] < $ECRm['balance']) { ?>
                                                        <a href="data.php?id=13&lid=3&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ECRm['id'] ?>" role="button" class="btn btn-info"><?= $ECRm['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($DRm['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($DRm['notify_quantity'] >= $DRm['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=4&gid=<?= $bDiscription['id'] ?>&lbid=<?= $DRm['id'] ?>" role="button" class="btn btn-danger"><?= $DRm['balance']; ?></a>
                                                    <?php } elseif ($DRm['notify_quantity'] < $DRm['balance']) { ?>
                                                        <a href="data.php?id=13&lid=4&gid=<?= $bDiscription['id'] ?>&lbid=<?= $DRm['id'] ?>" role="button" class="btn btn-info"><?= $DRm['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ScRm['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($ScRm['notify_quantity'] >= $ScRm['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=5&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ScRm['id'] ?>" role="button" class="btn btn-danger"><?= $ScRm['balance']; ?></a>
                                                    <?php } elseif ($ScRm['notify_quantity'] < $ScRm['balance']) { ?>
                                                        <a href="data.php?id=13&lid=5&gid=<?= $bDiscription['id'] ?>&lbid=<?= $ScRm['id'] ?>" role="button" class="btn btn-info"><?= $ScRm['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($VSrm['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($VSrm['notify_quantity'] >= $VSrm['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=6&gid=<?= $bDiscription['id'] ?>&lbid=<?= $VSrm['id'] ?>" role="button" class="btn btn-danger"><?= $VSrm['balance']; ?></a>
                                                    <?php } elseif ($VSrm['notify_quantity'] < $VSrm['balance']) { ?>
                                                        <a href="data.php?id=13&lid=6&gid=<?= $bDiscription['id'] ?>&lbid=<?= $VSrm['id'] ?>" role="button" class="btn btn-info"><?= $VSrm['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($ExamRms['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($ExamRms['notify_quantity'] >= $ExamRms['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=7&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-danger"><?= $ExamRms['balance']; ?></a>
                                                    <?php } elseif ($ExamRms['notify_quantity'] < $ExamRms['balance']) { ?>
                                                        <a href="data.php?id=13&lid=7&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-info"><?= $ExamRms['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($Ward['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($Ward['notify_quantity'] >= $Ward['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=8&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-danger"><?= $Ward['balance']; ?></a>
                                                    <?php } elseif ($Ward['notify_quantity'] < $Ward['balance']) { ?>
                                                        <a href="data.php?id=13&lid=8&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Ward['id'] ?>" role="button" class="btn btn-info"><?= $Ward['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($CTMr['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($CTMr['notify_quantity'] >= $CTMr['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=9&gid=<?= $bDiscription['id'] ?>&lbid=<?= $CTMr['id'] ?>" role="button" class="btn btn-danger"><?= $CTMr['balance']; ?></a>
                                                    <?php } elseif ($CTMr['notify_quantity'] < $CTMr['balance']) { ?>
                                                        <a href="data.php?id=13&lid=9&gid=<?= $bDiscription['id'] ?>&lbid=<?= $CTMr['id'] ?>" role="button" class="btn btn-info"><?= $CTMr['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>
                                            <td><?php if ($Pharmacy['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($Pharmacy['notify_quantity'] >= $Pharmacy['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=10&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Pharmacy['id'] ?>" role="button" class="btn btn-danger"><?= $Pharmacy['balance']; ?></a>
                                                    <?php } elseif ($Pharmacy['notify_quantity'] < $Pharmacy['balance']) { ?>
                                                        <a href="data.php?id=13&lid=10&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Pharmacy['id'] ?>" role="button" class="btn btn-info"><?= $Pharmacy['balance']; ?></a>
                                                <?php }
                                                } ?>
                                            </td>

                                            <td><?php if ($Other['balance'] == '') {
                                                    echo 'NA';
                                                } else {
                                                ?>
                                                    <?php if ($Other['notify_quantity'] >= $Other['balance']) {
                                                    ?>
                                                        <a href="data.php?id=13&lid=96&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Other['id'] ?>" role="button" class="btn btn-danger"><?= $Other['balance']; ?></a>
                                                    <?php } elseif ($Other['notify_quantity'] < $Other['balance']) { ?>
                                                        <a href="data.php?id=13&lid=96&gid=<?= $bDiscription['id'] ?>&lbid=<?= $Other['id'] ?>" role="button" class="btn btn-info"><?= $Other['balance']; ?></a>
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