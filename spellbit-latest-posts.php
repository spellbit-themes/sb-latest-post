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
		$read_more = '<a href="'.get_permalink().'" class="readmore-sp">read more</a>';
	?>

	
	<!-- image -->
	<div class="sp-blog-single-blog">
	<div class="sp-blog-thumb">
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
	</div>

	<!-- title && content -->
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<div class="sp-dflex bottom">
	<div class="sptagsa">
	    <!-- tags -->
		<?php 
		if( $tag === 'yes'){
        	the_tags(" "); 
    	}
    ?>
  </div>
	

	<div class="sptagsa">
			<!-- category -->
			<?php 

	if($cat === 'yes'){
		the_category(" "); 
	}
	?>
	</div>
	</div>
    <?php echo wp_trim_words(get_the_content(), $words, $read_more ); ?>

	<div class="sp-dflex  top">
	<div class="sptagsa">
		 <!-- date -->
		 <?php if( $date === 'yes' ){ ?>
		<a href="<?php the_permalink();?>"><?php echo get_the_time('d F, Y'); ?></a>
    <?php } ?>
	</div>
   

   <div class="sptagsa">
	    <!-- author -->
 	<?php if( $author === 'yes'){ ?>
		<a href="<?php  echo esc_url($author_link); ?>"><?php the_author(); ?></a>
 	<?php } ?>
   </div>
	</div>

</div>

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

add_action( 'admin_menu', 'register_my_custom_menu_page' );
function register_my_custom_menu_page() {
  // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
  add_menu_page( 'Latest Posts', 'SB Latest Posts', 'manage_options', 'custom.php', 'latest_post_panel_func', 'dashicons-welcome-widgets-menus', 90 );
}



function latest_post_panel_func(){
	?>
<h1>Latest Post</h1>
<h2>You can use this plugin in two ways as Widget & Shortcode.</h2>

<h3>1 : Widget</h3>
<p>To show latest post in sidebar, you can go <b>appearance -> Widget</b><br />
you will find a widget named "<b>Spellbit Latest Posts</b>"
</p>
<img src="<?php echo Plugins_url('/images/widget_1.png', __FILE__); ?>" alt="">
<h1>Let's have a look at the widget options</h1>
<h2>1. Widget Title:</h2> 
<p>If you want to show widget  title, then you can give a widget title here.
Otherwise, it will be blank.</p>

<h2>2. Post Number:</h2>
<p>You can define the number of latest posts to show in sidebar.</p>

<h2>3. Post Excerpt:</h2>
<p>Now you can decide whether you want to show the excert or not.
If you want, then you need to tick it. 
After that, you will find the options for the exerpt words.</p>

<h2>4. Excerpt Words:</h2>
<p>You can define the number of words, you want to show as the content.</p>

<h2>5. Posts Order</h2>
<p>You can define the post order from here. If you select ASC or DESC. By default, DESC is selected.</p>
<img src="<?php echo Plugins_url('/images/widget_3.png', __FILE__); ?>" alt="">


<h1>2: Shortcode</h1>
<h3>You can use the shorcode to any pages, you have created in your dashboard. <br />Go to page where you want to show the latest post.<br /> Then you need to use the shortcode</h3>

<code>[spellbit_latest_posts img="yes" date="yes" tag="yes" cat="yes" words="30"]</code>
<h2>Let's discuss with the options of shortcode</h2>
<ul>
	<li><h2>img="yes" :</h2> <h4>If you want to show the image thumbnail, then you will have to use "yes".<br />If you don't want to use image, then use "no"</h4></li>
	<li><h2>date="yes" :</h2> <h4>If you want to show the date, then you will have to use "yes".<br />If you don't want to use date, then use "no"</h4></h4></li>
	<li><h2>tag="yes" :</h2> <h4>If you want to show the tags, then you will have to use "yes".<br />If you don't want to use tag, then use "no"</h4></h4></li>
	<li><h2>cat="yes" :</h2> <h4>If you want to show the categories, then you will have to use "yes".<br />If you don't want to use categories, then use "no"</h4></h4></li>
	<li><h2>words="30" :</h2> <h4>If you want to modify the number text of the excerpt, then you can do it by changing the number</h4></h4></li>
</ul>
<br />
<br />
<img class="img-responsive" src="<?php echo Plugins_url('/images/shortcode-1.png', __FILE__); ?>" alt="">
	<?php
}



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






