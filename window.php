<?php

if (isset($_GET['uploaded_size']))  die (json_encode(wp_size_unit($_GET['uploaded_size'])));

$wpconfig = realpath('../../../wp-config.php');

if (!file_exists($wpconfig))    die (sprintf('Error!! Could not found: %s', $wpconfig));

require_once($wpconfig);

require_once(ABSPATH . 'wp-admin/admin.php');

$plugin_data = get_plugin_data('lim4wp.php');

$max_upload_size =  wp_max_upload_size();

$wp_size_unit = wp_size_unit($max_upload_size);

function wp_size_unit($upload_size) {   // Returns 'size formatted'
    $sizes = array('KB', 'MB', 'GB');
    for ($u = -1; $upload_size > 1024 && $u < count($sizes) - 1; $u++)  $upload_size /= 1024;
    if ($u < 0) {
        $upload_size = 0;
        $u = 0;
    } else  $upload_size = (int) $upload_size;
    return array('size' => $upload_size, 'unit' => $sizes[$u]);
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $plugin_data['Name'].' v'.$plugin_data['Version']; ?></title>
<!-- 	<meta http-equiv="Content-Type" content="<?php //bloginfo('html_type'); ?>; charset=<?php //echo get_option('blog_charset'); ?>" /> -->
        <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
<!--        <script language="javascript" type="text/javascript" src="<?php //echo WP_PLUGIN_URL ?>/lim4wp/tinymce.js"></script>-->
<!--        <script language="javascript" type="text/javascript" src="<?php //echo WP_PLUGIN_URL ?>/lim4wp/lim4wp.js"></script>-->
        <base target="_self" /><?php
        wp_deregister_script('jquery');
        wp_register_script('lim4wp_jquery', ('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'), false, '');
        wp_enqueue_script('lim4wp_jquery');
//        wp_register_style('lim4wp_jq_ui_style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/smoothness/jquery-ui.css', array(), '1.8.7');
//        wp_enqueue_style('lim4wp_jq_ui_style');
//        wp_register_script('lim4wp_jq_ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js', array('lim4wp_jquery'), '1.8.7');
//        wp_enqueue_script('lim4wp_jq_ui');
        wp_register_style('lim4wp_uploadify_styles', L4WP_PLUGIN_URL . '/css/uploadify.css', array(), '2.1.4');
        wp_enqueue_style('lim4wp_uploadify_styles');
        wp_register_script('lim4wp_uploadify', L4WP_PLUGIN_URL . '/js/jquery.uploadify.v2.1.4.min.js', array('lim4wp_jquery'), '2.1.4');
        wp_enqueue_script('lim4wp_uploadify');
        wp_register_script('lim4wp_swf_oject', L4WP_PLUGIN_URL . '/js/swfobject.js', array('lim4wp_uploadify'), '2.1.4');
        wp_enqueue_script('lim4wp_swf_oject');
        wp_register_style('lim4wp_windows_engine_styles', L4WP_PLUGIN_URL . '/css/jquery.windows-engine.css', array(), '1.4');
        wp_enqueue_style('lim4wp_windows_engine_styles');
        wp_register_script('lim4wp_windows_engine', L4WP_PLUGIN_URL . '/js/jquery.windows-engine.js', array(), '1.4');
        wp_enqueue_script('lim4wp_windows_engine');
        wp_register_script('lim4wp_tinyPopup', L4WP_PLUGIN_URL . '/js/tinyPopup.js', array(), '1.0');
        wp_enqueue_script('lim4wp_tinyPopup');
        wp_register_style('lim4wp_tinyPopup_styles', L4WP_PLUGIN_URL . '/css/tinyPopup.css', array(), '1.0');
        wp_enqueue_style('lim4wp_tinyPopup_styles');
        wp_head();
        $upload_parameters = $wpdb->get_results("SELECT * FROM `" . L4WP_TABLE . ';', ARRAY_A);
        $l4wp_upload_path = $upload_parameters[0]['upload_path'] . '/' . $upload_parameters[0]['upload_path_lim_subdir'];
        if (lim4wp_mkdir(ABSPATH . $l4wp_upload_path) !== false)    $l4wp_upload_path = ABSPATH . $l4wp_upload_path;
        else    $l4wp_upload_path = false; ?>
        <script type="text/javascript">
            function abs2uri(param) { // Need to be revised
                return '<?php echo get_bloginfo('url') . '/'; ?>' + str_replace('<?php echo ABSPATH; ?>', '', param, 1);
            }
            function insert_html(html_a, count) {
                return '<img src="img/success.png" /></a>' + html_a + '&nbsp;<a id="object_' + count + '_insert" class="insert black" href="#"><?php
                    _e('Insert HTML', 'lim4wp_main'); ?></a></p><div id="object_' + count + '_insert_toggle" class="scroll_insert"><div><p>' +
                    '<input id="width_' + count + '" type="text" class="width_" size="3" value="800" />&nbsp;<?php _e('px width', 'lim4wp_main'); ?>' +
                    '</p><p><input id="full_screen_' + count + '" type="checkbox" class="f_screen_" checked="checked" /><?php
                    _e('Enable fullscreen', 'lim4wp_main'); ?></p></div><div style="margin-left:10%"><p><input id="height_' + count + '" type="text" ' +
                    'class="height_" size="3" value="600" />&nbsp;<?php _e('px height', 'lim4wp_main'); ?></p><p><input id="ins_button_' + count +
                    '" type="button" class="ins_button_" value="<?php _e('INSERT', 'lim4wp_main'); ?>" />&nbsp;&nbsp;<input id="preview_' + count +
                    '" type="button" class="preview_" value="<?php _e('PREVIEW', 'lim4wp_main'); ?>" /></p></div></div>';
            }
            function object_html(swf_url, lim_url, width, height, allowFullScreen) {
                return  '<object type="application/x-shockwave-flash" ' +
                            'data="' + swf_url + '?libro=' + basename(lim_url) +'" ' +
                            'width="' + width + '" ' +
                            'height="' + height + '" ' +
                            'align="top" ' +
                            'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0">' +
                            '<param name="allowScriptAccess" value="always" />' +
                            '<param name="movie" value="' + swf_url + '?libro=' + basename(lim_url) + '" />' +
                            '<param name="quality" value="high" />' +
                            '<param name="allowFullScreen" value="' + allowFullScreen + '" />' +
                            '<param name="FlashVars" value="libro=' + lim_url + '" />' +
                        '</object>';
            }
            function uploaded_data(data_i, name, count, err_str, lim_array) {
                var str_ddbb = '';
                if (name !== '')    data_i.name += '&nbsp;(' + name + ')';
                var text = '';
                var a = '&nbsp;<a id="black_' + count + '" class="black" href="' + abs2uri(data_i.path) + '" >' + data_i.name + '</a>';
                if (data_i.lim.length == 0) {
                    str_ddbb = '<?php _e('No \"lim\" file found', 'lim4wp_main'); ?>';
                    text += '<p class="fail"><img class="fail_img" src="img/fail.png" />' + a + '</p><div class="sentence">' + err_str + str_ddbb + '</div>';
                } else if (data_i.lim.length > 1) {
                    var data_j = new Object();
                    for (var i in data_i.lim) {
                        data_j.name = data_i.name;
                        data_j.path = data_i.path;
                        data_j.output = data_i.output;
                        data_j.lim = [data_i.lim[i]];
                        data_j.swf = [data_i.swf[i]];
                        var up_data = uploaded_data(data_j, basename(data_i.lim[i]), count, err_str, lim_array);
                        text += up_data.text;
                        count = up_data.count;
                    }
                } else if (data_i.lim.length == 1) {
                    text += '<p class="success"><a id="lim_' + count + '" class="success_a" href="' + abs2uri(data_i.lim[0]) + '" target="_blank">';
                    var html_a = a + ':&nbsp;<a id="object_' + count + '" class="success black" href="#"><?php _e('Generate HTML', 'lim4wp_main'); ?></a>';
                    jQuery.ajax({
                        url:        'includes/process_upload.php',
                        data:       { 'swf_path' : '<?php echo L4WP_PLUGIN_DIR; ?>/lim.swf', 'lim_path' : data_i.lim[0], 'lim_url' : abs2uri(data_i.lim[0]) },
                        dataType:   'json',
                        success:    function(ret_val) {
                                        if (ret_val.isOk == false) {
                                            str_ddbb = ret_val.message;
                                            text += '<img class="fail_img" src="img/fail.png" /></a>' + html_a + '</p>' +
                                                '<div class="sentence">' + err_str + str_ddbb + '</div>';
                                        } else if (parseInt(ret_val[0], 10) == -1) {
                                            str_ddbb = '<?php echo _e('No \"lim.swf\" file found in the plugin folder', 'lim4wp_main'); ?>';
                                            text += '<img class="fail_img" src="img/fail.png" /></a>' + html_a + '</p>' + '<div class="sentence">' +
                                                err_str + str_ddbb + ':<br />' + str_replace('<?php echo ABSPATH; ?>', '', ret_val[1].from, 1) + '</div>';
                                        } else if (parseInt(ret_val[0], 10) == 0) {
                                            str_ddbb = '<?php echo _e('Unable to copy \"lim.swf\"', 'lim4wp_main'); ?>';
                                            text += '<img class="fail_img" src="img/fail.png" /></a>' + html_a + '</p>' + '<div class="sentence">' +
                                                err_str + str_ddbb + ':<br />' + str_replace('<?php echo ABSPATH; ?>', '', ret_val[1].from, 1) + ' >> ' +
                                                str_replace('<?php echo ABSPATH; ?>', '', ret_val[1].to, 1) + '</div>';
                                        } else if (parseInt(ret_val[0], 10) == 1) {
                                            data_i.lim[0] = ret_val[2];
                                            data_i.swf[0] = ret_val[1].to;
                                            text += insert_html(html_a, count);
                                        }
                                    },
                        async:      false
                    });
                    lim_array[count] = object_html(abs2uri(data_i.swf[0]), abs2uri(data_i.lim[0]), '800', '600', 'true');
                    text += '<textarea id="object_' + count + '_success" class="scroll theEditor">' + lim_array[count] + '</textarea>';
                }
                text += '<div id="black_' + count + '_toggle" class="scroll theEditor"><strong><?php _e('Path', 'lim4wp_main'); ?>:</strong> ' +
                    str_replace('<?php echo ABSPATH; ?>', '', data_i.path, 1) + '<br /><strong><?php _e('Content', 'lim4wp_main'); ?>:</strong>';
                for (var j in data_i.output)    text += '<br />.' + data_i.output[j];
                text += '</div>';
                if (str_ddbb === '') {
                    var is_lim = '1';
                    var lim_html = lim_array[count];
                    var lim_err_str = '';
                } else {
                    var is_lim = '0';
                    var lim_html = '';
                    var lim_err_str = str_ddbb;
                }
                jQuery.ajax({
                    url:        'includes/upload.php',
                    data:       { 'is_lim' : is_lim, 'path' : data_i.path, 'html' : lim_html, 'err_str' : lim_err_str },
                    dataType:   'text',
                    success:    function(ret_val) {
                                    if (parseInt(ret_val, 10) == 0) alert('<?php _e('ERROR! database table connection has failed!!', 'lim4wp_main'); ?>');
                                    jQuery('#writing').html('');
                                },
                    async:      false
                });
                var r = new Object();
                r.count = ++count;
                r.text = text;
                return r;
            }
            function upload_result(data, lim_array) {
                jQuery.getJSON('includes/process_upload.php', { 'ext' : 'lim' }, function(data) {
                    var textToInsert = '';
                    var err_str = '<span id="_err"><?php _e('Error:', 'lim4wp_main'); ?></span>&nbsp;';
                    var count = jQuery('.black').size();
                    for (var i in data) {
                        jQuery('#writing').append('<img alt="" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" />&nbsp;<?php _e('Populating database, wait', 'lim4wp_main'); ?>...');
//                        console.log(data[i].lim.length + ' ficheros encontrados:');
//                        for (var k in data[i].lim)   console.log(data[i].lim[k]);
                        var up_data = uploaded_data(data[i], '', count, err_str, lim_array);
                        textToInsert += up_data.text;
                        count = up_data.count;
                    }
                    jQuery('#div_body').append(textToInsert);
                    if (jQuery('.scroll_insert:first').is('div')) {
                        jQuery('.scroll_insert').hide();
                        jQuery('.scroll_insert:first').show();
                    }
                    jQuery('.black').unbind('click').click(function() {
                        if (jQuery(this).hasClass('insert')) {
                            if (jQuery('#' + this.id + '_toggle').is(':hidden')) {
                                jQuery('.scroll, .scroll_insert').animate({ opacity: 'hide' }, 200);
                                jQuery('#' + this.id + '_toggle').animate({ opacity: 'show' }, 400);
                            } else  jQuery('#' + this.id + '_toggle').animate({ opacity: 'hide' }, 200);
                        } else if (jQuery(this).hasClass('success')) {
                            if (jQuery('#' + this.id + '_success').is(':hidden')) {
                                jQuery('.scroll, .scroll_insert').animate({ opacity: 'hide' }, 200);
                                jQuery('#' + this.id + '_success').animate({ opacity: 'show' }, 400);
                            } else  jQuery('#' + this.id + '_success').animate({ opacity: 'hide' }, 200);
                        } else if (jQuery('#' + this.id + '_toggle').is(':hidden')) {
                            jQuery('.scroll, .scroll_insert').animate({ opacity: 'hide' }, 200);
                            jQuery('#' + this.id + '_toggle').animate({ opacity: 'show' }, 400);
                        } else  jQuery('#' + this.id + '_toggle').animate({ opacity: 'hide' }, 200);
                        return false;
                    });
                    jQuery('.ins_button_').unbind('click').click(function() {
                        var id = str_replace('ins_button_', '', this.id, 1);
                        var lim_obj = jQuery(lim_array[id]);
                        lim_obj.attr('width', jQuery('#width_' + id).val());
                        lim_obj.attr('height', jQuery('#height_' + id).val());
                        lim_obj.find('param').each(function() {
                            if (!jQuery('#full_screen_' + id).is(':checked') && this.name == 'allowFullScreen') jQuery(this).attr('value', 'false');
                        });
                        tinyMCEPopup.editor.execCommand('mceInsertRawHTML', true,   '<object type="' + lim_obj.attr('type') + '" ' +
                                                                                        'data="' + lim_obj.attr('data') + '" ' +
                                                                                        'width="' + lim_obj.attr('width') + '" ' +
                                                                                        'height="' + lim_obj.attr('height') + '" ' +
                                                                                        'align="' + lim_obj.attr('align') + '" ' +
                                                                                        'codebase="' + lim_obj.attr('codebase') + '">' +
                                                                                        lim_obj.clone().remove().html() +
                                                                                    '</object>');
                    });
                    jQuery('.preview_').unbind('click').click(function() {
                        var _id = str_replace('preview_', '', this.id, 1);
                        var lim_obj = jQuery(lim_array[_id]);
                        jQuery.newWindow( { id: 'preview',
                                            posx: 0,
                                            posy: 0,
                                            width: parseInt(lim_obj.attr('width'), 10) + 20,
                                            height: parseInt(lim_obj.attr('height'), 10) + 20,
                                            title: '<?php _e('Lim Preview', 'lim4wp_main'); ?>',
                                            type: 'text'
                                        } );
                        jQuery.updateWindowContent( 'preview',  '<object type="' + lim_obj.attr('type') + '" ' +
                                                                    'data="' + lim_obj.attr('data') + '" ' +
                                                                    'width="' + lim_obj.attr('width') + '" ' +
                                                                    'height="' + lim_obj.attr('height') + '" ' +
                                                                    'align="' + lim_obj.attr('align') + '" ' +
                                                                    'codebase="' + lim_obj.attr('codebase') + '">' +
                                                                    lim_obj.clone().remove().html() +
                                                                '</object>');
//                        if (parseInt(tinyMCEPopup.getWindowArg('mce_height')) < parseInt(lim_obj.attr('height'), 10))    tinyMCEPopup.editor.windowManager.resizeBy(0, 400, tinyMCEPopup.id);
//                        if (parseInt(tinyMCEPopup.getWindowArg('mce_width')) < parseInt(lim_obj.attr('width'), 10))    tinyMCEPopup.editor.windowManager.resizeBy(420, 0, tinyMCEPopup.id);

                    });
                });
            }
            function error_result(error_obj) {
//                console.log(error_obj);
            }
            jQuery(document).ready(function() {
                jQuery('body').load(function() {
                    tinyMCEPopup.executeOnLoad('init();');
                    document.body.style.display='';
                });
                var lim_array = new Object();
                var error_obj = new Object();
                var error_count = 0;
                jQuery('#flash-browse-button').uploadify({
                    'uploader': '<?php echo L4WP_PLUGIN_URL; ?>/includes/uploadify.swf',    //Default = 'uploadify.swf'.
                    'script': '<?php echo L4WP_PLUGIN_URL; ?>/includes/upload.php', //Default = 'uploadify.php'.
//                    'checkScript': '',    //No Default. 'check.php' is provided with core files.
//                    'fileDataName': '',   //Default = 'Filedata'.
//                    'method': '', //Either 'GET' or 'POST'. Default is set to 'POST'.
//                    'scriptAccess': '',   //Default = 'sameDomain'.
                    'scriptData': { 'session': '<?php echo session_id();?>' },
                    'folder': '<?php echo $l4wp_upload_path; ?>',  //The path to the folder you would like to save the files to. Do not end the path with a '/'.
//                    'queueID': '',    //The ID of the element you want to use as your file queue. By default, one is created on the fly below the 'Browse' button.
//                    'queueSizeLimit': '', //Default = 999.
                    'multi': true,  //Set to true if you want to allow multiple file uploads.
                    'auto': true,   //Set to true if you would like the files to be uploaded when they are selected.
                    'fileDesc': '<?php _e('Zip files', 'lim4wp_main'); ?>',  //The text that will appear in the file type drop down at the bottom of the browse dialog box.
                    'fileExt': '*.zip;*.Zip;*.ZIP', //Format like '*.ext1;*.ext2;*.ext3'.
                    'sizeLimit': '<?php echo $max_upload_size; ?>', //A number representing the limit in bytes for each upload.
                    'simUploadLimit': '2',  //A limit to the number of simultaneous uploads you would like to allow. Default: 1.
                    'buttonText': '<?php _e('Select Zip files', 'lim4wp_main'); ?>',
                    'buttonImg': '',    //<?php //echo L4WP_PLUGIN_URL; ?>/img/upload.png', //The path to the image you will be using for the browse button.
                    'hideButton': false,    //Set to true if you want to hide the button image.
                    'rollover': false,
                    'width': '130',
//                    'height': '40',
                    'wmode': 'transparent',
                    'cancelImg': '<?php echo L4WP_PLUGIN_URL; ?>/img/cancel.png',
//                    'onSelect':   function (event, queueID, fileObj) { },
//                    'onProgress': function (event, queueID, fileObj, response, data) { },
                    'onSelectOnce': function (event, data) {
                                        if (jQuery('#upload_result_string').is('div'))  jQuery('#upload_result_string').remove();
                                        var h = tinyMCEPopup.getWindowArg('mce_height') + (jQuery('.uploadifyQueueItem:first').height() * data.fileCount);
                                        tinyMCEPopup.editor.windowManager.resizeBy(0, h, tinyMCEPopup.id);
                                    },
                    'onError':  function (event, queueID, fileObj, errorObj) {
                                    error_count++;
                                    error_obj[error_count] = new Object();
                                    error_obj[error_count].id = queueID;
                                    error_obj[error_count].name = fileObj.name;
                                    if (errorObj.type === 'File Size') {
                                        error_obj[error_count].type = '<?php _e('File Size Error', 'lim4wp_main'); ?>';
//                                        error_obj[error_count].info = String(Math.round(parseFloat(parseInt(errorObj.info, 10) / 1024 / 1024) * 100) / 100) + 'MB';
                                        error_obj[error_count].info = '<?php printf(__('Max: %d%s', 'lim4wp_main'), $wp_size_unit['size'], $wp_size_unit['unit']); ?>';
                                    } else {
                                        error_obj[error_count].type = str_replace('%s', errorObj.type, '<?php _e('%s Error', 'lim4wp_main'); ?>', 1);
                                        error_obj[error_count].info = str_replace('%s', str_replace('Error ', '', errorObj.info, 1), '<?php _e('Code %s', 'lim4wp_main'); ?>', 1);
                                    }
//                                    console.log(error_obj);
                                },
                    'onComplete':   function (event, queueID, fileObj, response, data) { if (response !== '1')  alert(response); },
                    'onAllComplete':    function (event, data) {
                                            for (var i in error_obj)    jQuery('#flash-browse-button' + error_obj[i].id).find('.percentage').text(' - ' + error_obj[i].type + ' (' + error_obj[i].info + ')');
                                            if (error_count == 0) {
                                                jQuery.getJSON('window.php?uploaded_size=' + data.allBytesLoaded, function(size) {
                                                    if (data.filesUploaded == 1)    var string = str_replace('%1s', size.size + size.unit, '<?php _e('Has uploaded a %1s file', 'lim4wp_main'); ?>', 1);
                                                        else    var string = str_replace('%1s', data.filesUploaded, str_replace('%2s', size.size + size.unit, '<?php _e('%1s files have uploaded<br />with a total weight of %2s', 'lim4wp_main'); ?>', 1), 1);
                                                    jQuery('<div id="upload_result_string">' + string + '</div>').insertAfter('#flash-browse-buttonUploader');
                                                    upload_result(data, lim_array);
                                                });
                                            } else if (error_count > 0 && data.filesUploaded > 0) {
                                                jQuery.getJSON('window.php?uploaded_size=' + data.allBytesLoaded, function(size) {
                                                    if (data.filesUploaded == 1)    var string = str_replace('%1s', size.size + size.unit, str_replace('%2s', error_count, '<?php _e('Has uploaded a %1s file and<br />has failed uploading %2s file(s)', 'lim4wp_main'); ?>', 1), 1);
                                                    else    var string = str_replace('%1s', data.filesUploaded, str_replace('%2s', size.size + size.unit, str_replace('%3s', error_count, '<?php _e('%1s files have uploaded with a total weight<br />of %2s and has failed uploading %3s file(s)', 'lim4wp_main'); ?>', 1), 1), 1);
                                                    jQuery('<div id="upload_result_string">' + string + '</div>').insertAfter('#flash-browse-buttonUploader');
                                                    upload_result(data, lim_array);
                                                    error_result(error_obj);
                                                });
                                            } else if (error_count > 0) {
                                                var string = str_replace('%s', error_count, '<?php _e('Failed to upload %s file(s)!', 'lim4wp_main'); ?>', 1);
                                                jQuery('<div id="upload_result_string">' + string + '</div>').insertAfter('#flash-browse-buttonUploader');
                                                error_result(error_obj);
                                            }
                                        }
                });
            });
        </script>
    </head>
    <body style="background: none">
        <div class="wrap"><?php
        if ($l4wp_upload_path !== false) { ?>
            <p class="media-upload-size"><?php
                _e('Choose files to upload').printf(' ('.__('Maximum upload file size: %d%s').')', $wp_size_unit['size'], $wp_size_unit['unit']); ?>
            </p>
            <div id="upload_div">
                <div id="flash-browse-button"><?php
                    _e('There is an error with the upload applet', 'lim4wp_main'); ?>
                </div>
            </div><?php
        } else { ?>
            <p class="media-upload-size"><?php
                printf(__('Uploads directory %s does not have writing permissions!', 'lim4wp_main'), ABSPATH . $upload_parameters[0]['upload_path'] . '/' . $upload_parameters[0]['upload_path_lim_subdir']); ?>
            </p><?php
        } ?><div id="div_body"><p id="writing" class="search-box"></p></div>
        </div>
    </body>
</html>