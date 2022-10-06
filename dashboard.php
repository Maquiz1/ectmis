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
                print_r($_POST);
                $total_quantity = 0;
                if (Input::get('added') > 0) {
                    $total_quantity = Input::get('quantity_db') + Input::get('added');
                    try {
                        $user->updateRecord('generic', array(
                            'quantity' => $total_quantity,
                        ), Input::get('generic_id'));

                        $user->createRecord('batch_records', array(
                            'quantity' => $total_quantity,
                            'generic_id' => Input::get('generic_id'),
                            'batch_id' => Input::get('generic_id'),
                            'batch_no' => Input::get('generic_id'),
                            'staff_id' => $user->data()->id,
                            'use_group' => Input::get('use_group'),
                            'create_on' => date('Y-m-d'),
                            'use_case' => Input::get('use_case'),
                            'added' => Input::get('added'),
                            'study_id' => Input::get('study_id'),
                            'balance' => $total_quantity,
                            'status' => 1,
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

                                        <th width="15%">Generic</th>
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
                                        <th width="4%">Check</th>
                                        <th width="4%">Remark</th>
                                        <th width="4%">Validity</th>
                                        <th width="4%">Quantity</th>
                                        <th width="30%">Action</th>
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
                                        $generic_id = $override->get('batch_records', 'generic_id', $bDiscription['id'])[0]['generic_id'];
                                        // $brand_id = $override->get('batch_records', 'brand_id', $generic_id)[0]['brand_id'];
                                        $batch_id = $override->get('batch_records', 'generic_id', $bDiscription['id'])[0]['name'];
                                        $batch_no = $override->get('batch_records', 'generic_id', $bDiscription['id'])[0]['name'];
                                        $category = $override->get('batch_records', 'generic_id', $bDiscription['id'])[0]['category'];
                                        $study_id = $override->get('batch_records', 'generic_id', $bDiscription['id'])[0]['study_id'];
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
                                                <?php if ($batch['next_check'] == date('Y-m-d')) { ?>
                                                    <a href="#" role="button" class="btn btn-warning btn-sm">Check Date!</a>
                                                <?php } elseif ($batch['next_check'] < date('Y-m-d')) { ?>
                                                    <a href="#" role="button" class="btn btn-danger">NOT CHECKED!</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success">OK!</a>
                                                <?php } ?>
                                            </td>
                                            <td><?= $batch['remark'] ?></td>
                                            <td>
                                                <?php if ($bDiscription['expire_date'] <= $today) { ?>
                                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Expired</a>
                                                <?php } elseif ($bDiscription['expire_date'] > $today) { ?>
                                                    <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Not Expired</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success" data-toggle="modal">Un - Checked</a>
                                                <?php } ?>
                                            </td>
                                            <!-- <td>
                                                <?php if ($bDiscription['quantity'] <= $bDiscription['notify_quantity'] && $bDiscription['quantity'] > 0) { ?>
                                                    <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                <?php } elseif ($bDiscription['quantity'] == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                <?php } ?>
                                            </td> -->
                                            <td>
                                                <?php if ($sumLoctn <= $Notify && $sumLoctn > 0) { ?>
                                                    <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                <?php } elseif ($sumLoctn == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                <?php } else { ?>
                                                    <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="data.php?id=7&did=<?= $bDiscription['id'] ?>" class="btn btn-info">View</a>
                                                <a href="#edit_stock_guide_id<?= $bDiscription['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Update</a>
                                                <a href="#archive<?= $batchDesc['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Archive</a>
                                                <!-- <a href="#burn<?= $batchDesc['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Burn / Destroy</a> -->
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
                                                                            <input value="<?= $bDiscription['name'] ?>" type="text" id="name" disabled />
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
                                                                            <input value=" " class="validate[required]" type="number" name="added" id="added" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" value="<?= $bDiscription['id'] ?>">
                                                            <input type="hidden" name="generic_id" value="<?= $generic_id ?>">
                                                            <input type="hidden" name="study_id" value="<?= $bDiscription['study_id'] ?>">
                                                            <input type="hidden" name="quantity" value="<?= $bDiscription['quantity'] ?>">
                                                            <input type="hidden" name="notify_quantity" value="<?= $bDiscription['notify_quantity'] ?>">
                                                            <input type="hidden" name="use_group" value="<?= $bDiscription['use_group'] ?>">
                                                            <input type="hidden" name="use_case" value="<?= $bDiscription['use_case'] ?>">
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

    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script> -->


    <script>
        // $(document).ready(function() {
        //     $("#myInput").on("keyup", function() {
        //         var value = $(this).val().toLowerCase();
        //         $("#myTable tr").filter(function() {
        //             $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        //         });
        //     });

        //     $('#inventory_report1').DataTable({

        //         "language": {
        //             "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Report Searched</h1><div><span></span></div></div>"
        //         },


        //         dom: 'Bfrtip',
        //         buttons: [{

        //                 extend: 'excelHtml5',
        //                 title: 'Inventory_status_report',
        //                 className: 'btn-primary',
        //             },
        //             {
        //                 extend: 'pdfHtml5',
        //                 title: 'Inventory_status_report',
        //                 className: 'btn-primary',
        //                 orientation: 'landscape',
        //                 pageSize: 'LEGAL'

        //             },
        //         ],
        //     });
        // });


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
</body>

</html>