<?php
	/*
	*  Manga Archive
	*/

	use App\Madara;

	get_header();

	$wp_query = madara_get_global_wp_query();

	$madara_page_sidebar   = madara_get_manga_archive_sidebar();

	$madara_breadcrumb     = Madara::getOption( 'manga_archive_breadcrumb', 'on' );

	$manga_archive_heading = Madara::getOption( 'manga_archive_heading', esc_html__('All Mangas', 'madara') );
	$manga_archive_heading = apply_filters( 'madara_archive_heading', $manga_archive_heading );
	
	$manga_archives_item_layout = Madara::getOption( 'manga_archives_item_layout', 'default' );

	//set args
	if ( ! empty( get_query_var( 'paged' ) ) ) {
		$paged = get_query_var( 'paged' );
	} elseif ( ! empty( get_query_var( 'page' ) ) ) {
		$paged = get_query_var( 'page' );
	} else {
		$paged = 1;
	}

	$orderby = isset( $_GET['m_orderby'] ) ? $_GET['m_orderby'] : 'latest';

	$manga_args = array(
		'paged'    => $paged,
		'orderby'  => $orderby,
		'template' => 'archive',
		'sidebar'  => $madara_page_sidebar,
	);

	foreach ( $manga_args as $key => $value ) {
		$wp_query->set( $key, $value );
	}

	if ( is_home() || is_front_page() || is_manga_posttype_archive() ) {
		$manga_query = madara_manga_query( $manga_args );
	} else {
		$manga_query = madara_manga_query( $wp_query->query_vars );
	}

?>
<script type="text/javascript">
	var manga_args = <?php echo str_replace( '\/', '/', json_encode( $manga_query->query_vars ) ); ?>;
</script>
<?php
	if ( $madara_breadcrumb == 'on' ) {
		get_template_part( 'madara-core/manga', 'breadcrumb' );
	}
?>
<div class="c-page-content style-1">
    <div class="content-area">
        <div class="container">
            <div class="row <?php echo esc_attr( $madara_page_sidebar == 'left' ? 'sidebar-left' : ''); ?>">

                <div class="main-col <?php echo esc_attr( $madara_page_sidebar != 'full' && ( is_active_sidebar( 'manga_archive_sidebar' ) || is_active_sidebar( 'main_sidebar' ) ) ? 'col-md-8 col-sm-8' : 'sidebar-hidden col-md-12 col-sm-12'); ?>">

					<?php get_template_part( 'html/main-bodytop' ); ?>

                    <!-- container & no-sidebar-->
                    <div class="main-col-inner">
                        <div class="c-page">
							<?php if ( is_tax() ) { 
									$tax = get_queried_object();
								?>
                                <div class="entry-header">
                                    <div class="entry-header_wrap">
                                        <div class="entry-title">
                                            <h1 class="item-title h4"><?php echo apply_filters( 'madara_archive_taxonomy_heading', isset( $tax->name ) ? $tax->name : '' ); ?></h1>
											<?php
											if($tax->description != '') {?>
											<p class="item-description"><?php echo wp_kses_post($tax->description);?></p>
											<?php }?>
                                        </div>
                                    </div>
                                </div>
							<?php } else if ( is_manga_archive() && $manga_archive_heading != '' ) { ?>

                                <div class="entry-header">
                                    <div class="entry-header_wrap">
                                        <div class="entry-title">
                                            <h1 class="item-title h4"><?php echo esc_html( $manga_archive_heading ) ?></h1>
                                        </div>
                                    </div>
                                </div>

							<?php } ?>
                            <!-- <div class="c-page__inner"> -->
                            <div class="c-page__content">
                                <div class="tab-wrap">
                                    <div class="c-blog__heading style-2 font-heading">

                                        <div class="h4">
                                            <i class="<?php madara_default_heading_icon(); ?>"></i>
											<?php echo sprintf( _n( '%s result', '%s results', $manga_query->post_count, 'madara' ), $manga_query->found_posts ); ?>
                                        </div>
										<?php get_template_part( 'madara-core/manga-filter' ); ?>
                                    </div>
                                </div>
                                <!-- Tab panes -->
                                <div class="tab-content-wrap">
                                    <div role="tabpanel" class="c-tabs-item">
                                        <div class="page-content-listing <?php echo esc_attr('item-' . $manga_archives_item_layout);?>">
											<?php
												if ( $manga_query->have_posts() ) {

													$index = 1;
													$wp_query->set( 'madara_post_count', madara_get_post_count( $manga_query ) );
													$wp_query->set('manga_archives_item_layout', $manga_archives_item_layout);

													while ( $manga_query->have_posts() ) {

														$wp_query->set( 'madara_loop_index', $index );
														$index ++;

														$manga_query->the_post();
														get_template_part( 'madara-core/content/content', 'archive' );
														
														do_action('madara-manga-archive-loop', $index);
													}

												} else {
													get_template_part( 'madara-core/content/content-none' );
												}

												wp_reset_postdata();

											?>
                                        </div>
										<?php
											$madara_pagination = new App\Views\ParsePagination();
											$madara_pagination->renderPageNavigation( '.c-tabs-item .page-content-listing', 'madara-core/content/content-archive', $manga_query );
										?>
                                    </div>
                                </div>
                            </div>
                            <!-- </div> -->
                        </div>
                        <!-- paging -->
                    </div>

					<?php get_template_part( 'html/main-bodybottom' ); ?>

                </div>
				<?php
					if ( $madara_page_sidebar != 'full' && ( is_active_sidebar( 'manga_archive_sidebar' ) || is_active_sidebar( 'main_sidebar' ) ) ) {
						?>
                        <div class="sidebar-col col-md-4 col-sm-4">
							<?php get_sidebar(); ?>
                        </div>
						<?php
					}
				?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
