<?php 
	global $theme_option;

	echo '<div class="gdlr-navigation-wrapper">';

	// navigation
	if( has_nav_menu('main_menu') ){
		if( class_exists('gdlr_menu_walker') ){
			echo '<nav class="gdlr-navigation" id="gdlr-main-navigation" role="navigation">';
			wp_nav_menu( array(
				'theme_location'=>'main_menu', 
				'container'=> '', 
				'menu_class'=> 'sf-menu gdlr-main-menu',
				'walker'=> new gdlr_menu_walker() 
			) );
		}else{
			echo '<nav class="gdlr-navigation" role="navigation">';
			wp_nav_menu( array('theme_location'=>'main_menu') );
		}
?>
<img id="gdlr-menu-search-button" src="<?php echo GDLR_PATH . '/images/magnifier.png'; ?>" alt="" width="58" height="59" />
<div class="gdlr-menu-search" id="gdlr-menu-search">
	<form method="get" id="searchform" action="<?php  echo home_url(); ?>/">
		<?php
			$search_val = get_search_query();
			if( empty($search_val) ){
				$search_val = __("Cosa stai cercando?" , "gdlr_translate");
			}
		?>
		<div class="search-text">
			<input type="text" value="<?php echo $search_val; ?>" name="s" autocomplete="off" data-default="<?php echo $search_val; ?>" />
		</div>
		<input type="submit" value="" />
		<div class="clear"></div>
	</form>	
</div>		
<?php		
		gdlr_get_woocommerce_nav();
		echo '</nav>'; // gdlr-navigation
	}
	echo '<div class="clear"></div>';
	echo '</div>'; // gdlr-navigation-wrapper
?>