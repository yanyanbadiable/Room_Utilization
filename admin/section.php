<?php

include('db_connect.php');

if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM sections WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
$user_program_id = $_SESSION['login_program_id'];
?>

<div class="container-fluid p-3">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Sections</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Section Management</a></li>
                <li class="breadcrumb-item active">Manage Sections</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Section Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Section Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-section">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Program Code</label>
                            <select class="form-control" name="program_id" disabled>
                                <?php
                                $program = $conn->query("SELECT id, program_code FROM program WHERE id = $user_program_id");
                                $row = $program->fetch_assoc();
                                ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['program_code'] ?></option>
                            </select>
                            <input type="hidden" name="program_id" value="<?php echo $row['id'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Level</label>
                            <select class="form-control" name="level" required>
                                <option>Please Select here</option>
                                <option value="1st Year" <?php echo isset($level) && $level == '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                <option value="2nd Year" <?php echo isset($level) && $level == '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                <option value="3rd Year" <?php echo isset($level) && $level == '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                <option value="4th Year" <?php echo isset($level) && $level == '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Section Name</label>
                            <input type="text" class="form-control" name="section_name">
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" form="manage-section" class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                            <button class="btn btn-sm btn-light col-sm-3" type="button" onclick="_reset()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Section Form Panel -->

        <!-- Table Panel -->
        <section class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Section List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Program Code</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-center">Section Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $section = $conn->query("SELECT sections.*, program.program_code FROM sections INNER JOIN program ON sections.program_id = program.id WHERE sections.program_id = $user_program_id ORDER BY level ASC, section_name ASC");
                                if (!$section) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $section->fetch_assoc()) :
                                    $section_name_concatenated = $row['program_code'] . '-' . substr($row['level'], 0, 1) . $row['section_name'];
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class=""><?php echo $row['program_code'] ?></td>
                                        <td class=""><?php echo $row['level'] ?></td>
                                        <td class=""><?php echo $section_name_concatenated ?></td>
                                        <td class="text-center">
                                            <?php if ($row['is_active'] == 1) : ?>
                                                <span class="badge badge-success" style="font-size: 16px;">Active</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger" style="font-size: 16px;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_section" type="button" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-program_id="<?php echo $row['program_id'] ?>" data-level="<?php echo $row['level'] ?>" data-section_name="<?php echo $row['section_name'] ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete_section" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Table Panel -->
    </div>
</div>

<script>
    function _reset() {
        $('#manage-section').find('[name="id"]').val('');
        // $('#manage-section').find('[name="level"]').val('');
        $('#manage-section').find('[name="section_name"]').val('');
    }

    $('#manage-section').submit(function(e) {
        e.preventDefault();

        // Validate input fields
        var level = $('[name="level"]').val();
        var section_name = $('[name="section_name"]').val();

        if (!level || level == "Please Select here" || !section_name) {
            alert_toast("Please fill in all fields", 'danger');
            return; // Exit function if any field is empty
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_section',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully added", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 100);
                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 100);
                } else {
                    alert_toast("Section Already Exists", 'danger');
                }
            }

        });
    });

    $('.edit_section').click(function() {
        start_load();
        var cat = $('#manage-section');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='program_id']").val($(this).attr('data-program_id'));
        cat.find("[name='level']").val($(this).attr('data-level'));
        cat.find("[name='section_name']").val($(this).attr('data-section_name'));
        end_load();
    });

    $('.delete_section').click(function() {
        _conf("Are you sure to delete this section?", "delete_section", [$(this).attr('data-id')]);
    });

    function delete_section($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_section',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'danger');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
        });
    }
</script>