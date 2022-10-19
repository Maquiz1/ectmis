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
$numRec = 10;
$users = $override->getData('user');

$today = date('Y-m-d');
$todayPlus30 = date('Y-m-d', strtotime($today . ' + 30 days'));


if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('edit_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('position', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Position Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_staff')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                ),
                'email_address' => array(),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    //                    $staffSites=$override->get('staff_sites', 'staff_id', Input::get('id'));
                    //                    $staffStudy=$override->get('staff_study', 'staff_id', Input::get('id'));
                    $user->updateRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                    ), Input::get('id'));

                    //                    if($staffSites){
                    //                        $currentSite=array();
                    //                        foreach ($staffSites as $staffSite){
                    //                            array_push($currentSite, $staffSite['site_id']);
                    ////                            $user->deleteRecord('staff_sites','id',$staffSite['id']);
                    //                        }
                    //                        $changeSites = array_diff($currentSite,Input::get('sites'));
                    //                        if($changeSites){
                    //                            foreach ($changeSites as $changeSite){
                    //                                if(in_array($changeSite, $currentSite)){
                    ////                                    $user->deleteRecord('staff_sites','id',$changeSite);
                    //                                    print_r($changeSite);echo ' To delete, ';
                    //                                }else{
                    ////                                    $user->createRecord('staff_sites', array(
                    ////                                        'staff_id' => Input::get('id'),
                    ////                                        'site_id' => $changeSite,
                    ////                                    ));
                    //                                    print_r('New Site ,');
                    //                                }
                    //                            }
                    //                        }
                    //                    }
                    //                    foreach (Input::get('sites') as $site){
                    //                        $user->createRecord('staff_sites', array(
                    //                            'staff_id' => Input::get('id'),
                    //                            'site_id' => $site,
                    //                        ));
                    //                    }

                    $successMessage = 'Account Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('edit_batch')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'batch_no' => array(
                    'required' => true,
                ),
                'study' => array(
                    'required' => true,
                ),
                'amount' => array(
                    'required' => true,
                ),
                'manufactured_date' => array(
                    'required' => true,
                ),
                'expire_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('batch', array(
                        'name' => Input::get('name'),
                        'study_id' => Input::get('study'),
                        'batch_no' => Input::get('batch_no'),
                        'amount' => Input::get('amount'),
                        'notify_amount' => Input::get('notify_amount'),
                        'manufacturer' => Input::get('manufacturer'),
                        'manufactured_date' => Input::get('manufactured_date'),
                        'expire_date' => Input::get('expire_date'),
                        'create_on' => date('Y-m-d'),
                        'details' => Input::get('details'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ), Input::get('id'));

                    $successMessage = 'Batch Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('archive_batch')) {

            $checkBatch = $override->get('batch', 'id', Input::get('id'))[0];
            $batchLast = $checkBatch['last_check'];
            $batchNext = $checkBatch['next_check'];
            $batchexpire = $checkBatch['expire_date'];
            $generic_id = $checkBatch['generic_id'];
            $brand_id = $checkBatch['brand_id'];
            $batch_no = $checkBatch['batch_no'];
            $study_id = $checkBatch['study_id'];
            $category = $checkBatch['category'];
            $remarks = $checkBatch['remarks'];

            $batchBalance = $checkBatch['balance'];

            $checkGeneric = $override->get('generic', 'id', $generic_id)[0];
            $genericBalance = $checkGeneric['balance'] - $batchBalance;

            if ($batchBalance <= $checkGeneric['balance']) {
                $bufferBalance = 0;
                if ($checkGeneric['buffer'] >= $batchBalance) {
                    $bufferBalance = $checkGeneric['buffer'] - $batchBalance;
                }

                $user->updateRecord('batch', array(
                    'status' => 2,
                ), Input::get('id'));

                $user->updateRecord('generic', array(
                    'balance' => $genericBalance,
                    'buffer' => $bufferBalance,
                ), $generic_id);

                // $si = 0;
                // foreach ($override->getNews('generic_guide', 'generic_id', $generic_id,'location_id',$_GET['blid']) as $sid) {
                //     $user->updateRecord('generic_guide', array(
                //         'balance' => 0,
                //     ), $sid['id']);
                //     $si++;
                // }

                $user->createRecord('batch_records', array(
                    'generic_id' => $generic_id,
                    'brand_id' => $brand_id,
                    'batch_id' => Input::get('id'),
                    'batch_no' => $batch_no,
                    'quantity' => 0,
                    'assigned' => $batchBalance,
                    'balance' => $genericBalance,
                    'create_on' => date('Y-m-d'),
                    'staff_id' => $user->data()->id,
                    'status' => 2,
                    'study_id' => $study_id,
                    'last_check' => $batchLast,
                    'next_check' => $batchNext,
                    'category' => $category,
                    'remarks' => $remarks,
                    'expire_date' => $batchexpire,
                ));

                $successMessage = 'Medicine / Device Quarantine Successful';
            } else {
                $errorMessage = 'No Amount Available for Quarantine';
            }
        } elseif (Input::get('delete_batch')) {
            $user->updateRecord('batch', array(
                'status' => 3,
            ), Input::get('id'));
            $successMessage = 'Medicine / Device Destroyed / Burned Successful';
        } elseif (Input::get('edit_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('sites', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Site Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_study')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'pi' => array(
                    'required' => true,
                ),
                'coordinator' => array(
                    'required' => true,
                ),
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('study', array(
                        'name' => Input::get('name'),
                        'pi_id' => Input::get('pi'),
                        'co_id' => Input::get('coordinator'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                        'details' => Input::get('details'),
                    ), Input::get('id'));

                    $successMessage = 'Study Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_drug_cat')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('drug_cat', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Drug Category Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_batch_desc')) {
            $validate = $validate->check($_POST, array(
                'batch' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
                'category' => array(
                    'required' => true,
                ),
                'quantity' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $descSum = 0;
                $bSum = 0;
                $dSum = 0;
                $descSum = $override->getSumD1('batch_description', 'quantity', 'batch_id', Input::get('batch'));
                $bSum = $override->get('batch', 'id', Input::get('batch'))[0];
                $dSum = $descSum[0]['SUM(quantity)'] + Input::get('quantity');
                if ($dSum <= $bSum['amount']) {
                    try {
                        $user->updateRecord('batch_description', array(
                            'name' => Input::get('name'),
                            'cat_id' => Input::get('category'),
                            'quantity' => Input::get('quantity'),
                            'notify_amount' => Input::get('notify_amount'),
                        ), Input::get('id'));
                        $successMessage = 'Batch Description Successful Updated';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $errorMessage = 'Exceeded Batch Amount, Please cross check and try again';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_group')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('use_group', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Group Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_use_case')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('use_case', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Use Case Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_location')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('location', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Location Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_check')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'last_check' => array(
                    'required' => true,
                ),
                'next_check' => array(
                    'required' => true,
                )
            ));
            if ($validate->passed()) {
                if (Input::get('last_check') <= date('Y-m-d')) {
                    if (Input::get('last_check') >= Input::get('last')) {
                        if (Input::get('next_check') >= date('Y-m-d')) {
                            if (Input::get('next_check') >= Input::get('next')) {
                                try {
                                    $user->createRecord('check_records', array(
                                        'generic_id' => Input::get('check_generic_id'),
                                        'brand_id' => Input::get('check_brand_id'),
                                        'batch_id' => Input::get('check_batch_id'),
                                        'batch_no' => Input::get('check_batch_no'),
                                        'create_on' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id,
                                        'status' => 1,
                                        'last_check' => Input::get('last_check'),
                                        'next_check' => Input::get('next_check'),
                                        'remarks' => Input::get('remarks'),
                                    ));
                                    $BatchLastRow1 = $override->lastRow('check_records', 'id');
                                    $user->updateRecord('batch', array('next_check' => Input::get('next_check')), Input::get('check_batch_id'));
                                    $user->updateRecord('batch', array('last_check' => Input::get('last_check')), Input::get('check_batch_id'));
                                    $successMessage = 'Check Status Updated Successful';
                                } catch (Exception $e) {
                                    die($e->getMessage());
                                }
                            } else {
                                $errorMessage = 'Next Check Date already exists Check Again';
                            }
                        } else {
                            $errorMessage = 'Next Check Can not be Past';
                        }
                    } else {
                        $errorMessage = 'Last Check Date already exists Check Again';
                    }
                } else {
                    $errorMessage = 'Last Check Can not be Future';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_stock_guide')) {
            $validate = $validate->check($_POST, array(
                'added' => array(
                    'required' => true,
                ),
                'study_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $total_quantity = 0;
                if (Input::get('added') > 0) {
                    $checkGeneric = $override->get('generic', 'id', Input::get('update_generic_id'))[0];
                    $genericBalance = $checkGeneric['balance'] + Input::get('added');
                    $bufferBalance = $checkGeneric['buffer'] + Input::get('added');

                    $checkBatch = $override->get('batch', 'id', Input::get('id'))[0];
                    $batchLast = $checkBatch['last_check'];
                    $batchNext = $checkBatch['next_check'];
                    $batchexpire = $checkBatch['expire_date'];

                    $batchBalance = $checkBatch['balance'] + Input::get('added');

                    $batchBuffer = $checkBatch['buffer'] + Input::get('added');


                    $checkAssigned = $override->selectData1('assigned_batch', 'batch_id', Input::get('id'), 'status', 1)[0];


                    try {
                        $user->updateRecord('generic', array(
                            'balance' => $genericBalance,
                            'buffer' => $bufferBalance,
                        ), Input::get('update_generic_id'));

                        $user->updateRecord('batch', array(
                            'balance' => $batchBalance,
                            'buffer' => $batchBuffer,
                        ), Input::get('id'));

                        $user->updateRecord('assigned_batch', array(
                            'balance' => $batchBalance,
                        ), $checkAssigned['id']);

                        $user->createRecord('batch_records', array(
                            'generic_id' => Input::get('update_generic_id'),
                            'brand_id' => Input::get('update_brand_id'),
                            'batch_id' => Input::get('id'),
                            'batch_no' => Input::get('update_batch_no'),
                            'quantity' => Input::get('added'),
                            'assigned' => 0,
                            'balance' => $genericBalance,
                            'create_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'study_id' => Input::get('study_id'),
                            'last_check' => $batchLast,
                            'next_check' => $batchNext,
                            'category' => Input::get('update_category_id'),
                            'remarks' => Input::get('remarks'),
                            'expire_date' => $batchexpire,
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
        } elseif (Input::get('alocate_batch')) {
            $validate = $validate->check($_POST, array(
                'allocate_amount' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $total_quantity = 0;
                if (Input::get('allocate_amount') > 0) {
                    $checkAllocate = $override->selectData1('allocate_guide_records', 'batch_id', Input::get('id'), 'location_id', Input::get('location_batch_id'))[0];
                    $checkAllocateBalance = $checkAllocate['balance'] + Input::get('allocate_amount');


                    $checkGeneric = $override->get('generic', 'id', Input::get('update_generic_id'))[0];
                    $genericBalance = $checkGeneric['balance'] + Input::get('allocate_amount');

                    $checkBatch = $override->get('batch', 'id', Input::get('id'))[0];
                    $batchLast = $checkBatch['last_check'];
                    $batchNext = $checkBatch['next_check'];
                    $batchexpire = $checkBatch['expire_date'];
                    $use_group = $checkGeneric['use_group'];
                    $use_case = $checkGeneric['use_case'];

                    $checkGuide = $override->get('generic_guide', 'id', Input::get('location_guide_id'))[0];
                    $guideBalance = $checkGuide['balance'] + Input::get('allocate_amount');
                    if (Input::get('allocate_amount') <= $checkBatch['buffer']) {
                        if (Input::get('allocate_amount') <= $checkGeneric['buffer']) {
                            $bufferBalance = $checkGeneric['buffer'] - Input::get('allocate_amount');
                            $batchBufferBalance = $checkBatch['buffer'] - Input::get('allocate_amount');

                            try {
                                if ($checkAllocate) {
                                    $user->updateRecord('allocate_guide_records', array(
                                        'balance' => $checkAllocateBalance,
                                    ), $checkAllocate['id']);
                                } else {
                                    $user->createRecord('allocate_guide_records', array(
                                        'generic_id' => Input::get('update_generic_id'),
                                        'brand_id' => Input::get('update_brand_id'),
                                        'batch_id' => Input::get('id'),
                                        'batch_no' => Input::get('update_batch_no'),
                                        'guide_id' => Input::get('location_guide_id'),
                                        'quantity' => Input::get('allocate_amount'),
                                        'assigned' => 0,
                                        'balance' => $guideBalance,
                                        'buffer' => $batchBufferBalance,
                                        'location_id' => Input::get('location_batch_id'),
                                        'create_on' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id,
                                        'status' => 1,
                                        'study_id' => Input::get('study_id'),
                                    ));
                                }
                                $user->updateRecord('generic_guide', array(
                                    'balance' => $guideBalance,
                                ), Input::get('location_guide_id'));

                                $user->updateRecord('generic', array(
                                    'buffer' => $bufferBalance,
                                ), Input::get('update_generic_id'));

                                $user->updateRecord('batch', array(
                                    'buffer' => $batchBufferBalance,
                                ), Input::get('id'));

                                $user->createRecord('generic_guide_records', array(
                                    'generic_id' => Input::get('update_generic_id'),
                                    'brand_id' => Input::get('update_brand_id'),
                                    'batch_id' => Input::get('id'),
                                    'batch_no' => Input::get('update_batch_no'),
                                    'guide_id' => Input::get('location_guide_id'),
                                    'quantity' => Input::get('allocate_amount'),
                                    'notify_quantity' => 0,
                                    'assigned' => 0,
                                    'balance' => $guideBalance,
                                    'buffer' => $bufferBalance,
                                    'use_group' => $use_group,
                                    'location_id' => Input::get('location_batch_id'),
                                    'create_on' => date('Y-m-d'),
                                    'staff_id' => $user->data()->id,
                                    'status' => 1,
                                    'use_case' => $use_case,
                                    'study_id' => Input::get('study_id'),
                                ));

                                $successMessage = 'Stock guied Successful Allocated';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $errorMessage = 'Amount to Alocate Must not Be Greater Than Buffer Amount';
                        }
                    } else {
                        $errorMessage = 'Amount added Must not Be Greater Than Balance';
                    }
                } else {
                    $errorMessage = 'Amount added Must Be Greater Than 0';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('dispense_stock')) {
            $validate = $validate->check($_POST, array(
                'removed' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $total_quantity = 0;
                if (Input::get('removed') > 0) {
                    $checkGeneric = $override->selectData1('generic', 'status', 1, 'id', Input::get('update_generic_id'))[0];
                    $genericAssigned = $checkGeneric['assigned'] + Input::get('removed');
                    $genericBalance = $checkGeneric['balance'] - Input::get('removed');



                    $checkBatch = $override->selectData1('batch', 'status', 1, 'id', Input::get('id'))[0];
                    $batchAssigned = $checkBatch['assigned'] + Input::get('removed');
                    $batchBalance = $checkBatch['balance'] - Input::get('removed');
                    $batchLast = $checkBatch['last_check'];
                    $batchNext = $checkBatch['next_check'];
                    $batchexpire = $checkBatch['expire_date'];



                    $checkGuide = $override->get('generic_guide', 'id', Input::get('location_guide_id'))[0];
                    $guideBalance = $checkGuide['balance'] - Input::get('removed');

                    $bufferBalance = $checkGeneric['buffer'];

                    if (Input::get('removed') <= $checkGuide['balance']) {
                        try {
                            $user->updateRecord('batch', array(
                                'status' => 1,
                                'assigned' => $batchAssigned,
                                'balance' => $batchBalance
                            ), Input::get('id'));

                            $user->updateRecord('generic', array(
                                'status' => 1,
                                'assigned' => $genericAssigned,
                                'balance' => $genericBalance
                            ), Input::get('update_generic_id'));

                            $user->createRecord('assigned_stock_rec', array(
                                'study_id' => Input::get('dispense_study_id'),
                                'generic_id' => Input::get('update_generic_id'),
                                'brand_id' => Input::get('update_brand_id'),
                                'batch_id' => Input::get('id'),
                                'batch_no' => Input::get('update_batch_no'),
                                'staff_id' => Input::get('staff'),
                                'site_id' => Input::get('site'),
                                'quantity' => Input::get('removed'),
                                'notes' => Input::get('remarks'),
                                'status' => 1,
                                'admin_id' => $user->data()->id,
                            ));

                            $user->createRecord('batch_records', array(
                                'generic_id' => Input::get('update_generic_id'),
                                'brand_id' => Input::get('update_brand_id'),
                                'batch_id' => Input::get('id'),
                                'batch_no' => Input::get('update_batch_no'),
                                'quantity' => 0,
                                'assigned' => Input::get('removed'),
                                'balance' => $genericBalance,
                                'create_on' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'study_id' => Input::get('dispense_study_id'),
                                'last_check' => $batchLast,
                                'next_check' => $batchNext,
                                'category' => Input::get('update_category_id'),
                                'remarks' => Input::get('remarks'),
                                'expire_date' => $batchexpire,
                            ));

                            $user->updateRecord('generic_guide', array(
                                'balance' => $guideBalance,
                            ), Input::get('location_guide_id'));

                            $user->createRecord('generic_guide_records', array(
                                'generic_id' => Input::get('update_generic_id'),
                                'brand_id' => Input::get('update_brand_id'),
                                'batch_id' => Input::get('id'),
                                'batch_no' => Input::get('update_batch_no'),
                                'guide_id' => Input::get('location_guide_id'),
                                'quantity' => 0,
                                'notify_quantity' => 0,
                                'assigned' => Input::get('removed'),
                                'balance' => $guideBalance,
                                'buffer' => $bufferBalance,
                                'use_group' => $use_group,
                                'location_id' => Input::get('location_batch_id'),
                                'create_on' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'use_case' => $use_case,
                                'study_id' => Input::get('dispense_study_id'),
                            ));

                            $successMessage = 'Stock guied Successful Dispensed / Removed';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $errorMessage = 'Amount Dispensed Must not Be Greater Than Balance';
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
    <title> Info | e-CTMIS </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Info</a> <span class="divider">></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <?php include "header.php"; ?>

            <div class="workplace">
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

                <div class="row">

                    <?php if ($_GET['id'] == 1) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Expired Medicines/Devices</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Batch No</th>
                                            <th width="4%">Used</th>
                                            <th width="4%">Balance</th>
                                            <th width="8%">Expire Date</th>
                                            <th width="8%">Last Check</th>
                                            <th width="8%">Next Check</th>
                                            <th width="5%">Expiration</th>
                                            <th width="5%">Checking</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'generic_id', $_GET['gid'], 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        $amnt = 0;
                                        foreach ($override->getWithLimit1('batch', 'generic_id', $_GET['gid'], 'status', 1, $page, $numRec) as $batchDesc) {
                                            $generic_name = $override->get('generic', 'id', $_GET['gid'])[0]['name'];
                                            $brand_name = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['name'];
                                            $check_generic_id = $override->get('generic', 'id', $_GET['gid'])[0]['id'];
                                            $check_brand_id = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['id'];
                                            $check_batch_id = $batchDesc['id'];
                                            $check_batch_no = $batchDesc['batch_no'];

                                            // $generic_guide_id = $override->get('generic_guide', 'id', $_GET['gid'])[0];
                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td>
                                                    <?php if ($batchDesc['expire_date'] <= date('Y-m-d')) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal"><?= $check_batch_no ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"><?= $check_batch_no ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['assigned'] ?></td>
                                                <td><?= $batchDesc['balance'] ?></td>
                                                <td><?= $batchDesc['expire_date'] ?></td>
                                                <td><?= $batchDesc['last_check'] ?></td>
                                                <td><?= $batchDesc['next_check'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['expire_date'] <= $today) { ?>
                                                        <a href="#archive<?= $batchDesc['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Quarantine</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($batchDesc['next_check'] <= date('Y-m-d')) { ?>
                                                        <a href="#check_stock<?= $batchDesc['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Not Checked</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="archive<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Quarantine Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to Quarantine this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <!-- <input type="hidden" name="location[]" value="<?= $batchDesc['id'] ?>"> -->
                                                                <input type="submit" name="archive_batch" value="Archive" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="check_stock<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Update Check Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">

                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Generic Name:</label>
                                                                                <input value="<?= $generic_name ?>" type="text" name="generic_name" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Brand Name</label>
                                                                                <input value="<?= $brand_name ?>" type="text" name="brand_name" disabled />
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
                                                                                <input value="<?= $batchDesc['batch_no'] ?>" type="text" name="batch_no" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Check Date:</label>
                                                                                <input value=" " class="validate[required]" type="date" name="last_check" id="last_check" />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Next Check:</label>
                                                                                <input value=" " class="validate[required]" type="date" name="next_check" id="next_check" />

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="row">

                                                                    <div class="col-sm-12">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Remarks:</label>
                                                                                <div class="col-md-9">
                                                                                    <textarea class="" name="remarks" id="remarks" rows="4"></textarea>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input value="<?= $check_generic_id ?>" type="hidden" name="check_generic_id" id="check_generic_id" />
                                                                <input value="<?= $check_brand_id ?>" type="hidden" name="check_brand_id" id="check_brand_id" />
                                                                <input value="<?= $check_batch_id ?>" type="hidden" name="check_batch_id" id="check_batch_id" />
                                                                <input value="<?= $check_batch_no ?>" type="hidden" name="check_batch_no" id="check_batch_no" />
                                                                <input type="hidden" name="next" value="<?= $batchDesc['next_check'] ?>">
                                                                <input type="hidden" name="last" value="<?= $batchDesc['last_check'] ?>">
                                                                <input type="submit" name="update_check" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="burn<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to destroy / Burn this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="submit" name="delete_batch" value="Burn / Destroy" class="btn btn-danger">
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
                    <?php } elseif ($_GET['id'] == 2) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of medicine 30 days Before Expiration Date</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Generic Name</th>
                                            <th width="25%">Study</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%">Expire Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'expire_date', $todayPlus30, 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimitLessThan30('batch', 'expire_date', $todayPlus30, 'status', 1, $page, $numRec) as $list) {
                                            $study = $override->get('study', 'id', $list['study_id'])[0]['name'];
                                            // print_r($study);
                                        ?>
                                            <tr>
                                                <td> <a href="info.php?id=7&bt=<?= $list['id'] ?>"><?= $list['name'] ?></a></td>
                                                <td><?= $study ?></td>
                                                <td><?= $list['amount'] ?></td>
                                                <td><?= $list['expire_date'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 3) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Batch Description</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="35%">Generic Name</th>
                                            <th width="15%">Batch No</th>
                                            <th width="10%">Drug Category</th>
                                            <th width="10%">Current Quantity</th>
                                            <th width="10%">Last Check Date</th>
                                            <th width="25%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimitLessThanDate('batch_description', 'next_check_date', $today, 'status', 1, $page, $numRec) as $batchDesc) {
                                            $batch_no = $override->get('batch', 'id', $batchDesc['batch_id'])[0];
                                            $dCat = $override->get('drug_cat', 'id', $batchDesc['cat_id'])[0] ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>"><?= $batchDesc['name'] ?></a></td>
                                                <td><?= $batch_no['batch_no'] ?></td>
                                                <td><?= $dCat['name'] ?></td>
                                                <td> <?= $batchDesc['quantity'] ?></td>
                                                <td> <?= $batchDesc['last_check_date'] ?></td>
                                                <td>
                                                    <!-- <a href="info.php?id=8&dsc=<?= $batchDesc['id'] ?>" class="btn btn-info">Details</a> -->
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 4) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Unchecked Devices / Medicines</h1>
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
                                <table id='tableId4' cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Last Check</th>
                                            <th width="5%">Status</th>
                                            <th width="10%">Next Check</th>
                                            <th width="10%">Remarks</th>
                                            <th width="20%">Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_product', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        $type = $_GET['type'];
                                        foreach ($override->getNews('batch_product', 'status', 1, 'use_group', $type) as $batch) {
                                            $generic = $override->get('generic', 'id', $batch['generic_id'])[0]['name'];
                                            $brand = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                        ?>
                                            <tr>
                                                <td> <a href="info.php?id=5&bt=<?= $batch['id'] ?>"><?= $generic ?></a></td>
                                                <td><?= $brand ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td>
                                                    <?php if ($batch['next_check'] == date('Y-m-d')) { ?>
                                                        <a href="#" role="button" class="btn btn-warning btn-sm">Check Date!</a>
                                                    <?php } elseif ($batch['next_check'] < date('Y-m-d')) { ?>
                                                        <a href="#" role="button" class="btn btn-danger">NOT CHECKED!</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success">OK!</a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batch['next_check'] ?></td>
                                                <td><?= $batch['remark'] ?></td>
                                                <td>
                                                    <a href="data.php?id=8&updateId=<?= $batch['id'] ?>" class="btn btn-default">View</a>
                                                    <a href="#desc<?= $batch['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="desc<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Batch Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">

                                                                    <div class="col-sm-12">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>NAME:</label>
                                                                                <div class="col-md-9"><input type="text" name="name" value='<?= $brand ?>' readonly /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">


                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Maintainance Status:</label>
                                                                                <select name="maintainance_status" style="width: 100%;" required>
                                                                                    <option value="">Select Type</option>
                                                                                    <?php foreach ($override->getData('maintainance_status') as $study) { ?>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Check Date:</label>
                                                                                <div class="col-md-9"><input type="date" name="check_date" id="check_date" /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">


                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Remark:</label>
                                                                                <div class="col-md-9"><input type="text" name="remark" id="remark" /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Next Check Date:</label>
                                                                                <div class="col-md-9"><input type="date" name="next_check" id="next_check" /> <span></span></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="dr"><span></span></div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                <input type="hidden" name="maintainance" value="<?= $batch['maintainance'] ?>">
                                                                <input type="submit" name="update_check" value="Save updates" class="btn btn-warning">
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
                    <?php } elseif ($_GET['id'] == 19) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Product Assignment History</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="15%">Staff Name</th>
                                            <th width="25%">Study</th>
                                            <th width="25%">Drug</th>
                                            <th width="10%">Quantity</th>
                                            <th width="25%">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->get('assigned_stock_rec', 'drug_id', $_GET['dsc']) as $batchDesc) {
                                            $study = $override->get('study', 'id', $batchDesc['study_id'])[0];
                                            $staff = $override->get('user', 'id', $batchDesc['staff_id'])[0];
                                            $drug = $override->get('batch_description', 'id', $_GET['dsc'])[0];
                                        ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><a href="#"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></a></td>
                                                <td><?= $study['name'] ?></td>
                                                <td><?= $drug['name'] ?></td>
                                                <td> <?= $batchDesc['quantity'] ?></td>
                                                <td><?= $batchDesc['create_on'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 6) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Batch Description</h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="10%">Generic</th>
                                            <th width="10%">Brand</th>
                                            <th width="5%"> Use Case</th>
                                            <th width="5%">Quantity</th>
                                            <th width="5%"> ICU</th>
                                            <th width="5%"> EmKit</th>
                                            <th width="5%"> EmBuffer</th>
                                            <th width="5%"> AmbKit</th>
                                            <th width="5%"> CTM Room</th>
                                            <th width="5%"> Exam Room</th>
                                            <th width="5%"> Pharmacy</th>
                                            <th width="5%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_description', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->get4b('batch_description', 'status', 1, $page, $numRec) as $bDiscription) {
                                            $useGroup = $override->get('use_group', 'id', $bDiscription['use_group'])[0]['name'];
                                            $useCase = $override->get('use_case', 'id', $bDiscription['use_case'])[0]['name'];
                                            $icu = ($override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 1)[0]['quantity']);
                                            $EmKit = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 2)[0]['quantity'];
                                            $EmBuffer = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 3)[0]['quantity'];
                                            $AmKit = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 4)[0]['quantity'];
                                            $CTM = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 5)[0]['quantity'];
                                            $Exam = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 6)[0]['quantity'];
                                            $Pharmacy = $override->getNews('batch_guide_records', 'batch_description_id', $bDiscription['id'], 'location_id', 7)[0]['quantity'];
                                            $sumLoctn = $override->getSumD1('batch_guide_records', 'quantity', 'batch_description_id', $bDiscription['id'])[0]['SUM(quantity)'];

                                        ?>
                                            <tr>
                                                <td><?= $bDiscription['name'] ?></td>
                                                <td><?= $bDiscription['name'] ?></td>
                                                <td><?= $useCase ?></td>
                                                <td><?= $bDiscription['quantity'] ?></td>
                                                <td><?php if ($icu) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $icu; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($EmKit) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $EmKit; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($EmBuffer) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $EmBuffer; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($AmKit) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $AmKit; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($CTM) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $CTM; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($Exam) {
                                                    ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $Exam; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td><?php if ($Pharmacy) { ?>
                                                        <a href="#" role="button" class="btn btn-info"><?= $Pharmacy; ?></a>
                                                    <?php
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if ($bDiscription['quantity'] <= $bDiscription['notify_amount'] && $bDiscription['quantity'] > 0) { ?>
                                                        <a href="#" role="button" class="btn btn-warning btn-sm">Running Low</a>
                                                    <?php } elseif ($bDiscription['quantity'] == 0) { ?>
                                                        <a href="#" role="button" class="btn btn-danger">Out of Stock</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success">Sufficient</a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a href="info.php?id=16&gid=<?= $bDiscription['id'] ?>" class="btn btn-info">View</a>
                                                    <a href="#edit_stock_guide_id<?= $bDiscription['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Update</a>
                                                    <!-- <a href="#delete<?= $bDiscription['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a> -->
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
                                                                                <input value="<?= $override->get('batch', 'id', $bDiscription['batch_id'])[0]['name'] ?>" type="text" id="name" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Brand Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $bDiscription['name'] ?>" class="validate[required]" type="text" name="name" id="name" disabled />
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
                                                                                <input value=" " class="validate[required]" type="number" name="amount" id="amount" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="batch" value="<?= $bDiscription['batch_id'] ?>">
                                                                <input type="hidden" name="id" value="<?= $bDiscription['id'] ?>">
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
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device History Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="8%">Date</th>
                                            <th width="20%">Generic</th>
                                            <th width="20%">Brand</th>
                                            <th width="10%">Batch</th>
                                            <th width="5%">Used</th>
                                            <th width="5%">Balance</th>
                                            <th width="5%">Staff</th>
                                            <th width="8%">Expiration</th>
                                            <th width="8%">Next Check</th>
                                            <th width="5%">Expiration</th>
                                            <th width="5%">Checking</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'generic_id', $_GET['did'], 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        foreach ($override->getWithLimit1('batch', 'generic_id', $_GET['did'], 'status', 1, $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $generic = $override->get('generic', 'id', $_GET['did'])[0]['name'];
                                            $brand = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                            $generic_id = $override->get('generic', 'id', $_GET['did'])[0]['id'];
                                            $brand_id = $override->get('brand', 'id', $batch['brand_id'])[0]['id'];
                                            $batch_id = $batch['id'];
                                            $batch_no = $batch['batch_no'];
                                        ?>
                                            <tr>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $generic ?></td>
                                                <td><?= $brand ?></td>
                                                <td>
                                                    <?php if ($batch['expire_date'] <= date('Y-m-d')) { ?>
                                                        <a href="data.php?id=11&bid=<?= $batch['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal"><?= $batch_no ?></a>
                                                    <?php } else { ?>
                                                        <a href="data.php?id=11&bid=<?= $batch['id'] ?>" role="button" class="btn btn-success" data-toggle="modal"><?= $batch_no ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batch['assigned'] ?></td>
                                                <td><?= $batch['balance'] ?></td>
                                                <td><?= $staff ?></td>
                                                <td><?= $batch['expire_date'] ?></td>
                                                <td><?= $batch['next_check'] ?></td>
                                                <td>
                                                    <?php if ($batch['expire_date'] <= date('Y-m-d')) { ?>
                                                        <a href="#archive<?= $batchDesc['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Quarantine</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($batch['next_check'] <= date('Y-m-d')) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Not Checked</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Use Stock Guide Check Description</h1>
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
                                <table id='tableId8' cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic Name</th>
                                            <th width="15%">Brand Name</th>
                                            <th width="10%">check date</th>
                                            <th width="10%">Next check</th>
                                            <th width="10%">Remarks</th>
                                            <th width="10%">Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('check_records', 'generic_id', $_GET['gid']);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('check_records', 'generic_id', $_GET['gid'], $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $brand = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                            $generic = $override->get('generic', 'id', $batch['generic_id'])[0]['name'] ?>
                                            <tr>
                                                <td><?= $generic ?></td>
                                                <td><?= $brand ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td><?= $batch['next_check'] ?></td>
                                                <td><?= $batch['remark'] ?></td>
                                                <td><?= $staff ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Quarantined Medicines / Devices </h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="5%">Batch</th>
                                            <th width="5%">Quantity</th>
                                            <th width="8%">Last check date</th>
                                            <th width="8%">Date Expired</th>
                                            <th width="8%">Date Quarantined</th>
                                            <th width="5%">Staff</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch', 'status', 2);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('batch', 'status', 2, $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $generic_name = $override->get('generic', 'id', $batch['generic_id'])[0]['name'];
                                            $brand_name = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td><?= $batch['batch_no'] ?></td>
                                                <td><?= $batch['balance'] ?></td>
                                                <td><?= $batch['expire_date'] ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $staff ?></td>
                                                <td>
                                                    <a href="#destroy<?= $batch['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Burn / Destroy</a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="destroy<?= $batch['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete Product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to destroy / Burn this Product</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batch['id'] ?>">
                                                                <input type="submit" name="delete_batch" value="Burn / Destroy" class="btn btn-danger">
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
                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Destroyed/ Burn Medicines / Devices </h1>
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
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="5%">Batch</th>
                                            <th width="5%">Quantity</th>
                                            <th width="8%">Last check date</th>
                                            <th width="8%">Date Expired</th>
                                            <th width="8%">Date Destroyed / Burnt</th>
                                            <th width="5%">Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch', 'status', 3);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('batch', 'status', 3, $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $generic_name = $override->get('generic', 'id', $batch['generic_id'])[0]['name'];
                                            $brand_name = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td><?= $batch['batch_no'] ?></td>
                                                <td><?= $batch['balance'] ?></td>
                                                <td><?= $batch['expire_date'] ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $staff ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 11) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device History Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="8%">Date</th>
                                            <th width="20%">Generic</th>
                                            <th width="20%">Brand</th>
                                            <th width="10%">Batch</th>
                                            <th width="5%">Received</th>
                                            <th width="5%">Used</th>
                                            <th width="5%">Balance</th>
                                            <th width="5%">Staff</th>
                                            <th width="8%">Expiration</th>
                                            <th width="8%">last Check</th>
                                            <th width="5%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $amnt = 0;
                                        $pagNum = $override->getCount('batch_records', 'generic_id', $_GET['gid']);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('batch_records', 'generic_id', $_GET['gid'], $page, $numRec) as $batch) {
                                            $staff = $override->get('user', 'id', $batch['staff_id'])[0]['firstname'];
                                            $generic = $override->get('generic', 'id', $_GET['gid'])[0]['name'];
                                            $brand = $override->get('brand', 'id', $batch['brand_id'])[0]['name'];
                                            $generic_id = $override->get('generic', 'id', $_GET['gid'])[0]['id'];
                                            $brand_id = $override->get('brand', 'id', $batch['brand_id'])[0]['id'];
                                            $batch_id = $batch['id'];
                                            $batch_no = $batch['batch_no'];
                                        ?>
                                            <tr>
                                                <td><?= $batch['create_on'] ?></td>
                                                <td><?= $generic ?></td>
                                                <td><?= $brand ?></td>
                                                <td>
                                                    <?php if ($batch['status'] == 2) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal"><?= $batch_no ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"><?= $batch_no ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batch['quantity'] ?></td>
                                                <td><?= $batch['assigned'] ?></td>
                                                <td><?= $batch['balance'] ?></td>
                                                <td><?= $staff ?></td>
                                                <td><?= $batch['expire_date'] ?></td>
                                                <td><?= $batch['last_check'] ?></td>
                                                <td>
                                                    <?php if ($batch['expire_date'] <= date('Y-m-d')) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Quarantine</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 12) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device History Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Batch No</th>
                                            <th width="4%">Used</th>
                                            <th width="4%">Balance</th>
                                            <th width="10%">Expire Date</th>
                                            <th width="10%">Next Check</th>
                                            <th width="5%">Expiration</th>
                                            <th width="5%">Checking</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'generic_id', $_GET['gid'], 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        $amnt = 0;
                                        foreach ($override->getWithLimit1('batch', 'generic_id', $_GET['gid'], 'status', 1, $page, $numRec) as $batchDesc) {
                                            $generic_name = $override->get('generic', 'id', $_GET['gid'])[0]['name'];
                                            $brand_name = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['name'];
                                            $update_generic_id = $override->get('generic', 'id', $_GET['gid'])[0]['id'];
                                            $update_brand_id = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['id'];
                                            $update_batch_id = $batchDesc['id'];
                                            $update_batch_no = $batchDesc['batch_no'];
                                            $update_category_id = $batchDesc['category'];

                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal"><?= $update_batch_no ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"><?= $update_batch_no ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['assigned'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal"><?= $batchDesc['balance'] ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> <?= $batchDesc['balance'] ?> </a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['expire_date'] ?></td>
                                                <td><?= $batchDesc['next_check'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['expire_date'] <= $today) { ?>
                                                        <a href="#archive<?= $batchDesc['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Quarantine</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> OK! </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($batchDesc['next_check'] <= $today) { ?>
                                                        <a href="#" role="button" class="btn btn-warning">Not Checked</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-danger">OK!</a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a href="#update_stock_guide<?= $batchDesc['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Update Batch</a>
                                                </td>

                                            </tr>

                                            <div class="modal fade" id="update_stock_guide<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                                                                <input value="<?= $generic_name ?>" type="text" name="update_generic_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Brand Name</label>
                                                                                <input value="<?= $brand_name ?>" type="text" name="update_brand_name" style="width: 100%;" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Batch No:</label>
                                                                                <input value="<?= $update_batch_no ?>" type="text" name="update_batch_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Study Name:</label>
                                                                                <select name="study_id" id="study_id" style="width: 100%;" required>
                                                                                    <option value="">Select Study</option>
                                                                                    <?php
                                                                                    $batches = $override->get('study', 'status', 1) ?>
                                                                                    <?php foreach ($batches as $batch) { ?>
                                                                                        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
                                                                                    <?php }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Current Quantity::</label>
                                                                                <input value="<?= $batchDesc['balance'] ?>" type="number" name="quantity" id="quantity" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Quantity to Add:</label>
                                                                                <input value=" " class="validate[required]" type="number" name="added" id="added" style="width: 100%;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="hidden" name="update_generic_id" value="<?= $update_generic_id ?>" id="update_generic_id">
                                                                <input type="hidden" name="update_brand_id" value="<?= $update_brand_id ?>" id="update_brand_id">
                                                                <input type="hidden" name="update_batch_no" value="<?= $update_batch_no ?>" id="update_batch_no">
                                                                <input type="hidden" name="update_category_id" value="<?= $update_category_id ?>" id="update_category_id">
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

                    <?php } elseif ($_GET['id'] == 13) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device History Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Batch No</th>
                                            <th width="4%">Used</th>
                                            <th width="4%">Balance</th>
                                            <th width="4%">Buffer</th>
                                            <th width="8%">Expire Date</th>
                                            <th width="8%">Next Check</th>
                                            <th width="12%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'generic_id', $_GET['gid'], 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        $amnt = 0;
                                        foreach ($override->getWithLimit1('batch', 'generic_id', $_GET['gid'], 'status', 1, $page, $numRec) as $batchDesc) {
                                            $generic_name = $override->get('generic', 'id', $_GET['gid'])[0]['name'];
                                            $generic_buffer = $override->get('generic', 'id', $_GET['gid'])[0]['buffer'];
                                            $batch_buffer = $batchDesc['buffer'];
                                            $batch_balance = $batchDesc['balance'];
                                            $guide_quantinty = $override->get('generic_guide', 'id', $_GET['lbid'])[0]['balance'];
                                            $brand_name = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['name'];
                                            $update_generic_id2 = $override->get('generic', 'id', $_GET['gid'])[0]['id'];
                                            $update_brand_id2 = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['id'];
                                            $update_batch_id2 = $batchDesc['id'];
                                            $update_batch_no2 = $batchDesc['batch_no'];
                                            $update_category_id2 = $batchDesc['category'];
                                            $location_batch_id = $_GET['lid'];
                                            $location_guide_id = $_GET['lbid'];
                                            // $sumBtach = $override->getSumD2('allocate_guide_records', 'balance', 'batch_id', $batchDesc['id'],'location_id',$location_batch_id)[0]['SUM(balance)'];
                                            // foreach ($override->getNews('allocate_guide_records', 'guide_id', $location_guide_id, 'location_id', $location_batch_id) as $loc) {
                                            //     $sumBatchLoc = $loc['quantity'];
                                            // }

                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal"><?= $update_batch_no2 ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"><?= $update_batch_no2 ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['assigned'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal"><?= $sumBtachLoc ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> <?= $sumBtachLoc ?> </a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batch_buffer ?></td>
                                                <td><?= $batchDesc['expire_date'] ?></td>
                                                <td><?= $batchDesc['next_check'] ?></td>
                                                <td>
                                                    <a href="#alocate_batch<?= $batchDesc['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Alocate</a>
                                                    <a href="#dispense_batch_id<?= $batchDesc['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Remove</a>
                                                </td>

                                            </tr>

                                            <div class="modal fade" id="alocate_batch<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Alocate Stock Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Generic Name:</label>
                                                                                <input value="<?= $generic_name ?>" type="text" name="update_generic_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Brand Name</label>
                                                                                <input value="<?= $brand_name ?>" type="text" name="update_brand_name" style="width: 100%;" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Batch No:</label>
                                                                                <input value="<?= $update_batch_no2 ?>" type="text" name="update_batch_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Study Name:</label>
                                                                                <select name="study_id" id="study_id" style="width: 100%;" required>
                                                                                    <option value="">Select Study</option>
                                                                                    <?php
                                                                                    $batches = $override->get('study', 'status', 1) ?>
                                                                                    <?php foreach ($batches as $batch) { ?>
                                                                                        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
                                                                                    <?php }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Current Quantity::</label>
                                                                                <input value="<?= $batchDesc['balance'] ?>" type="number" name="quantity" id="quantity" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Buffer Amount:</label>
                                                                                <input value="<?= $batch_buffer ?>" type="text" name="update_generic_buffer" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Quantity to alocate:</label>
                                                                                <input value=" " class="validate[required]" type="number" name="allocate_amount" id="allocate_amount" style="width: 100%;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="hidden" name="update_generic_id" value="<?= $update_generic_id2 ?>" id="update_generic_id">
                                                                <input type="hidden" name="update_brand_id" value="<?= $update_brand_id2 ?>" id="update_brand_id">
                                                                <input type="hidden" name="update_batch_no" value="<?= $update_batch_no2 ?>" id="update_batch_no">
                                                                <input type="hidden" name="update_category_id" value="<?= $update_category_id2 ?>" id="update_category_id">
                                                                <input type="hidden" name="location_batch_id" value="<?= $location_batch_id ?>" id="location_batch_id">
                                                                <input type="hidden" name="location_guide_id" value="<?= $location_guide_id ?>" id="location_guide_id">
                                                                <input type="submit" name="alocate_batch" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="dispense_batch_id<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                                                                <input value="<?= $generic_name ?>" type="text" name="update_generic_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Brand Name</label>
                                                                                <input value="<?= $brand_name ?>" type="text" name="update_brand_name" style="width: 100%;" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Batch No:</label>
                                                                                <input value="<?= $update_batch_no2 ?>" type="text" name="update_batch_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Current Quantity::</label>
                                                                                <input value="<?= $batchDesc['balance'] ?>" type="number" name="quantity" id="quantity" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Location Quantity::</label>
                                                                                <input value="<?= $guide_quantinty ?>" type="number" name="location_quantity" id="location_quantity" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Quantity to Dispense:</label>
                                                                                <input value=" " class="validate[required]" type="number" name="removed" id="removed" style="width: 100%;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Study Name:</label>
                                                                                <select name="dispense_study_id" id="dispense_study_id" style="width: 100%;" required>
                                                                                    <option value="">Select Study</option>
                                                                                    <?php
                                                                                    $batches = $override->get('study', 'status', 1) ?>
                                                                                    <?php foreach ($batches as $batch) { ?>
                                                                                        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
                                                                                    <?php }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Staff:</label>
                                                                                <select name="staff" style="width: 100%;" id="staff" required>
                                                                                    <option value="">Select Staff</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-4">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Site:</label>
                                                                                <select name="site" style="width: 100%;" id="site" required>
                                                                                    <option value="">Select Site</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Remarks</div>
                                                                        <div class="col-md-9">
                                                                            <textarea name="remarks" rows="4"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="hidden" name="update_generic_id" value="<?= $update_generic_id2 ?>" id="update_generic_id">
                                                                <input type="hidden" name="update_brand_id" value="<?= $update_brand_id2 ?>" id="update_brand_id">
                                                                <input type="hidden" name="update_batch_no" value="<?= $update_batch_no2 ?>" id="update_batch_no">
                                                                <input type="hidden" name="update_category_id" value="<?= $update_category_id2 ?>" id="update_category_id">
                                                                <input type="hidden" name="location_batch_id" value="<?= $location_batch_id ?>" id="location_batch_id">
                                                                <input type="hidden" name="location_guide_id" value="<?= $location_guide_id ?>" id="location_guide_id">
                                                                <input type="submit" name="dispense_stock" value="Save updates" class="btn btn-warning">
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
                    <?php } elseif ($_GET['id'] == 14) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Medicine / Device Dispensing Description</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="15%">Generic</th>
                                            <th width="15%">Brand</th>
                                            <th width="10%">Batch No</th>
                                            <th width="4%">Used</th>
                                            <th width="4%">Balance</th>
                                            <th width="10%">Expire Date</th>
                                            <th width="10%">Next Check</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $amnt = 0;
                                        $pagNum = $override->getCount1('batch', 'generic_id', $_GET['gid'], 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        // print_r($pages);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        $amnt = 0;
                                        foreach ($override->getWithLimit1('batch', 'generic_id', $_GET['gid'], 'status', 1, $page, $numRec) as $batchDesc) {
                                            $generic_name = $override->get('generic', 'id', $_GET['gid'])[0]['name'];
                                            $brand_name = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['name'];
                                            $dispense_generic_id = $override->get('generic', 'id', $_GET['gid'])[0]['id'];
                                            $dispense_brand_id = $override->get('brand', 'id', $batchDesc['brand_id'])[0]['id'];
                                            $dispense_batch_id = $batchDesc['id'];
                                            $dispense_batch_no = $batchDesc['batch_no'];
                                            $dispense_category_id = $batchDesc['category'];
                                            $location_batch_id = $_GET['lid'];
                                            $location_guide_id = $_GET['lbid'];
                                            $dispense_last_check = $batchDesc['last_check'];
                                            $dispense_next_check = $batchDesc['next_check'];
                                            $dispense_expire_date = $batchDesc['expire_date'];

                                        ?>
                                            <tr>
                                                <td><?= $generic_name ?></td>
                                                <td><?= $brand_name ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-danger" data-toggle="modal"><?= $dispense_batch_no ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"><?= $dispense_batch_no ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['assigned'] ?></td>
                                                <td>
                                                    <?php if ($batchDesc['balance'] <= 0) { ?>
                                                        <a href="#" role="button" class="btn btn-warning" data-toggle="modal"><?= $batchDesc['balance'] ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-success" data-toggle="modal"> <?= $batchDesc['balance'] ?> </a>
                                                    <?php } ?>
                                                </td>
                                                <td><?= $batchDesc['expire_date'] ?></td>
                                                <td><?= $batchDesc['next_check'] ?></td>
                                                <td>
                                                    <a href="#edit_stock_guide_id<?= $batchDesc['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Add</a>
                                                    <a href="#edit_stock_guide_id<?= $batchDesc['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Remove</a>
                                                </td>

                                            </tr>

                                            <div class="modal fade" id="edit_stock_guide_id<?= $batchDesc['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                                                                <input value="<?= $generic_name ?>" type="text" name="dispense_generic_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Brand Name</label>
                                                                                <input value="<?= $brand_name ?>" type="text" name="dispense_brand_name" style="width: 100%;" disabled />

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Batch No:</label>
                                                                                <input value="<?= $dispense_batch_no ?>" type="text" name="dispense_batch_name" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Study Name:</label>
                                                                                <select name="study_id" id="study_id" style="width: 100%;" required>
                                                                                    <option value="">Select Study</option>
                                                                                    <?php
                                                                                    $batches = $override->get('study', 'status', 1) ?>
                                                                                    <?php foreach ($batches as $batch) { ?>
                                                                                        <option value="<?= $batch['id'] ?>"><?= $batch['name'] ?></option>
                                                                                    <?php }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Current Quantity::</label>
                                                                                <input value="<?= $batchDesc['balance'] ?>" type="number" name="quantity" id="quantity" style="width: 100%;" disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="row-form clearfix">
                                                                            <!-- select -->
                                                                            <div class="form-group">
                                                                                <label>Quantity to Dispense:</label>
                                                                                <input value=" " class="validate[required]" type="number" name="removed" id="removed" style="width: 100%;" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="dr"><span></span></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $batchDesc['id'] ?>">
                                                                <input type="hidden" name="dispense_generic_id" value="<?= $dispense_generic_id ?>" id="dispense_generic_id">
                                                                <input type="hidden" name="dispense_brand_id" value="<?= $dispense_brand_id ?>" id="dispense_brand_id">
                                                                <input type="hidden" name="dispense_batch_no" value="<?= $dispense_batch_no ?>" id="dispense_batch_no">
                                                                <input type="hidden" name="dispense_category_id" value="<?= $dispense_category_id ?>" id="dispense_category_id">
                                                                <input type="hidden" name="dispense_location_id" value="<?= $dispense_location_id ?>" id="dispense_location_id">
                                                                <input type="hidden" name="dispense_guide_id" value="<?= $dispense_guide_id ?>" id="dispense_guide_id">
                                                                <input type="hidden" name="dispense_last_check" value="<?= $dispense_last_check ?>" id="dispense_last_check">
                                                                <input type="hidden" name="dispense_next_check" value="<?= $dispense_next_check ?>" id="dispense_next_check">
                                                                <input type="hidden" name="dispense_expire_date" value="<?= $dispense_expire_date ?>" id="dispense_expire_date">
                                                                <input type="submit" name="update_stock_guide_records" value="Save updates" class="btn btn-warning">
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
                    <?php } ?>
                </div>
                <div class="pull-right">
                    <div class="btn-group">
                        <a href="data.php?id=<?= $_GET['id'] ?>&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                            echo $_GET['page'] - 1;
                                                                        } else {
                                                                            echo 1;
                                                                        } ?>" class="btn btn-default">
                            < </a>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <a href="data.php?id=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                        echo 'active';
                                                                                                                    } ?>"><?= $i ?></a>
                                <?php } ?>
                                <a href="data.php?page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                            echo $_GET['page'] + 1;
                                                        } else {
                                                            echo $i - 1;
                                                        } ?>" class="btn btn-default"> > </a>
                    </div>
                </div>

                <div class="dr"><span></span></div>
            </div>
        </div>
    </div>
</body>

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
        $('#wait_ds').hide();
        $('#region').change(function() {
            var getUid = $(this).val();
            $('#wait_ds').show();
            $.ajax({
                url: "process.php?content=region",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#ds_data').html(data);
                    $('#wait_ds').hide();
                }
            });

        });
        $('#wait_wd').hide();
        $('#ds_data').change(function() {
            $('#wait_wd').hide();
            var getUid = $(this).val();
            $.ajax({
                url: "process.php?content=district",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#wd_data').html(data);
                    $('#wait_wd').hide();
                }
            });

        });
        $('#download').change(function() {
            var getUid = $(this).val();
            $.ajax({
                url: "process.php?content=download",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {

                }
            });

        });


        $('#dispense_study_id').change(function() {
            var getUid = $(this).val();
            $('#fl_wait').show();
            $.ajax({
                url: "process.php?content=dispense_study_id",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#staff').html(data);
                    $('#fl_wait').hide();
                }
            });

        });

        $('#dispense_study_id').change(function() {
            var getUid = $(this).val();
            $('#fl_wait').show();
            $.ajax({
                url: "process.php?content=dispense_study_id2",
                method: "GET",
                data: {
                    getUid: getUid
                },
                success: function(data) {
                    $('#site').html(data);
                    $('#fl_wait').hide();
                }
            });

        });
    });
</script>

</html>