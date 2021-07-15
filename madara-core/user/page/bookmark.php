<?php

	if ( ! is_user_logged_in() ) {
		return;
	}

	$wp_manga_functions = madara_get_global_wp_manga_functions();
	$user_id       = get_current_user_id();
	$bookmarks     = get_user_meta( $user_id, '_wp_manga_bookmark', true );
	$reading_style = $GLOBALS['wp_manga_functions']->get_reading_style();
	$reading_style = ! empty( $reading_style ) ? $reading_style : 'paged';

?>

<table class="table table-hover list-bookmark">
    <thead>
    <tr>
        <th><?php esc_html_e( 'Manga Name', 'madara' ); ?></th>
        <th><?php esc_html_e( 'Updated Time', 'madara' ); ?></th>
        <th><?php esc_html_e( 'Edit', 'madara' ); ?></th>
    </tr>
    </thead>
    <tbody>

	<?php if ( ! empty( $bookmarks ) ) {
		foreach ( $bookmarks as $bookmark ) {

			$post = get_post( intval( $bookmark['id'] ) );

			if ( $post == null || $post->post_status !== 'publish' ) {
				continue;
			}

			$post_id = $bookmark['id'];

			//get bookmarked chapter
			$chapter = null; // reset $chapter variable
			if ( class_exists( 'WP_MANGA' ) && ! empty( $bookmark['c'] ) && ! is_array( $bookmark['c'] ) ) {
				$wp_manga_chapter = madara_get_global_wp_manga_chapter();
				$chapter          = $wp_manga_chapter->get_chapter_by_id( $post->ID, $bookmark['c'] );
			}
			
			$permalink = get_the_permalink( $post_id );
			$title     = get_the_title( $post_id );
			$time      = get_post_meta( $post_id, '_latest_update', true );

			if( class_exists( 'WP_MANGA_USER_ACTION' ) ){
				global $wp_manga_user_actions;
				$notify_num = $wp_manga_user_actions->get_user_notify_num( $user_id, $bookmark['id'] );
			}
			?>
            <tr>
                <td>
                    <div class="mange-name">
                        <div class="item-thumb">
							<?php if ( has_post_thumbnail( $post_id ) ) { ?>
                                <a href="<?php echo esc_url( $permalink ); ?>" title="<?php echo esc_attr( $title ); ?>">
									<?php echo madara_thumbnail( array( 75, 106 ), $post_id ); ?>
                                </a>
							<?php } ?>

							<?php if( !empty( $notify_num ) ){ ?>
								<div class="c-notifications">
									<?php echo esc_html( $notify_num ); ?>
								</div>
							<?php } ?>
                        </div>
                        <div class="item-infor">
                            <div class="post-title">
								<?php if ( ! empty( $title ) ) { ?>
                                    <h3>
                                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_attr( $title ); ?></a>
                                    </h3>
								<?php } ?>
                            </div>

							<?php if ( ! empty( $chapter ) ) {
								global $wp_manga_functions;
								$chapter_url = $wp_manga_functions->build_chapter_url( $post_id, $chapter['chapter_slug'], $reading_style );
								?>
                                <div class="chapter">
                                    <span><a href="<?php echo esc_url( $chapter_url ); ?>"><?php echo isset( $chapter['chapter_name'] ) ? esc_html( $chapter['chapter_name'] ) : ''; ?></a></span>

									<?php if ( ! empty( $bookmark['p'] ) && $reading_style == 'paged' ) {
										$paged_url = add_query_arg( array(
											'paged' => $bookmark['p'],
										), $chapter_url );
										?>
                                        <span><a href="<?php echo esc_url( $paged_url ); ?>"><?php esc_html_e( 'page ', 'madara' ); ?><?php echo esc_html( $bookmark['p'] ) ?></a></span>
									<?php } ?>

                                </div>
							<?php } ?>

							<div class="list-chapter">
								<?php
									$wp_manga_functions->manga_meta( $post_id );
								?>
							</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="post-on">
						<?php echo date( get_option( 'date_format'), $time ); ?>
                    </div>
                </td>
                <td>
                    <div class="action">
                        <div class="checkbox">
                            <input id="<?php echo esc_attr( $post_id ); ?>" class="bookmark-checkbox" value="<?php echo esc_attr( $post_id ); ?>" type="checkbox">
                            <label for="<?php echo esc_attr( $post_id ); ?>"></label>
                        </div>
                        <a class="wp-manga-delete-bookmark" href="javascript:void(0)" data-post-id="<?php echo esc_attr( $post_id ); ?>"><i class="icon ion-ios-close"></i></a>
                    </div>
                </td>
            </tr>
			<?php
		}
	} ?>
	<?php if ( ! empty( $bookmarks ) ) { ?>

		<?php foreach ( $bookmarks as $bookmark_id ) {
			$post_id = get_post_meta( $bookmark_id, '_post_id', true );

			?>

		<?php } ?>

        <tr>
            <td colspan="3">
                <div class="remove-all float-right">
                    <div class="checkbox">
                        <input id="checkall" type="checkbox">
                        <label for="checkall"><?php esc_html_e( 'Check all', 'madara' ); ?></label>
                    </div>
                    <button type="button" id="delete-bookmark-manga" class="btn btn-default"><?php esc_html_e( 'Delete', 'madara' ); ?></button>
                </div>
            </td>
        </tr>

	<?php } else { ?>
        <tr>
            <td colspan="3"> <?php esc_html_e( 'No Manga Bookmarked', 'madara' ); ?> </td>
        </tr>
	<?php } ?>
    </tbody>
</table>
