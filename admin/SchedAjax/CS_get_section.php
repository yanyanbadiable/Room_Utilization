<?php
include('../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['level']) && isset($_GET['program_id'])) {
    $level = $_GET['level'];
    $program_id = $_GET['program_id'];

    $sections_query = $conn->prepare("
        SELECT sections.*, program.program_code
        FROM sections
        INNER JOIN program ON sections.program_id = program.id
        WHERE sections.level = ? AND program.id = ?
        ORDER BY sections.section_name ASC
    ");

    if (!$sections_query) {

        die('Error: ' . $conn->error);
    }

    $sections_query->bind_param("si", $level, $program_id);
    $sections_query->execute();
    $sections_result = $sections_query->get_result();

    $sections = [];
    while ($row = $sections_result->fetch_assoc()) {
        $sections[] = $row;
    }
}     
?>
<label>Section</label>
<select class="form-control" id="section_id" onchange='getCoursesOffered(program_id.value,level.value,this.value)'>
    <option>Please Select</option>
    <?php foreach ($sections as $section) {
        $section_name_concatenated = $section['program_code'] . '-' . substr($section['level'], 0, 1) . $section['section_name'];
    ?>
        <option value="<?php echo $section['id']; ?>"><?php echo $section_name_concatenated; ?></option>
    <?php } ?>
</select>