<?php

use App\Madara;
global $mymangas;

$mymangas = apply_filters('madara_my_mangas_list', $mymangas); 

$madara_post_count = $mymangas->post_count;
$manga_archives_item_layout = 'simple'; // edit this if you want to change layout

	?>
<div class="c-page-content">
	<div class="c-page">
		<div class="c-blog-listing c-page__content manga_content">
			<div class="c-blog__inner">
				<div class="c-blog__content">
					<div id="loop-content" style="margin-top:0" class="page-content-listing <?php echo esc_attr('item-' . $manga_archives_item_layout);?>">

						<?php
							$index = 1;
							set_query_var( 'madara_post_count', $madara_post_count );
							set_query_var('manga_archives_item_layout', $manga_archives_item_layout);
						?>

						<?php while ( $mymangas->have_posts() ) : $mymangas->the_post(); ?>

							<?php
							set_query_var( 'madara_loop_index', $index );

							get_template_part( 'madara-core/user/page/item-mymanga' );

							$index ++;

							?>

						<?php endwhile;
							wp_reset_postdata(); ?>

					</div>
					

					<?php
						$template = 'madara-core/user/page/item-mymanga';
						//Get Pagination
						$madara_pagination = new App\Views\ParsePagination();
						$madara_pagination->renderPageNavigation( '#loop-content', $template, $mymangas, 'ajax' );
					?>
					
					<script type="text/javascript">
						// update args
						__madara_query_vars['manga_archives_item_layout'] = '<?php echo esc_js($manga_archives_item_layout);?>';
						__madara_query_vars['madara_post_count'] = <?php echo esc_js($madara_post_count);?>;
					</script>
				</div>
			</div>
		</div>	
	</div>
</div>