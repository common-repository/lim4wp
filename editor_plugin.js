// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('l4wp');
	 
    tinymce.create('tinymce.plugins.l4wp', {
		
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('l4wp', function() {
                var se = ed.selection;
                ed.windowManager.open({
                    file : url + '/window.php',
                    width : 415,
                    height : 100,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });

            });

            // Register example button
            ed.addButton('l4wp', {
                title : 'Lim4WP button',
                cmd : 'l4wp',
                image : url + '/img/lim.png'
            });
			
            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('l4wp', n.nodeName == 'pre');
            });
        },

        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname  : 'l4wp',
                author 	  : 'Anboto',
                authorurl : 'http://anboto.tk',
                infourl   : 'http://wordpress.org/extend/plugins/lim4wp',
                version   : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('l4wp', tinymce.plugins.l4wp);
})();


