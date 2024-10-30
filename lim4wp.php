<?php

/*
  Plugin Name: Lim4wp
  Plugin URI: http://anboto.tk
  Description: Provides a button for upload LIM books in Post/Page TinyMCE editor.
  Version: 1.1.1
  Author: Anboto
  Author URI: http://anboto.tk
  License: GPL3
 
  ----------------------------------------
  Copyright (C)  2010  Martin Mozos (email: anboto[at]gmail[dot]com)

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  http://www.gnu.org/licenses/
  ----------------------------------------
 */

if (!class_exists('lim4wpLoad')) {

    class lim4wpLoad {

        function lim4wpLoad() {
            $this->__construct();
        }

        function __construct() {
            $this->loadConstants();
//            $this->loadDependencies();
            add_action('plugins_loaded', array(&$this, 'start'));   // Start this plug-in once all other plugins are fully loaded
        }

//        function loadDependencies() {
//        }

        function loadConstants() {
            define('L4WP_CURRENT_VERSION', '1.0');
            define('L4WP_BASE_NAME', plugin_basename(dirname(__FILE__)));
            define('L4WP_PLUGIN_DIR', WP_PLUGIN_DIR . '/lim4wp');
            define('L4WP_PLUGIN_URL', WP_PLUGIN_URL . '/lim4wp');
            global $wpdb;
            define('L4WP_TABLE', $wpdb->prefix . 'lim4wp');
            define('L4WP_TABLE_LIST', $wpdb->prefix . 'lim4wp_list');
        }

        function start() {
            if (is_admin ()) {
                // Add FAQ, Support and Donate links
                add_filter('plugin_row_meta', array(&$this, 'addMetaLinks'), 10, 2);
                // Add option page to configure settings TODO
                add_action('admin_menu', array(&$this, 'loadOptionsPage'));
                // Calls the methods to load scripts and CSS.
                add_action('wp_print_scripts', array(&$this, 'loadScripts'));
                add_action('wp_print_styles', array(&$this, 'loadStyles'));
                // Add a version number to the header
                add_action('wp_head', create_function('', 'echo "\n<meta name=\'Lim4WP\' content=\'' . L4WP_CURRENT_VERSION . '\' />\n";'));
                // Editor
                add_filter('mce_css', 'replace_editor_css');
                add_filter('tiny_mce_before_init', 'lim4wp_mce_valid_elements', 0);
                add_filter('tiny_mce_version', 'lim4wp_change_tinymce_version');
                add_action('init', 'lim4wp_addbuttons');
                // End Editor
            }
        }

        // Add FAQ and support information
        function addMetaLinks($links, $file) {
            if ($file == plugin_basename(__FILE__)) {
                $links[] = '<a href="http://anboto.tk/" target="_blank">' . __('Support', 'lim4wp_main') . '</a>';
                $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=JSN6ZM2CY5JDY&lc=ES&currency_code=EUR" target="_blank">'.__('Donate', 'lim4wp_main').'</a>';
            }
            return $links;
        }

        function loadOptionsPage() {
            if (function_exists('add_options_page')) {
                add_options_page('Lim4WP Settings', 'Lim4WP', 8, __FILE__, array(&$this, 'lim4wp_option_page'));
//                add_filter('plugin_action_links', array(&$this, 'lim4wp_add_plugin_actions', 10, 2));
            }
        }

        function lim4wp_option_page() {
            if (function_exists('current_user_can') && !current_user_can('manage_options')) die(__('Cheatin&#8217; uh?'));
            add_action('in_admin_footer', array(&$this, 'lim4wp_add_admin_footer'));
            require_once('includes/adminOptions.php');
        }

        function loadScripts() {
            wp_register_script('lim4wp_script_functions', L4WP_PLUGIN_URL . '/js/tinyPopup.js', array(), '1.0');
            wp_enqueue_script('lim4wp_script_functions');
            wp_register_script('lim4wp_script', L4WP_PLUGIN_URL . '/js/script.js', array(), '1.0');
            wp_enqueue_script('lim4wp_script');
        }

        function loadStyles() {
            wp_register_style('lim4wp_styles', L4WP_PLUGIN_URL . '/css/styles.css', array(), '1.0');
            wp_enqueue_style('lim4wp_styles');
        }

//        function lim4wp_add_plugin_actions($links, $file) { //add's a "Settings"-link to the entry on the plugin screen
//            static $this_plugin;
//            if (!$this_plugin) {
//                $this_plugin = plugin_basename(__FILE__);
//            }
//            if ($file == $this_plugin) {
//                $settings_link = '<a href="options-general.php?page=' . $this_plugin . '">' . __('Settings') . '</a>';
//                array_unshift($links, $settings_link);
//            }
//            return $links;
//        }

        function lim4wp_add_admin_footer() {    // Shows some plugin info at the footer of the config screen.
            $plugin_data = get_plugin_data(__FILE__);
            printf(__('%1$s plugin | Version %2$s | by %3$s'), $plugin_data['Title'], $plugin_data['Version'], '<a href="http://profiles.wordpress.org/users/anboto/">' . $plugin_data['Author'] . '</a><br />');
        }

    }

    $lim4wp = new lim4wpLoad();

    register_activation_hook(__FILE__, 'lim4wp_activate');

////////// Editor //////////
    function replace_editor_css($css_file) {    // Apply the css for rich editor
        $css_file = get_option('siteurl') . '/wp-content/plugins/lim4wp/css/richeditor.css';
        return $css_file;
    }
    function lim4wp_mce_valid_elements($init) { // Add pre as a valid element to TinyMCE with lang and line arguments
        if (isset($init['extended_valid_elements']) && !empty($init['extended_valid_elements']))    $init['extended_valid_elements'] .= ',pre[name|class]';
        else    $init['extended_valid_elements'] = 'pre[name|class]';
        return $init;
    }
    function lim4wp_change_tinymce_version($version) { return ++$version; } // Modify the version when tinyMCE plugins are changed.
    function lim4wp_addbuttons() {  // init process for button control
        if (get_user_option('rich_editing') == 'true') {    // Add only in Rich Editor mode
            add_filter('mce_external_plugins', 'add_lim4wp_tinymce_plugin', 5); // Add the button for wp25 in a new way
            add_filter('mce_buttons', 'register_lim4wp_button', 5);
        }
    }
    function add_lim4wp_tinymce_plugin($plugin_array) { // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
        $plugin_array['l4wp'] = get_option('siteurl') . '/wp-content/plugins/lim4wp/editor_plugin.js';
        return $plugin_array;
    }
    function register_lim4wp_button($buttons) { // Used to insert button in wordpress 2.5x editor
        array_push($buttons, 'separator', 'l4wp');
        return $buttons;
    }
////////// End Editor //////////
////////// Functions //////////
    function lim4wp_activate() {
        global $wpdb;
        if ($wpdb->get_var("SHOW TABLES LIKE '" . L4WP_TABLE . "';") != L4WP_TABLE) {
            $sql = "CREATE TABLE `" . L4WP_TABLE . "` (" .
                    "`upload_path` varchar(64) NOT NULL," .
                    "`upload_path_lim_subdir` varchar(64) NOT NULL," .
                    "PRIMARY KEY (`upload_path_lim_subdir`)) DEFAULT CHARACTER SET utf8;";
            $wpdb->query($sql);
            if (get_option('upload_path') === '')   $upload_path = 'wp-content/uploads';
            else    $upload_path = lim4wp_clean_path(get_option('upload_path'));
            $wpdb->insert(L4WP_TABLE, array('upload_path' => $upload_path, 'upload_path_lim_subdir' => 'lim'), array('%s', '%s'));
        }
        if ($wpdb->get_var("SHOW TABLES LIKE '" . L4WP_TABLE_LIST . "';") != L4WP_TABLE_LIST) {
            $sql = "CREATE TABLE `" . L4WP_TABLE_LIST . "` (" .
                    "`id` bigint(16) unsigned NOT NULL auto_increment," .
                    "`is_lim` tinyint(1) NOT NULL," .
                    "`path` longtext NULL," .
                    "`html` longtext NULL," .
                    "`err_str` longtext NULL," .
                    "PRIMARY KEY (`id`)) DEFAULT CHARACTER SET utf8;";
            $wpdb->query($sql);
        }
    }
    function lim4wp_clean_path($path) {
        $clean_path_array = explode(DIRECTORY_SEPARATOR, $path);
        $clean_path = '';
        foreach ($clean_path_array as $path_piece) {
            if ($path_piece)    $clean_path .= $path_piece . DIRECTORY_SEPARATOR;
        }
        if (substr($clean_path, -1) == DIRECTORY_SEPARATOR) return substr($clean_path, 0, -1);
        else    return $clean_path;
    }
    function lim4wp_list_dir($dir = '.', $ext = 'php') {
        if (!is_dir($dir))  return false;
        $files = array();
        lim4wp_list_files_by_ext($files, $dir, $ext);
        return $files;
    }
    function lim4wp_list_files_by_ext(&$files, $dir, $ext) {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..')  continue;
            $filepath = $dir == '.' ? $file : $dir . DIRECTORY_SEPARATOR . $file;
            if (is_link($filepath)) continue;
            elseif (is_file($filepath) && strtolower(substr($file, strrpos($file, '.') + 1)) === strtolower($ext))  $files[] = $filepath;
            elseif (is_dir($filepath))  lim4wp_list_files_by_ext($files, $filepath, $ext);
        }
        closedir($handle);
    }
    function lim4wp_mkdir($target) {
        if (file_exists($target))   return $target;     // return @is_dir($target);
        if (@mkdir($target)) {
            $stat = @stat(dirname($target));            // Attempting to create the directory may clutter up our display.
            $dir_perms = $stat['mode'] & 0007777;       // Get the permission bits.
            @chmod($target, $dir_perms);
            return $target;                             // return true;
        } elseif (is_dir(dirname($target))) return false;
        if (lim4wp_mkdir(dirname($target)) !== false)   // If the above failed, attempt to create the parent node, then try again.
            return lim4wp_mkdir($target);
        else    return false;
    }
    function lim4wp_copy($file1, $file2) {
        $contentx = @file_get_contents($file1);
        $openedfile = fopen($file2, 'w');
        fwrite($openedfile, $contentx);
        fclose($openedfile);
        if ($contentx === false)    return false;
        else    return true;
    }
    function lim4wp_move($src, $dst) {
        $handle = opendir($src);                                // Opens source dir.
        if (!is_dir($dst))  lim4wp_mkdir($dst);                 // Make dest dir.
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..')  continue;       // Skips . and .. dirs
            $srcm = $src . DIRECTORY_SEPARATOR . $file;
            $dstm = $dst . DIRECTORY_SEPARATOR . $file;
            if (is_dir($srcm))  lim4wp_move($srcm, $dstm);      // If another dir is found calls itself - recursive WTG
            elseif (lim4wp_copy($srcm, $dstm))  unlink($srcm);  // If file is copied, delete the original one
        }
        closedir($handle);
    }
    function lim4wp_remove_empty_subfolders($path) {
        $empty = true;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file)  $empty &= is_dir($file) && lim4wp_remove_empty_subfolders($file);
        return $empty && rmdir($path);
    }
    function lim4wp_update_db($destination_folder, $input_val, $pk) {
        global $wpdb;
        return $wpdb->update(L4WP_TABLE, array('upload_path' => $destination_folder, 'upload_path_lim_subdir' => $input_val), array('upload_path_lim_subdir' => $pk), array('%s'), array('%s'));
    }
////////// End Functions //////////
}