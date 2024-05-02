<?php include('db_connect.php'); ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM building WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <!-- <section class="content-header">
        <h1><i class="fa  fa-folder-open"></i>
            List of Curriculum
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="#"> Curriculum Management</a></li>
            <li class="active"><a>View Curriculum </a></li>
            <li class="active"><a>List of Curriculum</a></li>
        </ol>
    </section> -->
    <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <form action="" id="manage-building">
                <div class="card">
                    <div class="card-header">
                        Building Form
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Building Name</label>
                            <input type="text" class="form-control" name="building">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Department</label>
                            <select class="form-control" name="department_id">
                                <?php
                                $department = $conn->query("SELECT id, department_name FROM department");
                                while ($row = $department->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['department_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
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
                    <b>Building List</b>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Building</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $building = $conn->query("SELECT building.*, program.program_name
                            FROM building INNER JOIN program ON building.program_id = program.id;");
                            if (!$building) {
                                die('Invalid query: ' . $conn->error);
                            }
                            while ($row = $building->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class="">
                                        <p>Building Name: <b><?php echo $row['building'] ?></b></p>
                                        <p>Program: <small><b><?php echo $row['program_name'] ?></b></small></p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_building" type="button" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-building="<?php echo $row['building'] ?>" data-department_name="<?php echo $row['department_name'] ?>">Edit</button>
                                        <button class="btn btn-sm btn-danger delete_building" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
<style>
    td {
        vertical-align: middle !important;
    }
</style>
<script>
    function _reset() {
        $('#manage-building').get(0).reset()
        $('#manage-building input,#manage-building textarea').val('')
    }
    $('#manage-building').submit(function(e) {
        e.preventDefault()
        start_load()

        $.ajax({
            url: 'ajax.php?action=save_building',
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
                    console.log('Before setTimeout');
                    setTimeout(function() {
                        console.log('Reloading page...');
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
    $('.edit_building').click(function() {
        start_load()
        var cat = $('#manage-building')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='building']").val($(this).attr('data-building'))
        cat.find("[name='department_name']").val($(this).attr('data-department_name'))
        end_load()
    })
    $('.delete_Building').click(function() {
        _conf("Are you sure to delete this Building?", "delete_building", [$(this).attr('data-id')])
    })

    function delete_building($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_building',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1000)

                }
            }
        })
    }
    $('table').dataTable()
</script>