<?php
	/*	
	*	Goodlayers Product Option file
	*	---------------------------------------------------------------------
	*	This file creates all product options and attached to the theme
	*	---------------------------------------------------------------------
	*/

	// add product in page builder area
	add_filter('gdlr_page_builder_option', 'gdlr_register_product_item');
	if( !function_exists('gdlr_register_product_item') ){
		function gdlr_register_product_item( $page_builder = array() ){
			global $gdlr_spaces;
		
			$page_builder['content-item']['options']['product'] = array(
				'title'=> __('Product', 'gdlr-portfolio'), 
				'type'=>'item',
				'options'=>array_merge(gdlr_page_builder_title_option(true), array(					
					'category'=> array(
						'title'=> __('Category' ,'gdlr-portfolio'),
						'type'=> 'multi-combobox',
						'options'=> gdlr_get_term_list('product_cat'),
						'description'=> __('You can use Ctrl/Command button to select multiple categories or remove the selected category. <br><br> Leave this field blank to select all categories.', 'gdlr-portfolio')
					),	
					'tag'=> array(
						'title'=> __('Tag' ,'gdlr-portfolio'),
						'type'=> 'multi-combobox',
						'options'=> gdlr_get_term_list('product_tag'),
						'description'=> __('Will be ignored when the filter option is enabled.', 'gdlr-portfolio')
					),					
					'portfolio-style'=> array(
						'title'=> __('Product Style' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> array(
							'classic2-portfolio' => __('Classic 2 Style', 'gdlr-portfolio'),
							'classic2-portfolio-no-space' => __('Classic 2 No Space Style', 'gdlr-portfolio'),
							'classic-portfolio' => __('Classic Style', 'gdlr-portfolio'),
							'classic-portfolio-no-space' => __('Classic No Space Style', 'gdlr-portfolio'),
							'modern-portfolio' => __('Modern Style', 'gdlr-portfolio'),
							'modern-portfolio-no-space' => __('Modern No Space Style', 'gdlr-portfolio'),
							'medium-portfolio' => __('Medium Style', 'gdlr-portfolio'),
						),
					),
					'num-excerpt'=> array(
						'title'=> __('Num Excerpt' ,'gdlr-portfolio'),
						'type'=> 'text',	
						'default'=> '25',
						'wrapper-class'=> 'portfolio-style-wrapper medium-portfolio-wrapper'
					),					
					'num-fetch'=> array(
						'title'=> __('Num Fetch' ,'gdlr-portfolio'),
						'type'=> 'text',	
						'default'=> '8',
						'description'=> __('Specify the number of products you want to pull out.', 'gdlr-portfolio')
					),				
					'portfolio-size'=> array(
						'title'=> __('Product Size' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> array(
							'1/4'=>'1/4',
							'1/3'=>'1/3',
							'1/2'=>'1/2',
							'1/1'=>'1/1'
						),
						'default'=>'1/3'
					),					
					'portfolio-layout'=> array(
						'title'=> __('Product Layout Order' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> array(
							'fitRows' =>  __('FitRows ( Order items by row )', 'gdlr-portfolio'),
							'masonry' => __('Masonry ( Order items by spaces )', 'gdlr-portfolio'),
							'carousel' => __('Carousel ( Cannot use with filter function )', 'gdlr-portfolio'),
						)
					),
					'portfolio-filter'=> array(
						'title'=> __('Enable Portfolio filter' ,'gdlr-portfolio'),
						'type'=> 'checkbox',
						'default'=> 'disable',
						'description'=> __('*** You have to select only 1 ( or none ) portfolio category when enable this option. This option cannot works with carousel function.','gdlr-portfolio')
					),						
					'thumbnail-size'=> array(
						'title'=> __('Thumbnail Size' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> gdlr_get_thumbnail_list(),
						'description'=> __('Only effects to <strong>standard and gallery post format</strong>','gdlr-portfolio')
					),	
					'orderby'=> array(
						'title'=> __('Order By' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> array(
							'date' => __('Publish Date', 'gdlr-portfolio'), 
							'title' => __('Title', 'gdlr-portfolio'), 
							'rand' => __('Random', 'gdlr-portfolio'), 
						)
					),
					'order'=> array(
						'title'=> __('Order' ,'gdlr-portfolio'),
						'type'=> 'combobox',
						'options'=> array(
							'desc'=>__('Descending Order', 'gdlr-portfolio'), 
							'asc'=> __('Ascending Order', 'gdlr-portfolio'), 
						)
					),			
					'pagination'=> array(
						'title'=> __('Enable Pagination' ,'gdlr-portfolio'),
						'type'=> 'checkbox'
					),					
					'margin-bottom' => array(
						'title' => __('Margin Bottom', 'gdlr-portfolio'),
						'type' => 'text',
						'default' => $gdlr_spaces['bottom-blog-item'],
						'description' => __('Spaces after ending of this item', 'gdlr-portfolio')
					),				
				))
			);
			return $page_builder;
		}
	}
	
?>