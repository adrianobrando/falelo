<?php 
	get_header(); 
	
	while( have_posts() ){ the_post();
?>
<div class="gdlr-content">

	<?php 
		global $gdlr_sidebar, $theme_option, $gdlr_post_option, $gdlr_is_ajax;
		
		if( empty($gdlr_post_option['sidebar']) || $gdlr_post_option['sidebar'] == 'default-sidebar' ){
			$gdlr_sidebar = array(
				'type'=>$theme_option['personnel-sidebar-template'],
				'left-sidebar'=>$theme_option['personnel-sidebar-left'], 
				'right-sidebar'=>$theme_option['personnel-sidebar-right']
			); 
		}else{
			$gdlr_sidebar = array(
				'type'=>$gdlr_post_option['sidebar'],
				'left-sidebar'=>$gdlr_post_option['left-sidebar'], 
				'right-sidebar'=>$gdlr_post_option['right-sidebar']
			); 				
		}
		$gdlr_sidebar = gdlr_get_sidebar_class($gdlr_sidebar);
	?>
	<div class="with-sidebar-wrapper">
		<div class="with-sidebar-container container gdlr-class-<?php echo $gdlr_sidebar['type']; ?>">
			<div class="with-sidebar-left <?php echo $gdlr_sidebar['outer']; ?> columns">
				<div class="with-sidebar-content <?php echo $gdlr_sidebar['center']; ?> columns">
					<div class="gdlr-item gdlr-single-personnel gdlr-item-start-content">
						<div id="personnel-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div class="gdlr-personnel-info-wrapper">
							<?php 
								echo gdlr_get_personnel_thumbnail($theme_option['personnel-thumbnail-size']);
								
								echo gdlr_get_personnel_info(array('mail', 'phone', 'social'), $gdlr_post_option);
							?>
							</div>
							<div class="gdlr-personnel-content"><?php the_content(); ?></div>	
						</div><!-- #personnel -->
						<?php //  ?>
						
						<div class="clear"></div>	
					</div>
				</div>
				<?php get_sidebar('left'); ?>
				<div class="clear"></div>
			</div>
			<?php get_sidebar('right'); ?>
			<div class="clear"></div>
		</div>				
	</div>				

</div><!-- gdlr-content -->
<?php
	}
	
	get_footer(); 
?>