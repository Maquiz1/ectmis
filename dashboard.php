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
$numRec=2;
$users = $override->getData('user');
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
    <title> Dashboard | Pharmacy Inventory</title>
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

                <div class="row">

                    <div class="col-md-3">

                        <div class="wBlock red clearfix">
                            <div class="dSpace">
                                <h3>Studies</h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--130,190,260,230,290,400,340,360,390-->
                                </span>
                                <span class="number"><?= $override->getNo('study') ?></span>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="wBlock green clearfix">
                            <div class="dSpace">
                                <h3>Staff</h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                                </span>
                                <span class="number"><?= $override->getNo('user') ?></span>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="wBlock blue clearfix">
                            <a href="info.php?id=3">
                                <div class="dSpace">
                                    <h3>GENERIC NAMES</h3>
                                    <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                        <!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190-->
                                    </span>
                                    <span class="number">
                                        <?= $override->getNo('batch') ?></span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- <div class="col-md-3">

                    <div class="wBlock blue clearfix">
                        <div class="dSpace">
                            <h3>Client</h3>
                            <span class="mChartBar" sparkType="bar" sparkBarColor="white"> -->
                    <!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190-->
                    <!-- </span>
                            <span class="number"> -->
                    <?php
                    // $override->getNo('clients')
                    ?>
                    <!-- </span>
                        </div>

                    </div>

                </div> -->

                    <div class="col-md-3">
                        <a href="info.php?id=10">
                            <div class="wBlock yellow clearfix">
                                <div class="dSpace">
                                    <h3>Brand Names</h3>
                                    <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                        <!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190-->
                                    </span>
                                    <span class="number"><?= $override->getCount('batch_description', 'status', 1) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

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
                            <h1>Drug Transfer</h1>
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
                            <!-- <thead>
                                    <tr>
                                        <th width="15%">From</th>
                                        <th width="15%">Drugs</th>
                                        <th width="15%">Quantity</th>
                                        <th width="15%">Date</th>
                                        <th width="25%">Manage</th>
                                    </tr>
                                </thead> -->

                            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="checkall" /></th>
                                        <th width="15%">Name</th>
                                        <th width="15%">Study</th>
                                        <th width="15%">Manufacturer</th>
                                        <th width="10%">Amount</th>
                                        <th width="10%">Man Date</th>
                                        <th width="10%">Exp Date</th>
                                        <th width="5%">Status</th>
                                        <th width="20%">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $amnt = 0;
                                    $pagNum=$override->getCount('batch','status',1);
                                    $pages = ceil($pagNum / $numRec);if(!$_GET['page'] || $_GET['page'] == 1){$page = 0;}else{$page = ($_GET['page']*$numRec)-$numRec;}
                                    foreach ($override->getWithLimit('batch', 'status', 1,$page,$numRec) as $batch) {
                                        $study = $override->get('study', 'id', $batch['study_id'])[0];
                                        $batchItems = $override->getSumD1('batch_description', 'assigned', 'batch_id', $batch['id']);
                                        // print_r($batchItems[0]['SUM(assigned)']);
                                        $amnt = $batch['amount'] - $batchItems[0]['SUM(assigned)']; ?>
                                        <tr>
                                            <td><input type="checkbox" name="checkbox" /></td>
                                            <td> <a href="info.php?id=5&bt=<?= $batch['id'] ?>"><?= $batch['name'] ?></a></td>
                                            <td><?= $study['name'] ?></td>
                                            <td><?= $batch['manufacturer'] ?></td>
                                            <td><?= $batch['amount'] ?></td>
                                            <td><?= $batch['manufactured_date'] ?></td>
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
                </div>
                <div class="pull-right">
                    <div class="btn-group">
                        <a href="dashboard.php?page=<?php if(($_GET['page']-1) > 0){echo $_GET['page']-1;}else{echo 1;}?>" class="btn btn-default"> < </a>
                        <?php for($i=1;$i<=$pages;$i++){?>
                            <a href="dashboard.php?page=<?=$_GET['id']?>&page=<?=$i?>" class="btn btn-default <?php if($i == $_GET['page']){echo 'active';}?>"><?=$i?></a>
                        <?php } ?>
                        <a href="dashboard.php?page=<?php if(($_GET['page']+1) <= $pages){echo $_GET['page']+1;}else{echo $i-1;}?>" class="btn btn-default"> > </a>
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
    </script>
</body>

</html>