<?php
/*
# cryptnotes
A quick bootstrap 5 blowfish jQuery PHP encrypted diary script

* author: Stephen Phillips
* date: 14/10/2024
* version: 1
*/
session_start();

// Define the secret key and directory
@$secretKey = $_SESSION['password'];
$directory = __DIR__ . '/logs/';

if (!isset($_SESSION['password']) || $_SESSION['password'] !== true) {
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
        $_SESSION['password'] =$password;
        $secretKey = $password;
    } else {
        ?>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Log Viewer - Password Required</title>
            <link href="css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <h2>Please enter the password to access the log viewer.</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Directory reading and displaying files in a menu
$files = glob($directory . '*.{txt}', GLOB_BRACE);
$files = array_reverse($files);

$encodedFiles = [];

foreach ($files as $file) {
    $filename = basename($file);
    if (strpos($filename, '.txt') !== false) {
        $content = file_get_contents($file);
        // Assuming the content is base64 encoded
        $encodedFiles[$filename] = $content;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Viewer</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        #logContentText, #editContentText {
            height: 800px;
            max-height:80vh;
        }
        .active a {
            color:#fff;
        }
    </style>
</head>
<body>
    <br><br>
    <div class="container-fluid">
        <button id="toggleButton" class="btn btn-primary">Show/Hide File List</button>
        <div class="row">
            <div class="col-md-3">
                <ul id="fileList" class="list-group">
                    <?php foreach ($encodedFiles as $filename => $content): ?>
                        <li class="list-group-item"><a href="#" data-filename="<?= htmlspecialchars($filename) ?>"><?= htmlspecialchars($filename) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-9">
                <form id="logForm" class="mb-3">

                    <!-- Content to be shown in tabs -->
                    <div class="container mt-4">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="logContent-tab" data-bs-toggle="tab" data-bs-target="#logContent" type="button" role="tab" aria-controls="logContent" aria-selected="true">Log Content</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="editContent-tab" data-bs-toggle="tab" data-bs-target="#editContent" type="button" role="tab" aria-controls="editContent" aria-selected="false">Edit Content</button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="logContent" role="tabpanel" aria-labelledby="logContent-tab">
                                <div class="mb-3">
                                    <label for="logContentText" class="form-label">Log Content:</label>
                                    <textarea class="form-control" id="logContentText" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="editContent" role="tabpanel" aria-labelledby="editContent-tab">
                                <div class="mb-3">
                                    <label for="editContentText" class="form-label">Edit Content:</label>
                                    <textarea class="form-control" id="editContentText" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--
                    <div class="mb-3">
                        <label for="debugContent" class="form-label">Debug Content:</label>
                        <textarea class="form-control" id="debugContent" rows="10"></textarea>
                    </div>                    
                    -->
                    <div class="mb-3">
                        <label for="filename" class="form-label">Filename:</label>
                        <input type="text" class="form-control" id="filename" value="<?php echo date('Ymd').'.txt'; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </form>

                <div id="settingsPanel" class="mt-3">
                    <label for="decryptionKey" class="form-label">Decryption Key:</label>
                    <input type="password" class="form-control" id="decryptionKey" value="<?php echo $secretKey.date('Ymd'); ?>">
                    <button type="button" id="decryptButton" class="btn btn-danger mt-2">Decrypt</button>
                </div>

            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/blowfish.js"></script>
    <script>
    $(document).ready(function() {
        $('#toggleButton').click(function() {
            $('#fileList').slideToggle();
        });

        // Event listener for filename change (using keyup to update immediately)
        $('#filename').on('keyup', function() {
            var newFilename = $(this).val();
            var secretKey = '<?php echo $secretKey; ?>';
            var newDecryptionKey = secretKey + newFilename.replace(/\.[^.]+$/, '');
            $('#decryptionKey').val(newDecryptionKey);
        });

        $('#fileList a').click(function(e) {
            e.preventDefault();
            // Access the existing active class
            $('.list-group-item.active').removeClass('active');                   
            $(this).parent().addClass('active');           
             
            var filename = $(this).data('filename');
            $.get('load_log.php', { filename: filename }, function(data) {
                $('#logContentText').val(data);
                $('#filename').val(filename);

                var secretKey = '<?php echo $secretKey; ?>';
                var decryptionKey = secretKey + filename.replace(/\.[^.]+$/, '');
                $('#decryptionKey').val(decryptionKey);
                /*
                var logContent = $('#logContent').val();
                var decryptionKey = $('#decryptionKey').val();
                $('#debugContent').val('blowfish.encrypt("'+logContent+'", "'+decryptionKey+'", {cipherMode: 0, outputType: 0});');
                */       
            });
        });

        $('#logForm').on('submit', function(e) {
            e.preventDefault();
                
            // Determine which textarea to use based on the active tab
            var activeTab = $('.nav-tabs button.active').attr('id');
            if (activeTab === 'editContent-tab') {
                var contentToSave = $('#editContentText').val();
            } else {
                var contentToSave = $('#logContentText').val();
            }

            var decryptionKey = $('#decryptionKey').val();
            var encryptedContent = blowfish.encrypt(contentToSave, decryptionKey, {cipherMode: 0, outputType: 0});

            var filename = $('#filename').val();
            $.post('save_log.php', { content: encryptedContent, filename: filename }, function(data) {
                if (activeTab === 'editContent-tab') {
                    $('#editContentText').val(encryptedContent);
                } else {
                    $('#logContentText').val(encryptedContent);
                }  
            });
                
        });

        $('#decryptButton').click(function() {
            
            // Determine which textarea to use based on the active tab
            var activeTab = $('.nav-tabs button.active').attr('id');
            if (activeTab === 'editContent-tab') {
                var logContent = $('#editContentText').val();
                var decryptionKey = $('#decryptionKey').val();
                var decryptedContent = blowfish.decrypt(logContent, decryptionKey, {cipherMode: 0, outputType: 0});
                $('#editContentText').val(decryptedContent);
            } else {
                var logContent = $('#logContentText').val();
                var decryptionKey = $('#decryptionKey').val();
                var decryptedContent = blowfish.decrypt(logContent, decryptionKey, {cipherMode: 0, outputType: 0});
                $('#logContentText').val(decryptedContent);
            }                
        });
    });
</script>

</body>
</html>
