<div class="modal fade" id="edit_stock_guide_id<?= $bDiscription['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Update Stock Info<?php print_r($generic_id); ?></h4>
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
                <?= $generic_id ?>
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