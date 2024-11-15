<?php
include '../admin/db_connect.php';
if (isset($_GET['id'])) {
    $user = $conn->query("SELECT * FROM unit_loads where id =" . $_GET['id']);
    foreach ($user->fetch_array() as $k => $v) {
        $meta[$k] = $v;
    }
}
?>

<div class="container-fluid">
    <form id="manage-academic_rank">
        <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
        <div class="form-group row">
            <div class="col-md-8">
                <label class="control-label">Academic Rank</label>
                <input type="text" class="form-control" name="academic_rank" value="<?php echo isset($meta['academic_rank']) ? $meta['academic_rank'] : '' ?>">
            </div>
            <div class="col-md-4">
                <label class="control-label">Hours</label>
                <input type="number" class="form-control" name="hours" value="<?php echo isset($meta['hours']) ? $meta['hours'] : '' ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="control-label">Administrative</label>
                <input type="number" class="form-control" name="administrative" value="<?php echo isset($meta['administrative']) ? $meta['administrative'] : '' ?>">
            </div>
            <div class="col-md-6">
                <label class="control-label">Research</label>
                <input type="number" class="form-control" name="research" value="<?php echo isset($meta['research']) ? $meta['research'] : '' ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="control-label">Extension Services</label>
                <input type="number" class="form-control" name="ext_service" value="<?php echo isset($meta['ext_service']) ? $meta['ext_service'] : '' ?>">
            </div>
            <div class="col-md-6">
                <label class="control-label">Consultation</label>
                <input type="number" class="form-control" name="consultation" value="<?php echo isset($meta['consultation']) ? $meta['consultation'] : '' ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="control-label">Instructional Functions</label>
                <input type="number" class="form-control" name="instructional" value="<?php echo isset($meta['instructional']) ? $meta['instructional'] : '' ?>">
            </div>
            <div class="col-md-6">
                <label class="control-label">Others</label>
                <input type="number" class="form-control" name="others" value="<?php echo isset($meta['others']) ? $meta['others'] : '' ?>">
            </div>
        </div>
    </form>
</div>
<script>
    function _reset() {
        $('#manage-academic_rank').get(0).reset()
        $('#manage-academic_rank input,#manage-academic_rank textarea').val('')
    }

    $('#manage-academic_rank').submit(function(e) {
        e.preventDefault()
        var academic_rank = $("input[name='academic_rank']").val();
        var hours = $("input[name='hours']").val();
        var administrative = $("input[name='administrative']").val();
        var research = $("input[name='research']").val();
        var ext_service = $("input[name='ext_service']").val();
        var consultation = $("input[name='consultation']").val();
        var instructional = $("input[name='instructional']").val();
        var others = $("input[name='others']").val();

        if (!academic_rank || !hours || !administrative || !research || !ext_service || !consultation || !instructional || !others) {
            alert_toast("Please fill in all fields!", 'warning');
            return;
        }
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=save_academic_rank',
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
                    }, 1500)
                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                }
            }
        })
    })
</script>