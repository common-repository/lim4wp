<?php
if (isset($_GET['lim_path']) && $_GET['lim_url']) {
    $str = file_get_contents($_GET['lim_path']);
    $position = strpos($str, '<recursos>');
    if ($position !== false)    $resources = substr($str, $position + 10, strpos($str, '</recursos>') - $position - 10);
    else    $resources = false;
    $lim_path_folder = dirname($_GET['lim_path']);
    $name = sha1(basename($lim_path_folder) . '|' . rand(0, 999));
    if ($resources !== false && is_dir($lim_path_folder . DIRECTORY_SEPARATOR . $resources)) {
        $content = substr($str, 0, $position) . substr($str, strpos($str, '</recursos>') + 11);
        $new_lim = $lim_path_folder . DIRECTORY_SEPARATOR . $resources . DIRECTORY_SEPARATOR . $name . '.lim';
        if (file_put_contents($new_lim, $content) !== false) {
            $new_swf = $lim_path_folder . DIRECTORY_SEPARATOR . $resources . DIRECTORY_SEPARATOR . $name . '.swf';
            if (!is_file($_GET['swf_path']))    die (json_encode(array('-1', array('from' => $_GET['swf_path'], 'to' => $new_swf), $new_lim)));
            else {
                $wpconfig = realpath('../../../../wp-config.php');
                if (!file_exists($wpconfig))    die (json_encode(array('0', array('from' => $_GET['swf_path'], 'to' => $new_swf), $new_lim)));
                require_once($wpconfig);
                $command = lim4wp_copy($_GET['swf_path'], $new_swf);
                if (!$command)  die (json_encode(array('0', array('from' => $_GET['swf_path'], 'to' => $new_swf), $new_lim)));
                else    die (json_encode(array('1', array('from' => $_GET['swf_path'], 'to' => $new_swf), $new_lim)));
            }
        }
    } elseif ($resources === false) {
        $new_swf = $lim_path_folder . DIRECTORY_SEPARATOR . $name . '.swf';
        if (!is_file($_GET['swf_path']))    die (json_encode(array('-1', array('from' => $_GET['swf_path'], 'to' => $new_swf), $_GET['lim_path'])));
        else {
            $wpconfig = realpath('../../../../wp-config.php');
            if (!file_exists($wpconfig))    die (json_encode(array('0', array('from' => $_GET['swf_path'], 'to' => $new_swf), $_GET['lim_path'])));
            require_once($wpconfig);
            $command = lim4wp_copy($_GET['swf_path'], $new_swf);
            if (!$command)  die (json_encode(array('0', array('from' => $_GET['swf_path'], 'to' => $new_swf), $_GET['lim_path'])));
            else    die (json_encode(array('1', array('from' => $_GET['swf_path'], 'to' => $new_swf), $_GET['lim_path'])));
        }
    }
} elseif (is_file('/tmp/unzip_return.txt')) {
    $wpconfig = realpath('../../../../wp-config.php');
    if (!file_exists($wpconfig))    die ('0');
    require_once($wpconfig);
    $content = file('/tmp/unzip_return.txt');
    unlink('/tmp/unzip_return.txt');
    $return_array = array();
    for ($i = 0; $i < count($content); $i++) {
        $content[$i] = trim(str_replace("\n", '', $content[$i]));
        if (strpos($content[$i], 'Name:') !== false) {
            $path = trim(str_replace('Path:', '', $content[$i + 1]));
            $new_array = array( 'name' => trim(str_replace('Name:', '', $content[$i])),
                                'archive' => trim(str_replace('Archive:', '', $content[$i + 2])),
                                'path' => $path,
                                'output' => array(),
                                'lim' => lim4wp_list_dir($path, $_GET['ext']),
                                'swf' => lim4wp_list_dir($path, 'swf'));
            while ($i + 3 < count($content) && strpos($content[$i + 3], 'Name:') === false) {
                $output = str_replace('creating:', '', str_replace('inflating:', '', str_replace($path, '', str_replace('extracting:', '', $content[$i + 3]))));
                $new_array['output'][] = trim(str_replace("\n", '', $output));
                $i++;
            }
            $return_array[] = $new_array;
        }
    }
    echo json_encode($return_array);
} ?>