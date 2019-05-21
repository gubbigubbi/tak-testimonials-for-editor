<?php
function tak_register_block() {

	// Only load if Gutenberg is available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type('cgb/tak-posts-slider', array(
		'render_callback' => 'posts_slider_render_callback',
			'attributes' => array(
				'numberCols' => array(
					'type' 		=> 'number',
					'default'	=> '2' // nb: a default is needed!
				),
				'postsToShow' => array(
					'type' 		=> 'number',
					'default' => 4
				),
				'categories'=> array(
					'type' 		=> 'string',
				),
				'order'     => array(
					'type'    => 'string',
					'default' => 'desc',
				),
				'orderBy'   => array(
					'type'    => 'string',
					'default' => 'date',
				),
				'startAt'   => array(
					'type'    => 'number',
					'default' => 0,
				),
				'slidesToScroll' => array(
					'type'    => 'number',
					'default' => 1,
				),
				'autoPlay' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'align'	=> array(
					'type' => 'string',
					'default' => '',
				),
			)
		)
	);
}

add_action('init', 'tak_register_block');

function posts_slider_render_callback( array $attributes ){

	$align = $attributes['align'];
	$numberCols = $attributes['numberCols'];
	$slidesToScroll = $attributes['slidesToScroll'];
	$autoPlay = isset($attributes['autoPlay']) ? 'true' : 'false';
	$categories = $attributes['categories'];
	
	$args = array(
		'post_type'    		=> 'post',
		'post_status' 		=> 'publish',
		'posts_per_page'  => $attributes['postsToShow'],
		'order'       		=> $attributes['order'],
		'orderby'    		  => $attributes['orderBy'],
		'category__in' 		=> $categories,
		'offset'					=> $attributes['startAt'],
	);

	$query = new WP_Query($args);

	if ( $query->have_posts() ) { 

		$projectContainer = '<div class="wp-block-cgb-posts-slider slick-loading slick-slider slick-has-'.$numberCols.'-columns align'.$align.'" data-slick=\'{
			"slidesToShow": '.$numberCols.', 
			"slidesToScroll": '.$slidesToScroll.',
			"autoplay": '.$autoPlay.',
			"autoplaySpeed": 4000,
			"infinite": true
		}\'>';

		while( $query->have_posts() ) { 
			$query->the_post();

			$button = '<p class="sk-blog-item-read-more">Read More</p>';

			$projectContainer .= '
			
			<article class="sk-posts-slider-item">
				<a class="sk-blog-item-link" href="'.get_the_permalink().'">
					<span class="sk-blog-item-content">
						<p class="sk-blog-item-content__title">'.get_the_title().'</p>';
						$projectContainer .= $button;
						$projectContainer .= ' 
					</span>
					'.get_the_post_thumbnail($project->ID, 'square_thumbnail', array('class' => 'image-responsive display-block')).'
				</a>		
			</article>
			';
		}
		wp_reset_postdata();

		return "{$projectContainer}</div>";
	}

}
// 	}