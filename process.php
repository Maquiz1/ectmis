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
} elseif ($_GET['content'] == 'a_study') {
    $batches = $override->get('batch_product', 'study_id', $_GET['getUid']) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'gen') {
    $batches = $override->get('brand', 'generic_id', $_GET['getUid']) ?>
    <option value="">Select Brand</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'bat') {
    $batches = $override->get('batch', 'brand_id', $_GET['getUid']) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'bat') {
    $batches = $override->get('maintainance_type', 'id', $_GET['getUid']) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'a_batch') {
    $a_batch = $override->get('batch_product', 'brand_id', $_GET['getUid'])[0];
    print_r($a_batch);
    $a_study_staff = $override->get('staff_study', 'study_id', $a_batch['study_id']);
    // $a_desc=$override->getNews('brand', 'id',$_GET['getUid'], 'status', 1);
    $study_sites = $override->get('study_sites', 'study_id', $a_batch['study_id']) ?>
    <div class="row-form clearfix">
        <div class="col-md-3">Mediction/Equipment</div>
        <div class="col-md-9">
            <select name="drug" style="width: 100%;" id="s2_1" required>
                <option value="">Select Mediction/Equipment</option>
                <?php foreach ($a_batch as $dec) { ?>
                    <option value="<?= $dec['id'] ?>"><?= $dec['batch_no'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row-form clearfix">
        <div class="col-md-3">Staff</div>
        <div class="col-md-9">
            <select name="staff" style="width: 100%;" required>
                <option value="">Select </option>
                <?php foreach ($a_study_staff as $staff) {
                    $stf = $override->get('user', 'id', $staff['staff_id'])[0] ?>
                    <option value="<?= $stf['id'] ?>"><?= $stf['firstname'] . ' ' . $stf['lastname'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row-form clearfix">
        <div class="col-md-3">Site</div>
        <div class="col-md-9">
            <select name="site" style="width: 100%;" required>
                <option value="">Select Site</option>
                <?php foreach ($study_sites as $study_site) {
                    $site = $override->get('sites', 'id', $study_site['site_id'])[0] ?>
                    <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

<?php
} elseif ($_GET['content'] == 'gen2') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('generic', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['gen_name'] = $name['batch_no'];
            $output['gen_id'] = $name['id'];
            $output['use_case'] = $name['use_case'];
            $output['use_group'] = $name['use_group'];
            $output['maintainance'] = $name['maintainance'];
            $output['category'] = $name['category'];
        }
        echo json_encode($output);
    }
}
elseif ($_GET['content'] == 'bat2') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'generic_id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['gen_id'] = $name['id'];
            $output['use_case'] = $name['use_case'];
            $output['use_group'] = $name['use_group'];
            $output['maintainance'] = $name['maintainance'];
            $output['category'] = $name['category'];
            $output['batch_no'] = $name['batch_no'];
            $output['brand_id'] = $name['brand_id'];
            $output['batch_id'] = $name['id'];
        }
        echo json_encode($output);
    }
}

elseif ($_GET['content'] == 'bat3') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'generic_id', $_GET['getUid']);
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
}
?>