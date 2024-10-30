function change_l4wp_uploads_dir(l4wp_url, lims_path, wp_uploads_path, dis, what, str) {
    var url = l4wp_url + '/includes/adminOptions.php';
    if (confirm(str)) {
        var t = jQuery(dis);
        if (t.hasClass('button-primary'))   t.addClass('button-primary-disabled');
        else    t.addClass('button-disabled');
        t.prev('img').css('visibility', 'visible');
        if (what == 'uploads_dir')      var input_val = 0;
        else if (what == 'lim_subdir')  var input_val = t.prev().prev('#' + str_replace('change_', '', dis.id, 1)).val();
        jQuery.get( url,
                    {'lims_path' : lims_path, 'wp_uploads_path' : wp_uploads_path, 'input_val' : input_val},
                    function (data) {
                        if (data !== false) {
                            jQuery('#lim4wp_admin_options').load(url + '?let=me', function() {
                                t.prev('img').css('visibility', 'hidden');
                                if (t.hasClass('button-primary-disabled'))  t.removeClass('button-primary-disabled');
                            });
                        } else {
                            alert('error');
                            t.prev('img').css('visibility', 'hidden');
                            if (t.hasClass('button-primary-disabled'))  t.removeClass('button-primary-disabled');
                        }
                    });
    } else  jQuery('#lim4wp_admin_options').load(url + '?let=me');
}