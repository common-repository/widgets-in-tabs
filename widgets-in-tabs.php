<?php
/*
 * Plugin Name: Widgets In Tabs
 * Plugin URI: http://wordpress.org/plugins/widgets-in-tabs/
 * Description: Show all the widgets you love, forget about clutter.
 * Author: Anas H. Sulaiman
 * Version: 2.2.1
 * Author URI: 
 * Text Domain: wit
 * Domain Path: /langs/
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

define( 'WIT_VERSION', '2.2.1' );

// Register WIT
add_action('widgets_init', 'register_wit');
function register_wit() {
	 register_sidebar(array(
        'id' => 'wit_area',
        'name' => __('Widgets In Tabs', 'wit'),
        'description'   => __('Add widgets here to show them in WIT Widget. If you put WIT widget here, bad things will happen!', 'wit'),
        'before_widget' => '<li id="%1$s" class="%2$s wit-tab-content">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="wit-tab-title">',
        'after_title' => '</h3>'
        )
	 );
	 // register extra widgets
	 $areas = trim(get_option('wit_areas'));
		if ($areas !== "") {
			$areas = explode('+', $areas);
			for ($i=0; $i < count($areas); $i++) {
				$area_id = preg_replace("/\s+/", "-", sanitize_title(trim($areas[$i])));
				$area_name = preg_replace("/\s+/", " ", $areas[$i]);
				register_sidebar(array(
			        'id' => 'wit_area-' . $area_id,
			        'name' => 'WIT - ' . $area_name,
			        'description'   => __('Add widgets here to show them in WIT Widget. If you put WIT widget here, bad things will happen!', 'wit'),
			        'before_widget' => '<li id="%1$s" class="%2$s wit-tab-content">',
			        'after_widget' => '</li>',
			        'before_title' => '<h3 class="wit-tab-title">',
			        'after_title' => '</h3>'
			        )
				 );
			}
		}

	register_widget('Widgets_In_Tabs');

	add_action('admin_menu', 'wit_admin_menu');
}
add_action( 'plugins_loaded', 'wit_load_textdomain' );
function wit_load_textdomain() {
	load_plugin_textdomain( 'wit', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}

function wit_admin_menu() {
	add_theme_page("Widgets In Tabs", "WIT", 'edit_themes', basename(__FILE__), 'wit_settings_form');
	add_action('admin_enqueue_scripts', 'wit_settings_form_assets');
}

function wit_settings_form() {
	$updated = false;
	if (isset($_POST['wit_areas'])) {
		/* update areas */
		update_option('wit_areas', $_POST['wit_areas']);
		$updated = true;
	}
	?>
	<h2><?php _e("Widgets In Tabs Areas","wit") ?></h2>
	<p><?php _e("From here you can add as many extra WIT areas as you want.","wit") ?></p>
	<button class="button-primary" id="wit-add-new"><?php _e("Add New Area","wit") ?></button>
	<ul id="wit-area-list">
		<li class="wit-area-template" style="display: none;">
			<span class="wit-area-name"><?php _e("Area","wit") ?></span>
			<input type="text" value="Area" style="display: none;">
			<span class="wit-area-remove" title="<?php _e("Remove this area","wit") ?>"><?php _e("Delete","wit") ?></span>
			<span class="wit-area-edit" title="<?php _e("Edit area name","wit") ?>"><?php _e("Edit","wit") ?></span>
		</li>
		<?php
		$areas = trim(get_option('wit_areas'));
		if ($areas !== "") {
			$areas = explode('+', $areas);
			for ($i=0; $i < count($areas); $i++) { 
				?>
				<li class="wit-area">
					<span class="wit-area-name"><?php echo $areas[$i]; ?></span>
					<input type="text" value="Area" style="display: none;">
					<span class="wit-area-remove" title="<?php _e("Remove this area","wit") ?>"><?php _e("Delete","wit") ?></span>
					<span class="wit-area-edit" title="<?php _e("Edit area name","wit") ?>"><?php _e("Edit","wit") ?></span>
				</li>
				<?php
			}
		}
		?>
	</ul>
	<p class="wit-error" style="display: none;"><?php _e("Each area must have a different name! And don't use the + sign!", "wit") ?></p>
	<p class="wit-success" style="<?php if ($updated) echo 'display: block;'; else echo 'display: none;'; ?>"><?php _e("Changes saved successfully", "wit") ?></p>
	<form id="wit-form" method="post" action="">
		<input type="hidden" name="wit_areas" value="">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'wit') ?>">
	</form>
	<?php
}

function wit_settings_form_assets($hook) {
	if( strpos($hook, 'widgets-in-tabs') === false )
        return;
	wp_register_style('wit_settings_form', plugins_url( 'css/wit-settings-form.min.css', __FILE__ ), array(), WIT_VERSION);
	wp_enqueue_style( 'wit_settings_form' );

	wp_register_script('wit_settings_form', plugins_url( 'js/wit-settings-form.min.js', __FILE__ ), array('jquery'), WIT_VERSION, true);
	wp_enqueue_script('wit_settings_form');
}

// WIT class
class Widgets_In_Tabs extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Widgets_In_Tabs',
			__('Widgets In Tabs', 'wit'),
			array( 'description' => __( 'Show all the widgets you love, forget about clutter.', 'wit' ) )
		);

		add_shortcode( 'wit', array( $this, 'wit_shortcode' ) );
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'wit_go_to_widgets_link' ) );

		// Register assets
		if ( is_active_widget( false, false, $this->id_base ) && !is_admin()) {
			add_action( 'wp_print_styles', array( $this, 'enqueue_style' ) );
			add_action( 'wp_print_scripts', array( $this, 'enqueue_scripts' ) );
		}
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ));
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ));

		// Register shortcode button
		if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'shortcode_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'shortcode_mce_plugin' ) );
		}
		add_action( 'admin_print_footer_scripts', array ( $this, 'shortcode_quicktag' ) );

		// Get registered WIT areas
		foreach($GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			if (strpos($sidebar['id'], 'wit_area') === 0) {
				$this->wit_areas_ids[] = $sidebar['id'];
			}
		}

		// Send TinyMCE some options
		add_action ( "wp_enqueue_editor", array( $this, 'shortcode_mce_options' ) );
	}

	public function enqueue_style() {
		wp_enqueue_style( 
			'wit', 
			plugins_url( 'css/wit-all.min.css', __FILE__ ), 
			array(), 
			WIT_VERSION
		);
	}

	public function enqueue_scripts($isAnimate = false) {
		$isAnimate = $isAnimate || $this->isActiveWithAnimation();

		$dep = array();
		if ($isAnimate) {
			$dep = array(
				'jquery',
				'jquery-effects-blind',
				'jquery-effects-bounce',
				'jquery-effects-clip',
				'jquery-effects-drop',
				'jquery-effects-explode',
				'jquery-effects-fade',
				'jquery-effects-fold',
				'jquery-effects-pulsate',
				'jquery-effects-scale',
				'jquery-effects-shake',
				'jquery-effects-puff',
				'jquery-effects-slide'
			);
		} else {
			$dep = array('jquery');
		}

		if (!wp_script_is( 'wit', 'enqueued' )) {
			$l10n = array(
				'string_untitled' => __('Untitled', 'wit')
				);
			wp_localize_script('wit', 'WIT_L10N', $l10n);
			wp_localize_script('wit', 'WIT_DEFAULTS', $this->defaults);
		}

		wp_enqueue_script( 
			'wit',
			 plugins_url( 'js/wit-all.min.js', __FILE__ ),
			 $dep,
			 WIT_VERSION,
			 true
		);
	}

	public function enqueue_admin_styles($hook) {
		if ('widgets.php' != $hook)
			return;

		wp_enqueue_style( 
			'wit_admin', 
			plugins_url( 'css/wit-admin.min.css', __FILE__ ), 
			array(), 
			WIT_VERSION 
		);
	}

	public function enqueue_admin_scripts($hook) {
		if ('widgets.php' != $hook)
			return;
		
		wp_enqueue_script( 
			'wit-admin', 
			plugins_url( 'js/wit-admin.min.js', __FILE__ ), 
			array('jquery'), 
			WIT_VERSION, 
			true 
		);
	}

	public function wit_go_to_widgets_link($actions) {
		return array_merge(
			array( 'settings' => sprintf( '<a href="%s">%s</a>', 'widgets.php', __( 'Go to Widgets', 'wit' ) ) ),
			$actions
		);
	}

	public function shortcode_mce_button( $buttons ) {
		array_push( $buttons, '|', 'wit_button' );
		return $buttons;
	}

	public function shortcode_mce_plugin( $plugins ) {
		$plugins['wit_button'] = plugins_url( 'js/wit-button.min.js', __FILE__ );
		return $plugins;
	}

	public function shortcode_quicktag() {
	    if (wp_script_is('quicktags')){
			?>
		    <script type="text/javascript">
			    QTags.addButton( 'wit_quicktag', '[wit]', '[wit]', '', 'w', 'WIT Widget' );
		    </script>
			<?php
	    }
	}

	public function shortcode_mce_options() {
		$tinymce_effects = "[\n";
		foreach ($this->effects as $key => $value) {
			$tinymce_effects .= "{text: '$value', value: '$key'},\n";
		}
		$tinymce_effects = substr($tinymce_effects, 0, strlen($tinymce_effects) -2) ."\n]";

		$areas = "[\n";
		foreach ($this->wit_areas_ids as $id) {
			$txt = $GLOBALS['wp_registered_sidebars'][$id]['name'];
			$areas .= "{text: '$txt', value: '$id'},\n";
		}
		$areas = substr($areas, 0, strlen($areas) -2) ."\n]";

		?>
			<!-- WIT TinyMCE Options { -->
			<script type='text/javascript'>
			/* <![CDATA[ */
				var wit_mce_areas = <?php echo $areas ?>;
				var wit_mce_effects = <?php echo $tinymce_effects ?>;
			/* ]]> */
			</script>
			<!-- } WIT TinyMCE Options -->
		<?php
	}

	public function wit_shortcode( $atts ) {
		// example:
		// [wit interval='3' tab_style='scroll']
		 
		$atts = shortcode_atts( $this->defaults, $atts );
		$instance = 
			"area={$atts['area']}".
			"&interval={$atts['interval']}".
			"&tab_style={$atts['tab_style']}".
			"&hide_effect={$atts['hide_effect']}".
			"&show_effect={$atts['show_effect']}".
			"&effect_style={$atts['effect_style']}".
			"&duration={$atts['duration']}".
			"&height={$atts['height']}".
			"&extra_closable_tabs={$atts['extra_closable_tabs']}".
			"&extra_closable_tabs_init={$atts['extra_closable_tabs_init']}".
			"&extra_remember_last={$atts['extra_remember_last']}";
		$args = array(
			'before_widget' => '<div id="' . $atts['id'] . '" class="widget widget_widgets_in_tabs witsc">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="wit-title">',
			'after_title' => '</h2>'
			);

		$this->enqueue_style();
		$this->enqueue_scripts(
			strpos($atts['hide_effect'], 'classic') !== 0 || 
			strpos($atts['show_effect'], 'classic') !== 0
		);

		ob_start();
		the_widget( 'Widgets_In_Tabs', $instance, $args );
		return ob_get_clean();
	}

	/**
	 * Front-end display of Widgets In Tabs.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$options = wp_parse_args((array) $instance, $this->defaults);
		$data_string = "";
		$area = $options['area'];
		unset($options['area']);
		unset($options['conditions']); // For Jetpack Widget Visibility Module
		foreach ($options as $key => $value) {
			$data_string .= "data-$key=\"$value\" ";
		}

		$title = __('Widgets In Tabs', 'wit');

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo "<ul class=\"wit-tab-container\" $data_string>";
		dynamic_sidebar($area);
		echo '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Back-end Widgets In Tabs form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args((array) $instance, $this->defaults);

		?>
		<p><?php _e('All widgets added to the selected area will appear as tabs in place of this widget.', 'wit' ); ?></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'area' ); ?>"><?php _e( 'Show widgets from the following area' , 'wit'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'area' ); ?>" name="<?php echo $this->get_field_name( 'area' ); ?>" >
				<?php
				if (empty($this->wit_areas_ids)) {
					?>
					<option><?php _e('Something is wrong!', 'wit') ?></option>
					<?php
				} else {
					foreach($this->wit_areas_ids as $area_id ) :
					?>
					<option value="<?php echo $area_id ?>" <?php if ($instance['area'] === $area_id) echo "selected"; ?>><?php echo $GLOBALS['wp_registered_sidebars'][$area_id]['name'] ?></option>
					<?php
    				endforeach;
				}
				?>
			</select>
		</p>
		<p>
			<legend><?php _e( 'Tabs rows:' , 'wit'); ?></legend>
			<input id="<?php echo $this->get_field_id( 'tab_style' ) . '-1'; ?>" name="<?php echo $this->get_field_name( 'tab_style' ); ?>" type="radio" value="scroll"   <?php if ($instance['tab_style'] == 'scroll')   echo "checked"; ?>/><label for="<?php echo $this->get_field_id( 'tab_style' ) . '-1'; ?>"><?php _e('Only one row (Scroll)', 'wit') ?></label>
			<input id="<?php echo $this->get_field_id( 'tab_style' ) . '-2'; ?>" name="<?php echo $this->get_field_name( 'tab_style' ); ?>" type="radio" value="show_all" <?php if ($instance['tab_style'] == 'show_all') echo "checked"; ?>/><label for="<?php echo $this->get_field_id( 'tab_style' ) . '-2'; ?>"><?php _e('Allow multiple rows (Show all)', 'wit') ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Widgets have different heights' , 'wit'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" >
				<option value="adaptive" <?php if ($instance['height'] == "adaptive") echo "selected" ?> ><?php _e('Allow WIT to change its height', 'wit') ?></option>
				<option value="fixed" <?php if ($instance['height'] == "fixed") echo "selected" ?> ><?php _e('Keep WIT height fixed', 'wit') ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'interval' ); ?>"><?php _e( 'Auto-Rotate tabs every (seconds): (0 = disable)' , 'wit'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'interval' ); ?>" name="<?php echo $this->get_field_name( 'interval' ); ?>" type="text" value="<?php echo esc_attr( $instance['interval'] ); ?>" />
		</p>
		<div>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'wit-animate' ); ?>" name="<?php echo $this->get_field_name( 'animate' ); ?>" <?php if ($instance['animate'] === 'true') echo 'checked'; ?>/>
			<label for="<?php echo $this->get_field_id( 'wit-animate' ); ?>"> <?php _e( 'Animate WIT', 'wit' ) ?></label>
			<div id="<?php echo $this->get_field_id( 'wit-animate-controls' ); ?>" <?php if ($instance['animate'] === 'false') echo 'style="display: none"'; ?>>
				<p>
					<label for="<?php echo $this->get_field_id( 'hide_effect' ); ?>"><?php _e( 'Hide tab effect' , 'wit'); ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id( 'hide_effect' ); ?>" name="<?php echo $this->get_field_name( 'hide_effect' ); ?>" >
						<?php
						foreach ($this->effects as $key => $value):
						?>
						<option value="<?php echo $key ?>" <?php if ($instance['hide_effect'] == $key) echo "selected" ?> ><?php echo $value ?></option>
						<?php
						endforeach;
						?>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'show_effect' ); ?>"><?php _e( 'Show tab effect' , 'wit'); ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id( 'show_effect' ); ?>" name="<?php echo $this->get_field_name( 'show_effect' ); ?>" >
						<?php
						foreach ($this->effects as $key => $value):
						?>
						<option value="<?php echo $key ?>" <?php if ($instance['show_effect'] == $key) echo "selected" ?> ><?php echo $value ?></option>
						<?php
						endforeach;
						?>
					</select>
				</p>
				<p>
					<legend><?php _e( 'Effect style:' , 'wit'); ?></legend>
					<input id="<?php echo $this->get_field_id( 'effect_style' ) . '-1'; ?>" name="<?php echo $this->get_field_name( 'effect_style' ); ?>" type="radio" value="prll"   <?php if ($instance['effect_style'] == 'prll')   echo "checked"; ?>/><label for="<?php echo $this->get_field_id( 'effect_style' ) . '-1'; ?>"><?php _e('Parallel', 'wit') ?></label>
					<input id="<?php echo $this->get_field_id( 'effect_style' ) . '-2'; ?>" name="<?php echo $this->get_field_name( 'effect_style' ); ?>" type="radio" value="seq" <?php if ($instance['effect_style'] == 'seq') echo "checked"; ?>/><label for="<?php echo $this->get_field_id( 'effect_style' ) . '-2'; ?>"><?php _e('Sequential', 'wit') ?></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'duration' ); ?>"><?php _e( 'Effect duration in milliseconds: (0 = disable)' , 'wit'); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'duration' ); ?>" name="<?php echo $this->get_field_name( 'duration' ); ?>" type="text" value="<?php echo esc_attr( $instance['duration'] ); ?>" />
				</p>
			</div>
		</div>
		<div>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'wit-extra' ); ?>" name="<?php echo $this->get_field_name( 'extra' ); ?>" <?php if ($instance['extra'] === 'true') echo 'checked'; ?>/>
			<label for="<?php echo $this->get_field_id( 'wit-extra' ); ?>"> <?php _e('Use extra features', 'wit') ?></label>
			<div id="<?php echo $this->get_field_id( 'wit-extra-controls' ); ?>" <?php if ($instance['extra'] === 'false') echo 'style="display: none"'; ?>>
				<p>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'wit_extra_closable_tabs' ); ?>" name="<?php echo $this->get_field_name( 'extra_closable_tabs' ); ?>" <?php if ($instance['extra_closable_tabs'] === 'true') echo 'checked'; ?>/>
					<label for="<?php echo $this->get_field_id( 'wit_extra_closable_tabs' ); ?>"> <?php _e('Hide tab\'s content if its title is clicked', 'wit') ?></label>
				</p>
				<p <?php if ($instance['extra_closable_tabs'] === 'false') echo 'style="display: none;"'; ?>>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'wit_extra_closable_tabs_init' ); ?>" name="<?php echo $this->get_field_name( 'extra_closable_tabs_init' ); ?>" <?php if ($instance['extra_closable_tabs_init'] === 'true') echo 'checked'; ?>/>
					<label for="<?php echo $this->get_field_id( 'wit_extra_closable_tabs_init' ); ?>"> <?php _e('Also, start with all tabs\' contents hidden', 'wit') ?></label>
				</p>
				<p>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'wit_extra_remember_last' ); ?>" name="<?php echo $this->get_field_name( 'extra_remember_last' ); ?>" <?php if ($instance['extra_remember_last'] === 'true') echo 'checked'; ?>/>
					<label for="<?php echo $this->get_field_id( 'wit_extra_remember_last' ); ?>"> <?php _e('Remember last shown tab when page is reloaded', 'wit') ?> <span class="wit-warning">(<?php _e('this feature will use cookies', 'wit') ?>)</span></label>
				</p>

			</div>
		</div>
		<?php
	}

	/**
	 * Sanitize Widgets In Tabs form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// make sure the interval string is an integer && is greater than or equal to zero
		$intervali = (int)$new_instance['interval'];
		if (((string)$intervali == $new_instance['interval']) && $intervali >= 0)
			$instance['interval'] =  $new_instance['interval'];
		else 
			$instance['interval'] = $this->defaults['interval'];

		if (in_array($new_instance['tab_style'], array('show_all', 'scroll'))) {
			$instance['tab_style'] = $new_instance['tab_style'];
		} else {
			$instance['tab_style'] = $this->defaults['tab_style'];
		}
		
		if (in_array($new_instance['hide_effect'], array_keys($this->effects)))
			$instance['hide_effect'] = $new_instance['hide_effect'];
		else
			$instance['hide_effect'] = $this->defaults['hide_effect'];

		if (in_array($new_instance['show_effect'], array_keys($this->effects)))
			$instance['show_effect'] = $new_instance['show_effect'];
		else
			$instance['show_effect'] = $this->defaults['show_effect'];

		if (in_array($new_instance['effect_style'], array('prll', 'seq'))) {
			$instance['effect_style'] = $new_instance['effect_style'];
		} else {
			$instance['effect_style'] = $this->defaults['effect_style'];
		}
		
		// make sure the duration string is an integer && is greater than or equal to zero
		$durationi = (int)$new_instance['duration'];
		if (((string)$durationi == $new_instance['duration']) && $durationi >= 0)
			$instance['duration'] =  $new_instance['duration'];
		else 
			$instance['duration'] = $this->defaults['duration'];

		if (in_array($new_instance['height'], array('adaptive', 'fixed'))) {
			$instance['height'] = $new_instance['height'];
		} else {
			$instance['height'] = $this->defaults['height'];
		}

		if (in_array($new_instance['area'], $this->wit_areas_ids)) {
			$instance['area'] = $new_instance['area'];
		} else {
			$instance['area'] = $this->defaults['area'];
		}

		if (isset($new_instance['animate'])) {
			$instance['animate'] = 'true';
		} else {
			$instance['animate'] = 'false';
		}

		if (isset($new_instance['extra'])) {
			$instance['extra'] = 'true';
		} else {
			$instance['extra'] = 'false';
		}

		if ($instance['extra'] == 'true' && isset($new_instance['extra_closable_tabs'])) {
			$instance['extra_closable_tabs'] = 'true';
		} else {
			$instance['extra_closable_tabs'] = 'false';
		}

		if ($instance['extra'] == 'true' 
			&& $instance['extra_closable_tabs'] == 'true'
			&& isset($new_instance['extra_closable_tabs_init'])) {
			$instance['extra_closable_tabs_init'] = 'true';
		} else {
			$instance['extra_closable_tabs_init'] = 'false';
		}

		if ($instance['extra'] == 'true' && isset($new_instance['extra_remember_last'])) {
			$instance['extra_remember_last'] = 'true';
		} else {
			$instance['extra_remember_last'] = 'false';
		}

		return $instance;
	}

	private function isActiveWithAnimation() {
		global $wp_registered_widgets;
 
    $sidebars_widgets = wp_get_sidebars_widgets();
 
    if ( !is_array($sidebars_widgets) ) {
    	return false;
    }

    foreach ( $sidebars_widgets as $sidebar => $widgets ) {
      if ('wp_inactive_widgets' === $sidebar || 'orphaned_widgets' === substr($sidebar, 0,16)) {
        continue;
      }

      if ( !is_array($widgets) ) {
      	continue;
      }

      foreach ( $widgets as $widget ) {
        if ( ( _get_widget_id_base($widget) == $this->id_base ) ) {
        	$widget_id = $wp_registered_widgets[$widget]['id'];
        	$widget_number = (int)substr($widget_id, strrpos($widget_id, '-')+1);
          if ( get_option('widget_widgets_in_tabs')[$widget_number]['animate'] === 'true' )
              return true;
        }
      }
    }
    return false;
	}

	private $wit_areas_ids = array();
	private $defaults = array(
			'area' => 'wit_area',
			'interval' => '0',
			'tab_style' => 'scroll',
			'hide_effect' => 'classic_slide',
			'show_effect' => 'classic_slide',
			'effect_style' => 'seq',
			'duration' => '600',
			'height' => 'adaptive',
			'animate' => 'true',
			'extra' => 'false',
			'extra_closable_tabs' => 'false',
			'extra_closable_tabs_init' => 'false',
			'extra_remember_last' => 'false',
			'id' => ''
			);
	private $effects = array(
			'classic_fade' => 'Classic Fade',
			'classic_slide' => 'Classic Slide',
			'blind_up' => 'Blind Up',
			'blind_down' => 'Blind Down',
			'blind_left' => 'Blind Left',
			'blind_right' => 'Blind Right',
			'blind_ver' => 'Blind Vertical',
			'blind_hor' => 'Blind Horizontal',
			'bounce' => 'Bounce',
			'clip_ver' => 'Clip Vertical',
			'clip_hor' => 'Clip Horizontal',
			'drop_up' => 'Drop Up',
			'drop_down' => 'Drop Down',
			'drop_left' => 'Drop Left',
			'drop_right' => 'Drop Right',
			'explode' => 'Explode',
			'fade' => 'Fade',
			'fold_ver_hor' => 'Fold Vertical->Horizontal',
			'fold_hor_ver' => 'Fold Horizontal->Vertical',
			'puff' => 'Puff',
			'pulsate' => 'Pulsate',
			'scale_ver' => 'Scale Vertical',
			'scale_hor' => 'Scale Horizontal',
			'scale_ver_hor' => 'Scale Vertical+Horizontal',
			'shake_left' => 'Shake Left',
			'shake_right' => 'Shake Right',
			'shake_up' => 'Shake Up',
			'shake_down' => 'Shake Down',
			'slide_up' => 'Slide Up',
			'slide_down' => 'Slide Down',
			'slide_left' => 'Slide Left',
			'slide_right' => 'Slide Right'
		);

} // class Widgets_In_Tabs
