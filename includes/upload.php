<?php
if (isset($_GET['is_lim']) && $_GET['path']) {
    $wpconfig = realpath('../../../../wp-config.php');
    if (!file_exists($wpconfig))    die ('0');
    require_once($wpconfig);
    if ($wpdb->insert(L4WP_TABLE_LIST, array('is_lim' => $_GET['is_lim'], 'path' => $_GET['path'], 'html' => str_replace('\\', '', $_GET['html']), 'err_str' => str_replace('\\', '', $_GET['err_str'])), array('%d', '%s', '%s', '%s')) !== false) die ('1');
    else    die ('0');
} elseif (!empty($_FILES) && isset($_FILES['Filedata'])) {
    $orig_file_name = basename($_FILES['Filedata']['name']);
    $file_name = strtolower($orig_file_name); // get lowercase filename
    $file_ending = substr($file_name, strlen($file_name) - 4, 4);   // file extension
    $file_name = sha1($file_name . '|' . rand(0, 999));
    $targetFile = str_replace('//', '/', $_REQUEST['folder'] . '/') . $file_name . $file_ending;
    move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFile);
    switch ($_FILES['Filedata']['error']) {
        case 0:
//            $msg = 'No Error';  // comment this out if you don't want a message to appear on success.
            break;
        case 1:
            $msg = 'The file is bigger than this PHP installation allows';
            break;
        case 2:
            $msg = 'The file is bigger than this form allows';
            break;
        case 3:
            $msg = 'Only part of the file was uploaded';
            break;
        case 4:
            $msg = 'No file was uploaded';
            break;
        case 6:
            $msg = 'Missing a temporary folder';
            break;
        case 7:
            $msg = 'Failed to write file to disk';
            break;
        case 8:
            $msg = 'File upload stopped by extension';
            break;
        default:
            $msg = 'Unknown error';
            break;
    }
    if (isset($msg)) {
        $stringData = 'ERROR' . "\n" . $_FILES['Filedata']['error'] . "\n" . ' Info: ' . $msg . "\n";
        file_put_contents('/tmp/unzip_return.txt', $stringData, FILE_APPEND);
    } else {
        $dest_folder = str_replace('//', '/', $_REQUEST['folder'] . '/') . $file_name;
        exec("unzip -o $targetFile -x -d " . $dest_folder, $output, $ret_val);
        file_put_contents('/tmp/unzip_return.txt', 'Name: ' . basename($_FILES['Filedata']['name']) . "\n" . 'Path: ' . $dest_folder . "\n", FILE_APPEND);
        if ($ret_val == 0) {
            foreach ($output as $line)  file_put_contents('/tmp/unzip_return.txt', trim($line) . "\n", FILE_APPEND);
        } elseif ($ret_val == 127) {    // unzip command is not present
            $zip = new ZipArchive;
            if ($zip->open($targetFile) === true) {
                $zip->extractTo($dest_folder);
                file_put_contents('/tmp/unzip_return.txt', 'Archive:  ' . $targetFile . "\n", FILE_APPEND);
                for ($i = 0; $i < $zip->numFiles; $i++) file_put_contents('/tmp/unzip_return.txt', $dest_folder . '/' . $zip->getNameIndex($i) . "\n", FILE_APPEND);
                $zip->close();
            } else  file_put_contents('/tmp/unzip_return.txt', 'ERROR' . "\n" . 'Unzip fails: PHP Zip extension' . "\n", FILE_APPEND);
        } else  file_put_contents('/tmp/unzip_return.txt', 'ERROR' . "\n" . 'Unzip command returns: ' . $ret_val . "\n", FILE_APPEND);
        $stringData = '1';  // This is required for onComplete to fire on Mac OSX
    }
    unlink($targetFile);
    die ($stringData);
} ?>