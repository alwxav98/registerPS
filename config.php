<?php
define('DB_HOST', 'ec2-18-207-77-6.compute-1.amazonaws.com');
define('DB_USER', 'root');
define('DB_PASSWORD', 'claveSegura123@');
define('DB_NAME', 'loginPS');

define('UPLOAD_FOLDER', __DIR__ . '/uploads/');
define('ALLOWED_EXTENSIONS', ['png', 'jpg', 'jpeg', 'gif']);

if (!is_dir(UPLOAD_FOLDER)) {
    mkdir(UPLOAD_FOLDER, 0777, true);
}
?>
