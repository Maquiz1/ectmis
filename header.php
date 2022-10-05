<?php
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
$numRec = 10;
$users = $override->getData('user');
$today = date('Y-m-d');
$todayPlus30 = date('Y-m-d', strtotime($today . ' + 30 days'));
?>

<div class="workplace">

    <div class="row">

        <div class="col-md-4">

            <div class="wBlock green clearfix">
                <a href="info.php?id=3&type=1">
                    <div class="dSpace">
                        <h3>MEDICATIONS</h3>
                        <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                            <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                        </span>
                        <span class="number"><?= $override->countData('batch_product', 'use_group', 1, 'status', 1) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="wBlock blue clearfix">
                <a href="data.php?id=4&type=2">
                    <div class="dSpace">
                        <h3>MEDICAL EQUIPMENTS</h3>
                        <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                            <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                        </span>
                        <span class="number"><?= $override->countData('batch_product', 'use_group', 2, 'status', 1) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="wBlock yellow clearfix">
                <a href="info.php?id=12&type=3">
                    <div class="dSpace">
                        <h3>ACCESSORIES / SUPPLIES </h3>
                        <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                            <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                        </span>
                        <span class="number"><?= $override->countData('batch_product', 'use_group', 3, 'status', 1) ?></span>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>