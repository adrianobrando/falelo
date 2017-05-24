<?php
	/*	
	*	Goodlayers Product Item Management File
	*	---------------------------------------------------------------------
	*	This file contains functions that help you create product item
	*	---------------------------------------------------------------------
	*/
	
	// add action to check for product item
	add_action('gdlr_print_item_selector', 'gdlr_check_product_item', 10, 2);
	if( !function_exists('gdlr_check_product_item') ){
		function gdlr_check_product_item( $type, $settings = array() ){
			if($type == 'product'){
				echo gdlr_print_product_item( $settings );
			}
		}
	}

	// print product item
	if( !function_exists('gdlr_print_product_item') ){
		function gdlr_print_product_item( $settings = array() ){
			gdlr_include_portfolio_scirpt();
		
			$item_id = empty($settings['page-item-id'])? '': ' id="' . $settings['page-item-id'] . '" ';

			global $gdlr_spaces;
			$margin = (!empty($settings['margin-bottom']) && 
				$settings['margin-bottom'] != $gdlr_spaces['bottom-blog-item'])? 'margin-bottom: ' . $settings['margin-bottom'] . ';': '';
			$margin_style = (!empty($margin))? ' style="' . $margin . '" ': '';
			
			if( $settings['portfolio-layout'] == 'carousel' ){ 
				$settings['carousel'] = true;
			}
			
			$ret  = gdlr_get_item_title($settings);				
			$ret .= '<div class="portfolio-item-wrapper type-' . $settings['portfolio-style'] . '" ';
			$ret .= $item_id . $margin_style . ' data-ajax="' . AJAX_URL . '" >'; 

			// query posts section
			$args = array('post_type' => 'product', 'suppress_filters' => false);
			$args['posts_per_page'] = (empty($settings['num-fetch']))? '5': $settings['num-fetch'];
			$args['orderby'] = (empty($settings['orderby']))? 'post_date': $settings['orderby'];
			$args['order'] = (empty($settings['order']))? 'desc': $settings['order'];
			$args['paged'] = (get_query_var('paged'))? get_query_var('paged') : 1;

			if( !empty($settings['category']) || (!empty($settings['tag']) && $settings['portfolio-filter'] == 'disable') ){
				$args['tax_query'] = array('relation' => 'OR');
				
				if( !empty($settings['category']) ){
					array_push($args['tax_query'], array('terms'=>explode(',', $settings['category']), 'taxonomy'=>'product_cat', 'field'=>'slug'));
				}
				if( !empty($settings['tag']) && $settings['portfolio-filter'] == 'disable' ){
					array_push($args['tax_query'], array('terms'=>explode(',', $settings['tag']), 'taxonomy'=>'product_tag', 'field'=>'slug'));
				}				
			}			
			$query = new WP_Query( $args );

			// create the portfolio filter
			$settings['portfolio-size'] = str_replace('1/', '', $settings['portfolio-size']);
			if( $settings['portfolio-filter'] == 'enable' ){
			
				// ajax infomation
				$ret .= '<div class="gdlr-ajax-info" data-num-fetch="' . $args['posts_per_page'] . '" data-num-excerpt="' . $settings['num-excerpt'] . '" ';
				$ret .= 'data-orderby="' . $args['orderby'] . '" data-order="' . $args['order'] . '" ';
				$ret .= 'data-thumbnail-size="' .  $settings['thumbnail-size'] . '" data-port-style="' . $settings['portfolio-style'] . '" ';
				$ret .= 'data-port-size="' . $settings['portfolio-size'] . '" data-port-layout="' .  $settings['portfolio-layout'] . '" ';
				$ret .= 'data-ajax="' . admin_url('admin-ajax.php') . '" data-category="' . $settings['category'] . '" ></div>';
			
				// category filter
				if( empty($settings['category']) ){
					$parent = array('gdlr-all'=>__('All', 'gdlr-portfolio'));
					$settings['category-id'] = '';
				}else{
					$term = get_term_by('slug', $settings['category'], 'product_cat');
					$parent = array($settings['category']=>$term->name);
					$settings['category-id'] = $term->term_id;
				}
				
				$filters = $parent + gdlr_get_term_list('product_cat', $settings['category-id']);
				$filter_active = 'active';
				$ret .= '<div class="portfolio-item-filter">';
				foreach($filters as $filter_id => $filter){
					$filter_id = ($filter_id == 'gdlr-all')? '': $filter_id;

					$ret .= '<a class="' . $filter_active . '" href="#" ';
					$ret .= 'data-category="' . $filter_id . '" >' . $filter . '</a>';
					$filter_active = '';
				}
				$ret .= '</div>';
			}
			
			$no_space  = (strpos($settings['portfolio-style'], 'no-space') > 0)? 'gdlr-item-no-space': '';
			$no_space .= ' gdlr-portfolio-column-' . $settings['portfolio-size'];
			$ret .= '<div class="portfolio-item-holder ' . $no_space . '">';
			if( $settings['portfolio-style'] == 'classic-portfolio' || 
				$settings['portfolio-style'] == 'classic-portfolio-no-space'){
				$ret .= gdlr_get_classic_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}else if( $settings['portfolio-style'] == 'classic2-portfolio' || 
				$settings['portfolio-style'] == 'classic2-portfolio-no-space'){
				$ret .= gdlr_get_classic2_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}else if( $settings['portfolio-style'] == 'medium-portfolio' ){
				$ret .= gdlr_get_medium_product($query, $settings['thumbnail-size'], $settings['num-excerpt']);
			}else if($settings['portfolio-style'] == 'modern-portfolio' || 
				$settings['portfolio-style'] == 'modern-portfolio-no-space'){	
				$ret .= gdlr_get_modern_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}
			$ret .= '<div class="clear"></div>';
			$ret .= '</div>';
			
			// create pagination
			if($settings['portfolio-filter'] == 'enable' && $settings['pagination'] == 'enable'){
				$ret .= gdlr_get_ajax_pagination($query->max_num_pages, $args['paged']);
			}else if($settings['pagination'] == 'enable'){
				$ret .= gdlr_get_pagination($query->max_num_pages, $args['paged']);
			}
			
			$ret .= '</div>'; // portfolio-item-wrapper
			return $ret;
		}
	}
	
	// ajax function for portfolio filter / pagination
	add_action('wp_ajax_gdlr_get_product_ajax', 'gdlr_get_product_ajax');
	add_action('wp_ajax_nopriv_gdlr_get_product_ajax', 'gdlr_get_product_ajax');
	if( !function_exists('gdlr_get_product_ajax') ){
		function gdlr_get_product_ajax(){
			$settings = $_POST['args'];

			$args = array('post_type' => 'product', 'suppress_filters' => false);
			$args['posts_per_page'] = (empty($settings['num-fetch']))? '5': $settings['num-fetch'];
			$args['orderby'] = (empty($settings['orderby']))? 'post_date': $settings['orderby'];
			$args['order'] = (empty($settings['order']))? 'desc': $settings['order'];
			$args['paged'] = (empty($settings['paged']))? 1: $settings['paged'];
				
			if( !empty($settings['category']) ){
				$args['tax_query'] = array(
					array('terms'=>explode(',', $settings['category']), 'taxonomy'=>'product_cat', 'field'=>'slug')
				);
			}			
			$query = new WP_Query( $args );
			
			$no_space = (strpos($settings['portfolio-style'], 'no-space') > 0)? 'gdlr-item-no-space': '';
			$no_space .= ' gdlr-portfolio-column-' . $settings['portfolio-size'];
			$ret  = '<div class="portfolio-item-holder ' . $no_space . '">';
			if( $settings['portfolio-style'] == 'classic-portfolio' || 
				$settings['portfolio-style'] == 'classic-portfolio-no-space'){
				$ret .= gdlr_get_classic_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}else if( $settings['portfolio-style'] == 'classic2-portfolio' || 
				$settings['portfolio-style'] == 'classic2-portfolio-no-space'){
				$ret .= gdlr_get_classic2_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}else if( $settings['portfolio-style'] == 'medium-portfolio' ){
				$ret .= gdlr_get_medium_product($query, $settings['thumbnail-size'], $settings['num-excerpt']);
			}else if($settings['portfolio-style'] == 'modern-portfolio' || 
				$settings['portfolio-style'] == 'modern-portfolio-no-space'){	
				
				$ret .= gdlr_get_modern_product($query, $settings['portfolio-size'], 
							$settings['thumbnail-size'], $settings['portfolio-layout'] );
			}
			$ret .= '<div class="clear"></div>';
			$ret .= '</div>';
			
			// pagination section
			$ret .= gdlr_get_ajax_pagination($query->max_num_pages, $args['paged']);
			die($ret);
		}
	}
	
	// get product info
	if( !function_exists('gdlr_get_product_info') ){
		function gdlr_get_product_info( $array = array(), $option = array(), $wrapper = true ){
			$ret = '';
			
			foreach($array as $post_info){	
				switch( $post_info ){
					case 'clients':
						if(empty($option['clients'])) break;
					
						$ret .= '<div class="portfolio-info portfolio-clients">';
						$ret .= '<span class="info-head gdlr-title">' . __('Client', 'gdlr-portfolio') . ' </span>';
						$ret .= $option['clients'];						
						$ret .= '</div>';						
					
						break;	
					case 'skills':
						if(empty($option['skills'])) break;
					
						$ret .= '<div class="portfolio-info portfolio-skills">';
						$ret .= '<span class="info-head gdlr-title">' . __('Skills', 'gdlr-portfolio') . ' </span>';
						$ret .= $option['skills'];						
						$ret .= '</div>';						

						break;	
					case 'website':
						if(empty($option['website'])) break;
					
						$ret .= '<div class="portfolio-info portfolio-website">';
						$ret .= '<span class="info-head gdlr-title">' . __('Website', 'gdlr-portfolio') . ' </span>';
						$ret .= '<a href="' . $option['website'] . '" target="_blank" >' . $option['website'] . '</a>';					
						$ret .= '</div>';						
					
						break;
					case 'tag':
						$tag = get_the_term_list(get_the_ID(), 'product_tag', '', '<span class="sep">,</span> ' , '' );
						if(empty($tag)) break;					
					
						$ret .= '<div class="portfolio-info portfolio-tag">';
						$ret .= '<span class="info-head gdlr-title">' . __('Tags', 'gdlr-portfolio') . ' </span>';
						$ret .= $tag;						
						$ret .= '</div>';						
						break;					
				}
			}

			if($wrapper && !empty($ret)){
				return '<div class="gdlr-portfolio-info gdlr-skin-info">' . $ret . '<div class="clear"></div></div>';
			}else if( !empty($ret) ){
				return $ret . '<div class="clear"></div>';
			}
			return '';
		}
	}
	
	// get portfolio thumbnail
	if( !function_exists('gdlr_get_product_thumbnail') ){
		function gdlr_get_product_thumbnail($size = 'full', $modern = false){
			global $gdlr_related_section, $theme_option;

			$image_id = get_post_thumbnail_id();
			if( !empty($image_id) ){
				$ret  = gdlr_get_image($image_id, $size);
				if( $modern ){
					$ret .= '<div class="portfolio-overlay-wrapper gdlr-product-overlay" >';
					$ret .= '<div class="portfolio-overlay"></div>';
					$ret .= '<div class="portfolio-overlay-content" >';

					$ret .= '<a class="product-overlay-cart add_to_cart_button product_type_simple" href="#" rel="nofollow" data-product_id="' . get_the_ID() . '" data-product_sku="" data-quantity="1" >';
					$ret .= '<img src="' . GDLR_PATH . '/images/cart-light.png" alt="add-to-cart" >';
					$ret .= '</a>';	

					$ret .= '<a class="product-overlay-link" href="' . get_permalink() . '" >';
					$ret .= '<i class="icon-link fa fa-link" ></i>';
					$ret .= '</a>';
					
					$ret .= '<div class="gdlr-product-title"><a href="' . get_permalink() . '" >' . get_the_title() . '</a></div>';
					
					$ret .= '<div class="clear"></div>';
					$ret .= '</div>'; // product-overlay-content
					$ret .= '</div>'; // product-overlay-wrapper					
				}else{
					$ret .= '<div class="product-overlay-wrapper" >';
					$ret .= '<div class="product-overlay"></div>';
					$ret .= '<div class="product-overlay-content" >';

					$ret .= '<a class="product-overlay-cart add_to_cart_button product_type_simple" href="#" rel="nofollow" data-product_id="' . get_the_ID() . '" data-product_sku="" data-quantity="1" >';
					$ret .= '<img src="' . GDLR_PATH . '/images/cart-light.png" alt="add-to-cart" >';
					$ret .= '<span class="gdlr-title-font">' . __('Add to Cart', 'gdlr-portfolio') . '</span>';
					$ret .= '</a>';	

					$ret .= '<a class="product-overlay-link" href="' . get_permalink() . '" >';
					$ret .= '<i class="icon-link fa fa-link" ></i>';
					$ret .= '<span class="gdlr-title-font">' . __('Read More', 'gdlr-portfolio') . '</span>';
					$ret .= '</a>';
					
					$ret .= '<div class="clear"></div>';
					$ret .= '</div>'; // product-overlay-content
					$ret .= '</div>'; // product-overlay-wrapper
				}
			}		

			return $ret;
		}
	}	
	
	
	// print medium portfolio
	if( !function_exists('gdlr_get_medium_product') ){
		function gdlr_get_medium_product($query, $thumbnail_size, $excerpt_num){
			global $post;
			$ret  = '';
			while($query->have_posts()){ $query->the_post();
				$ret .= '<div class="gdlr-item gdlr-portfolio-item gdlr-medium-portfolio">';
				$ret .= '<div class="gdlr-ux gdlr-medium-portfolio-ux">';
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size);
				$ret .= '</div>'; // portfolio-thumbnail
				
				$ret .= '<div class="gdlr-portfolio-content">';
				$ret .= '<h3 class="portfolio-title"><a href="' . get_permalink() . '" >' . get_the_title() . '</a></h3>';
				
				$excerpt = get_the_excerpt();
				$words = explode(' ', $excerpt, $excerpt_num + 1);
				array_pop($words);
				$words = implode(' ', $words);
				
				$ret .= '<div class="portfolio-excerpt">' . $words . '</div>';
				$ret .= '</div>'; // gdlr-portfolio content
				
				$ret .= '<div class="clear"></div>';
				$ret .= '</div>'; // gdlr-ux
				$ret .= '</div>'; // gdlr-item
			}
			wp_reset_postdata();
			
			return $ret;
		}
	}
	
	// print classic portfolio
	if( !function_exists('gdlr_get_classic_product') ){
		function gdlr_get_classic_product($query, $size, $thumbnail_size, $layout = 'fitRows'){
			if($layout == 'carousel'){ 
				return gdlr_get_classic_carousel_product($query, $size, $thumbnail_size); 
			}		
		
			global $post;

			$current_size = 0;
			$ret  = '<div class="gdlr-isotope" data-type="portfolio" data-layout="' . $layout  . '" >';
			while($query->have_posts()){ $query->the_post();
				if( $current_size % $size == 0 ){
					$ret .= '<div class="clear"></div>';
				}			
    
				$ret .= '<div class="' . gdlr_get_column_class('1/' . $size) . '">';
				$ret .= '<div class="gdlr-item gdlr-portfolio-item gdlr-classic-portfolio">';
				$ret .= '<div class="gdlr-ux gdlr-classic-portfolio-ux">';
				
				$product = new WC_Product( get_the_ID() );
				$price = $product->get_price_html();
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size);
				$ret .= '</div>'; // portfolio-thumbnail
 
				$ret .= '<h3 class="gdlra-skin-box portfolio-title ' . (empty($price)? '': 'with-price') . '">';
				$ret .= '<a href="' . get_permalink() . '" >' . get_the_title() . '</a>';
				
				if( !empty($price) ){
					$ret .= '<span class="gdlr-port-price">' . $price[0] . '</span>';
				}
				$ret .= '</h3>';

				$ret .= '</div>'; // gdlr-ux
				$ret .= '</div>'; // gdlr-item
				$ret .= '</div>'; // column class
				$current_size ++;
			}
			$ret .= '</div>';
			wp_reset_postdata();
			
			return $ret;
		}
	}	
	if( !function_exists('gdlr_get_classic_carousel_product') ){
		function gdlr_get_classic_carousel_product($query, $size, $thumbnail_size){	
			global $post;

			$ret  = '<div class="gdlr-portfolio-carousel-item gdlr-item" >';	
			$ret .= '<div class="flexslider" data-type="carousel" data-nav-container="portfolio-item-wrapper" data-columns="' . $size . '" >';	
			$ret .= '<ul class="slides" >';
			while($query->have_posts()){ $query->the_post();
				$ret .= '<li class="gdlr-item gdlr-portfolio-item gdlr-classic-portfolio">';

				$product = new WC_Product( get_the_ID() );
				$price = $product->get_price_html();
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size);
				$ret .= '</div>'; // portfolio-thumbnail
 
				$ret .= '<h3 class="gdlra-skin-box portfolio-title ' . (empty($price)? '': 'with-price') . '">';
				$ret .= '<a href="' . get_permalink() . '" >' . get_the_title() . '</a>';
				
				if( !empty($price) ){
					$ret .= '<span class="gdlr-port-price">' . $price . '</span>';
				}
				$ret .= '</h3>';
				
				$ret .= '</li>';
			}			
			$ret .= '</ul>';
			$ret .= '</div>';
			$ret .= '</div>';
			
			return $ret;
		}		
	}	
	
	// print classic2 portfolio
	if( !function_exists('gdlr_get_classic2_product') ){
		function gdlr_get_classic2_product($query, $size, $thumbnail_size, $layout = 'fitRows'){
			if($layout == 'carousel'){ 
				return gdlr_get_classic2_carousel_product($query, $size, $thumbnail_size); 
			}		
		
			global $post;

			$current_size = 0;
			$ret  = '<div class="gdlr-isotope" data-type="portfolio" data-layout="' . $layout  . '" >';
			while($query->have_posts()){ $query->the_post();
				if( $current_size % $size == 0 ){
					$ret .= '<div class="clear"></div>';
				}			
				
				$ret .= '<div class="' . gdlr_get_column_class('1/' . $size) . '">';
				$ret .= '<div class="gdlr-item gdlr-portfolio-item gdlr-classic2-portfolio">';
				$ret .= '<div class="gdlr-ux gdlr-classic-portfolio-ux">';
				
				$product = new WC_Product( get_the_ID() );
				$price = $product->get_price_html();
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size);
				$ret .= '</div>'; // portfolio-thumbnail
 
				$ret .= '<h3 class="portfolio-title ' . (empty($price)? '': 'with-price') . '">';
				$ret .= '<a href="' . get_permalink() . '" >' . get_the_title() . '</a>';
				
				
				if( !empty($price) ){
					$ret .= '<span class="gdlr-port-price">' . $price . '</span>';
				}
				$ret .= '</h3>';
				
				$ret .= '</div>'; // gdlr-ux
				$ret .= '</div>'; // gdlr-item
				$ret .= '</div>'; // column class
				$current_size ++;
			}
			$ret .= '</div>';
			wp_reset_postdata();
			
			return $ret;
		}
	}	
	if( !function_exists('gdlr_get_classic2_carousel_product') ){
		function gdlr_get_classic2_carousel_product($query, $size, $thumbnail_size){	
			global $post;

			$ret  = '<div class="gdlr-portfolio-carousel-item gdlr-item" >';	
			$ret .= '<div class="flexslider" data-type="carousel" data-nav-container="portfolio-item-wrapper" data-columns="' . $size . '" >';	
			$ret .= '<ul class="slides" >';
			while($query->have_posts()){ $query->the_post();
				$ret .= '<li class="gdlr-item gdlr-portfolio-item gdlr-classic2-portfolio">';

				$product = new WC_Product( get_the_ID() );
				$price = $product->get_price_html();
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size);
				$ret .= '</div>'; // portfolio-thumbnail
 
				$ret .= '<h3 class="portfolio-title ' . (empty($price)? '': 'with-price') . '">';
				$ret .= '<a href="' . get_permalink() . '" >' . get_the_title() . '</a>';
				
				if( !empty($price) ){
					$ret .= '<span class="gdlr-port-price">' . $price . '</span>';
				}
				$ret .= '</h3>';
				
				$ret .= '</li>';
			}			
			$ret .= '</ul>';
			$ret .= '</div>';
			$ret .= '</div>';
			
			return $ret;
		}		
	}	
	
	// print modern portfolio
	if( !function_exists('gdlr_get_modern_product') ){
		function gdlr_get_modern_product($query, $size, $thumbnail_size, $layout = 'fitRows'){
			if($layout == 'carousel'){ 
				return gdlr_get_modern_carousel_product($query, $size, $thumbnail_size); 
			}
			
			global $post;

			$current_size = 0;
			$ret  = '<div class="gdlr-isotope" data-type="portfolio" data-layout="' . $layout  . '" >';
			while($query->have_posts()){ $query->the_post();
				if( $current_size % $size == 0 ){
					$ret .= '<div class="clear"></div>';
				}	
    
				$ret .= '<div class="' . gdlr_get_column_class('1/' . $size) . '">';
				$ret .= '<div class="gdlr-item gdlr-portfolio-item gdlr-modern-portfolio">';
				$ret .= '<div class="gdlr-ux gdlr-modern-portfolio-ux">';
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size, true);
				$ret .= '</div>'; // portfolio-thumbnail	
				$ret .= '</div>'; // gdlr-ux
				$ret .= '</div>'; // gdlr-item
				$ret .= '</div>'; // gdlr-column-class
				$current_size ++;
			}
			$ret .= '</div>';
			wp_reset_postdata();
			
			return $ret;
		}
	}	
	if( !function_exists('gdlr_get_modern_carousel_product') ){
		function gdlr_get_modern_carousel_product($query, $size, $thumbnail_size){	
			global $post;

			$ret  = '<div class="gdlr-portfolio-carousel-item gdlr-item" >';		
			$ret .= '<div class="flexslider" data-type="carousel" data-nav-container="portfolio-item-wrapper" data-columns="' . $size . '" >';	
			$ret .= '<ul class="slides" >';
			while($query->have_posts()){ $query->the_post();
				$ret .= '<li class="gdlr-item gdlr-portfolio-item gdlr-modern-portfolio">';
				
				$ret .= '<div class="portfolio-thumbnail gdlr-image">';
				$ret .= gdlr_get_product_thumbnail($thumbnail_size, true);
				$ret .= '</div>'; // portfolio-thumbnail
				$ret .= '</li>';
			}			
			$ret .= '</ul>';
			$ret .= '</div>'; // flexslider
			$ret .= '</div>'; // gdlr-item
			
			return $ret;
		}		
	}
	
?>