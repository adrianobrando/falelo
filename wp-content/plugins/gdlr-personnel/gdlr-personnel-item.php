<?php
	/*	
	*	Goodlayers Personnel Item Management File
	*	---------------------------------------------------------------------
	*	This file contains functions that help you create personnel item
	*	---------------------------------------------------------------------
	*/
	
	// add action to check for personnel item
	add_action('gdlr_print_item_selector', 'gdlr_check_personnel_item', 10, 2);
	if( !function_exists('gdlr_check_personnel_item') ){
		function gdlr_check_personnel_item( $type, $settings = array() ){
			if($type == 'personnel'){
				echo gdlr_print_personnel_item( $settings );
			}
		}
	}
	
	// print personnel item
	if( !function_exists('gdlr_print_personnel_item') ){
		function gdlr_print_personnel_item( $settings = array() ){
			$item_id = empty($settings['page-item-id'])? '': ' id="' . $settings['page-item-id'] . '" ';

			global $gdlr_spaces;
			$margin = (!empty($settings['margin-bottom']) && 
				$settings['margin-bottom'] != $gdlr_spaces['bottom-blog-item'])? 'margin-bottom: ' . $settings['margin-bottom'] . ';': '';
			$margin_style = (!empty($margin))? ' style="' . $margin . '" ': '';
			
			if( $settings['personnel-layout'] == 'carousel' ){ 
				$settings['carousel'] = true;
			}
			
			$ret  = gdlr_get_item_title($settings);				
			$ret .= '<div class="personnel-item-wrapper" ' . $item_id . $margin_style . ' data-ajax="' . AJAX_URL . '" >'; 

			// query posts section
			$args = array('post_type' => 'personnel', 'suppress_filters' => false);
			$args['posts_per_page'] = (empty($settings['num-fetch']))? '5': $settings['num-fetch'];
			$args['orderby'] = (empty($settings['orderby']))? 'post_date': $settings['orderby'];
			$args['order'] = (empty($settings['order']))? 'desc': $settings['order'];
			$args['paged'] = (get_query_var('paged'))? get_query_var('paged') : 1;

			if( !empty($settings['category']) ){
				$args['tax_query'] = array(array(
					'terms'=>explode(',', $settings['category']), 'taxonomy'=>'personnel_category', 'field'=>'slug'
				));
			}			
			$query = new WP_Query( $args );
			
			$ret .= '<div class="personnel-item-holder">';
			if($settings['personnel-layout'] == 'carousel'){ 
				$ret .= gdlr_get_classic_carousel_personnel($query, $settings['item-size'], $settings['thumbnail-size']); 
			}else{
				$ret .= gdlr_get_classic_personnel($query, $settings['item-size'], $settings['thumbnail-size'], $settings['personnel-layout'] );
			}
			$ret .= '<div class="clear"></div>';
			$ret .= '</div>';
			
			// create pagination
			if(!empty($settings['pagination']) && $settings['pagination'] == 'enable'){
				$ret .= gdlr_get_pagination($query->max_num_pages, $args['paged']);
			}
			
			$ret .= '</div>'; // personnel-item-wrapper
			return $ret;
		}
	}
	
	// get personnel info
	if( !function_exists('gdlr_get_personnel_info') ){
		function gdlr_get_personnel_info( $array = array(), $option = array(), $wrapper = true ){
			$ret = '';
			
			foreach($array as $post_info){	
				switch( $post_info ){
					case 'mail':
						if(empty($option['mail'])) break;
					
						$ret .= '<div class="personnel-info personnel-mail">';
						$ret .= '<i class="icon-envelope-alt fa fa-envelope-o" ></i>';
						$ret .= $option['mail'];						
						$ret .= '</div>';						
					
						break;	
					case 'phone':
						if(empty($option['telephone'])) break;
					
						$ret .= '<div class="personnel-info personnel-phone">';
						$ret .= '<i class="icon-phone fa fa-phone" ></i>';
						$ret .= $option['telephone'];						
						$ret .= '</div>';						

						break;	
					case 'social':
						if(empty($option['social'])) break;
					
						$ret .= '<div class="personnel-info personnel-social">';
						$ret .= gdlr_content_filter($option['social']);					
						$ret .= '</div>';						
					
						break;				
				}
			}

			if($wrapper && !empty($ret)){
				return '<div class="gdlr-personnel-info">' . $ret . '<div class="clear"></div></div>';
			}else if( !empty($ret) ){
				return $ret . '<div class="clear"></div>';
			}
			return '';
		}
	}
	
	// get personnel thumbnail
	if( !function_exists('gdlr_get_personnel_thumbnail') ){
		function gdlr_get_personnel_thumbnail($size = 'full'){
			$image_id = get_post_thumbnail_id();		
			
			$ret = '';
			if( !empty($image_id) ){
				$ret .= '<div class="gdlr-personnel-thumbnail" >';
				$ret .= gdlr_get_image($image_id, $size, true);
				$ret .= '</div>';
			}

			return $ret;
		}
	}	
	
	// print classic personnel
	if( !function_exists('gdlr_get_classic_personnel') ){
		function gdlr_get_classic_personnel($query, $size, $thumbnail_size, $layout = 'fitRows'){
			global $post;

			$current_size = 0;
			$ret  = '<div class="gdlr-isotope" data-type="personnel" data-layout="' . $layout  . '" >';
			while($query->have_posts()){ $query->the_post();
				if( $current_size % $size == 0 ){
					$ret .= '<div class="clear"></div>';
				}			
    
				$ret .= '<div class="' . gdlr_get_column_class('1/' . $size) . '">';
				$ret .= '<div class="gdlr-item gdlr-personnel-item">';
				
				$pers_option = json_decode(gdlr_decode_preventslashes(get_post_meta($post->ID, 'post-option', true)), true);
				$ret .= gdlr_get_personnel_thumbnail($thumbnail_size);
 
				$ret .= '<h3 class="personnel-title"><a href="' . get_permalink() . '" >' . get_the_title() . '</a></h3>';
				if( !empty($pers_option['position']) ){
					$ret .= '<div class="personnel-position" >' . $pers_option['position'] . '</div>';
				}
				
				if( !empty($pers_option['social']) ){
					$ret .= '<div class="personnel-social" >';
					$ret .= '<div class="personnel-social-divider"></div>';
					$ret .= gdlr_content_filter($pers_option['social']);
					$ret .= '</div>';
				}
				$ret .= '<a href="' . get_permalink() . '" class="gdlr-button small gdlr-personnel-read-more" >' . __('Read Profile', 'gdlr-personnel') . '</a>';
				
				$ret .= '</div>'; // gdlr-item
				$ret .= '</div>'; // column class
				$current_size ++;
			}
			$ret .= '</div>';
			wp_reset_postdata();
			
			return $ret;
		}
	}	
	if( !function_exists('gdlr_get_classic_carousel_personnel') ){
		function gdlr_get_classic_carousel_personnel($query, $size, $thumbnail_size){	
			global $post;

			$ret  = '<div class="gdlr-personnel-carousel-item gdlr-item" >';	
			$ret .= '<div class="flexslider" data-type="carousel" data-nav-container="personnel-item-wrapper" data-columns="' . $size . '" >';	
			$ret .= '<ul class="slides" >';
			while($query->have_posts()){ $query->the_post();
				$ret .= '<li class="gdlr-item gdlr-personnel-item">';

				$pers_option = json_decode(gdlr_decode_preventslashes(get_post_meta($post->ID, 'post-option', true)), true);
				$ret .= gdlr_get_personnel_thumbnail($thumbnail_size);
 
				$ret .= '<h3 class="personnel-title"><a href="' . get_permalink() . '" >' . get_the_title() . '</a></h3>';
				if( !empty($pers_option['position']) ){
					$ret .= '<div class="personnel-position" >' . $pers_option['position'] . '</div>';
				}
				
				if( !empty($pers_option['social']) ){
					$ret .= '<div class="personnel-social" >';
					$ret .= '<div class="personnel-social-divider"></div>';
					$ret .= gdlr_content_filter($pers_option['social']);
					$ret .= '</div>';
				}
				$ret .= '<a href="' . get_permalink() . '" class="gdlr-button small gdlr-personnel-read-more" >' . __('Read Profile', 'gdlr-personnel') . '</a>';
				
				$ret .= '</li>';
			}			
			$ret .= '</ul>';
			$ret .= '</div>';
			$ret .= '</div>';
			
			return $ret;
		}		
	}	
	
?>