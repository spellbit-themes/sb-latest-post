<?php 
/*
Plugin Name: Spellbit SB Latest Posts
Plugin URI: http://spellbit.com
Author: Spellbit
Author URI: http://spellbit.com
Version: 1.0.1
Description: Premium Type Latest Posts For Free
License: GPLv2 or later
Text Domain: spellbit
*/
defined('ABSPATH') or die("Get Lost from here, you idot");


/**
*
* [spellbit_latest_posts img="yes" date="yes" tag="yes" cat="yes" words="30"]
*
*/

add_shortcode('spellbit_latest_posts', 'spellbit_latest_posts_func');

function spellbit_latest_posts_func($atts, $content){
	
	extract( shortcode_atts(array(
		'img'		=> '',
		'date'		=> '',
		'tag'		=> '',
		'cat'		=> '',
		'author'	=> 'yes',
		'words'		=> 20
	), $atts));

	ob_start(); 

	$q = new WP_Query(array(
		'post_type' => 'post',
		'posts_per_page' => -1,
	));

	
	while( $q->have_posts() ):$q->the_post(); 
		$author_link = get_author_posts_url( get_the_author_meta( 'ID' ));
		$read_more = '<br /><a href="'.get_permalink().'">read more</a>';
	?>

	
	<!-- image -->
	<?php	
		if( $img === 'yes'){

			if( has_post_thumbnail() ){
				
				the_post_thumbnail( 'thumbnail', array(
					'class' => 'img-responsive',
					'alt' => the_title_attribute( array(
						'echo' => false,
					) ),
				) );
			}

		}
	?>

	<!-- title && content -->
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <?php echo wp_trim_words(get_the_content(), $words, $read_more ); ?>

    <!-- date -->
    <?php if( $date === 'yes' ){ ?>
		<h6><?php echo get_the_time('d F, Y'); ?></h6>
    <?php } ?>


    <!-- tags -->
    <?php 
		if( $tag === 'yes'){
        	the_tags(); 
    	}
    ?>
	

	<!-- category -->
    <?php 

        if($cat === 'yes'){
        	the_category(); 
        }
    ?>

    <!-- author -->
 	<?php if( $author === 'yes'){ ?>
		<a href="<?php  echo esc_url($author_link); ?>"><?php the_author(); ?></a>
 	<?php } ?>

	<hr />

	<?php endwhile; wp_reset_postdata(); 
	return ob_get_clean();
}








add_action('init', 'spellbit_latest_posts_files');


	// plugin all css and js files
function spellbit_latest_posts_files(){

	/**
	*
	* css files
	*
	*/		
	
	wp_register_style('sb-latest-posts-style', Plugins_url('/css/sb-latest-posts-style.css', __FILE__), array(), '1.0.0', 'all');		
	wp_enqueue_style('sb-latest-posts-style');


	/**
	*
	* js files
	*
	*/
	 wp_enqueue_script('sb-latest-posts-js', Plugins_url('/js/sb-latest-posts.js', __FILE__), array('jquery'), '5.0.1', true);

}





/**
* settings api
*
*/
add_action('admin_init', 'sp_latest_posts_func');

function sp_latest_posts_func(){


	add_settings_field( 'header_text', 'Header Title', 'header_text_func', 'reading', 'default', array( '' ) );

	register_setting('reading', 'header_text');

}

function header_text_func(){
?>
	
	<input type="text" name="header_text" class="regular-text" value="<?php echo get_option('header_text'); ?>">

<?php }







/**
* latest post widget
*
*/

Class Latest_posts_Widget extends WP_Widget{

	public function __construct(){
		parent::__construct('spellbit-latest-posts', 'Spellbit Latest Posts', array(
			'description'	=> 'Latest Post Widget by Spellbit'
		));
	}


	public function widget($args, $instance){

		extract($args);
		extract($instance);
	 	echo $before_widget; 
	 		if($instance['title']):
     		echo $before_title; ?> 
     			<?php echo apply_filters( 'widget_title', $instance['title'] ); ?>
     		<?php echo $after_title; ?>
     	<?php endif; ?>
		    <div class="sidebar-rc-post">
		        <ul class="spLP-sidebar">
		        	
		    <?php 
			$q = new WP_Query( array(
			    'post_type'     => 'post',
			    'posts_per_page'=> ($instance['count']) ? $instance['count'] : '3',
			    'order'			=> ($instance['posts_order']) ? $instance['posts_order'] : 'DESC',
			    'ignore_sticky_posts' => 1
			));

			if( $q->have_posts() ):
			while( $q->have_posts() ):$q->the_post();
				$words = ($instance['content_words']) ? $instance['content_words'] : '10';
				$read_more = '<a href="<?php the_permalink();?>" class="readmore-sp">Read More </a>';
			?>
		            <li class="sbSingleLatesPost">
						<?php $sp_latest_post_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_queried_object_id()),'full', true);?>
						<?php if( has_post_thumbnail() ): ?>
							<a class="rc-post-thumb" href="<?php the_permalink(); ?>" style="background-image: url(<?php echo esc_url($sp_latest_post_thumb[0]);?>)">
								
							</a>
						<?php endif; ?>
		                <div class="rc-post-content">
		                    <h4>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								<?php
									edit_post_link(
										sprintf(
										/* translators: %s: Name of current post */
											esc_html__( 'Edit %s', 'spellbit' ),
											the_title( '<span class="screen-reader-text">"', '"</span>', false )
										),
										'<small class="edit-linksp">',
										'</small>'
									); ?>
							</h4>
							<span class="postDate"><small><?php the_time('F d, Y'); ?></small></span>
		                     <?php 
		                     if( !empty($show_content)){
		                     	print wp_trim_words(get_the_content(), $words, $read_more ); 
		                     }                    
		                     ?>
		                </div>
		            </li>
				<?php endwhile;            
			 endif; ?> 
		        </ul>
		    </div>
		<?php echo $after_widget; ?>

		<?php
	}



	public function form($instance){
		extract($instance);
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = ! empty( $instance['count'] ) ? $instance['count'] : esc_html__( '3', 'spellbit' );
		$content_words = ! empty( $instance['content_words'] ) ? $instance['content_words'] : esc_html__( '10', 'spellbit' );
		$posts_order = ! empty( $instance['posts_order'] ) ? $instance['posts_order'] : esc_html__( 'DESC', 'spellbit' );
		$show_content = ! empty( $instance['show_content'] ) ? $instance['show_content'] : '';
	?>	
			

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">How many posts you want to show ?</label>
			<input type="number" name="<?php echo $this->get_field_name('count'); ?>" id="<?php echo $this->get_field_id('count'); ?>" value="<?php echo esc_attr( $count ); ?>" class="widefat">
		</p>


		<p class="spellbit_show_excerpt">
			<label for="<?php echo $this->get_field_id('show_content'); ?>">Show the excerpt </label>
			<input type="checkbox" name="<?php echo $this->get_field_name('show_content'); ?>" id="<?php echo $this->get_field_id('show_content'); ?>" <?php if($show_content){ echo "checked"; } ?> class="sp_show_excerpt"
			 >
		</p>

		<p class="widget_show_content_wrapper">
			<label for="<?php echo $this->get_field_id('content_words'); ?>">Excerpt Words?</label>
			<input type="number" name="<?php echo $this->get_field_name('content_words'); ?>" id="<?php echo $this->get_field_id('content_words'); ?>" value="<?php echo esc_attr( $content_words ); ?>" class="widefat">
		</p>
	
		<p>
			<label for="<?php echo $this->get_field_id('posts_order'); ?>">Posts Order</label>
			<select name="<?php echo $this->get_field_name('posts_order'); ?>" id="<?php echo $this->get_field_id('posts_order'); ?>" class="widefat">
				<option value="" disabled="disabled">Select Post Order</option>
				<option value="ASC" <?php if($posts_order === 'ASC'){ echo 'selected="selected"'; } ?>>ASC</option>
				<option value="DESC" <?php if($posts_order === 'DESC'){ echo 'selected="selected"'; } ?>>DESC</option>
			</select>
		</p>

		
	<?php }


}



add_action('widgets_init', function(){
	register_widget('Latest_posts_Widget');
});




/**
*
*
*/
add_action('admin_print_scripts', 'comet_inline_js', 1000);
function comet_inline_js(){
	?>


		<script type="text/javascript">
			jQuery(document).ready(function(){


				var id = jQuery('input[type="checkbox"]:checked').attr('id');
				var inputName = jQuery('input[type="checkbox"]:checked').attr('name');
				var value = jQuery('.spellbit_show_excerpt input[name="'+inputName+'"]').val();


				if( value == 'on'){
					jQuery('.widget_show_content_wrapper').show();
				}else{
					jQuery('.widget_show_content_wrapper').hide();
				}


				jQuery('.spellbit_show_excerpt input[name="'+inputName+'"]').change(function(){

					var value = jQuery('.spellbit_show_excerpt input[name="'+inputName+'"]').val();


					if( value == 'on'){
						jQuery('.widget_show_content_wrapper').show();
					}else{
						jQuery('.widget_show_content_wrapper').hide();
					}


				});
		
		
			});	
		</script>

	<?php			
}






