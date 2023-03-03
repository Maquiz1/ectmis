<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
if ($_GET['content'] == 'region') {
    $districts = $override->get('district', 'region_id', $_GET['getUid']); ?>
    <option value="">Select District</option>
    <?php foreach ($districts as $district) { ?>
        <option value="<?= $district['id'] ?>"><?= $district['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'district') {
    $wards = $override->get('ward', 'district_id', $_GET['getUid']); ?>
    <option value="">Select Ward</option>
    <?php foreach ($wards as $ward) { ?>
        <option value="<?= $ward['id'] ?>"><?= $ward['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'download') {
    $user->exportData('citizen', 'citizen_data'); ?>

<?php } elseif ($_GET['content'] == 'study') {
    $sts = $override->get('study_files', 'study_id', $_GET['getUid']) ?>
    <option value="">Select File</option>
    <?php foreach ($sts as $st) { ?>
        <option value="<?= $st['id'] ?>"><?= $st['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'generic_id3') {
    $batches = $override->getNews('brand', 'generic_id', $_GET['getUid'], 'status', 1) ?>
    <option value="">Select Brand</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
}elseif ($_GET['content'] == 'generic_id33') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('generic', 'id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['use_group'] .= $name['use_group'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'generic_id4') {
    $batches = $override->get('generic_location', 'generic_id', $_GET['getUid']);
    ?>
    <option value="">Select Locations</option>
    <?php foreach ($batches as $batch) {
        $location_id = $override->get('location', 'id', $batch['location_id'])[0];
    ?>
        <option value="<?= $location_id['id'] ?>"><?= $location_id['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'generic_id5') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('generic_location', 'generic_id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['notify_quantity'] .= $name['notify_quantity'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'dispense_study_id') {
    $staff_study = $override->get('staff_study', 'study_id', $_GET['getUid']);
    ?>
    <option value="">Select Staffs</option>
    <?php foreach ($staff_study as $batch) {
        $batches = $override->get('user', 'id', $batch['staff_id'])[0];
    ?>
        <option value="<?= $batches['id'] ?>"><?= $batches['username'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'dispense_study_id2') {
    $staff_study = $override->get('study_sites', 'study_id', $_GET['getUid']);
    ?>
    <option value="">Select Sites</option>
    <?php foreach ($staff_study as $batch) {
        $batches = $override->get('sites', 'id', $batch['site_id'])[0];
    ?>
        <option value="<?= $batches['id'] ?>"><?= $batches['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'dispense_generic_id') {
    $batches = $override->getNews('brand', 'generic_id', $_GET['getUid'], 'status', 1) ?>
    <option value="">Select Brands</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'dispense_brand_id') {
    $batches = $override->getNews('batch', 'brand_id', $_GET['getUid'], 'status', 1) ?>
    <option value="">Select Batchs</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'dispense_batch_id') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
            $output['last_check'] = $name['last_check'];
            $output['next_check'] = $name['next_check'];
            $output['expire_date'] = $name['expire_date'];
            $output['category'] = $name['category'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'update_generic_id') {
    $batches = $override->getNews('brand', 'generic_id', $_GET['getUid'], 'status', 1) ?>
    <option value="">Select Brands</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'update_brand_id') {
    $batches = $override->getNews('batch', 'brand_id', $_GET['getUid'], 'status', 1) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'update_batch_id') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('batch', 'id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
            $output['category'] = $name['category'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'batch_id_check') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('batch', 'id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'generic_update_details') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('batch', 'generic_id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['gen_id'] = $name['id'];
            $output['generic_id'] = $name['generic_id'];
            $output['use_case'] = $name['use_case'];
            $output['use_group'] = $name['use_group'];
            $output['maintainance'] = $name['maintainance'];
            $output['category'] = $name['category'];
            $output['batch_no'] = $name['batch_no'];
            $output['brand_id'] = $name['brand_id'];
            $output['batch_id'] = $name['id'];
            $output['last_check'] = $name['last_check'];
            $output['next_check'] = $name['next_check'];
        }
        echo json_encode($output);
    }
} elseif ($_GGET['content'] == 'generic_check_details') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->getNews('batch', 'generic_id', $_GET['getUid'], 'status', 1);
        foreach ($project_id as $name) {
            $output['gen_id'] = $name['id'];
            $output['use_case'] = $name['use_case'];
            $output['use_group'] = $name['use_group'];
            $output['maintainance'] = $name['maintainance'];
            $output['category'] = $name['category'];
            $output['batch_no'] = $name['batch_no'];
            $output['brand_id'] = $name['brand_id'];
            $output['batch_id'] = $name['id'];
            $output['last_check'] = $name['last_check'];
            $output['next_check'] = $name['next_check'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'update_generic_name') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('generic', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['update_generic_id'] = $name['id'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'dispense_study_id3') {
    $batches = $override->get('generic_location', 'generic_id', $_GET['getUid']);
    $location = $override->get('location', 'id', $a_batch['location_id']);
    ?>
    <span>
        <table cellpadding="0" cellspacing="0" width="100%" class="table">
            <thead>
                <tr>
                    <th width="15%">Location</th>
                    <th width="15%">Required</th>
                    <th width="10%">Received</th>
                    <!-- <th width="4%">Action</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                $f = 0;
                foreach ($batches as $batch) {
                    $location_id = $override->get('location', 'id', $batch['location_id'])[0];
                ?>

                    <tr>
                        <td><?= $location_id['name'] ?></td>
                        <td>
                            <div class="form-group">
                                <input value="<?= $batch['notify_quantity'] ?>" type="text" disabled />
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="hidden" name="location[<?= $f ?>]" value="<?= $location_id['id'] ?>">
                                <input value="" type="number" min="0" class="validate[required]" name="amount[]" />
                            </div>
                        </td>
                        <!-- <td>
                            <?php if ($count == '') { ?>
                                <button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>
                            <?php } else { ?>
                                <button type="button" name="remove" id="' + count + '" class="btn btn-danger btn-xs remove">-</button>
                            <?php } ?>
                        </td> -->
                    <tr>
                    <?php
                    $f++;
                }
                    ?>
            </tbody>
        </table>
    </span>

<?php } elseif ($_GET['content'] == 'use_case_id') {
    $batches = $override->get('use_case_location', 'use_case_id', $_GET['getUid']);
?>
    <span>
        <table cellpadding="0" cellspacing="0" width="100%" class="table">
            <thead>
                <tr>
                    <th width="15%">Location</th>
                    <th width="15%">Required</th>
                    <!-- <th width="4%">Action</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                $f = 0;
                foreach ($batches as $batch) {
                    $location_id = $override->get('location', 'id', $batch['location_id'])[0];
                ?>

                    <tr>
                        <td><?= $location_id['name'] ?></td>
                        <td>
                            <div class="form-group">
                                <input type="hidden" name="location[<?= $f ?>]" value="<?= $location_id['id'] ?>">
                                <input value="" type="number" min="0" class="validate[required]" name="location_quantity[]" required />
                            </div>
                        </td>
                        <!-- <td>
                            <?php if ($count == '') { ?>
                                <button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>
                            <?php } else { ?>
                                <button type="button" name="remove" id="' + count + '" class="btn btn-danger btn-xs remove">-</button>
                            <?php } ?>
                        </td> -->
                    <tr>
                    <?php
                    $f++;
                }
                    ?>
            </tbody>
        </table>
    </span>

<?php } ?>