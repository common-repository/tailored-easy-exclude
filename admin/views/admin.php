<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   TailoredEasyExclude
 * @author    Zoran Ugrina <zoran@zugrina.com>
 * @license   GPL-2.0+
 * @link      www.zugrina.com
 * @copyright 2014 Zoran Ugrina
 */
?>

<!-- Create a header in the default WordPress 'wrap' container -->
<div class="wrap">

	<div id="icon-themes" class="icon32"></div>
	
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<?php

	$active_post_type = 'post';

	if( isset( $_GET[ 'tab' ] ) ) {
		
		$active_post_type = $_GET[ 'tab' ];
	
	}

	$plugin = TailoredEasyExclude::get_instance();

	$post_types = TailoredEasyExcludeAdmin::get_all_custom_post_type(); ?>

	<h2 class="nav-tab-wrapper">

		<a href="?page=<?php echo $plugin->get_plugin_slug(); ?>&tab=post" class="nav-tab <?php echo $active_post_type == 'post' ? 'nav-tab-active' : ''; ?>"><?php _e('Posts', $plugin->get_plugin_slug()); ?></a>
		<a href="?page=<?php echo $plugin->get_plugin_slug(); ?>&tab=page" class="nav-tab <?php echo $active_post_type == 'page' ? 'nav-tab-active' : ''; ?>"><?php  _e('Pages', $plugin->get_plugin_slug()); ?></a>

		<?php foreach($post_types as $post_type): ?>

			<a href="?page=<?php echo $plugin->get_plugin_slug(); ?>&tab=<?php echo $post_type->name; ?>" class="nav-tab <?php echo $active_post_type == $post_type->name ? 'nav-tab-active' : ''; ?>"><?php echo $post_type->labels->name; ?></a>
		
		<?php endforeach; ?>

	</h2>

	<?php if(TailoredEasyExcludeAdmin::get_all_posts( $post_type = $active_post_type)): ?>
	
		<form method="post" action="options.php">
			<?php

			settings_fields( 'tailored_'.$active_post_type.'_excluded_pages' );
			do_settings_sections( 'tailored_'.$active_post_type.'_excluded_pages' );

			submit_button();

			?>
		</form>

	<?php else:
		
		echo '<br>';
		_e('Oops, Posts in this Post Type not Found!', $plugin->get_plugin_slug());

	endif; ?>
	
</div><!-- /.wrap -->