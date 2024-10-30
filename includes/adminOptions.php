<?php
echo('Will be available in next version, be patient ;)');
if (false) {
//avoid direct calls to this file where wp core files not present
if (function_exists('current_user_can') && !current_user_can('manage_options')) die (__('Cheatin&#8217; uh?'));
elseif (isset($_GET['lims_path']) && $_GET['wp_uploads_path']) {
    $wpconfig = realpath('../../../../wp-config.php');
    if (!file_exists($wpconfig))    die ('k1');
    require_once($wpconfig);
    if ($_GET['input_val'] !== '0') $destination = lim4wp_mkdir(ABSPATH . lim4wp_clean_path($_GET['wp_uploads_path'] . '/' . $_GET['input_val']));
    else    $destination = lim4wp_mkdir(ABSPATH . lim4wp_clean_path($_GET['wp_uploads_path'] . '/' . basename($_GET['lims_path'])));
    if ($destination !== false) {
        $source = ABSPATH . $_GET['lims_path'];
        if (lim4wp_mkdir($source) !== false) {
            lim4wp_move($source, $destination);
            if ((is_dir($source) && lim4wp_remove_empty_subfolders($source) && rmdir($source)) || !file_exists($source)) {
                if ($_GET['input_val'] !== '0') die (lim4wp_update_db($_GET['wp_uploads_path'], lim4wp_clean_path($_GET['input_val']), basename($source)));
                else    die (lim4wp_update_db($_GET['wp_uploads_path'], lim4wp_clean_path(basename($_GET['lims_path'])), basename($source)));
            } else  die ('k4');
        } else  die ('k3');
    } else  die ('k2');
} elseif (isset($_GET['let'])) {
    $wpconfig = realpath('../../../../wp-config.php');
    if (!file_exists($wpconfig))    die ();
    require_once($wpconfig);
//    if (isset($_REQUEST['_icl_current_language']))  define('FORCE_ADMIN_LANG', $_REQUEST['_icl_current_language']);
} elseif (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die ();
}
//WP 3.0 compatibility
if (!function_exists('update_user_meta')) {
    function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
        return update_usermeta($user_id, $meta_key, $meta_value);
    }
} ?>
<div id="lim4wp_admin_options"><?php

printf(__('%s<h2>for Wordpress (Lim4WP)</h2>', 'lim4wp_main'), '<div id="icon-lim-for-wp"><br /></div>'); ?>

<div id="poststuff" class="metabox-holder has-right-sidebar">
    <div id="side-info-column" class="inner-sidebar">
        <div id="side-sortables" class="meta-box-sortables ui-sortable"></div>
    </div>
    <div id="post-body" class="has-sidebar">
        <div id="post-body-content" class="has-sidebar-content">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                <div id="lim-for-wp-acl" class="postbox ">
                    <div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br /></div>
                    <h3 class="hndle"><span><?php _e('Set custom upload folder for \'Lim\' files', 'lim4wp_main'); ?></span></h3>
                    <div class="inside"><?php
                        require_once(ABSPATH . 'wp-admin/admin.php');
                        global $wpdb;
                        $upload_parameters = $wpdb->get_results("SELECT * FROM `" . L4WP_TABLE . ';', ARRAY_A);
                        $lim_ddbb_path = $upload_parameters[0]['upload_path'];
                        $lim_subdir_path = $upload_parameters[0]['upload_path_lim_subdir']; ?>
                        <p><span class="description"><?php
                            $str = __('Are you sure? This will invalidate all generated data!', 'lim4wp_main');
                            if (get_option('upload_path') === '') {
                                _e('If uploads folder parameter has <u>never</u> been <a href="options-media.php">set</a>, we will assume the default directory for uploads:', 'lim4wp_main'); ?>&nbsp;<code>wp-content/uploads</code><?php
                            } elseif (lim4wp_clean_path(get_option('upload_path')) != $lim_ddbb_path) {
                                $new_path = lim4wp_clean_path(get_option('upload_path'));
                                printf(__('Uploads folder parameter (%s) differs from Lim4WP stored settings (%s).', 'lim4wp_main'), '<code>'.$new_path.'</code>', '<code>'.$lim_ddbb_path.'</code>'); ?>
                                </span></p><p><span class="description"><?php _e('Correct this issue?', 'lim4wp_main'); ?>
                                <img alt="" style="visibility: hidden;" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" />
                                <input id="change_l4wp_upload_dir_1" type="button" class="button-primary" value="<?php _e('Yes'); ?>" onclick="jQuery(this).unbind('click');
                                    change_l4wp_uploads_dir('<?php echo L4WP_PLUGIN_URL; ?>', '<?php echo $lim_ddbb_path . '/' . $lim_subdir_path; ?>', '<?php echo $new_path; ?>', this, 'uploads_dir', '<?php echo $str; ?>');" />
                                </span></p><p><span class="description"><?php
                            } ?>
                        </span></p>
                        <p><?php
                            echo $lim_ddbb_path; ?>/&nbsp;
                            <input id="l4wp_upload_dir_2" type="text" class="code" value="<?php echo $lim_subdir_path; ?>" size="10" />
                            <img alt="" style="visibility: hidden;" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" />
                            <input id="change_l4wp_upload_dir_2" type="button" class="button-primary" value="<?php _e('Save Changes'); ?>" onclick="jQuery(this).unbind('click');
                                change_l4wp_uploads_dir('<?php echo L4WP_PLUGIN_URL; ?>', '<?php echo $lim_ddbb_path . '/' . $lim_subdir_path; ?>', '<?php echo $lim_ddbb_path; ?>', this, 'lim_subdir', '<?php echo $str; ?>');" />
                        </p>
                    </div>
                </div>
<!-- Table of LIMs management -->
<!--TODO-->
<!-- End Table -->
            </div><br />
        </div>
    </div>
    <br class="clear" />
</div>

</div><?php } ?>