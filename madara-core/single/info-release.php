<div class="post-content_item">
	<div class="summary-heading">
		<h5>
			<?php echo esc_html__( 'Release', 'madara' ); ?>
		</h5>
	</div>
	<div class="summary-content">
		<?php
			echo wp_kses_post( $release );
		?>
	</div>
</div>