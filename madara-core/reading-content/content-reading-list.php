<?php
	/** Manga Reading Content - List Style **/

	use App\Madara;

	$wp_manga_functions = madara_get_global_wp_manga_functions();
	$post_id  = get_the_ID();
	
	$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
	
	if(!$reading_chapter){
		 // support Madara Core before 1.6
		 if($chapter_slug = get_query_var('chapter')){
			global $wp_manga_functions;
			$reading_chapter = $wp_manga_functions->get_chapter_by_slug( $post_id, $chapter_slug );
		 }
		 if(!$reading_chapter){
			return;
		 }
	}
	
	$name = $reading_chapter['chapter_slug'];
	
	global $wp_manga;
	$paged    = isset( $_GET[$wp_manga->manga_paged_var] ) ? $_GET[$wp_manga->manga_paged_var] : 1;
	$style    = isset( $_GET['style'] ) ? $_GET['style'] : 'paged';

	$manga_reading_content_gaps = Madara::getOption( 'manga_reading_content_gaps', 'on' );

	$is_lazy_load = Madara::getOption( 'lazyload', 'off' ) == 'on' ? true : false;
	if ( $is_lazy_load ) {
		$lazyload = 'wp-manga-chapter-img img-responsive lazyload effect-fade';
	} else {
		$lazyload = 'wp-manga-chapter-img';
	}

	$chapter  = $wp_manga_functions->get_single_chapter( $post_id, $reading_chapter['chapter_id'] );
	$in_use   = $chapter['storage']['inUse'];
	$alt_host = isset( $_GET['host'] ) ? $_GET['host'] : null;
	if ( $alt_host ) {
		$in_use = $alt_host;
	}
	
	$storage = $chapter['storage'];
	if ( ! isset( $storage[ $in_use ] ) || ! is_array( $storage[ $in_use ]['page'] ) ) {
		return;
	}

	$madara_reading_list_total_item = 0;
	
	$need_button_fullsize = false;

	foreach ( $chapter['storage'][ $in_use ]['page'] as $page => $link ) {

		$madara_reading_list_total_item = count( $chapter['storage'][ $in_use ]['page'] );

		$host = $chapter['storage'][ $in_use ]['host'];
		$src  = apply_filters('wp_manga_chapter_image_url', $host . $link['src'], $host, $link['src'], $post_id, $name);
		
		if($src != ''){

		?>

		<?php do_action( 'madara_before_chapter_image_wrapper', $page, $madara_reading_list_total_item ); ?>

        <div class="page-break <?php echo( esc_attr($manga_reading_content_gaps == 'off' ? 'no-gaps' : '' )); ?>">

			<?php do_action( 'madara_before_chapter_image', $page, $madara_reading_list_total_item ); ?>

            <img id="image-<?php echo esc_attr( $page ); ?>" <?php if($is_lazy_load){ echo 'data-src="'; } else { echo 'src="';}?>
			
			<?php echo esc_url( $src ); ?>" class="<?php echo esc_attr( $lazyload ); ?>">
			
			<?php 
			/**
			 * temporary comment this to improve performance
			 
			if(!$need_button_fullsize) {
				list($width, $height, $type, $attr) = @getimagesize($src);
				
				if(isset($width) && $width > 1140) {
					$need_button_fullsize = true;
				}
			}
			**/
			
			do_action( 'madara_after_chapter_image', $page, $madara_reading_list_total_item ); ?>
        </div>

		<?php do_action( 'madara_after_chapter_image_wrapper', $page, $madara_reading_list_total_item ); 
		
		}
	}
	
	if($need_button_fullsize){ ?>
			<a href="javascript:void(0)" id="btn_view_full_image"><?php esc_html_e('View Full Size Image', 'madara');?></a>
		<?php
		}