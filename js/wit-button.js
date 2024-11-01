/*! Wigets In Tabs TinyMCE plugin
* Copyright (c) 2014-2016 - Anas H. Sulaiman
* Licensed under the GPL2 or later
*/

(function(){
	tinymce.PluginManager.requireLangPack('wit_button');

	tinymce.create('tinymce.plugins.wit_button', {
		init : function(ed, url) {

			/* This is expected from the PHP part */
			var areas = wit_mce_areas;
			var effects = wit_mce_effects;

 			ed.addButton('wit_button', {
 				text: "WIT",
                title : 'WIT Shortcode',
                cmd : 'wit_shortcode',
                icon : false,
            });

            ed.addCommand('wit_shortcode', function() {
            	ed.windowManager.open({
	                title: 'Widgets In Tabs Shortcode Options',
	                body: [
	                	{type: 'listbox', 
						    name: 'area', 
						    label: 'Area', 
						    'values': areas
						},
	                    {type: 'listbox', 
						    name: 'tab_style', 
						    label: 'Tab Style', 
						    'values': [
						        {text: 'Scrollbar', value: 'scroll'},
						        {text: 'Show All', value: 'show_all'}
						    ]
						},
						{type: 'listbox', 
						    name: 'height', 
						    label: 'Height', 
						    'values': [
						        {text: 'Adaptive', value: 'adaptive'},
						        {text: 'Fixed', value: 'fixed'}
						    ]
						},
	                    {type: 'textbox', name: 'interval', label: 'Interval', value: '0'},
						{type: 'listbox',
							name: 'hide_effect',
							label: 'Hide tab effect',
							'values': effects
						},
						{type: 'listbox',
							name: 'show_effect',
							label: 'Show tab effect',
							'values': effects
						},
						{type: 'listbox', 
						    name: 'effect_style', 
						    label: 'Effect style', 
						    'values': [
						        {text: 'Parallel', value: 'prll'},
						        {text: 'Sequential', value: 'seq'}
						    ]
						},
						{type: 'textbox', name: 'duration', label: 'Effect duration', value: '400'}
	                ],
	                onsubmit: function(e) {
	                    ed.insertContent(
	                    	'[wit' + 
	                    	' area="' + e.data.area + '"' +
	                    	' interval="' + e.data.interval + '"' +
	                    	' tab_style="' + e.data.tab_style + '"' +
	                    	' hide_effect="' + e.data.hide_effect + '"' +
	                    	' show_effect="' + e.data.show_effect + '"' +
	                    	' effect_style="' + e.data.effect_style + '"' +
	                    	' duration="' + e.data.duration + '"' +
	                    	' height="' + e.data.height + '"' +
	                    	' extra_closable_tabs="false"' +
	                    	' extra_closable_tabs_init="false"' +
	                    	' extra_remember_last="false"' +
	                    	']');
	                }
	            });
            });
        }
	});
	
	tinymce.PluginManager.add('wit_button', tinymce.plugins.wit_button);
})()