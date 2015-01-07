<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>Hoy Brightcove Media Importer Plugin</h2>
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<div class="inside">
							<h3>Current Options</h3>
							<pre style="display: none;"><?php var_dump( $options ); ?></pre>

							<form name="hoy_brightcove_importer_reset_options_form" method="post" action="">
								<input type="hidden" name="hoy_brightcove_importer_reset_options_form_submitted" value="Y">
								<p>	
									<?php submit_button( 'Reset Options', $type = 'delete', $name = 'hoy_brightcove_importer_reset_options_form_submit', $wrap = true, $other_attributes = null ); ?>
								</p>
							</form>
						</div> <!-- .inside -->
					</div> <!-- .postbox -->

<?php if( !isset( $hoy_brightcove_importer_api_key ) || $hoy_brightcove_importer_api_key == '' ): ?>
					<div class="postbox">
						<h3><span>Let's get started!</span></h3>
						<div class="inside">
							<form name="hoy_brightcove_importer_api_key_form" method="post" action="">
								<input type="hidden" name="hoy_brightcove_importer_api_key_form_submitted" value="Y">
								<table class="form-table">
									<tr valign="top">
										<td scope="row">
											<label for="hoy_brightcove_importer_api_key"><?php _e( 'Brightcove Media API Key'); ?></label>
										</td>
										<td>
											<input name="hoy_brightcove_importer_api_key" id="hoy_brightcove_importer_api_key" type="text" value="" class="regular-text" />
										</td>
									</tr>
								</table>
								<p>	
									<input class="button-primary" type="submit" name="hoy_brightcove_importer_api_key_form_submit" value="<?php _e( 'Save', 'hoy-brightcove-importer' ); ?>" /> 
								</p>
							</form>
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
<?php else: ?>
					<div class="postbox">
						<form name="hoy_brightcove_importer_update_videos_form" method="post" action="">
							<input type="hidden" name="hoy_brightcove_importer_update_videos_form_submitted" value="Y">
							<p>	
								<input class="button-primary" type="submit" name="hoy_brightcove_importer_update_videos_submit" value="<?php _e( 'Get New Videos', 'hoy-brightcove-importer' ); ?>" /> 
							</p>
						</form>
						<form name="hoy_brightcove_importer_create_posts_form" method="post" action="">
							<input type="hidden" name="hoy_brightcove_importer_create_posts_form_submitted" value="Y">
							<p>	
								<input class="button-primary" type="submit" name="hoy_brightcove_importer_create_posts_submit" value="<?php _e( 'Import Videos to Posts', 'hoy-brightcove-importer' ); ?>" /> 
							</p>
						</form>
						<h3>
							<span><?php _e( 'New Brightcove Videos', 'hoy-brightcove-importer' ); ?> (<?php echo count( $hoy_brightcove_importer_new_videos ); ?>)</span>
						</h3>
						<span><?php _e( 'Last Update:', 'hoy-brightcove-importer' ); ?> <?php if( $hoy_brightcove_importer_last_updated == 0) { _e( 'Never', 'hoy-brightcove-importer' ); } else { echo date( 'r T', (int) $hoy_brightcove_importer_last_updated ); } ?></span>
						<div class="inside">
<?php if ( count( $hoy_brightcove_importer_new_videos ) > 0 ) : ?>
							<table id="new_videos" class="display">
								<thead>
									<tr>
										<th>Video ID</th>
										<th>Thumbnail</th>
										<th>Name</th>
										<th>Tags</th>
										<th>Last Modified</th>
									</tr>
								</thead>
								<tbody>
<?php for( $i = 0; $i < count( $hoy_brightcove_importer_new_videos ); $i++ ): ?>
<?php 
	$video = $hoy_brightcove_importer_new_videos[$i];
?>
									<tr>
										<td><?php echo $video['id']; ?></td>
										<td><div style="max-width: 120px;"><img style="width: 100%;" src="<?php echo $video['thumbnailURL']; ?>"></div></td>
										<td><?php echo $video['name']; ?></td>
										<td><?php echo implode( ', ', $video['tags'] ); ?></td>
										<td><?php echo date( 'c', (int) ( $video['lastModifiedDate'] / 1000.0 ) ); ?></td>
									</tr>
<?php endfor; ?>
								</tbody>
							</table>
<?php endif; ?><!-- // if ( count( $hoy_brightcove_importer_new_videos ) > 0 ) -->
						</div> <!-- .inside -->
						<h3>
							<span><?php _e( 'Imported Brightcove Videos', 'hoy-brightcove-importer' ); ?> (<?php echo count( $hoy_brightcove_importer_imported_videos ); ?>)</span>
						</h3>
						<span><?php _e( 'Last Import:', 'hoy-brightcove-importer' ); ?> <?php if( $hoy_brightcove_importer_last_imported == 0) { _e( 'Never', 'hoy-brightcove-importer' ); } else { echo date( 'r T', (int) $hoy_brightcove_importer_last_imported ); } ?></span>
						<div class="inside">
<?php if ( count( $hoy_brightcove_importer_imported_videos ) > 0 ) : ?>
							<table id="imported_videos" class="display">
								<thead>
									<tr>
										<th>Video ID</th>
										<th>Thumbnail</th>
										<th>Name</th>
										<th>Tags</th>
										<th>Last Modified</th>
										<th>Post ID</th>
									</tr>
								</thead>
								<tbody>
<?php for( $i = 0; $i < count( $hoy_brightcove_importer_imported_videos ); $i++ ): ?>
<?php 
	$video = $hoy_brightcove_importer_imported_videos[$i]['video'];
	$new_post = $hoy_brightcove_importer_imported_videos[$i]['post'];
?>
									<tr>
										<td><?php echo $video['id']; ?></td>
										<td><div style="max-width: 120px;"><img style="width: 100%;" src="<?php echo $video['thumbnailURL']; ?>"></div></td>
										<td><?php echo $video['name']; ?></td>
										<td><?php echo implode( ', ', $video['tags'] ); ?></td>
										<td><?php echo date( 'c', (int) ( $video['lastModifiedDate'] / 1000.0 ) ); ?></td>
										<td>
											<?php if ( $new_post ) : ?>
												<a href="<?php echo get_permalink( $new_post['id'] ); ?>"><?php echo $new_post['id']; ?></a>
											<?php else: ?>
												Not imported!
											<?php endif; ?>
										</td>
									</tr>
<?php endfor; ?>
								</tbody>
							</table>
<?php endif; ?><!-- // if ( count( $hoy_brightcove_importer_imported_videos ) > 0 ) -->
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
<?php if( $display_json == true ): ?>
					<div class="postbox">
						<h3><span>JSON Feed</span></h3>
						<div class="inside">
							<pre><code><?php var_dump( $hoy_brightcove_importer_videos ); ?></code></pre>
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
<?php endif; // if( $display_json ) ?>
<?php endif; // if( !isset( $hoy_brightcove_importer_api_key ) ... ?>
				</div> <!-- .meta-box-sortables .ui-sortable -->
			</div> <!-- post-body-content -->
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
<?php if( isset( $hoy_brightcove_importer_api_key ) && $hoy_brightcove_importer_api_key != '' ): ?>
					<div class="postbox">
						<h3><span><?php _e( 'Brightcove Media API Key', 'hoy-brightcove-importer' ); ?></span></h3>
						<div class="inside">
							<form name="hoy_brightcove_importer_api_key_form" method="post" action="">
								<input type="hidden" name="hoy_brightcove_importer_form_submitted" value="Y">
								<p>
									<label for="hoy_brightcove_importer_api_key"><?php _e( 'Update API Key', 'hoy-brightcove-importer' ); ?></label>
									<input name="hoy_brightcove_importer_api_key" id="hoy_brightcove_importer_api_key" type="text" value="<?php echo $hoy_brightcove_importer_api_key; ?>" />
								</p>
								<p>	
									<input class="button-primary" type="submit" name="hoy_brightcove_importer_api_key_submit" value="<?php _e( 'Update', 'hoy-brightcove-importer' ); ?>" /> 
								</p>
							</form>
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
<?php endif; // if( isset( $hoy_brightcove_importer_api_key ) ... ?>	
				</div> <!-- .meta-box-sortables -->
			</div> <!-- #postbox-container-1 .postbox-container -->
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		<br class="clear">
	</div> <!-- #poststuff -->
</div> <!-- .wrap -->
<script>
/*

This little chunk of jQuery adds a toggle button to all <pre></pre> blocks and hides them by default.

*/
(function($) {
	$(document).ready(function() {
		$('pre').hide();
		$('pre').each(function() {
			$button = $('<button>Toggle</button>');
			$button.click(function() {
				$(this).next().toggle();
			})
			$(this).before($button);
		});

		// column index 3 (4th column) is Date and we want newest on top
		$('#new_videos').DataTable({
			"order": [ [ 3, 'desc' ] ]
		});

		// column index 3 (4th column) is Date and we want newest on top
		$('#imported_videos').DataTable({
			"order": [ [ 3, 'desc' ] ]
		});
	});
})(jQuery);
</script>
