<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
// $noE = 0;
// $noC = 0;
// $noD = 0;
// $numRec = 10;
// $users = $override->getData('user');
// $today = date('Y-m-d');
// $todayPlus30 = date('Y-m-d', strtotime($today . ' + 30 days'));

?>

<div class="workplace">
    <div class="row">
        <?php foreach ($override->get('study_group', 'staff_id', $user->data()->id) as $id) {
            if ($id['group_id'] == 1) {
        ?>
                <div class="col-md-4">
                    <div class="wBlock green clearfix">
                        <a href="dashboard.php?use_group=1">
                            <div class="dSpace">
                                <h3>MEDICINES</h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                                </span>
                                <span class="number"><?= $override->countData('generic', 'use_group', 1, 'status', 1) ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php
            }
            ?>

            <?php
            if ($id['group_id'] == 2) {
            ?>
                <div class="col-md-4">
                    <div class="wBlock blue clearfix">
                        <a href="dashboard.php?use_group=2">
                            <div class="dSpace">
                                <h3>MEDICAL EQUIPMENT</h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                                </span>
                                <span class="number"><?= $override->countData('generic', 'use_group', 2, 'status', 1) ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if ($id['group_id'] == 3) {
            ?>
                <div class="col-md-4">
                    <div class="wBlock yellow clearfix">
                        <a href="dashboard.php?use_group=3">
                            <div class="dSpace">
                                <h3>ACCESSORIES</h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                                </span>
                                <span class="number"><?= $override->countData('generic', 'use_group', 3, 'status', 1) ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php
            }
            ?>

            <?php
            if ($id['group_id'] == 4) {
            ?>
                <div class="col-md-4">
                    <div class="wBlock orrange clearfix">
                        <a href="dashboard.php?use_group=4">
                            <div class="dSpace">
                                <h3>SUPPLIES </h3>
                                <span class="mChartBar" sparkType="bar" sparkBarColor="white">
                                    <!--5,10,15,20,23,21,25,20,15,10,25,20,10-->
                                </span>
                                <span class="number"><?= $override->countData('generic', 'use_group', 4, 'status', 1) ?></span>
                            </div>
                        </a>
                    </div>
                </div>
        <?php
            }
        }
        ?>


    </div>
</div>