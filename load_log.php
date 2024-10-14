<?php
if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    $directory = __DIR__ . '/logs/' . $filename;
    if (file_exists($directory)) {
        $content = file_get_contents($directory);
        echo $content;
    }
}
?>
