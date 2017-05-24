<?php
	/*	
	*	Goodlayers Personnel Option file
	*	---------------------------------------------------------------------
	*	This file creates all personnel options and attached to the theme
	*	---------------------------------------------------------------------
	*/
	
	// add a personnel option to personnel page
	if( is_admin() ){ add_action('after_setup_theme', 'gdlr_create_personnel_options'); }
	if( !function_exists('gdlr_create_personnel_options') ){
	
		function gdlr_create_personnel_options(){
			global $gdlr_sidebar_controller;
			
			if( !class_exists('gdlr_page_options') ) return;
			new gdlr_page_options( 
				
				// page option attribute
				array(
					'post_type' => array('personnel'),
					'meta_title' => __('Goodlayers Personnel Option', 'gdlr-personnel'),
					'meta_slug' => 'goodlayers-page-option',
					'option_name' => 'post-option',
					'position' => 'normal',
					'priority' => 'high',
				),
					  
				// page option settings
				array(
					'page-layout' => array(
						'title' => __('Page Layout', 'gdlr-personnel'),
						'options' => array(
								'sidebar' => array(
									'type' => 'radioimage',
									'options' => array(
										'default-sidebar'=>GDLR_PATH . '/include/images/default-sidebar-2.png',
										'no-sidebar'=>GDLR_PATH . '/include/images/no-sidebar-2.png',
										'both-sidebar'=>GDLR_PATH . '/include/images/both-sidebar-2.png', 
										'right-sidebar'=>GDLR_PATH . '/include/images/right-sidebar-2.png',
										'left-sidebar'=>GDLR_PATH . '/include/images/left-sidebar-2.png'
									),
									'default' => 'default-sidebar'
								),	
								'left-sidebar' => array(
									'title' => __('Left Sidebar' , 'gdlr-personnel'),
									'type' => 'combobox',
									'options' => $gdlr_sidebar_controller->get_sidebar_array(),
									'wrapper-class' => 'sidebar-wrapper left-sidebar-wrapper both-sidebar-wrapper'
								),
								'right-sidebar' => array(
									'title' => __('Right Sidebar' , 'gdlr-personnel'),
									'type' => 'combobox',
									'options' => $gdlr_sidebar_controller->get_sidebar_array(),
									'wrapper-class' => 'sidebar-wrapper right-sidebar-wrapper both-sidebar-wrapper'
								),						
						)
					),
					
					'page-option' => array(
						'title' => __('Page Option', 'gdlr-personnel'),
						'options' => array(						
							'header-background' => array(
								'title' => __('Header Background Image' , 'gdlr-personnel'),
								'button' => __('Upload', 'gdlr-personnel'),
								'type' => 'upload',
							),
							'position' => array(
								'title' => __('Position' , 'gdlr-personnel'),
								'type' => 'text',
							),
							'mail' => array(
								'title' => __('E-Mail' , 'gdlr-personnel'),
								'type' => 'text',
							),
							'telephone' => array(
								'title' => __('Telephone' , 'gdlr-personnel'),
								'type' => 'text',
							),	
							'social' => array(
								'title' => __('Social Shortcode' , 'gdlr-personnel'),
								'type' => 'textarea',
							)							
						)
					),

				)
			);
			
		}
	}	
	
	// add personnel in page builder area
	add_filter('gdlr_page_builder_option', 'gdlr_register_personnel_item');
	if( !function_exists('gdlr_register_personnel_item') ){
		function gdlr_register_personnel_item( $page_builder = array() ){
			global $gdlr_spaces;
		
			$page_builder['content-item']['options']['personnel'] = array(
				'title'=> __('Personnel', 'gdlr-personnel'), 
				'type'=>'item',
				'options'=>array_merge(gdlr_page_builder_title_option(true), array(					
					'category'=> array(
						'title'=> __('Category' ,'gdlr-personnel'),
						'type'=> 'multi-combobox',
						'options'=> gdlr_get_term_list('personnel_category'),
						'description'=> __('You can use Ctrl/Command button to select multiple categories or remove the selected category. <br><br> Leave this field blank to select all categories.', 'gdlr-personnel')
					),				
					'num-fetch'=> array(
						'title'=> __('Num Fetch' ,'gdlr-personnel'),
						'type'=> 'text',	
						'default'=> '8',
						'description'=> __('Specify the number of personnel you want to pull out.', 'gdlr-personnel')
					),					
					'item-size'=> array(
						'title'=> __('Item Size' ,'gdlr-personnel'),
						'type'=> 'combobox',
						'options'=> array(
							'4'=>'1/4',
							'3'=>'1/3',
							'2'=>'1/2',
							'1'=>'1/1'
						),
						'default'=>'1/3'
					),					
					'personnel-layout'=> array(
						'title'=> __('Personnel Layout Order' ,'gdlr-personnel'),
						'type'=> 'combobox',
						'options'=> array(
							'fitRows' =>  __('FitRows ( Order items by row )', 'gdlr-personnel'),
							'masonry' => __('Masonry ( Order items by spaces )', 'gdlr-personnel'),
							'carousel' => __('Carousel', 'gdlr-personnel'),
						),
						'description'=> __('You can see an example of these two layout here', 'gdlr-personnel') . 
							'<br><br> http://isotope.metafizzy.co/demos/layout-modes.html'
					),					
					'thumbnail-size'=> array(
						'title'=> __('Thumbnail Size' ,'gdlr-personnel'),
						'type'=> 'combobox',
						'options'=> gdlr_get_thumbnail_list(),
						'description'=> __('Only effects to <strong>standard and gallery post format</strong>','gdlr-personnel')
					),	
					'orderby'=> array(
						'title'=> __('Order By' ,'gdlr-personnel'),
						'type'=> 'combobox',
						'options'=> array(
							'date' => __('Publish Date', 'gdlr-personnel'), 
							'title' => __('Title', 'gdlr-personnel'), 
							'rand' => __('Random', 'gdlr-personnel'), 
						)
					),
					'order'=> array(
						'title'=> __('Order' ,'gdlr-personnel'),
						'type'=> 'combobox',
						'options'=> array(
							'desc'=>__('Descending Order', 'gdlr-personnel'), 
							'asc'=> __('Ascending Order', 'gdlr-personnel'), 
						)
					),			
					'pagination'=> array(
						'title'=> __('Enable Pagination' ,'gdlr-personnel'),
						'type'=> 'checkbox'
					),					
					'margin-bottom' => array(
						'title' => __('Margin Bottom', 'gdlr-personnel'),
						'type' => 'text',
						'default' => $gdlr_spaces['bottom-blog-item'],
						'description' => __('Spaces after ending of this item', 'gdlr-personnel')
					),				
				))
			);
			return $page_builder;
		}
	}
	
?>