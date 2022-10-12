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
    $batches = $override->get('generic', 'study_id', $_GET['getUid']) ?>
    <option value="">Select Generic</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
    
}
elseif ($_GET['content'] == 'a_generic') {
    $batches = $override->get('brand', 'generic_id', $_GET['getUid']) ?>
    <option value="">Select BrandSS</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
    
}
elseif ($_GET['content'] == 'a_brand') {
    $batches = $override->get('batch', 'brand_id', $_GET['getUid']) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
    
} elseif ($_GET['content'] == 'update_generic_id') {
    $batches = $override->getNews('brand', 'generic_id', $_GET['getUid'],'status',1) ?>
    <option value="">Select Brand</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'update_brand_id') {
    $batches = $override->getNews('batch', 'brand_id', $_GET['getUid'],'status',1) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['batch_no'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'update_batch_id') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'update_batch_id') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['category'] = $name['category'];
        }
        echo json_encode($output);
    }
} elseif ($_GET['content'] == 'batch_id_check') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
        }
        echo json_encode($output);
    }
}elseif ($_GET['content'] == 'generic_update_details') {
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
}elseif ($_GET['content'] == 'generic_check_details') {
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
}elseif ($_GET['content'] == 'bat9') {
    $batches = $override->get('maintainance_type', 'id', $_GET['getUid']) ?>
    <option value="">Select Batch</option>
    <?php foreach ($batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'a_batch') {
    $batches = $override->get('staff_study', 'study_id', $_GET['getUid'])[0]; 
    $a_batches = $override->get('user', 'id', $batches['staff_id']) ?>
    <option value="">Select Staff</option>
    <?php foreach ($a_batches as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['username'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'a_batch2') {
        $batches2 = $override->get('study_sites', 'study_id', $_GET['getUid'])[0];
        $a_batches2 = $override->get('sites', 'id', $batches2['site_id']) ?>
    <option value="">Select Site</option>
    <?php foreach ($a_batches2 as $batch) { ?>
        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
    <?php }
} elseif ($_GET['content'] == 'gen2') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('generic', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['gen_name'] = $name['name'];
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

elseif ($_GET['content'] == 'a_brand2') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('batch', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['batch_no'] = $name['batch_no'];
        }
        echo json_encode($output);
    }
}

elseif ($_GET['content'] == 'a_generic2') {
    if ($_GET['getUid']) {
        $output = array();
        $project_id = $override->get('generic', 'id', $_GET['getUid']);
        foreach ($project_id as $name) {
            $output['generic_id'] = $name['id'];
            $output['use_case'] = $name['use_case'];
            $output['use_group'] = $name['use_group'];
            $output['maintainance'] = $name['maintainance'];
            $output['category'] = $name['category'];
        }
        echo json_encode($output);
    }
}
?>