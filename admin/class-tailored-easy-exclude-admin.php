<?php
/**
 * Tailored Easy Exclude
 *
 * @package   TailoredEasyExcludeAdmin
 * @author    Zoran Ugrina <zoran@zugrina.com>
 * @license   GPL-2.0+
 * @link      www.zugrina.com
 * @copyright 2014 Zoran Ugrina
 */

/**
 * @package TailoredEasyExcludeAdmin
 * @author  Zoran Ugrina <zoran@zugrina.com>
 */
class TailoredEasyExcludeAdmin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = TailoredEasyExclude::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		//Initializes the theme's display options page by registering the Sections, Fields, and Settings
		add_action( 'admin_init', array( $this, 'initialize_plugin_options' ) );

		// Exclude pages
		add_action( 'pre_get_posts', array($this, 'exclude_pages') );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 * 
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-chosen', plugins_url( 'assets/css/chosen.css', __FILE__ ), array(), TailoredEasyExclude::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 * 
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-chosen', plugins_url( 'assets/js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), TailoredEasyExclude::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), TailoredEasyExclude::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Tailored Easy Exclude', $this->plugin_slug ),
			__( 'Tailored Easy Exclude', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Get all public custom post types
	 *
	 * @since    1.0.0
	 */
	public function get_all_custom_post_type(){

		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$output = 'objects';

		$custom_post_types = get_post_types($args, $output);

		return $custom_post_types;

	}

	/**
	 * Initializes the plugin display options page by registering the Sections,
	 * Fields, and Settings.
	 */ 
	public function initialize_plugin_options() {

		add_settings_section(
			'tailored_post_section',
			__( 'Posts', $this->plugin_slug ),
			'',
			'tailored_post_excluded_pages'
		);

		add_settings_field(
			__( 'Exclude Posts', $this->plugin_slug ),
			__( 'Exclude Posts', $this->plugin_slug ),
			array( $this, 'option_exclude_page_callback'),
			'tailored_post_excluded_pages',
			'tailored_post_section'
		);

		add_settings_field(	
			__( 'Exclude Posts for User Roles', $this->plugin_slug ),
			__( 'Exclude Posts for User Roles', $this->plugin_slug ),
			array( $this, 'option_exclude_page_per_role_callback'),
			'tailored_post_excluded_pages',
			'tailored_post_section'
		);

		register_setting(
			'tailored_post_excluded_pages',
			'tailored_post_excluded_pages'
		);

		add_settings_section(
			'tailored_page_section',
			__( 'Pages', $this->plugin_slug ),
			'',
			'tailored_page_excluded_pages'
		);

		add_settings_field(
			__( 'Exclude Pages', $this->plugin_slug ),
			__( 'Exclude Pages', $this->plugin_slug ),
			array( $this, 'option_exclude_page_callback'),
			'tailored_page_excluded_pages',
			'tailored_page_section'
		);

		add_settings_field(	
			__( 'Exclude Pages for User Roles', $this->plugin_slug ),
			__( 'Exclude Pages for User Roles', $this->plugin_slug ),
			array( $this, 'option_exclude_page_per_role_callback'),
			'tailored_page_excluded_pages',
			'tailored_page_section'
		);

		register_setting(
			'tailored_page_excluded_pages',
			'tailored_page_excluded_pages'
		);

		$custom_post_types = $this->get_all_custom_post_type();

		if($custom_post_types){

			foreach($custom_post_types as $custom_post_type){

				add_settings_section(
					'tailored_'.$custom_post_type->name.'_section',
					$custom_post_type->labels->name,
					'',
					'tailored_'.$custom_post_type->name.'_excluded_pages'
				);

				add_settings_field(	
					sprintf( __( 'Exclude %s', $this->plugin_slug ), $custom_post_type->labels->name ),
					sprintf( __( 'Exclude %s', $this->plugin_slug ), $custom_post_type->labels->name ),
					array( $this, 'option_exclude_page_callback'),
					'tailored_'.$custom_post_type->name.'_excluded_pages',
					'tailored_'.$custom_post_type->name.'_section'
				);

				add_settings_field(	
					sprintf( __( 'Exclude %s for User Roles', $this->plugin_slug ), $custom_post_type->labels->name ),
					sprintf( __( 'Exclude %s for User Roles', $this->plugin_slug ), $custom_post_type->labels->name ),
					array( $this, 'option_exclude_page_per_role_callback'),
					'tailored_'.$custom_post_type->name.'_excluded_pages',
					'tailored_'.$custom_post_type->name.'_section'
				);

				register_setting(
					'tailored_'.$custom_post_type->name.'_excluded_pages',
					'tailored_'.$custom_post_type->name.'_excluded_pages'
				);

			}

		}

	}

	/**
	 * Return active post type
	 *
	 * @since    1.0.0
	 */
	public function active_post_types(){

		$active_post_type = 'post';

		if( isset( $_GET[ 'tab' ] ) )
			$active_post_type = $_GET[ 'tab' ];
		
		return $active_post_type;
	}

	/**
	 * Return all posts for specific post type
	 *
	 * @since    1.0.0
	 */
	public function get_all_posts($post_type = ''){

		if(!$post_type)
			return false;

		$args = array(

			'posts_per_page' => -1,
			'post_type' => $post_type,

		);

		if( $sticky = get_option( 'sticky_posts' ) )
			$args['post__not_in'] = get_option( 'sticky_posts' );

		return $posts = get_posts( $args );

	}

	/**
	 * Add settings exclude page callback
	 *
	 * @since    1.0.0
	 */
	public function option_exclude_page_callback($args) {

		$active_post_type = $this->active_post_types();

		$posts = $this->get_all_posts($active_post_type);

		if($posts){

			$options = get_option( 'tailored_'.$active_post_type.'_excluded_pages' );

			echo '<select data-placeholder="'.__('Exclude posts or pages', $this->plugin_slug).'" class="tailored-chosen-select" multiple name="tailored_'.$active_post_type.'_excluded_pages[excluded][]">';
				foreach($posts as $post){

					$selected = (isset($options['excluded']) && in_array($post->ID, $options['excluded'])) ? 'selected="selected"' : '';

					echo '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.'</option>';
				}
			echo '</select>';

		}
	}

	/**
	 * Add settings exclude page per role callback
	 *
	 * @since    1.0.0
	 */
	public function option_exclude_page_per_role_callback(){

		$active_post_type = $this->active_post_types();

		$options = get_option( 'tailored_'.$active_post_type.'_excluded_pages' );

		$editable_roles = array_reverse( get_editable_roles() );

		if($editable_roles){

			echo '<select data-placeholder="'.__('Exclude posts or pages per roles', $this->plugin_slug).'" class="tailored-chosen-select" multiple name="tailored_'.$active_post_type.'_excluded_pages[roles][]">';
				foreach($editable_roles as $role => $details){
					$name = translate_user_role($details['name']);
					$selected = (isset($options['roles']) && in_array($role, $options['roles'])) ? 'selected="selected"' : '';

					echo '<option value="'.$role.'" '.$selected.'>'.$name.'</option>';
				}
			echo '</select>';
			echo ' <small>'.__('If you don\'t select any role, selected posts will be excluded for everyone', $this->plugin_slug).'</small>';

		}
	}

	/**
	 * Exclude pages/posts from admin per users role
	 *
	 * @since    1.0.0
	 */
	public function exclude_pages( $query ) {
	        
        if( !is_admin() )
        	return $query;
        
        global $pagenow;
        
        if( 'edit.php' == $pagenow && ( get_query_var('post_type') ) && $query->is_main_query() ) {

        	$options = get_option( 'tailored_'.get_query_var('post_type').'_excluded_pages' );

        	$exclude = (isset($options['excluded'])) ? $options['excluded'] : '';
        	$exclude_per_role = (isset($options['roles'])) ? $options['roles'] : '';

        	if( !empty($exclude) && is_array($exclude)){
				
				// Exclude posts/pages
				if(!empty($exclude_per_role) && is_array($exclude_per_role)){

					global $current_user;
					$current_role = array_shift($current_user->roles);

					if(in_array($current_role, $exclude_per_role )){
						$query->set( 'post__not_in', $exclude ); // array page ids
						// New post count in edit view screen
						add_action( 'wp_count_posts', array($this, 'change_post_number') );
					}
					return false;
				}

				$query->set( 'post__not_in', $exclude ); // array page ids
				
				// New post count in edit view screen
				add_action( 'wp_count_posts', array($this, 'change_post_number') );
			}
        }

        return $query;
	}

	/**
	 * Change post number in edit screen after exclude posts or pages
	 *
	 * @since    1.0.0
	 */
	public function change_post_number($counts){

		$post_type = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';

		$options = get_option( 'tailored_'.$post_type.'_excluded_pages' );

		if( isset($options['excluded']) ) {
			
			$args = array(
				'posts_per_page' => -1, 
				'post_type' => sanitize_title_for_query( $post_type ),
				'post__not_in' => $options['excluded'],
				'post_status' => 'any'
			);

			$posts =  get_posts( $args );

			$counts = array_fill_keys( get_post_stati(), 0 );
			
			foreach ( $posts as $row ){
				$counts[ $row->post_status ] = $counts[ $row->post_status ] + 1;
			}

			$counts = (object) $counts;
		}
		return $counts;
	}
}