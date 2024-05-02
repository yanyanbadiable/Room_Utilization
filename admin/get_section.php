<label>Section</label>
<select class="form-control" id="section_name">
    <?php foreach ($sections as $section) : ?>
        <option value="<?php echo $section->section_name; ?>"><?php echo $section->section_name; ?></option>
    <?php endforeach; ?>
</select>
