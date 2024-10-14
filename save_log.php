<?php
ini_set('display_errors',1);
error_Reporting(E_ALL);
if (isset($_REQUEST['content']) && isset($_REQUEST['filename'])) {
    $content = $_REQUEST['content'];
    $filename = $_REQUEST['filename'];
    $directory = __DIR__ . '/logs/' . $filename;
/*
    if (file_exists($directory)) {
        echo 'File already exists. Overwrite?';
    } else {*/
        file_put_contents($directory, $content);
        echo 'Saved successfully.';
   /* }*/
}
?>
