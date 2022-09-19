<?php
if (Input::exists('post')) {
    if (Input::get('change_password')) {
        $validate = new validate();
        $validate = $validate->check($_POST, array(
            'new_password' => array(
                'required' => true,
                'min' => 6,
            ),
            'current_password' => array(
                'required' => true,
            ),
            'retype_password' => array(
                'required' => true,
                'matches' => 'new_password'
            )
        ));
        if ($validate->passed()) {
            $salt = $random->get_rand_alphanumeric(32);
            if (Hash::make(Input::get('current_password'), $user->data()->salt) !== $user->data()->password) {
                $errorMessage = 'Your current password is wrong';
            } else {
                try {
                    $user->updateRecord('user', array(
                        'password' => Hash::make(Input::get('new_password'), $salt),
                        'salt' => $salt,
                        'pswd' => 1
                    ), $user->data()->id);
                } catch (Exception $e) {
                    $e->getMessage();
                }
            }
            $successMessage = 'Password changed successfully';
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            $pageError = $validate->errors();
        }
    }
    elseif (Input::get('add_batch_desc')) {
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
            $descSum=0;$bSum=0;$dSum=0;
            $descSum = $override->getSumD1('batch_description','quantity', 'batch_id', Input::get('batch'));
            $bSum = $override->get('batch', 'id', Input::get('batch'))[0];
            $dSum = $descSum[0]['SUM(quantity)'] + Input::get('quantity');
            if($dSum <= $bSum['amount']){
                try {
                    $user->createRecord('batch_description', array(
                        'batch_id' => Input::get('batch'),
                        'name' => Input::get('name'),
                        'cat_id' => Input::get('category'),
                        'quantity' => Input::get('quantity'),
                        'notify_amount' => Input::get('notify_amount'),
                        'create_on' => date('Y-m-d'),
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                    ));
                    $user->updateRecord('batch', array('dsc_status' => 1),Input::get('batch'));
                    $successMessage = 'Batch Description Successful Added';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else{
                $errorMessage = 'Exceeded Batch Amount, Please cross check and try again';
            }
        } else {
            $pageError = $validate->errors();
        }
    }
}
?>
<div class="header">
    <!-- <div class="row justify-content-center"> -->
        <h4 style="font-weight: bold;color: #f8f8f8" href="#" class="text-center"> &nbsp;Pharmacy Inventory</h4>
    <!-- </div> -->
    <ul class="header_menu">
        <li class="list_icon"><a href="#">&nbsp;</a></li>
        <li class="settings_icon">
            <a href="#" class="link_themeSettings">&nbsp;</a>

            <div id="themeSettings" class="popup">
                <div class="head clearfix">
                    <div class="arrow"></div>
                    <span class="isw-settings"></span>
                    <span class="name">Theme settings</span>
                </div>
                <div class="body settings">
                    <div class="row">
                        <div class="col-md-3"><strong>Style:</strong></div>
                        <div class="col-md-9">
                            <a class="styleExample tip active" title="Default style" data-style="">&nbsp;</a>
                            <a class="styleExample silver tip" title="Silver style" data-style="silver">&nbsp;</a>
                            <a class="styleExample dark tip" title="Dark style" data-style="dark">&nbsp;</a>
                            <a class="styleExample marble tip" title="Marble style" data-style="marble">&nbsp;</a>
                            <a class="styleExample red tip" title="Red style" data-style="red">&nbsp;</a>
                            <a class="styleExample green tip" title="Green style" data-style="green">&nbsp;</a>
                            <a class="styleExample lime tip" title="Lime style" data-style="lime">&nbsp;</a>
                            <a class="styleExample purple tip" title="Purple style" data-style="purple">&nbsp;</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><strong>Background:</strong></div>
                        <div class="col-md-9">
                            <a class="bgExample tip active" title="Default" data-style="">&nbsp;</a>
                            <a class="bgExample bgCube tip" title="Cubes" data-style="cube">&nbsp;</a>
                            <a class="bgExample bghLine tip" title="Horizontal line" data-style="hline">&nbsp;</a>
                            <a class="bgExample bgvLine tip" title="Vertical line" data-style="vline">&nbsp;</a>
                            <a class="bgExample bgDots tip" title="Dots" data-style="dots">&nbsp;</a>
                            <a class="bgExample bgCrosshatch tip" title="Crosshatch" data-style="crosshatch">&nbsp;</a>
                            <a class="bgExample bgbCrosshatch tip" title="Big crosshatch" data-style="bcrosshatch">&nbsp;</a>
                            <a class="bgExample bgGrid tip" title="Grid" data-style="grid">&nbsp;</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><strong>Flat style:</strong></div>
                        <div class="col-md-9">
                            <a class="styleExample flat tip" title="Flat style" data-style="flat">&nbsp;</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><strong>Fixed layout:</strong></div>
                        <div class="col-md-9">
                            <input type="checkbox" name="settings_fixed" value="1" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><strong>Hide menu:</strong></div>
                        <div class="col-md-9">
                            <input type="checkbox" name="settings_menu" value="1" />
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button class="btn btn-default link_themeSettings" type="button">Close</button>
                </div>
            </div>

        </li>
    </ul>
</div>
<div class="modal" id="change_password_n" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post">
            <?php if (Input::get('change_password')) {
                if ($errorMessage) { ?>
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
            <?php }
            } ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="color: orangered;font-weight: bold">You have Login for the fist time please change your password</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="col-md-3">Current Password:</div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="password" name="current_password" id="pass1" />
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">New Password:</div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="password" name="new_password" id="pass2" />
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Re-type Password:</div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="password" name="retype_password" id="pass3" />
                                </div>
                            </div>
                        </div>
                        <div class="dr"><span></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" name="change_password" value="Update Password" class="btn btn-warning">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal" id="batch_desc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post">
            <?php if(Input::get('change_password')){if($errorMessage){?>
                <div class="alert alert-danger">
                    <h4>Error!</h4>
                    <?=$errorMessage?>
                </div>
            <?php }elseif($pageError){?>
                <div class="alert alert-danger">
                    <h4>Error!</h4>
                    <?php foreach($pageError as $error){echo $error.' , ';}?>
                </div>
            <?php }elseif($successMessage){?>
                <div class="alert alert-success">
                    <h4>Success!</h4>
                    <?=$successMessage?>
                </div>
            <?php }}?>
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="color: orangered;font-weight: bold">You have add a new Batch, please specify the description </h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="col-md-3">Batch</div>
                                <div class="col-md-9">
                                    <?php $batchs=$override->lastRow2('batch','dsc_status', 0, 'id')?>
                                    <select name="batch" style="width: 100%;" required>
                                        <option value="<?=$batchs[0]['id']?>"><?=$batchs[0]['name'].' '.$batchs[0]['batch_no']?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Name:</div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="text" name="name" id="name"/>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Category</div>
                                <div class="col-md-9">
                                    <select name="category" style="width: 100%;" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($override->getData('drug_cat') as $dCat){?>
                                            <option value="<?=$dCat['id']?>"><?=$dCat['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Quantity:</div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="number" name="quantity" id="name"/>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Notification Amount: </div>
                                <div class="col-md-9">
                                    <input value="" class="validate[required]" type="text" name="notify_amount" id="name" required/>
                                </div>
                            </div>
                        </div>
                        <div class="dr"><span></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" name="add_batch_desc" value="Submit" class="btn btn-info">
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    <?php if($override->lastRow2('batch','dsc_status', 0, 'id')){?>
    $(window).on('load',function(){
        $("#batch_desc").modal({
            backdrop: 'static',
            keyboard: false
        },'show');
    });
    <?php }?>
</script>