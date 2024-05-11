<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['level']) && isset($_GET['program_code'])) {
    $level = $_GET['level'];
    $program_code = $_GET['program_code'];

    $sections_query = $conn->prepare("
        SELECT sections.*, program.program_code
        FROM sections
        INNER JOIN program ON sections.program_id = program.id
        WHERE sections.level = ? AND sections.is_active = 1 AND program.program_code = ?
    ");

    if (!$sections_query) {
        // If prepare() returns false, there's an error
        die('Error: ' . $conn->error);
    }

    $sections_query->bind_param("ss", $level, $program_code);
    $sections_query->execute();
    $sections_result = $sections_query->get_result();

    $sections = [];
    while ($row = $sections_result->fetch_assoc()) {
        $sections[] = $row;
    }
}
?>
<label>Section</label>
<select class="form-control" id="section_id">
    <option>Please Select</option>
    <?php foreach ($sections as $section) {
        $section_name_concatenated = $section['program_code'] . '-' . substr($section['level'], 0, 1) . $section['section_name'];
    ?>
        <option value="<?php echo $section['id']; ?>"><?php echo $section_name_concatenated; ?></option>
    <?php } ?>
</select>
