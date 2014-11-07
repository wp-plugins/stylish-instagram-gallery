<?php
/**
Plugin Name:Stylish Instagram Gallery 
Plugin URI:http://plugin.freelancezonebd.com/instagram-gallery/
Description: This plugin is used for adding instagram image gallery into your wordpress site.It's easy to use,user friendly,completely responsive and have 4 styles.
Author: Farjana Rashid
Version: 1.0
Author URI: http://farjana-rashid.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

Class fr_instagramGallery{
	function __construct(){	
	//Register plugin stylesheets
	add_action('wp_enqueue_scripts',array($this,'register_fr_plugin_style'));	
	//Register plugin Javascripts
	add_action('wp_enqueue_scripts',array($this,'register_fr_plugin_js'));
	
	//add option page
	add_action('admin_menu',array($this,'fr_plugin_settings_page'));
	add_action('admin_init',array($this,'fr_register_settings'));
	
	//Enable widget shortcode 
	add_filter('widget_text', 'do_shortcode');
	
	//active  gallery
	add_action('wp_head',array($this,'fr_instagram_gallery_active'));
	
	//Display team members
	add_shortcode('fr-instagram-gallery',array($this,'fr_instagram_gallery_view'));
	
	}
	
	public function register_fr_plugin_style(){
		wp_enqueue_style('fr-instagram-gallery-style',plugins_url( '/css/fr-instafeed-style.css', __FILE__ ));
		wp_enqueue_style('fr-instagram-gallery-style-responsive',plugins_url( '/css/fr-instagram-plugin-responsive.css', __FILE__ ));
	}
	public function register_fr_plugin_js(){
	    wp_enqueue_script('jquery');
		wp_enqueue_script( 'fr-instagram_gallery-js', plugins_url( '/js/fr-instafeed.min.js', __FILE__ ), array('jquery'), 1.0, false);
	}
	
	function fr_plugin_settings_page() {
		// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
		add_options_page( 'Instagram Plugin', 'Instagram Gallery', 'manage_options', __FILE__, array($this, 'fr_create_plugin_settings_page') );
	}
 
	function fr_create_plugin_settings_page() {
		?>
		<div class="wrap">
		<?php screen_icon(); ?>
			<h2> Instagram Settings</h2>
		 
			<form method="post" action="options.php">
			<?php
			// settings_fields( $option_group )
			settings_fields( 'fr-main-settings-group' );
			// do_settings_sections( $page )
			do_settings_sections( 'fr-plugin-main-settings-section' );
			?>
			<?php submit_button('Save Changes'); ?>
			</form>
		</div>
		<?php
	}
	
	//Settings API
	function fr_register_settings() { 
		// add_settings_section( $id, $title, $callback, $page )
		add_settings_section(
		'fr-main-settings-section',
		'',
		array($this, 'fr_print_main_settings_section_info'),
		'fr-plugin-main-settings-section'
		);
		 
		// add_settings_field( $id, $title, $callback, $page, $section, $args )
		add_settings_field(
		'fr_user_id',
		'User ID',
		array($this, 'fr_create_input_some_setting'),
		'fr-plugin-main-settings-section',
		'fr-main-settings-section'
		);
		add_settings_field(
		'fr_accessToken',
		'Access Token',
		array($this, 'fr_create_input_some_setting2'),
		'fr-plugin-main-settings-section',
		'fr-main-settings-section'
		);
		add_settings_field(
		'fr_client_id',
		'Client ID',
		array($this, 'fr_create_input_some_setting3'),
		'fr-plugin-main-settings-section',
		'fr-main-settings-section'
		);	
		add_settings_field(
		'fr_item_limit',
		'Show image No',
		array($this, 'fr_create_input_some_setting4'),
		'fr-plugin-main-settings-section',
		'fr-main-settings-section'
		);
		 
		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( 'fr-main-settings-group', 'fr_instagram_plugin_main_settings_arraykey', array($this, 'fr_plugin_main_settings_validate') );
	}
 
	function fr_print_main_settings_section_info() {
		echo '<p>Add your Instagram Info here.</p><p>You can use this Instagram Gallery in your pages or posts or widgets by using this shortcode: [instagram-gallery]</p>';
	}
 
	function fr_create_input_some_setting() {
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		?><label for="blog_public"><input type="text" name="fr_instagram_plugin_main_settings_arraykey[fr_user_id]" value="<?php echo $options['fr_user_id']; ?>" /></label>
			<p class="description"> Example: 262351</p><?php
	}
	function fr_create_input_some_setting2() {
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		?><label for="blog_public"><input type="text" name="fr_instagram_plugin_main_settings_arraykey[fr_accessToken]" value="<?php echo $options['fr_accessToken']; ?>" /></label>
			<p class="description"> Example : 262351.467ede5.176ab1984b1d47e6b8dea518109d7a5e</p><?php
	}
 	function fr_create_input_some_setting3() {
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		?><label for="blog_public"><input type="text" name="fr_instagram_plugin_main_settings_arraykey[fr_client_id]" value="<?php echo $options['fr_client_id']; ?>" /></label>
			<p class="description"> Example : f7f319ceb411486593db14897291810</p><?php
	}
  	function fr_create_input_some_setting4() {
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		?><label for="blog_public"><input type="text" name="fr_instagram_plugin_main_settings_arraykey[fr_item_limit]" value="<?php echo ($options['fr_item_limit'])?$options['fr_item_limit']:60; ?>" /></label>
			<p class="description">You can change Limit.</p><?php
	}
 
	function fr_plugin_main_settings_validate($arr_input) {
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		$options['fr_user_id'] = trim( $arr_input['fr_user_id'] );
		$options['fr_accessToken'] = trim( $arr_input['fr_accessToken'] );
		$options['fr_client_id'] = trim( $arr_input['fr_client_id'] );
		$options['fr_item_limit'] = trim( $arr_input['fr_item_limit'] );
		return $options;
	}
	
	//Activate Gallery 
	public function fr_instagram_gallery_active(){
		$options = get_option('fr_instagram_plugin_main_settings_arraykey');
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {		 
					 var feed = new Instafeed({
					get: 'user',
					userId: <?php echo $options['fr_user_id']; ?>,//262351
					template: '<a href="{{link}}" target="_blank"><img src="http:{{image}}" /></a>',
					accessToken: '<?php echo $options['fr_accessToken']; ?>',//262351.467ede5.176ab1984b1d47e6b8dea518109d7a5e
					clientId: '<?php echo $options['fr_client_id']; ?>',//f7f319ceb411486593db14897291810
					limit: '<?php echo ($options['fr_item_limit'])?$options['fr_item_limit']:60; ?>'//60
				});
				feed.run();	 
				});
			</script>
		<?php
	} 
	
	//shortcode
	public function fr_instagram_gallery_view($atts){
	
			extract ( shortcode_atts ( array (
				'style' => 'default'
			), $atts, 'fr-instagram-gallery') );
			
		return "<div id=\"instafeed\" class=\"$style\"></div>";
	}
}

new fr_instagramGallery;
?>