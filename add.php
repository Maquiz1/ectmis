<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();
$successMessage = null;
$pageError = null;
$errorMessage = null;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('add_user')) {
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
                'username' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'phone_number' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'email_address' => array(
                    'unique' => 'user'
                ),
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
                    $user->createRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'username' => Input::get('username'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'password' => Hash::make($password, $salt),
                        'salt' => $salt,
                        'create_on' => date('Y-m-d'),
                        'last_login' => '',
                        'status' => 1,
                        'power' => 0,
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'user_id' => $user->data()->id,
                        'count' => 0,
                        'pswd' => 0,
                    ));

                    $staff_id = $override->lastRow('user', 'id')[0];

                    foreach (Input::get('sites') as $site) {
                        $user->createRecord('staff_sites', array(
                            'staff_id' => $staff_id['id'],
                            'site_id' => $site,
                        ));
                    }

                    foreach (Input::get('study') as $site) {
                        $user->createRecord('staff_study', array(
                            'staff_id' => $staff_id['id'],
                            'study_id' => $site,
                            'status' => 1,
                            'create_on' => date('Y-m-d'),
                        ));
                    }

                    $successMessage = 'Account Created Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_user_study')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'username' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
            ));
            if ($validate->passed()) {
                try {
                    foreach (Input::get('study') as $site) {
                        $user->createRecord('staff_study', array(
                            'staff_id' => $staff_id['id'],
                            'study_id' => $site,
                            'status' => 1,
                            'create_on' => date('Y-m-d'),
                        ));
                    }

                    $successMessage = 'Account Created Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('position', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Position Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_study')) {
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
                    $user->createRecord('study', array(
                        'name' => Input::get('name'),
                        'pi_id' => Input::get('pi'),
                        'co_id' => Input::get('coordinator'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                        'details' => Input::get('details'),
                        'date_created' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id,
                    ));

                    $study_id = $override->lastRow('study', 'id')[0];

                    foreach (Input::get('sites') as $site) {
                        $user->createRecord('study_sites', array(
                            'study_id' => $study_id['id'],
                            'site_id' => $site,
                        ));
                    }

                    $successMessage = 'Study Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_client')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'file_id' => array(
                    'required' => true,
                    'unique' => 'clients',
                ),
                'study_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('clients', array(
                        'study_id' => Input::get('study_id'),
                        'file_id' => Input::get('file_id'),
                        'create_on' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ));

                    $successMessage = 'Client Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_batch')) {
            if (Input::get('complete_batch')) {
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    // 'use_case' => array(
                    //     'required' => true,
                    // ),
                    // 'use_group' => array(
                    //     'required' => true,
                    // ),
                    // 'product_name' => array(
                    //     'required' => true,
                    // ),
                    // 'batch_no' => array(
                    //     'required' => true,
                    // ),
                    // 'quantity' => array(
                    //     'required' => true,
                    // ),
                    // 'manufactured_date' => array(
                    //     'required' => true,
                    // ),
                    // 'expire_date' => array(
                    //     'required' => true,
                    // ),
                    // 'maintainance' => array(
                    //     'required' => true,
                    // )
                ));
                if ($validate->passed()) {
                    $sii = 0;
                    $q = 0;
                    foreach (Input::get('location') as $sid) {
                        $q = $q + Input::get('quantity')[$sii];
                        $sii++;
                    }
                    try {
                        $user->createRecord('batch_product', array(
                            'generic_id' => Input::get('generic_id'),
                            'brand_id' => Input::get('brand_id'),
                            'use_group' => Input::get('use_group'),
                            'use_case' => Input::get('use_case'),
                            'batch_no' => Input::get('batch_no'),
                            'study_id' => Input::get('study_id'),
                            'quantity' => Input::get('quantity'),
                            'notify_quantity' => Input::get('notify_quantity'),
                            'manufacturer' => Input::get('manufacturer'),
                            'manufactured_date' => Input::get('manufactured_date'),
                            'expire_date' => Input::get('expire_date'),
                            'details' => Input::get('details'),
                            'status' => 1,
                            'staff_id' => $user->data()->id,
                            'category_id' => Input::get('category'),
                            'create_on' => date('Y-m-d'),
                            'maintainance' => Input::get('maintainance'),
                        ));

                        $BatchLastRow = $override->lastRow('batch_product', 'id');

                        $user->createRecord('batch_records', array(
                            'product_id' => $BatchLastRow[0]['id'],
                            'batch_id' => Input::get('batch_no'),
                            'quantity' => Input::get('quantity'),
                            'assigned' => 0,
                            'added' => 0,
                            'balance' => Input::get('quantity'),
                            'create_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'status' => 1,
                            'study_id' => Input::get('study_id'),
                            'use_group' => Input::get('use_group'),
                            'use_case' => Input::get('use_case'),
                        ));

                        $si = 0;
                        foreach (Input::get('location') as $sid) {

                            $q = Input::get('amount')[$si];
                            $location = $override->get('location', 'id', $sid['id'])[0];
                            $product_id = $override->lastRow('batch_product', 'id')[0]['id'];
                            $use_group = $override->lastRow('use_group', 'id')[0]['id'];
                            $use_case = $override->lastRow('use_case', 'id')[0]['id'];
                            $user->createRecord('batch_guide_records', array(
                                'product_id' => $product_id,
                                'batch_id' => Input::get('batch_no'),
                                'quantity' => $q,
                                'assigned' => 0,
                                'added' => 0,
                                'balance' => $q,
                                'use_group' => $use_group,
                                'location_id' => $location['id'],
                                'create_on' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'use_case' => $use_case,
                                'study_id' => Input::get('study_id'),
                            ));
                            $si++;
                        }
                        $successMessage = 'Batch Added Successful';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                    // } else {
                    //     $errorMessage = 'Amount entered not correct amount, please re - check each location!';
                    // }
                } else {
                    $pageError = $validate->errors();
                }
            }
        } elseif (Input::get('add_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('sites', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Site Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_drug_cat')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('drug_cat', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Drug Category Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_use_case_location')) {
            $validate = $validate->check($_POST, array(
                'use_case_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $check_use_case_location = 0;
                // foreach (Input::get('location_id') as $sid) {
                // in_array("100", $marks);
                //     $check_use_case_location = $override->selectData1('use_case_location', 'use_case_id', Input::get('use_case_id'), 'location_id', $sid);

                // }
                if ($check_use_case_location) {
                    $errorMessage = 'Use Case Location ALready Exists';
                } else {
                    try {
                        $si = 0;
                        foreach (Input::get('location_id') as $sid) {
                            $user->createRecord('use_case_location', array(
                                'use_case_id' => Input::get('use_case_id'),
                                'location_id' => $sid,
                                'status' => 1,
                                'create_on' => date('Y-m-d'),
                            ));
                            $si++;
                        }
                        $successMessage = 'Use Case Location Successful Added';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('assign_stock')) {
            $validate = $validate->check($_POST, array(
                'dispense_study_id' => array(
                    'required' => true,
                ),
                'dispense_generic_id' => array(
                    'required' => true,
                ),
                'dispense_brand_id' => array(
                    'required' => true,
                ),
                'dispense_batch_id' => array(
                    'required' => true,
                ),
                'staff' => array(
                    'required' => true,
                ),
                'site' => array(
                    'required' => true,
                ),
                'quantity' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $checkBatch = $override->selectData1('batch', 'status', 1, 'id', Input::get('dispense_batch_id'))[0];
                    $batchAssigned = $checkBatch['assigned'] + Input::get('quantity');
                    $batchBalance = $checkBatch['balance'] -  Input::get('quantity');

                    $checkGeneric = $override->selectData1('generic', 'status', 1, 'id', Input::get('dispense_generic_id'))[0];
                    $genericAssigned = $checkGeneric['assigned'] + Input::get('quantity');
                    $genericBalance = $checkGeneric['balance'] - Input::get('quantity');


                    $checkGeneric = $override->get('generic', 'id', Input::get('update_generic_id'))[0];
                    $genericBalance = $checkGeneric['balance'] + Input::get('added');

                    if (Input::get('quantity') <= $checkBatch['balance']) {
                        if ($checkBatch) {
                            $user->updateRecord('batch', array(
                                'status' => 1,
                                'assigned' => $batchAssigned,
                                'balance' => $batchBalance
                            ), Input::get('dispense_batch_id'));

                            $user->updateRecord('generic', array(
                                'status' => 1,
                                'assigned' => $genericAssigned,
                                'balance' => $genericBalance
                            ), Input::get('dispense_generic_id'));

                            $user->createRecord('assigned_stock_rec', array(
                                'study_id' => Input::get('dispense_study_id'),
                                'generic_id' => Input::get('dispense_generic_id'),
                                'brand_id' => Input::get('dispense_brand_id'),
                                'batch_id' => Input::get('dispense_batch_id'),
                                'batch_no' => Input::get('dispense_batch_no'),
                                'staff_id' => Input::get('staff'),
                                'site_id' => Input::get('site'),
                                'quantity' => Input::get('quantity'),
                                'notes' => Input::get('notes'),
                                'status' => 1,
                                'admin_id' => $user->data()->id,
                            ));

                            $user->createRecord('batch_records', array(
                                'generic_id' => Input::get('dispense_generic_id'),
                                'brand_id' => Input::get('dispense_brand_id'),
                                'batch_id' => Input::get('dispense_batch_id'),
                                'batch_no' => Input::get('dispense_batch_no'),
                                'quantity' => 0,
                                'assigned' => Input::get('quantity'),
                                'balance' => $genericBalance,
                                'create_on' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'study_id' => Input::get('dispense_study_id'),
                                'last_check' => Input::get('dispense_last_check'),
                                'next_check' => Input::get('dispense_next_check'),
                                'category' => Input::get('dispense_category_id'),
                                'remarks' => Input::get('notes'),
                                'expire_date' => Input::get('dispense_expire_date'),
                            ));

                            $user->updateRecord('generic_guide', array(
                                'balance' => $guideBalance,
                            ), Input::get('location_guide_id'));

                            $user->createRecord('generic_guide_records', array(
                                'generic_id' => Input::get('dispense_generic_id'),
                                'brand_id' => Input::get('dispense_brand_id'),
                                'batch_id' => Input::get('dispense_batch_id'),
                                'batch_no' => Input::get('dispense_batch_no'),
                                'guide_id' => Input::get('location_guide_id'),
                                'quantity' => 0,
                                'notify_quantity' => 0,
                                'assigned' => Input::get('quantity'),
                                'balance' => $guideBalance,
                                'use_group' => $use_group,
                                'location_id' => Input::get('dispense_location_id'),
                                'create_on' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'use_case' => $use_case,
                            ));


                            $successMessage = 'Stock Assigned Successful';
                        } else {
                            $errorMessage = 'That  Batch is not Active';
                        }
                    } else {
                        $errorMessage = 'Insufficient Amount on Stock Batch';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_generic')) {
            if (Input::get('complete_batch')) {
                $validate = $validate->check($_POST, array(
                    'name' => array(
                        'required' => true,
                    ),
                    'notify_quantity' => array(
                        'required' => true,
                    ),
                    'use_case' => array(
                        'required' => true,
                    ),
                    'use_group' => array(
                        'required' => true,
                    ),
                    'maintainance' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    $sii = 0;
                    $q = 0;
                    foreach (Input::get('location') as $sid) {
                        $q = $q + Input::get('amount')[$sii];
                        $sii++;
                    }

                    $checkGeneric = $override->selectData1('generic', 'name', Input::get('name'), 'status', 1)[0];

                    if (!$checkGeneric) {
                        if (Input::get('notify_quantity') >= 0) {
                            if (Input::get('notify_quantity') == $q) {
                                try {

                                    $user->createRecord('generic', array(
                                        'name' => Input::get('name'),
                                        'status' => 1,
                                        'notify_quantity' => Input::get('notify_quantity'),
                                        'assigned' => 0,
                                        'balance' => 0,
                                        'create_on' => date('Y-m-d'),
                                        'use_group' => Input::get('use_group'),
                                        'use_case' => Input::get('use_case'),
                                        'maintainance' => Input::get('maintainance'),
                                        'staff_id' => $user->data()->id,
                                    ));

                                    $si = 0;
                                    foreach (Input::get('location') as $sid) {
                                        $q = Input::get('amount')[$si];
                                        $location = $override->get('location', 'id', $sid)[0];
                                        $generic_id = $override->lastRow('generic', 'id')[0]['id'];
                                        $use_group = $override->lastRow('generic', 'id')[0]['use_group'];
                                        $use_case = $override->lastRow('generic', 'id')[0]['use_case'];
                                        $user->createRecord('generic_location', array(
                                            'generic_id' => $generic_id,
                                            'notify_quantity' => $q,
                                            'location_id' => $location['id'],
                                            'create_on' => date('Y-m-d'),
                                            'staff_id' => $user->data()->id,
                                            'status' => 1,
                                        ));

                                        $user->createRecord('generic_records', array(
                                            'generic_id' => $generic_id,
                                            'notify_quantity' => $q,
                                            'location_id' => $location['id'],
                                            'create_on' => date('Y-m-d'),
                                            'staff_id' => $user->data()->id,
                                            'status' => 1,
                                            'use_case' => $use_case,
                                            'use_group' => $use_group,
                                            'maintainance' => Input::get('maintainance'),
                                        ));

                                        $si++;
                                    }
                                    $successMessage = 'Generic Name Added Successful';
                                } catch (Exception $e) {
                                    die($e->getMessage());
                                }
                            } else {
                                $errorMessage = 'Required Quantity Must Be equal to some of all required locations';
                            }
                        } else {
                            $errorMessage = 'Required Quantity Must Not Be equal to Negatve Number';
                        }
                    } else {
                        $errorMessage = 'Generic Name Already Registered';
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
        } elseif (Input::get('add_generic2')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'notify_quantity' => array(
                    'required' => true,
                ),
                'use_case' => array(
                    'required' => true,
                ),
                'use_group' => array(
                    'required' => true,
                ),
                'maintainance' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $sii = 0;
                $q = 0;
                foreach (Input::get('location') as $sid) {
                    $q = $q + Input::get('location_quantity')[$sii];
                    $sii++;
                }

                $checkGeneric = $override->selectData1('generic', 'name', Input::get('name'), 'status', 1)[0];

                if (!$checkGeneric) {
                    if (Input::get('notify_quantity') >= 0) {
                        if (Input::get('notify_quantity') == $q) {
                            try {

                                $user->createRecord('generic', array(
                                    'name' => Input::get('name'),
                                    'status' => 1,
                                    'notify_quantity' => Input::get('notify_quantity'),
                                    'assigned' => 0,
                                    'balance' => 0,
                                    'create_on' => date('Y-m-d'),
                                    'use_group' => Input::get('use_group'),
                                    'use_case' => Input::get('use_case'),
                                    'maintainance' => Input::get('maintainance'),
                                    'staff_id' => $user->data()->id,
                                ));

                                $si = 0;
                                foreach (Input::get('location') as $sid) {
                                    $q = Input::get('location_quantity')[$si];
                                    $location = $override->get('location', 'id', $sid)[0];
                                    $generic_id = $override->lastRow('generic', 'id')[0]['id'];
                                    $use_group = $override->lastRow('generic', 'id')[0]['use_group'];
                                    $use_case = $override->lastRow('generic', 'id')[0]['use_case'];
                                    $user->createRecord('generic_location', array(
                                        'generic_id' => $generic_id,
                                        'notify_quantity' => $q,
                                        'location_id' => $location['id'],
                                        'create_on' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id,
                                        'status' => 1,
                                    ));

                                    $user->createRecord('generic_records', array(
                                        'generic_id' => $generic_id,
                                        'notify_quantity' => $q,
                                        'location_id' => $location['id'],
                                        'create_on' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id,
                                        'status' => 1,
                                        'use_case' => $use_case,
                                        'use_group' => $use_group,
                                        'maintainance' => Input::get('maintainance'),
                                    ));

                                    $si++;
                                }
                                $successMessage = 'Generic Name Added Successful';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $errorMessage = 'Required Quantity Must Be equal to some of all required locations';
                        }
                    } else {
                        $errorMessage = 'Required Quantity Must Not Be equal to Negatve Number';
                    }
                } else {
                    $errorMessage = 'Generic Name Already Registered';
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_brand')) {
            $validate = $validate->check($_POST, array(
                'generic_id2' => array(
                    'required' => true,
                ),
                'brand_id2' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {

                try {
                    $checkBrand = $override->selectData1('brand', 'name', Input::get('brand_id2'), 'status', 1)[0];
                    if ($checkBrand) {
                        $errorMessage = 'Brand Name Already Registered';
                    } else {
                        $user->createRecord('brand', array(
                            'generic_id' => Input::get('generic_id2'),
                            'name' => Input::get('brand_id2'),
                            'status' => 1,
                            'quantity' => 0,
                            'notify_quantity' => 0,
                            'assigned' => 0,
                            'added' => 0,
                            'balance' => 0,
                            'create_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                        ));

                        $user->createRecord('brand_records', array(
                            'generic_id' => Input::get('generic_id2'),
                            'name' => Input::get('brand_id2'),
                            'status' => 1,
                            'quantity' => 0,
                            'notify_quantity' => 0,
                            'assigned' => 0,
                            'added' => 0,
                            'balance' => 0,
                            'create_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                        ));

                        $successMessage = 'Brand Name Added Successful';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('register_batch')) {
            if (Input::get('complete_batch')) {
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'generic_id3' => array(
                        'required' => true,
                    ),
                    'brand_id3' => array(
                        'required' => true,
                    ),
                    'batch_no' => array(
                        'required' => true,
                    ),
                    'quantity' => array(
                        'required' => true,
                    ),
                    'manufacturer' => array(
                        'required' => true,
                    ),
                    // 'manufactured_date' => array(
                    //     'required' => true,
                    // ),
                    'expire_date' => array(
                        'required' => true,
                    ),
                    'study_id' => array(
                        'required' => true,
                    ),
                    'category' => array(
                        'required' => true,
                    ),
                    'site_id' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    $sii = 0;
                    $q = 0;
                    foreach (Input::get('location') as $sid) {
                        $q = $q + Input::get('amount')[$sii];
                        $sii++;
                    }

                    if (Input::get('quantity') == $q) {
                        if (Input::get('manufactured_date') < date('Y-m-d')) {
                            if (Input::get('expire_date') > date('Y-m-d')) {
                                if (Input::get('quantity') > 0) {
                                    $checkBatch = $override->selectData1('batch', 'batch_no', Input::get('batch_no'), 'status', 1)[0];
                                    if ($checkBatch) {
                                        $errorMessage = 'Batch Number Already Registered';
                                    } else {
                                        try {
                                            $user->createRecord('batch', array(
                                                'generic_id' => Input::get('generic_id3'),
                                                'brand_id' => Input::get('brand_id3'),
                                                'batch_no' => Input::get('batch_no'),
                                                'study_id' => Input::get('study_id'),
                                                'assigned' => 0,
                                                'balance' => Input::get('quantity'),
                                                'manufacturer' => Input::get('manufacturer'),
                                                'manufactured_date' => Input::get('manufactured_date'),
                                                'expire_date' => Input::get('expire_date'),
                                                'details' => Input::get('details'),
                                                'status' => 1,
                                                'staff_id' => $user->data()->id,
                                                'create_on' => date('Y-m-d'),
                                                'category' => Input::get('category'),
                                                'last_check' => date('Y-m-d'),
                                                'next_check' => date('Y-m-d'),
                                                'site_id' => Input::get('site_id'),
                                            ));

                                            $genericBalance = $override->get('generic', 'id', Input::get('generic_id3'))[0];
                                            $newQty = $genericBalance['balance'] +  Input::get('quantity');
                                            $user->updateRecord('generic', array(
                                                'balance' => $newQty,
                                            ), Input::get('generic_id3'));

                                            $BatchLastRow = $override->lastRow('batch', 'id');

                                            $user->createRecord('batch_records', array(
                                                'generic_id' => Input::get('generic_id3'),
                                                'brand_id' => Input::get('brand_id3'),
                                                'batch_id' => $BatchLastRow[0]['id'],
                                                'batch_no' => Input::get('batch_no'),
                                                'quantity' => Input::get('quantity'),
                                                'assigned' => 0,
                                                'batch_balance' => Input::get('quantity'),
                                                'balance' => $newQty,
                                                'create_on' => date('Y-m-d'),
                                                'staff_id' => $user->data()->id,
                                                'status' => 1,
                                                'study_id' => Input::get('study_id'),
                                                'last_check' => date('Y-m-d'),
                                                'next_check' => date('Y-m-d'),
                                                'category' => Input::get('category'),
                                                'remarks' => '',
                                                'expire_date' => Input::get('expire_date'),
                                                'admin_id' => $user->data()->id,
                                                'site_id' => Input::get('site_id'),
                                            ));

                                            $si = 0;
                                            foreach (Input::get('location') as $sid) {
                                                $q = Input::get('amount')[$si];
                                                $location = $override->get('location', 'id', $sid)[0];
                                                $notify_quantity = $override->selectData1('generic_location', 'generic_id', Input::get('generic_id3'), 'location_id', $location['id'])[0];
                                                $checkAsigned = $override->selectData1('assigned_batch', 'batch_id', $BatchLastRow[0]['id'], 'study_id', Input::get('study_id'))[0];
                                                $checkAsignedBalance = $checkAsigned['balance'] + $q;

                                                if ($checkAllocate) {
                                                    $user->updateRecord('assigned_batch', array(
                                                        'balance' => $checkAsignedBalance,
                                                    ), $checkAsigned['id']);
                                                } else {
                                                    $user->createRecord('assigned_batch', array(
                                                        'study_id' => Input::get('study_id'),
                                                        'generic_id' => Input::get('generic_id3'),
                                                        'brand_id' => Input::get('brand_id3'),
                                                        'batch_id' => $BatchLastRow[0]['id'],
                                                        'location_id' => $location['id'],
                                                        'batch_no' => Input::get('batch_no'),
                                                        'staff_id' => $user->data()->id,
                                                        'notify_quantity' => $notify_quantity['notify_quantity'],
                                                        'used' => 0,
                                                        'balance' => $q,
                                                        'notes' => Input::get('details'),
                                                        'status' => 1,
                                                        'admin_id' => $user->data()->id,
                                                        'create_on' => date('Y-m-d'),
                                                        'admin_id' => $user->data()->id,
                                                        'site_id' => Input::get('site_id'),
                                                    ));
                                                }

                                                $user->createRecord('assigned_batch_records', array(
                                                    'generic_id' => Input::get('generic_id3'),
                                                    'brand_id' => Input::get('brand_id3'),
                                                    'batch_id' => $BatchLastRow[0]['id'],
                                                    'batch_no' => Input::get('batch_no'),
                                                    'quantity' => $q,
                                                    'assigned' => 0,
                                                    'batch_balance' => $q,
                                                    'balance' => $newQty,
                                                    'location_id' => $location['id'],
                                                    'create_on' => date('Y-m-d'),
                                                    'staff_id' => $user->data()->id,
                                                    'status' => 1,
                                                    'study_id' => Input::get('study_id'),
                                                    'last_check' => date('Y-m-d'),
                                                    'next_check' => date('Y-m-d'),
                                                    'category' => Input::get('category'),
                                                    'remarks' => '',
                                                    'expire_date' => Input::get('expire_date'),
                                                    'admin_id' => $user->data()->id,
                                                    'site_id' => Input::get('site_id'),
                                                ));

                                                $si++;
                                            }

                                            $successMessage = 'Batch Added Successful';
                                        } catch (Exception $e) {
                                            die($e->getMessage());
                                        }
                                    }
                                } else {
                                    $errorMessage = 'Quantity can not be 0';
                                }
                            } else {
                                $errorMessage = 'Expire date date Can not be of Past';
                            }
                        } else {
                            $errorMessage = 'Manufactured date Can not be of Future';
                        }
                    } else {
                        $errorMessage = 'Sum for Each Location Must be equal to Amount Received';
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
        } elseif (Input::get('register_batch2')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'generic_id3' => array(
                    'required' => true,
                ),
                'brand_id3' => array(
                    'required' => true,
                ),
                'batch_no' => array(
                    'required' => true,
                ),
                'quantity' => array(
                    'required' => true,
                ),
                'manufacturer' => array(
                    'required' => true,
                ),
                'manufactured_date' => array(
                    'required' => true,
                ),
                'expire_date' => array(
                    'required' => true,
                ),
                'study_id' => array(
                    'required' => true,
                ),
                'category' => array(
                    'required' => true,
                ),
                'site_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $sii = 0;
                $q = 0;
                foreach (Input::get('location') as $sid) {
                    $q = $q + Input::get('amount')[$sii];
                    $sii++;
                }

                if (Input::get('quantity') == $q) {
                    if (Input::get('manufactured_date') < date('Y-m-d')) {
                        if (Input::get('expire_date') > date('Y-m-d')) {
                            if (Input::get('quantity') > 0) {
                                $checkBatch = $override->selectData1('batch', 'batch_no', Input::get('batch_no'), 'status', 1)[0];
                                if ($checkBatch) {
                                    $errorMessage = 'Batch Number Already Registered';
                                } else {
                                    try {
                                        $user->createRecord('batch', array(
                                            'generic_id' => Input::get('generic_id3'),
                                            'brand_id' => Input::get('brand_id3'),
                                            'batch_no' => Input::get('batch_no'),
                                            'study_id' => Input::get('study_id'),
                                            'assigned' => 0,
                                            'balance' => Input::get('quantity'),
                                            'manufacturer' => Input::get('manufacturer'),
                                            'manufactured_date' => Input::get('manufactured_date'),
                                            'expire_date' => Input::get('expire_date'),
                                            'details' => Input::get('details'),
                                            'status' => 1,
                                            'staff_id' => $user->data()->id,
                                            'create_on' => date('Y-m-d'),
                                            'category' => Input::get('category'),
                                            'last_check' => date('Y-m-d'),
                                            'next_check' => date('Y-m-d'),
                                            'site_id' => Input::get('site_id'),
                                        ));

                                        $genericBalance = $override->get('generic', 'id', Input::get('generic_id3'))[0];
                                        $newQty = $genericBalance['balance'] +  Input::get('quantity');
                                        $user->updateRecord('generic', array(
                                            'balance' => $newQty,
                                        ), Input::get('generic_id3'));

                                        $BatchLastRow = $override->lastRow('batch', 'id');

                                        $user->createRecord('batch_records', array(
                                            'generic_id' => Input::get('generic_id3'),
                                            'brand_id' => Input::get('brand_id3'),
                                            'batch_id' => $BatchLastRow[0]['id'],
                                            'batch_no' => Input::get('batch_no'),
                                            'quantity' => Input::get('quantity'),
                                            'assigned' => 0,
                                            'batch_balance' => Input::get('quantity'),
                                            'balance' => $newQty,
                                            'create_on' => date('Y-m-d'),
                                            'staff_id' => $user->data()->id,
                                            'status' => 1,
                                            'study_id' => Input::get('study_id'),
                                            'last_check' => date('Y-m-d'),
                                            'next_check' => date('Y-m-d'),
                                            'category' => Input::get('category'),
                                            'remarks' => '',
                                            'expire_date' => Input::get('expire_date'),
                                            'admin_id' => $user->data()->id,
                                            'site_id' => Input::get('site_id'),
                                        ));

                                        $si = 0;
                                        foreach (Input::get('location') as $sid) {
                                            $q = Input::get('amount')[$si];
                                            $location = $override->get('location', 'id', $sid)[0];
                                            $notify_quantity = $override->selectData1('generic_location', 'generic_id', Input::get('generic_id3'), 'location_id', $location['id'])[0];
                                            $checkAsigned = $override->selectData1('assigned_batch', 'batch_id', $BatchLastRow[0]['id'], 'study_id', Input::get('study_id'))[0];
                                            $checkAsignedBalance = $checkAsigned['balance'] + $q;

                                            if ($checkAllocate) {
                                                $user->updateRecord('assigned_batch', array(
                                                    'balance' => $checkAsignedBalance,
                                                ), $checkAsigned['id']);
                                            } else {
                                                $user->createRecord('assigned_batch', array(
                                                    'study_id' => Input::get('study_id'),
                                                    'generic_id' => Input::get('generic_id3'),
                                                    'brand_id' => Input::get('brand_id3'),
                                                    'batch_id' => $BatchLastRow[0]['id'],
                                                    'location_id' => $location['id'],
                                                    'batch_no' => Input::get('batch_no'),
                                                    'staff_id' => $user->data()->id,
                                                    'notify_quantity' => $notify_quantity['notify_quantity'],
                                                    'used' => 0,
                                                    'balance' => $q,
                                                    'notes' => Input::get('details'),
                                                    'status' => 1,
                                                    'admin_id' => $user->data()->id,
                                                    'create_on' => date('Y-m-d'),
                                                    'admin_id' => $user->data()->id,
                                                    'site_id' => Input::get('site_id'),
                                                ));
                                            }

                                            $user->createRecord('assigned_batch_records', array(
                                                'generic_id' => Input::get('generic_id3'),
                                                'brand_id' => Input::get('brand_id3'),
                                                'batch_id' => $BatchLastRow[0]['id'],
                                                'batch_no' => Input::get('batch_no'),
                                                'quantity' => $q,
                                                'assigned' => 0,
                                                'batch_balance' => $q,
                                                'balance' => $newQty,
                                                'location_id' => $location['id'],
                                                'create_on' => date('Y-m-d'),
                                                'staff_id' => $user->data()->id,
                                                'status' => 1,
                                                'study_id' => Input::get('study_id'),
                                                'last_check' => date('Y-m-d'),
                                                'next_check' => date('Y-m-d'),
                                                'category' => Input::get('category'),
                                                'remarks' => '',
                                                'expire_date' => Input::get('expire_date'),
                                                'admin_id' => $user->data()->id,
                                                'site_id' => Input::get('site_id'),
                                            ));

                                            $si++;
                                        }

                                        $successMessage = 'Batch Added Successful';
                                    } catch (Exception $e) {
                                        die($e->getMessage());
                                    }
                                }
                            } else {
                                $errorMessage = 'Quantity can not be 0';
                            }
                        } else {
                            $errorMessage = 'Expire date date Can not be of Past';
                        }
                    } else {
                        $errorMessage = 'Manufactured date Can not be of Future';
                    }
                } else {
                    $errorMessage = 'Sum for Each Location Must be equal to Amount Received';
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
    <title> e-CTMIS </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Simple Admin</a> <span class="divider"></span></li>
                    <li class="active">Add Info</li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>
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
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add User</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">First Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="firstname" id="firstname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Last Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="lastname" id="lastname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Username:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="username" id="username" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-5">Select Study:</div>
                                        <div class="col-md-7">
                                            <select name="study[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                <option value="">choose a study...</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-5">Select sites:</div>
                                        <div class="col-md-7">
                                            <select name="sites[]" id="s2_1" style="width: 100%;" multiple="multiple" required>
                                                <option value="">choose a site...</option>
                                                <?php foreach ($override->getData('sites') as $site) { ?>
                                                    <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Position</div>
                                        <div class="col-md-9">
                                            <select name="position" style="width: 100%;" required>
                                                <option value="">Select position</option>
                                                <?php foreach ($override->getData('position') as $position) { ?>
                                                    <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">E-mail Address:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_user" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 2) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Position</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_position" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 3) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Study</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" required />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">PI</div>
                                        <div class="col-md-9">
                                            <select name="pi" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Coordinator</div>
                                        <div class="col-md-9">
                                            <select name="coordinator" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-5">Select sites:</div>
                                        <div class="col-md-7">
                                            <select name="sites[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                <option value="">choose a site...</option>
                                                <?php foreach ($override->getData('sites') as $site) { ?>
                                                    <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Start Date:</div>
                                        <div class="col-md-9"><input type="date" name="start_date" required /> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">End Date:</div>
                                        <div class="col-md-9"><input type="date" name="end_date" required /> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study details:</div>
                                        <div class="col-md-9"><textarea name="details" rows="4" required></textarea></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_study" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 4) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Stock Batch</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <?php if (!Input::get('location') && !Input::get('location_1')) { ?>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Generic Name::</label>
                                                        <select name="generic_id" style="width: 100%;" required>
                                                            <option value="">Select Generic Name</option>
                                                            <?php foreach ($override->getData('generic') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Brand Name:</label>
                                                        <select name="brand_id" style="width: 100%;" required>
                                                            <option value="">Select Brand Name</option>
                                                            <?php foreach ($override->getData('brand') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Use Group:</label>
                                                        <select name="use_group" style="width: 100%;" required>
                                                            <option value="">Select Use Group</option>
                                                            <?php foreach ($override->getData('use_group') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <!-- select -->
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Use Case:</label>
                                                        <select name="use_case" style="width: 100%;" required>
                                                            <option value="">Select Use Case</option>
                                                            <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>


                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Batch No:</label>
                                                        <input value="" class="validate[required]" type="text" name="batch_no" id="batch_no" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Maintainance Type:</label>
                                                        <select name="maintainance" style="width: 100%;" required>
                                                            <option value="">Select Type</option>
                                                            <?php foreach ($override->getData('maintainance_type') as $study) { ?>
                                                                <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Current Quantity:</label>
                                                        <input value="" class="validate[required]" type="text" name="quantity" id="quantity" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Re-Stock Level:</label>
                                                        <input value="" class="validate[required]" type="text" name="notify_quantity" id="notify_quantity" required />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Study:</label>
                                                        <select name="study_id" style="width: 100%;" required>
                                                            <option value="">Select Study</option>
                                                            <?php foreach ($override->getData('study') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Forms:</label>
                                                        <select name="category" style="width: 100%;" required>
                                                            <option value="">Select Form</option>
                                                            <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Item Location:</label>
                                                        <select name="location[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                            <option value="">Select Use Case Location...</option>
                                                            <?php foreach ($override->getData('location') as $drinks) {
                                                            ?>
                                                                <option value="<?= $drinks['id'] ?>"><?= $drinks['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Manufacturer:</label>
                                                        <div class="col-md-9"><input type="text" name="manufacturer" id="manufacturer" /></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">


                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Manufactured Date:</label>
                                                        <div class="col-md-9"><input type="date" name="manufactured_date" required /> <span>Example: 2012-01-01</span></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Valid / Check / Expire Date:</label>
                                                        <div class="col-md-9"><input type="date" name="expire_date" required /> <span>Example: 2012-01-0</span></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="row-form clearfix">
                                                    <div class="col-md-3">Details: </div>
                                                    <div class="col-md-9">
                                                        <textarea class="" name="details" id="details" rows="4"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="footer tar">
                                            <input type="submit" name="add_batch" value="Submit" class="btn btn-default">
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <?php if (Input::get('location')) { ?>
                                        <label> Complete Stock Guide:
                                        </label>
                                        <div class="col-md-2"><strong>Current Amount Is<?php echo ' '; ?><?= Input::get('quantity') ?> : </strong>

                                            <span>Notification Amount Is<?php echo ' '; ?><?= Input::get('notify_quantity') ?> </span>

                                        </div>
                                        <?php
                                        $f = 0;
                                        foreach (Input::get('location') as $lctn) {
                                            $location = $override->get('location', 'id', $lctn['id'])[0];
                                        ?>
                                            <div class="row-form clearfix">
                                                <div class="col-md-2"><strong><?= $location['name'] ?> : </strong></div>
                                                <input type="hidden" name="location[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="generic_id" value="<?= Input::get('generic_id') ?>">
                                                <input type="hidden" name="use_group" value="<?= Input::get('use_group') ?>">
                                                <input type="hidden" name="use_case" value="<?= Input::get('use_case') ?>">
                                                <input type="hidden" name="brand_id" value="<?= Input::get('brand_id') ?>">
                                                <input type="hidden" name="batch_no" value="<?= Input::get('batch_no') ?>">
                                                <input type="hidden" name="study_id" value="<?= Input::get('study_id') ?>">
                                                <input type="hidden" name="quantity" value="<?= Input::get('quantity') ?>">
                                                <input type="hidden" name="notify_quantity" value="<?= Input::get('notify_quantity') ?>">
                                                <input type="hidden" name="category" value="<?= Input::get('category') ?>">
                                                <input type="hidden" name="location_1[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="manufacturer" value="<?= Input::get('manufacturer') ?>">
                                                <input type="hidden" name="manufactured_date" value="<?= Input::get('manufactured_date') ?>">
                                                <input type="hidden" name="maintainance" value="<?= Input::get('maintainance') ?>">
                                                <input type="hidden" name="expire_date" value="<?= Input::get('expire_date') ?>">
                                                <input type="hidden" name="details" value="<?= Input::get('details') ?>">
                                                <div class="col-md-3"><input value="" class="validate[required]" type="number" name="amount[]" id="amount" /> <span></span></div>
                                            </div>
                                        <?php $f++;
                                        } ?>
                                        <div class="footer tar">
                                            <input type="hidden" name="complete_batch" value="1">
                                            <input type="hidden" name="total_cost" value="<?= $total ?>">
                                            <input type="submit" name="add_batch" value="Submit" class="btn btn-default">
                                        </div>
                                    <?php } ?>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 5) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Site</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_site" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 6) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Drug Category</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_drug_cat" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add BRAND Descriptions</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Group</div>
                                        <div class="col-md-9">
                                            <select name="category" style="width: 100%;" required>
                                                <option value="">Select Group</option>
                                                <?php foreach ($override->getData('use_group') as $dCat) { ?>
                                                    <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Use Case</div>
                                        <div class="col-md-9">
                                            <select name="category" style="width: 100%;" required>
                                                <option value="">Select Use Case</option>
                                                <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                    <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Batch</div>
                                        <div class="col-md-9">
                                            <select name="batch" style="width: 100%;" required>
                                                <option value="">Select Batch</option>
                                                <?php foreach ($override->get('batch', 'status', 1) as $batch) { ?>
                                                    <option value="<?= $batch['id'] ?>"><?= $batch['name'] . ' ' . $batch['batch_no'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Category</div>
                                        <div class="col-md-9">
                                            <select name="category" style="width: 100%;" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                    <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Quantity:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="number" name="quantity" id="name" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Notification Amount: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="notify_amount" id="name" required />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_batch_desc" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Dispense Medicine/Equipment</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Study</label>
                                                    <select name="dispense_study_id" style="width: 100%;" id="dispense_study_id" required>
                                                        <option value="">Select Study</option>
                                                        <?php foreach ($override->get('study', 'status', 1) as $study) { ?>
                                                            <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Generic:</label>
                                                    <select name="dispense_generic_id" style="width: 100%;" id="dispense_generic_id" required>
                                                        <option value="">Select Generic</option>
                                                        <?php foreach ($override->get('generic', 'status', 1) as $study) { ?>
                                                            <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Brand:</label>
                                                    <select name="dispense_brand_id" style="width: 100%;" id="dispense_brand_id" required>
                                                        <option value="">Select Brand</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Quantity:</label>
                                                    <input value="" class="validate[required]" type="number" name="quantity" id="quantity" />
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Batch:</label>
                                                    <select name="dispense_batch_id" style="width: 100%;" id="dispense_batch_id" required>
                                                        <option value="">Select batch</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
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

                                        <div class="col-sm-3">
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

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Location:</label>
                                                    <select name="dispense_location_id" style="width: 100%;" id="dispense_location_id" required>
                                                        <option value="">Select Location</option>
                                                        <?php foreach ($override->getData('location') as $study) { ?>
                                                            <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Notes</div>
                                        <div class="col-md-9">
                                            <textarea name="notes" rows="4"></textarea>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="dispense_last_check" value="" id="dispense_last_check">
                                        <input type="hidden" name="dispense_next_check" value="" id="dispense_next_check">
                                        <input type="hidden" name="dispense_expire_date" value="" id="dispense_expire_date">
                                        <input type="hidden" name="dispense_category_id" value="" id="dispense_category_id">
                                        <input type="hidden" name="dispense_batch_no" value="" id="dispense_batch_no">
                                        <input type="submit" name="assign_stock" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Generic Name</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <?php if (!Input::get('location') && !Input::get('location_1')) { ?>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Generic Name:</label>
                                                        <input value="" class="validate[required]" type="text" name="name" id="name" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Required Quantity:</label>
                                                        <input value="" class="validate[required]" type="number" name="notify_quantity" id="notify_quantity" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Use Form (Group):</label>
                                                        <select name="use_group" style="width: 100%;" required>
                                                            <option value="">Select Use Group</option>
                                                            <?php foreach ($override->getData('use_group') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <!-- select -->
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Use Case:</label>
                                                        <select name="use_case" style="width: 100%;" required>
                                                            <option value="">Select Use Case</option>
                                                            <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>maintainance Type:</label>
                                                        <select name="maintainance" style="width: 100%;" required>
                                                            <option value="">Select Brand Name</option>
                                                            <?php foreach ($override->getData('maintainance_type') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Item Location(Select Only Required Locations Fot This Generic Name According to the Guide):</label>
                                                        <select name="location[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                            <option value="">Select Use Case Location...</option>
                                                            <?php foreach ($override->getData('location') as $drinks) {
                                                            ?>
                                                                <option value="<?= $drinks['id'] ?>"><?= $drinks['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="submit" name="add_generic" value="Submit" class="btn btn-default">
                                        </div>

                                    <?php
                                    }
                                    ?>

                                    <?php if (Input::get('location')) { ?>
                                        <div>
                                            <label> Complete Stock Guide:</label>
                                        </div>

                                        <div class="col-md-2">

                                            <span>Required Amount Is<?php echo ' '; ?><?= Input::get('notify_quantity') ?> </span>

                                        </div>
                                        <?php
                                        $f = 0;
                                        foreach (Input::get('location') as $lctn) {
                                            $location = $override->get('location', 'id', $lctn)[0];
                                        ?>
                                            <div class="row-form clearfix">
                                                <div class="col-md-2"><strong><?= $location['name'] ?> : </strong></div>
                                                <input type="hidden" name="location[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="name" value="<?= Input::get('name') ?>">
                                                <input type="hidden" name="use_group" value="<?= Input::get('use_group') ?>">
                                                <input type="hidden" name="use_case" value="<?= Input::get('use_case') ?>">
                                                <input type="hidden" name="notify_quantity" value="<?= Input::get('notify_quantity') ?>">
                                                <input type="hidden" name="location_1[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="maintainance" value="<?= Input::get('maintainance') ?>">
                                                <div class="col-md-3"><input value="" class="validate[required]" type="number" name="amount[]" id="amount" /> <span></span></div>
                                            </div>
                                        <?php $f++;
                                        } ?>
                                        <div class="footer tar">
                                            <input type="hidden" name="complete_batch" value="1">
                                            <input type="submit" name="add_generic" value="Submit" class="btn btn-default">
                                        </div>
                                    <?php } ?>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Brand</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Generic Name</label>
                                                    <select name="generic_id2" id="generic_id2" style="width: 100%;" required>
                                                        <option value="">Select Generic</option>
                                                        <?php foreach ($override->getData('generic') as $cat) { ?>
                                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>BRAND NAME:</label>
                                                    <input value="" type="text" name="brand_id2" id="brand_id2" />
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_brand" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 11) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Batch Details</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <?php if (!Input::get('location') && !Input::get('location_1')) { ?>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Generic Name::</label>
                                                        <select name="generic_id3" id="generic_id3" style="width: 100%;" required>
                                                            <option value="">Select Generic Name</option>
                                                            <?php foreach ($override->getData('generic') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Brand Name</label>
                                                        <select name="brand_id3" id="brand_id3" style="width: 100%;" required>
                                                            <option value="">Select brand</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Batch No:</label>
                                                        <input value="" class="validate[required]" type="text" name="batch_no" id="batch_no" required />
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Received Quantity:</label>
                                                        <input value="" class="validate[required]" type="text" name="quantity" id="quantity" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Category:</label>
                                                        <select name="category" style="width: 100%;" required>
                                                            <option value="">Select category</option>
                                                            <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Item Location(Please Select All locations):</label>
                                                        <select name="location[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                            <option value="">Select Use Case Location...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Study:</label>
                                                        <select name="study_id" id="add_study_id" style="width: 100%;" required>
                                                            <option value="">Select Study</option>
                                                            <?php foreach ($override->getData('study') as $dCat) { ?>
                                                                <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Site Name:</label>
                                                        <select name="site_id" id="add_site_id" style="width: 100%;" required>
                                                            <option value="">Select Site</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Manufacturer:</label>
                                                        <input value="" class="validate[required]" type="text" name="manufacturer" id="manufacturer" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Manufactured Date:</label>
                                                        <div class="col-md-9"><input type="date" name="manufactured_date" required /> </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="row-form clearfix">
                                                    <div class="form-group">
                                                        <label>Validity / Expire Date:</label>
                                                        <div class="col-md-9"><input type="date" name="expire_date" required /> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-sm-8">
                                                <div class="row-form clearfix">
                                                    <div class="col-md-3">Details: </div>
                                                    <div class="col-md-9">
                                                        <textarea class="" name="details" id="details" rows="4"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="submit" name="register_batch" value="Submit" class="btn btn-default">
                                        </div>

                                    <?php
                                    }
                                    ?>

                                    <?php if (Input::get('location')) { ?>
                                        <div>
                                            <label> Complete Batch:</label>
                                        </div>

                                        <div class="col-md-2">

                                            <span>Required Amount Is<?php echo ' '; ?><?= Input::get('quantity') ?> </span>

                                        </div>
                                        <?php
                                        $f = 0;
                                        foreach (Input::get('location') as $lctn) {
                                            $location = $override->get('location', 'id', $lctn)[0];
                                        ?>
                                            <div class="row-form clearfix">
                                                <div class="col-md-2"><strong><?= $location['name'] ?> : </strong></div>
                                                <input type="hidden" name="location[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="generic_id3" value="<?= Input::get('generic_id3') ?>">
                                                <input type="hidden" name="brand_id3" value="<?= Input::get('brand_id3') ?>">
                                                <input type="hidden" name="batch_no" value="<?= Input::get('batch_no') ?>">
                                                <input type="hidden" name="notify_quantity" id="notify_quantity" value="">
                                                <input type="hidden" name="location_1[<?= $f ?>]" value="<?= $lctn ?>">
                                                <input type="hidden" name="quantity" value="<?= Input::get('quantity') ?>">
                                                <input type="hidden" name="category" value="<?= Input::get('category') ?>">
                                                <input type="hidden" name="study_id" value="<?= Input::get('study_id') ?>">
                                                <input type="hidden" name="manufacturer" value="<?= Input::get('manufacturer') ?>">
                                                <input type="hidden" name="manufactured_date" value="<?= Input::get('manufactured_date') ?>">
                                                <input type="hidden" name="expire_date" value="<?= Input::get('expire_date') ?>">
                                                <input type="hidden" name="details" value="<?= Input::get('details') ?>">
                                                <input type="hidden" name="site_id" value="<?= Input::get('site_id') ?>">
                                                <div class="col-md-3"><input value="" class="validate[required]" type="number" name="amount[]" id="amount" /> <span></span></div>
                                            </div>
                                        <?php $f++;
                                        } ?>
                                        <div class="footer tar">
                                            <input type="hidden" name="complete_batch" value="1">
                                            <input type="submit" name="register_batch" value="Submit" class="btn btn-default">
                                        </div>
                                    <?php } ?>

                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 12) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add User to Study</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">First Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="firstname" id="firstname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Last Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="lastname" id="lastname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Username:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="username" id="username" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-5">Select Study:</div>
                                        <div class="col-md-7">
                                            <select name="study[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                <option value="">choose a study...</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-5">Select sites:</div>
                                        <div class="col-md-7">
                                            <select name="sites[]" id="s2_1" style="width: 100%;" multiple="multiple" required>
                                                <option value="">choose a site...</option>
                                                <?php foreach ($override->getData('sites') as $site) { ?>
                                                    <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Position</div>
                                        <div class="col-md-9">
                                            <select name="position" style="width: 100%;" required>
                                                <option value="">Select position</option>
                                                <?php foreach ($override->getData('position') as $position) { ?>
                                                    <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">E-mail Address:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_user" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 13) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Batch Details</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Generic Name::</label>
                                                    <select name="generic_id3" id="generic_id3" style="width: 100%;" required>
                                                        <option value="">Select Generic Name</option>
                                                        <?php foreach ($override->getData('generic') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Brand Name</label>
                                                    <select name="brand_id3" id="brand_id3" style="width: 100%;" required>
                                                        <option value="">Select brand</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Batch No:</label>
                                                    <input value="" class="validate[required]" type="text" name="batch_no" id="batch_no" required />
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Received Quantity:</label>
                                                    <input value="" class="validate[required]" type="text" name="quantity" id="quantity" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Category:</label>
                                                    <select name="category" style="width: 100%;" required>
                                                        <option value="">Select category</option>
                                                        <?php foreach ($override->getData('drug_cat') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Study:</label>
                                                    <select name="study_id" id="add_study_id" style="width: 100%;" required>
                                                        <option value="">Select Study</option>
                                                        <?php foreach ($override->getData('study') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Site Name:</label>
                                                    <select name="site_id" id="add_site_id" style="width: 100%;" required>
                                                        <option value="">Select Site</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Divide Quantity to Locations(Put 0 if You don not want to put anything in a Location) </label>
                                        <span id="span_product_details">
                                        </span>
                                        <hr />
                                    </div>

                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Manufacturer:</label>
                                                    <input value="" class="validate[required]" type="text" name="manufacturer" id="manufacturer" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Manufactured Date:</label>
                                                    <div class="col-md-9"><input type="date" name="manufactured_date" required /> </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Validity / Expire Date:</label>
                                                    <div class="col-md-9"><input type="date" name="expire_date" required /> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-8">
                                            <div class="row-form clearfix">
                                                <div class="col-md-3">Details: </div>
                                                <div class="col-md-9">
                                                    <textarea class="" name="details" id="details" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer tar">
                                        <input type="submit" name="register_batch2" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 14) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Generic Name</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Generic Name:</label>
                                                    <input value="" class="validate[required]" type="text" name="name" id="name" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Required Quantity:</label>
                                                    <input value="" class="validate[required]" type="number" name="notify_quantity" id="notify_quantity" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Use Form (Group):</label>
                                                    <select name="use_group" style="width: 100%;" required>
                                                        <option value="">Select Use Group</option>
                                                        <?php foreach ($override->getData('use_group') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- select -->
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Use Case:</label>
                                                    <select name="use_case" id="use_case_id2" style="width: 100%;" required>
                                                        <option value="">Select Use Case</option>
                                                        <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>maintainance Type:</label>
                                                    <select name="maintainance" style="width: 100%;" required>
                                                        <option value="">Select Maintainance</option>
                                                        <?php foreach ($override->getData('maintainance_type') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Item Location(Select Only Required Locations Fot This Generic Name According to the Guide):</label>
                                        <span id="span_product_details2">
                                        </span>
                                        <hr />
                                    </div>


                                    <div class="footer tar">
                                        <input type="submit" name="add_generic2" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 15) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Use Case</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <!-- select -->
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Use Case:</label>
                                                    <select name="use_case_id" id="use_case_id" style="width: 100%;" required>
                                                        <option value="">Select Use Case</option>
                                                        <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Item Location(Select Only Required Locations Fot This Generic Name According to the Guide):</label>
                                            <span id="span_product_details2">
                                            </span>
                                            <hr />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_use_case_location" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 16) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Use Case Location</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <!-- select -->
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Use Case:</label>
                                                    <select name="use_case_id" id="use_case_id" style="width: 100%;" required>
                                                        <option value="">Select Use Case</option>
                                                        <?php foreach ($override->getData('use_case') as $dCat) { ?>
                                                            <option value="<?= $dCat['id'] ?>"><?= $dCat['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Item Location(Select Only Required Locations According to the Guide):</label>
                                                    <select name="location_id[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                        <option value="">Select Use Case Location...</option>
                                                        <?php foreach ($override->getData('location') as $drinks) {
                                                        ?>
                                                            <option value="<?= $drinks['id'] ?>"><?= $drinks['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_use_case_location" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>

                        </div>
                    <?php } ?>
                    <div class="dr"><span></span></div>
                </div>

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
            $('#fl_wait').hide();
            $('#wait_ds').hide();
            $('#ld_staff').hide();
            $('#ld_batch').hide();
            $('#ld_stf').hide();
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

            $('#generic_id3').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=generic_id3",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#brand_id3').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#generic_id3').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=generic_id4",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#s2_2').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('#generic_id3').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=generic_id5",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#notify_quantity').val(data.notify_quantity);
                        $('#fl_wait').hide();
                    }
                });

            });



            $('#generic_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=gen2",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#maintainance').val(data.maintainance);
                        $('#use_group').val(data.use_group);
                        $('#use_case').val(data.use_case);
                        $('#gen_id').val(data.gen_id);
                        $('#gen_name').val(data.gen_name);
                        $('#fl_wait').hide();
                    }
                });

            });


            $('#dispense_generic_id').change(function() {
                var getUid = $(this).val();
                $('#ld_batch').show();
                $.ajax({
                    url: "process.php?content=dispense_generic_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#dispense_brand_id').html(data);
                        $('#ld_batch').hide();
                    }
                });

            });

            $('#dispense_brand_id').change(function() {
                var getUid = $(this).val();
                $('#ld_staff').show();
                $.ajax({
                    url: "process.php?content=dispense_brand_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#dispense_batch_id').html(data);
                        $('#ld_staff').hide();
                    }
                });
            });

            $('#dispense_batch_id').change(function() {
                var getUid = $(this).val();
                $('#ld_staff').show();
                $.ajax({
                    url: "process.php?content=dispense_batch_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#dispense_batch_no').val(data.batch_no);
                        $('#dispense_last_check').val(data.last_check);
                        $('#dispense_next_check').val(data.next_check);
                        $('#dispense_expire_date').val(data.expire_date);
                        $('#dispense_category_id').val(data.category);
                        $('#fl_wait').hide();
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

            $('#add_study_id').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=dispense_study_id2",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        console.log(data);
                        $('#add_site_id').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

            $('.dispense2').click(function() {
                var getUid = $(this).attr('data-dispense');
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=dispense_study_id3",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        console.log(data);
                        $('#span_product_details').html(data);
                        $('#fl_wait').hide();
                    }
                });
                // $('#orderModal').modal('show');
                // $('#order_form')[0].reset();
                // $('.modal-title').html("<i class='fa fa-plus'></i> Create Order");
                // $('#action').val('Add');
                // $('#btn_action').val('Add');
                // $('#span_product_details').html('');
                // add_product_row();


            });

            $('#generic_id3').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=dispense_study_id3",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        console.log(data);
                        $('#span_product_details').html(data);
                        $('#fl_wait').hide();
                    }
                });
            });

            $('#use_case_id2').change(function() {
                var getUid = $(this).val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?content=use_case_id",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        console.log(data);
                        $('#span_product_details2').html(data);
                        $('#fl_wait').hide();
                    }
                });
            });
        });
    </script>
</body>

</html>