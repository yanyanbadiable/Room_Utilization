<?php include('db_connect.php'); ?>

<div class="container-fluid">

    <div class="col-lg-12">
        <div class="row">
            <!-- FORM Panel -->
            <div class="col-md-4">
                <form action="" id="manage-department">
                    <div class="card">
                        <div class="card-header">
                            Department Form
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label class="control-label">Department Code</label>
                                <input type="text" class="form-control" name="department_code">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Department Name</label>
                                <input type="text" class="form-control" name="department_name">
                            </div>

                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
                                    <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="_reset()"> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- FORM Panel -->

            <!-- Table Panel -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <b>Department List</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $department = $conn->query("SELECT * FROM department order by id asc");
                                while ($row = $department->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class="">
                                            <p>Department Code: <b><?php echo $row['department_code'] ?></b></p>
                                            <p>Department Name: <small><b><?php echo $row['department_name'] ?></b></small></p>

                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_department" type="button" data-id="<?php echo $row['id'] ?>" data-department-code="<?php echo $row['department_code'] ?>" data-department-name="<?php echo $row['department_name'] ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete_department" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>

</div>
<style>
    td {
        vertical-align: middle !important;
    }
</style>
<script>
    function _reset() {
        $('#manage-department').get(0).reset()
        $('#manage-department input,#manage-department textarea').val('')
    }
    $('#manage-department').submit(function(e) {
        e.preventDefault()
        start_load()
        $.ajax({
            url: 'ajax.php?action=save_department',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                console.log(resp)
                if (resp == 1) {
                    alert_toast("Data successfully added", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 100)

                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 100)

                }
            }
        })
    })
    $('.edit_department').click(function() {
        start_load()
        var cat = $('#manage-department')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='department_code']").val($(this).attr('data-department-code'))
        cat.find("[name='department_name']").val($(this).attr('data-department-name'))
        end_load()
    })
    $('.delete_department').click(function() {
        _conf("Are you sure to delete this department?", "delete_department", [$(this).attr('data-id')])
    })

    function delete_department($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_department',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 100)

                }
            }
        })
    }
    $('table').dataTable()
</script>
