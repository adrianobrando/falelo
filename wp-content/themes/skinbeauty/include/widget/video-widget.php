<?php
/**
 * Plugin Name: Goodlayers Video
 * Plugin URI: http://goodlayers.com/
 * Description: A widget that show video.
 * Version: 1.0
 * Author: Goodlayers
 * Author URI: http://www.goodlayers.com
 *
 */

add_action( 'widgets_init', 'gdlr_video_widget' );
if( !function_exists('gdlr_video_widget') ){
	function gdlr_video_widget() {
		register_widget( 'Goodlayers_Video_Widget' );
	}
}

if( !class_exists('Goodlayers_Video_Widget') ){
	class Goodlayers_Video_Widget extends WP_Widget{

		// Initialize the widget
		function __construct() {
			parent::__construct(
				'gdlr-video-widget', 
				__('Goodlayers Video Widget','gdlr_translate'), 
				array('description' => __('A widget that show video specify by url', 'gdlr_translate')));  
		}

		// Output of the widget
		function widget( $args, $instance ) {
			global $theme_option;	
				
			$title = apply_filters( 'widget_title', $instance['title'] );
			$url = $instance['url'];
			$video_title = $instance['video_title'];
			$video_caption = $instance['video_caption'];
			
			// Opening of widget
			echo $args['before_widget'];
			
			// Open of title tag
			if( !empty($title) ){ 
				echo $args['before_title'] . $title . $args['after_title']; 
			}
				
			// Widget Content
			echo '<div class="gdlr-video-widget">';
			echo '<div class="gdlr-video-widget-video">';
			echo gdlr_get_video($url, 300);
			echo '</div>';
			
			echo '<h3 class="gdlr-video-widget-title" >' . $video_title . '</h3>';
			echo '<div class="gdlr-video-widget-caption" >' . $video_caption . '</div>';
			echo '</div>';
					
			// Closing of widget
			echo $args['after_widget'];	
		}

		// Widget Form
		function form( $instance ) {
			$title = isset($instance['title'])? $instance['title']: '';
			$url = isset($instance['url'])? $instance['url']: '';
			$video_title = isset($instance['video_title'])? $instance['video_title']: '';
			$video_caption = isset($instance['video_caption'])? $instance['video_caption']: '';
			?>

			<!-- Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :', 'gdlr_translate'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>		
			
			<!-- URL --> 
			<p>
				<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Video URL :', 'gdlr_translate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
			</p>

			<!-- Video Title -->
			<p>
				<label for="<?php echo $this->get_field_id('video_title'); ?>"><?php _e('Video Title :', 'gdlr_translate'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('video_title'); ?>" name="<?php echo $this->get_field_name('video_title'); ?>" type="text" value="<?php echo $video_title; ?>" />
			</p>
			
			<!-- Video Caption -->
			<p>
				<label for="<?php echo $this->get_field_id('video_caption'); ?>"><?php _e('Video Caption :', 'gdlr_translate'); ?></label> 
				<textarea class="widefat" id="<?php echo $this->get_field_id('video_caption'); ?>" name="<?php echo $this->get_field_name('video_caption'); ?>" type="text" ><?php echo $video_caption; ?></textarea>
			</p>				
			
		<?php
		}
		
		// Update the widget
		function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = (empty($new_instance['title']))? '': strip_tags($new_instance['title']);
			$instance['url'] = (empty($new_instance['url']))? '': strip_tags($new_instance['url']);
			$instance['video_title'] = (empty($new_instance['video_title']))? '': strip_tags($new_instance['video_title']);
			$instance['video_caption'] = (empty($new_instance['video_caption']))? '': strip_tags($new_instance['video_caption']);
			
			return $instance;
		}	
	}
}
?>