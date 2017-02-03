// Plucin static class
var TinyMCE_CodeHighLightPlugin = {
	getInfo : function() {
		return {
			longname : 'CodeHighLight',
			author : 'loki',
			authorurl : 'http://blog.lokizone.com',
			version : 0.1
		};
	},

	/**
	 * Returns the HTML contents of the emotions control.
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "codehighlight":
				return tinyMCE.getButtonHTML(cn, 'lang_codehighlight_desc', '{$pluginurl}/images/codehighlight.gif', 'mceCodeHighLight');
		}

		return "";
	},


	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceCodeHighLight":
				var template = new Array();

				template['file'] = '../../plugins/codehighlight/codehighlight.php'; // Relative to theme
				template['width'] = 600;
				template['height'] = 550;

				tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});

				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('codehighlight', TinyMCE_CodeHighLightPlugin);
