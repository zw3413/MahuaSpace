<?php
use App\Madara;
global $wp_manga_functions;
			
$current_read_chapter = 0;
if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$history = madara_get_current_reading_chapter($user_id, $manga_id);
	if($history){
		$current_read_chapter = $history['c'];
	}
}

$init_links_enabled = Madara::getOption('init_links_enabled', 'on') == 'on' ? true : false;

if($init_links_enabled){ ?>

<div id="init-links" class="nav-links">
	<?php 
	if($current_read_chapter) {
		$reading_style     = $wp_manga_functions->get_reading_style();
		global $wp_manga_chapter;
		$current_chapter = $wp_manga_chapter->get_chapter_by_id( $manga_id, $current_read_chapter );
		$current_chapter_link = $wp_manga_functions->build_chapter_url( $manga_id, $current_chapter, $reading_style );
	?>
	<a href="<?php echo esc_url($current_chapter_link);?>" class="c-btn c-btn_style-1" title="<?php echo esc_attr($current_chapter['chapter_name']);?>"><?php esc_html_e('Continue reading', 'madara');?></a>
	<?php
	} else {
		global $wp_manga_database;
		global $sort_setting;
		
		if(!isset($sort_setting)){
			$sort_setting = $wp_manga_database->get_sort_setting();
		}

		$sort_order = $sort_setting['sort'];
		
		if($sort_order == 'asc'){
			?>
			<a href="#" id="btn-read-first" class="c-btn c-btn_style-1">
			<?php esc_html_e('Read First', 'madara');?></a>
			<a href="#" id="btn-read-last" class="c-btn c-btn_style-1"><?php esc_html_e('Read Last', 'madara');?></a>
			<?php
		} else {
			?>
			<a href="#" id="btn-read-last" class="c-btn c-btn_style-1">
			<?php esc_html_e('Read First', 'madara');?></a>
			<a href="#" id="btn-read-first" class="c-btn c-btn_style-1"><?php esc_html_e('Read Last', 'madara');?></a>
			<?php
		}
	}?>
</div>

<?php }?>