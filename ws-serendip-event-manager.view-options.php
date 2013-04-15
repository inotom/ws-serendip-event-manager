<?php
/**
 * View class for Options, Serendip Event Manager Plugin
 *
 * @package Serendip Event Manager
 * @since 0.1
 *
 * file created in 2013/03/27 21:06:18.
 * LastUpdated :2013/03/31 23:19:53.
 * author iNo
 */

class Serendip_Event_Manager_Options_View {
	/**
	 * Open Event Manager Options page
	 * @return void
	 */
	public function event_manager_options() {
		$this->show_options_form_fields();
	}

	private function save_options() {
		if ( !empty( $_POST ) ) {
			$options_nonce = isset( $_POST['event-manager-options-nonce'] ) ? $_POST['event-manager-options-nonce'] : '';
			if ( !wp_verify_nonce( $place_nonce, 'event-manager-options-nonce' ) ) die ( __( 'Security check failed', Serendip_Event_Manager::$domain ) );
		}
	}

	private function show_options_form_fields() {
?>
<div class="wrap nosubsub">
<?php screen_icon( 'options-general' ); ?>
<h2><?php _e( 'Event Manager Options', Serendip_Event_Manager::$domain ); ?></h2>

<div class="postbox-container" style="width: 90%">
	<div class="metabox-holder">
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="ws_serendip_event_manager_count_posts_per_page,ws_serendip_event_manager_count_days_from_posts" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ws_serendip_event_manager_count_posts_per_page"><?php _e( 'Count posts per page', Serendip_Event_Manager::$domain ); ?></label></th>
					<td>
						<input type="number" name="ws_serendip_event_manager_count_posts_per_page" id="ws_serendip_event_manager_count_posts_per_page" class="small-text" value="<?php echo get_option( 'ws_serendip_event_manager_count_posts_per_page', Serendip_Event_Manager::COUNT_POSTS_PER_PAGE ); ?>" min="1" step="1" required pattern="\d+" />
						<p class="description"><?php _e( 'Display count posts in event archives page.', Serendip_Event_Manager::$domain ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ws_serendip_event_manager_count_days_from_posts"><?php _e( 'Count days from posts', Serendip_Event_Manager::$domain ); ?></label></th>
					<td>
						<input type="number" name="ws_serendip_event_manager_count_days_from_posts" id="ws_serendip_event_manager_count_days_from_posts" class="small-text" value="<?php echo get_option( 'ws_serendip_event_manager_count_days_from_posts', Serendip_Event_Manager::COUNT_DAYS_FROM_POSTS ); ?>" min="0" step="1" required pattern="\d+" />
						<p class="description"><?php _e( 'Display &quot;New!&quot; text, when posts within this days count.', Serendip_Event_Manager::$domain ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit"><input id="submit" class="button button-primary" type="submit" value="<?php _e( 'Update Options &#187;', Serendip_Event_Manager::$domain ); ?>" name="submit" /></p>
		</form>
	</div>
</div>
</div>
<?php
	}
}

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
