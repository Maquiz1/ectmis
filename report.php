<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 50;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('search')) {
            $validate = $validate->check($_POST, array(
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
                'report' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    switch (Input::get('report')) {
                        case 1:
                            $data = $override->searchBtnDate3('batch_records', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'category', Input::get('group'));
                            break;
                        case 2:
                            $data = $override->searchBtnDate3('check_records', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'), 'use_group', Input::get('group'));
                            break;
                        case 3:
                            $data = $override->searchBtnDate2('batch', 'create_on', Input::get('start_date'), 'create_on', Input::get('end_date'));
                            break;
                    }
                    $successMessage = 'Report Successful Created';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }

        if (Input::get('report') == 11) {
            $data = null;
            $filename = null;
            if (Input::get('full_report')) {
                $data = $override->getFull('batch_product', 'quantity', 'notify_quantity', 'status', 1);
                $filename = 'Full Report' . '-' . date('Y-m-d');
            } elseif (Input::get('sufficient')) {
                $data = $override->get4('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 1);
                $filename = 'SUFFICIENT' . '-' . date('Y-m-d');
            } elseif (Input::get('running_low')) {
                $data = $override->get5('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 1);
                $filename = 'RUNNING LOW' . '-' . date('Y-m-d');
            } elseif (Input::get('out_stock')) {
                $data = $override->get6('batch_product', 'quantity', 0, 'status', 1, 'type', 1);
                $filename = 'Out of Stock' . '-' . date('Y-m-d');
            } elseif (Input::get('expired')) {
                $data = $override->get7('batch_product', 'expire_date', date('Y-m-d'), 'status', 1, 'use_group', 1);
                $filename = 'Expired' . '-' . date('Y-m-d');
            } elseif (Input::get('not_checked')) {
                $data = $override->get4('batch_product', 'quantity', 'notify_quantity', 'status', 1, 'use_group', 2);
                $filename = 'NOT CHECKED' . '-' . date('Y-m-d');
            }
            $user->exportData($data, $filename);
        }
    }
} else {
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Report - e-ctmis </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Report</a> <span class="divider">></span></li>
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
                        <div class="col-md-offset-1 col-md-10">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Search Report</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation3" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-1">Start Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="start_date" id="start_date3" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">End Date:</div>
                                        <div class="col-md-2">
                                            <input value="" class="validate[required,custom[date]]" type="date" name="end_date" id="end_date3" /><span>Example: 2010-12-01</span>
                                        </div>
                                        <div class="col-md-1">Type</div>
                                        <div class="col-md-2">
                                            <select name="report" style="width: 100%;" required>
                                                <option value="">Select Report</option>
                                                <!-- <option value="1">Stock Report(Quantity)</option>
                                                <option value="2">Check Report</option> -->
                                                <option value="3">Current Status</option>
                                            </select>
                                        </div>
                                        <!-- <div class="col-md-1">Group</div>
                                        <div class="col-md-2">
                                            <select name="group" style="width: 100%;" required>
                                                <option value="">Select Group</option>
                                                <option value="1">Medicine</option>
                                                <option value="2">Medical Equipment</option>
                                                <option value="3">Accesssories</option>
                                                <option value="4">Supplies</option>
                                            </select>
                                        </div> -->
                                        <div class="col-md-2">
                                            <input type="submit" id="submit3" name="search" value="Search Report" class="btn btn-info">
                                        </div>
                                    </div>

                                    <div class="footer tar">

                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Report</h1>
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
                                <?php if ($user->data()->power == 1) {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch_records');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getNo('batch_records');
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    // $data = $override->getDataWithLimit('batch', $page, $numRec);
                                } ?>
                                <?php if ($_POST && Input::get('report') == 1) { ?>
                                    <table id="stock" cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">GENERIC</th>
                                                <th width="10%">BRAND</th>
                                                <th width="10%">BATCH</th>
                                                <th width="10%">RECEIVED</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">STAFF</th>
                                                <!-- <th width="10%"></th> -->
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $received = $records['quantity'];
                                                $used = $records['assigned'];
                                                $balance = $records['balance'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $generic = $override->get('generic', 'id', $records['generic_id'])[0]['name'];
                                                $brand = $override->get('brand', 'id', $records['brand_id'])[0]['name'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $generic ?></td>
                                                    <td><?= $brand ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $received ?></td>
                                                    <td><?= $used ?></td>
                                                    <td><?= $balance ?></td>
                                                    <td><?= $username ?></td>
                                                    <!-- <td>
                                                        <a href="data.php?id=10&report_id=<?= $records['id'] ?>" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td> -->
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 2) { ?>
                                    <table id="check" cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="15%">Date</th>
                                                <th width="15%">Generic</th>
                                                <th width="15%">Brand</th>
                                                <th width="15%">Batch No</th>
                                                <th width="5%">Last Check</th>
                                                <th width="5%">Staff</th>
                                                <th width="5%">Next Check</th>
                                                <th width="5%">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $received = $records['quantity'];
                                                $used = $records['assigned'];
                                                $balance = $records['balance'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $generic = $override->get('generic', 'id', $records['generic_id'])[0]['name'];
                                                $brand = $override->get('brand', 'id', $records['brand_id'])[0]['name'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $generic ?></td>
                                                    <td><?= $brand ?></td>
                                                    <td><?= $records['batch_no'] ?></td>
                                                    <td><?= $records['last_check'] ?></td>
                                                    <td><?= $username ?></td>
                                                    <td><?= $records['next_check'] ?></td>
                                                    <!-- <td>
                                                        <a href="data.php?id=10&report_id=<?= $records['id'] ?>" role="button" class="btn btn-info btn-sm">View Report</a>
                                                    </td> -->
                                                    <td><?= $records['details'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } elseif ($_POST && Input::get('report') == 3) { ?>
                                    <table id="status" cellpadding="0" cellspacing="0" width="100%" class="table">
                                        <thead>
                                            <tr>
                                                <th width="10%">DATE</th>
                                                <th width="10%">GENERIC</th>
                                                <th width="10%">BRAND</th>
                                                <th width="10%">USED</th>
                                                <th width="10%">BALANCE</th>
                                                <th width="10%">EXPIRE</th>
                                                <th width="10%">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $records) {
                                                $received = $records['quantity'];
                                                $used = $records['assigned'];
                                                $balance = $records['balance'];
                                                $expire = $records['status'];
                                                $username = $override->get('user', 'id', $records['staff_id'])[0]['username'];
                                                $generic = $override->get('generic', 'id', $records['generic_id'])[0]['name'];
                                                $brand = $override->get('brand', 'id', $records['brand_id'])[0]['name'];
                                            ?>
                                                <tr>
                                                    <td><?= $records['create_on'] ?></td>
                                                    <td><?= $generic ?></td>
                                                    <td><?= $brand ?></td>
                                                    <td><?= $used ?></td>
                                                    <td><?= $balance ?></td>
                                                    <td><?= $records['expire_date'] ?></td>
                                                    <td>
                                                        <?php if ($records['expire_date'] <= date('Y-m-d')) { ?>
                                                            <a href="data.php?id=1&gid=<?= $records['id'] ?>" role="button" class="btn btn-warning btn-sm check" check_id="<?= $records['id'] ?>" data-toggle="modal" id="check">Expired!</a>
                                                        <?php } else { ?>
                                                            <a href="#" role="button" class="btn btn-success btn-sm check" data-toggle="modal" id="check">Valid</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                            <?php if (!$_POST) { ?>
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <a href="report.php?id=1&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                            echo $_GET['page'] - 1;
                                                                        } else {
                                                                            echo 1;
                                                                        } ?>" class="btn btn-default">
                                            < </a>
                                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                    <a href="report.php?id=1&page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                                echo 'active';
                                                                                                                                            } ?>"><?= $i ?></a>
                                                <?php } ?>
                                                <a href="report.php?id=1&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                    echo $_GET['page'] + 1;
                                                                                } else {
                                                                                    echo $i - 1;
                                                                                } ?>" class="btn btn-default"> > </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="dr"><span></span></div>
            </div>
        </div>
    </div>
</body>

<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>


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

        var currentDate = new Date()
        var day = currentDate.getDate()
        var month = currentDate.getMonth() + 1
        var year = currentDate.getFullYear()

        var d = day + "-" + month + "-" + year;

        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, row, column, node) {
                        // Strip $ from salary column to make it numeric
                        return column === 5 ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                }
            }
        };


        // $(document).on('submit', '#validation3', function(event) {
        //     event.preventDefault();
        //     // $('#action').attr('disabled', 'disabled');
        //     var form_data = $(this).serialize();
        //     var start_date3 = $(this).attr('start_date3');
        //     var end_date3 = $('#end_date3').val();
        //     alert('form_data');
        //     alert('start_date3');
        //     alert('end_date3');

        // })

        $('#stock').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Report Searched</h1><div><span></span></div></div>"
            },

            "columnDefs": [{
                "width": "20%",
                "targets": 0
            }],



            dom: 'Bfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'STOCK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary',
                    // displayFormat: 'dddd D MMMM YYYY',
                    // wireFormat: 'YYYY-MM-DD',
                    // columnDefs: [{
                    // targets: [6],
                    // render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    // }],
                    Customize: function() {
                        doc.content[1].table.width = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'STOCK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },
                {
                    extend: 'csvHtml5',
                    title: 'STOCK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary'
                },
                // {
                //     extend: 'copyHtml5',
                //     title: 'VISITS',
                //     className: 'btn-primary'
                // },
                //     {
                //         extend: 'print',
                //         // name: 'printButton'
                //         title: 'VISITS'
                //     }
            ],

            // paging: true,
            // scrollY: 10
        });


        $('#check').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Report Searched</h1><div><span></span></div></div>"
            },


            dom: 'Bfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'CHECK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary',
                    // displayFormat: 'dddd D MMMM YYYY',
                    // wireFormat: 'YYYY-MM-DD',
                    // columnDefs: [{
                    // targets: [6],
                    // render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    // }],
                },
                {
                    extend: 'pdfHtml5',
                    title: 'CHECK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },
                {
                    extend: 'csvHtml5',
                    title: 'CHECK REPORT' + ' ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .......................................',
                    className: 'btn-primary'
                },
                // {
                //     extend: 'copyHtml5',
                //     title: 'VISITS',
                //     className: 'btn-primary'
                // },
                //     {
                //         extend: 'print',
                //         // name: 'printButton'
                //         title: 'VISITS'
                //     }
            ],

            // paging: true,
            // scrollY: 10
        });

        $('#status').DataTable({

            "language": {
                "emptyTable": "<div class='display-1 font-weight-bold'><h1 style='color: tomato;visibility: visible'>No Report Searched</h1><div><span></span></div></div>"
            },


            dom: 'Bfrtip',
            buttons: [{

                    extend: 'excelHtml5',
                    title: 'STATUS REPORT:- ' + 'DATE PRINTED: ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .....................' + 'FOR MONTH: ' + ' ..............',
                    className: 'btn-primary',
                    // displayFormat: 'dddd D MMMM YYYY',
                    // wireFormat: 'YYYY-MM-DD',
                    // columnDefs: [{
                    // targets: [6],
                    // render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    // }],
                },
                {
                    extend: 'pdfHtml5',
                    title: 'STATUS REPORT:- ' + 'DATE PRINTED: ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .....................' + 'FOR MONTH: ' + ' ..............',
                    className: 'btn-primary',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'

                },
                {
                    extend: 'csvHtml5',
                    title: 'STATUS REPORT:- ' + 'DATE PRINTED: ' + d + '  ' + ' :' + 'PRINTED BY : ' + ' .....................' + 'FOR MONTH: ' + ' ..............',
                    className: 'btn-primary'
                },
                // {
                //     extend: 'copyHtml5',
                //     title: 'VISITS',
                //     className: 'btn-primary'
                // },
                //     {
                //         extend: 'print',
                //         // name: 'printButton'
                //         title: 'VISITS'
                //     }
            ],

            // paging: true,
            // scrollY: 50
        });

    });
</script>

</html>