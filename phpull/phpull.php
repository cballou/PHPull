<?php
/*
Plugin Name: PHPull
Plugin URI: http://wordpress.org/extend/plugins/phpull/
Description: A plugin for web developers with php blogs.  Simply reference a php function using <code>[phpull class="DOMDocument"]getElementById[/phpull]</code> and PHPull will generate a tooltip containing pertinent function information.  The <code>class</code> attribute is optional and values are case insensitive.
Author: Corey Ballou
Version: 0.1
Author URI: http://www.jqueryin.com/
*/

#
#  Copyright (c) 2009 Corey Ballou (email: webmaster@jqueryin.com)
#
#  This file is part of PHPull
#
#  PHPull is free software; you can redistribute it and/or modify it under
#  the terms of the GNU General Public License as published by the Free
#  Software Foundation; either version 2 of the License, or (at your option)
#  any later version.
#
#  WPHPull is distributed in the hope that it will be useful, but WITHOUT ANY
#  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
#  FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
#  details.
#
#  You should have received a copy of the GNU General Public License along
#  with PHPull; if not, write to the Free Software Foundation, Inc.,
#  51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
#

require_once dirname(__FILE__) . '/includes/simple_html_dom.php';

class PHPull {

	private $pluginurl = '';

	/**
	 * Default constructor to bind actions.  The gruntwork is done by the
	 * add_shortcode method.
	 */
	function __construct() {

		// shortcodes only work in WP 2.5+
		if (!function_exists('add_shortcode')) return;

		// store the plugin URL
		$this->pluginurl = get_bloginfo('wpurl') . '/wp-content/plugins/phpull/';

		// register new shortcode with callback function
		add_shortcode('phpull', array(&$this, 'phpull_shortcode'));

		// load stylesheet
		add_action( 'wp_head', array(&$this, 'load_stylesheet'), 1000);
		add_action( 'admin_head', array(&$this, 'load_stylesheet'), 1000);

		// load phpull
		add_action( 'wp_footer', array(&$this, 'load_javascript'), 1000);

		// add to admin menu
		add_action( 'admin_menu', array(&$this, 'phpull_admin_menu') );

		// add the quicktag to the admin post menu
		add_action( 'edit_form_advanced', array(&$this, 'phpull_quicktag'), 1000);
		add_action( 'edit_page_form', array(&$this, 'phpull_quicktag'), 1000);

	}

	/**
	 * Add the configuration options page to the admin menu.
	 *
	 * @access public
	 * @return void
	 */
	function phpull_admin_menu() {
		if (function_exists('add_options_page'))
			add_options_page(__('PHPull Theme Selector'), __('PHPull'), 8, __FILE__, array(&$this, 'phpull_options'));
	}

	/**
	 * Loads the stylesheet in the header based on the user's preselected theme.
	 *
	 * @access public
	 * @return void
	 */
	function load_stylesheet() {
		$options = get_option('phpull_options');
		$options['phpull_theme'] = isset($options['phpull_theme']) ? $options['phpull_theme'] : 'default.css';
		echo '<link type="text/css" rel="stylesheet" href="' . $this->pluginurl . 'themes/' . attribute_escape($options['phpull_theme']) . '"></link>' . "\n";
	}

	/**
	 * Loads the javascript in the footer.
	 *
	 * @access public
	 * @return void
	 */
	function load_javascript() {
		echo '<script type="text/javascript" src="'. $this->pluginurl . 'js/phpull.js"></script>';
	}

	/**
	 * Finds all instances of the shortcode and replaces them with the tooltip link.
	 *
	 * @access 	public
	 * @param 	array 	$attrs
	 * @param 	string 	$content
	 * @return 	string
	 */
	function phpull_shortcode($attrs, $content = null) {
		// if no function is specified, return
		if (is_null($content)) return false;
		// make sure we only obtain class attribute
		extract(shortcode_atts(array('class'=>null, 'show'=>''), $attrs));
		// determine if returning display version or transformed version
		if ($show != '') {
			// return version for sample display
			return ($class != null) ? '[phpull class="' . htmlentities(str_replace('"', '', $class)) . '"]' . $content . '[/phpull]' : '[phpull]' . $content . '[/phpull]';
		} else {
			if (($class != null) && strpos($content, '::') === false) {
				$content = htmlentities($class) . '::' . $content;
			}
			// return replaced instance
			return '<a href="#" title="' . (($class != null) ? htmlentities($class) : '') . '" onmouseout="php_tooltip.hide();" onmouseover="php_tooltip.show(this);">' . $content . '</a>';
		}
	}

	/**
	 * Provides a configuration page for selecting a css theme from a dropdown.
	 *
	 * @access public
	 * @return void
	 */
	function phpull_options() {
		$options = get_option('phpull_options');

		if (!empty($_POST)) {
			check_admin_referer('update-options');
			$options['phpull_theme'] = $_POST['theme'];
			update_option('phpull_options', $options);
			echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
		}
		?>
		<h2><?php _e('PHPull Theme Selector'); ?>.</h2>

		<form action="" method="post" id="phpull" accept-charset="utf-8">
			<?php wp_nonce_field('update-options'); ?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="phpull_theme" />

			<table class="form-table">
				<tr valign="top">
					<th scope="row">Theme</th>
					<td>
						<select name="phpull_theme" id="phpull_theme">
						<?php
						$themes = scandir(ABSPATH . PLUGINDIR . '/phpull/themes/');
						foreach ((array) $themes as $theme) {
							if (substr($theme, -3) == 'css') {
								$selected = ($theme == $options['theme']) ? ' selected="selected"' : '';
								echo '<option value="' . attribute_escape($theme) . '"' . $selected . '>' . attribute_escape($theme) . '</option>'."\n";
							}
						}
						?>
						</select>&nbsp;
						<a href="http://www.jqueryin.com/phpull/themes" target="_blank">Preview Themes</a>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>"/></p>
		</form>
		<p>* To create your own theme:</p>
		<ol>
			<li>Copy the CSS from /wp-content/plugins/phpull/themes/default.css</li>
			<li>Modify the styles as needed in a new file.</li>
			<li>Save the CSS file in the /wp-content/plugins/phpull/themes/ directory.</li>
			<li>Refresh the PHPull Theme Selector page to switch to your new theme.</li>
		</ol>
		<?php
	}

	/**
	 * Adds a PHPull button to the admin blog post pages.
	 *
	 * @access public
	 * @return void
	 */
	public function phpull_quicktag() { ?>

<script type="text/javascript">
var phpull_openTag = false;
var tmce_toolbar = document.getElementById("ed_toolbar");
if (tmce_toolbar) {
	var php_qt = document.createElement('input');
	php_qt.type = 'button';
	php_qt.value = 'phpull';
	php_qt.onclick = phpull_onclick;
	php_qt.className = 'ed_button';
	php_qt.title = 'PHPull Quicktag';
	php_qt.id = 'ed_phpull';
	tmce_toolbar.appendChild(php_qt);
}

function phpull_onclick() {
	var phpull_content = document.getElementById('content');

	// IE support
	if (document.selection) {
		phpull_content.focus();
		var sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = '[phpull]' + sel.text + '[/phpull]';
		} else {
			if (!phpull_openTag) {
				sel.text = '[phpull]';
				phpull_openTag = true;
				document.getElementById('ed_phpull').value = '/' + document.getElementById('ed_phpull').value;
			} else {
				sel.text = '[/phpull]';
				phpull_openTag = false;
				document.getElementById('ed_phpull').value = document.getElementById('ed_phpull').value.replace('/', '');
			}
		}
		phpull_content.focus();
	}
	// MOZILLA/NETSCAPE support
	else if (phpull_content.selectionStart || phpull_content.selectionStart == '0') {
		var startPos = phpull_content.selectionStart;
		var endPos = phpull_content.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = phpull_content.scrollTop;

		if (startPos != endPos) {
			phpull_content.value = phpull_content.value.substring(0, startPos)
			              + '[phpull]'
			              + phpull_content.value.substring(startPos, endPos)
			              + '[/phpull]'
			              + phpull_content.value.substring(endPos, phpull_content.value.length);
			cursorPos += 17;
		} else {
			if (!phpull_openTag) {
				phpull_content.value = phpull_content.value.substring(0, startPos)
				              + '[phpull]'
				              + phpull_content.value.substring(endPos, phpull_content.value.length);
				phpull_openTag = true;
				document.getElementById('ed_phpull').value = '/' + document.getElementById('ed_phpull').value;
				cursorPos = startPos + 8;
			} else {
				phpull_content.value = phpull_content.value.substring(0, startPos)
				              + '[/phpull]'
				              + phpull_content.value.substring(endPos, phpull_content.value.length);
				phpull_openTag = false;
				document.getElementById('ed_phpull').value = document.getElementById('ed_phpull').value.replace('/', '');
				cursorPos = startPos + 9;
			}
		}
		phpull_content.focus();
		phpull_content.selectionStart = cursorPos;
		phpull_content.selectionEnd = cursorPos;
		phpull_content.scrollTop = scrollTop;
	} else {
		if (!phpull_openTag) {
			phpull_content.value += '[phpull]';
			phpull_openTag = true;
			document.getElementById('ed_phpull').value = '/' + document.getElementById('ed_phpull').value;
		} else {
			phpull_content.value += '[/phpull]';
			phpull_openTag = false;
			document.getElementById('ed_phpull').value = document.getElementById('ed_phpull').value.replace('/', '');
		}
		phpull_content.focus();
	}
	return false;
}
</script>

<?php
	}

}

// initialize
$phpull = new PHPull();
?>
