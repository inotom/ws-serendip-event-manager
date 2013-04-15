<?php
/**
 * View class for Place, Serendip Event Manager Plugin
 *
 * @package Serendip Event Manager
 * @since 0.1
 *
 * file created in 2013/03/25 09:50:00.
 * LastUpdated :2013/03/31 23:18:04.
 * author iNo
 */

class Serendip_Event_Manager_Place_View {
	/**
	 * Open add place page
	 * @return void
	 */
	public function add_place() {
		$this->show_place_form_fields( 'add' );
	}

	/**
	 * Open list place page
	 * @return void
	 */
	public function list_place() {
		if ( isset( $_GET['mode'] ) && isset( $_GET['place_id'] ) ) {
			if ( $_GET['mode'] == 'edit' ) {
				$this->show_place_form_fields( 'edit', (int) $_GET['place_id'] );
			} else if ( $_GET['mode'] == 'delete' ) {
				$this->show_place_form_fields( 'delete', (int) $_GET['place_id'] );
			} else {
				$this->show_list_place_table();
			}
		} else if ( isset( $_POST['mode'] ) ) {
			if ( $_POST['mode'] == 'add' ) {
				$this->place_edit( 'add' );
			} else if ( $_POST['mode'] == 'edit' ) {
				$this->place_edit( 'edit' );
			} else if ( $_POST['mode'] == 'delete' ) {
				$this->place_edit( 'delete' );
			}
		} else {
			$this->show_list_place_table();
		}
	}

	/**
	 * Output list place JSON
	 * @return void
	 */
	public function list_place_json_format() {
		global $ws_serendip_event_manager;
		if ( isset( $_GET['plid'], $_GET['json'] ) && $_GET['json'] == 'true' ) {
			header( 'Content-type: application/json' );
			$place_id = (int) $_GET['plid'];
			if ( $place_id > 0 ) {
				echo json_encode( $ws_serendip_event_manager->get_place( $place_id ) );
			} else {
				echo json_encode( $ws_serendip_event_manager->get_all_place() );
			}
		} else {
			return;
		}
		exit;
	}

	/**
	 * Display list place table
	 * @return void
	 */
	private function show_list_place_table() {
		global $ws_serendip_event_manager;
		$places = $ws_serendip_event_manager->get_all_place();
?>
<div class="wrap nosubsub">
<?php screen_icon( 'edit' ); ?>
<h2>
	<?php _e( 'Places list', Serendip_Event_Manager::$domain ); ?>
	<a class="add-new-h2" href="<?php echo admin_url( 'edit.php?post_type=event_manager&page=add_place' ); ?>"><?php _e( 'Add new place', Serendip_Event_Manager::$domain ); ?></a>
</h2>
<table class="widefat page fixed" id="my-calendar-admin-table" style="width:99%;">
	<thead> 
		<tr>
			<th class="manage-column" scope="col"><?php _e( 'Name', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Address', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Tel', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Zip', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Web Url', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Map Url', Serendip_Event_Manager::$domain ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Description', Serendip_Event_Manager::$domain ); ?></th>
		</tr>
	</thead>
<?php
		$class = '';
		if ( !empty( $places ) ) {
			foreach ( $places as $place ) {
				$class = ( $class == 'alternate' ) ? '' : 'alternate';
?>
	<tr class="<?php echo $class; ?>">
		<td rel="ID-<?php echo esc_attr( $place->ID ) ?>">
			<strong><a title="<?php printf( __( 'Edit %s', Serendip_Event_Manager::$domain ), ' &quot;' . esc_attr( $place->name ) . '&quot;' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=event_manager&page=list_place&mode=edit&place_id=' . $place->ID ); ?>"><?php echo esc_attr( $place->name ) ?></a></strong>
			<div class="row-actions">
				<span class="edit"><a href="<?php echo admin_url( 'edit.php?post_type=event_manager&page=list_place&mode=edit&place_id=' . $place->ID ); ?>"><?php _e( 'Edit' ); ?></a></span>|
				<span class="trash"><a href="<?php echo admin_url( 'edit.php?post_type=event_manager&page=list_place&mode=delete&place_id=' . $place->ID ); ?>"><?php _e( 'Delete' ); ?></a></span>
			</div>
		</td>
		<td><?php echo esc_attr( $place->address ); ?></td>
		<td><?php echo esc_attr( $place->tel ); ?></td>
		<td><?php echo esc_attr( $place->zip ); ?></td>
		<td><a target="_blank" href="<?php echo esc_attr( $place->web_url ); ?>"><?php echo substr( esc_html( $place->web_url ), 0, 30 ); ?><?php if ( strlen( esc_html( $place->web_url ) ) > 30 ) : ?>...<?php endif; ?></a></td>
		<td><a target="_blank" href="<?php echo esc_attr( $place->map_url ); ?>"><?php echo substr( esc_html( $place->map_url ), 0, 30 ); ?><?php if ( strlen( esc_html( $place->map_url ) ) > 30 ) : ?>...<?php endif; ?></a></td>
		<td><?php echo mb_substr( esc_html( $place->desc ), 0, 30 ); ?><?php if ( strlen( esc_html( $place->desc ) ) > 30 ) : ?>...<?php endif; ?></td>
	</tr>
<?php
			}
?>
</table>
</div>
<?php
		} else {
			echo '<p>' . __( 'There are no places in the database yet!', Serendip_Event_Manager::$domain ) . '</p>';
		}
	}

	private function place_edit( $mode = 'add' ) {
		global $ws_serendip_event_manager;
		if ( !empty( $_POST ) ) {
			$place_nonce = isset( $_POST['place-nonce'] ) ? $_POST['place-nonce'] : '';
			if ( !wp_verify_nonce( $place_nonce, 'place-nonce' ) ) die ( __( 'Security check failed', Serendip_Event_Manager::$domain ) );
		}

		if ( $mode == 'add' ) {
			$result_id = $ws_serendip_event_manager->add_place(
				$_POST['place_name'],
				$_POST['place_address'],
				$_POST['place_desc'],
				$_POST['place_tel'],
				$_POST['place_zip'],
				$_POST['place_web_url'],
				$_POST['place_map_url']
			);
			if ( $result_id > 0 ) {
				echo '<div class="updated"><p><strong>' . __( 'Place added succlessfully.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				$this->show_place_form_fields( 'edit', $result_id );
			} else {
				echo '<div class="error"><p><strong>' . __( 'Place coud not be added to database.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				$this->show_place_form_fields( 'add' );
			}
		} else if ( $mode == 'edit' ) {
			if ( isset( $_POST['place_id'] ) ) {
				$place_id = (int) $_POST['place_id'];
				$result = $ws_serendip_event_manager->update_place(
					$place_id,
					$_POST['place_name'],
					$_POST['place_address'],
					$_POST['place_desc'],
					$_POST['place_tel'],
					$_POST['place_zip'],
					$_POST['place_web_url'],
					$_POST['place_map_url']
				);
				if ( $result ) {
					echo '<div class="updated"><p><strong>' . __( 'Place edited succlessfully.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				} else {
					echo '<div class="updated error"><p><strong>' . __( 'Place was not changed.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				}
				$this->show_place_form_fields( 'edit', $place_id );
			}
		} else if ( $mode == 'delete' ) {
			if ( isset( $_POST['place_id'] ) ) {
				$place_id = (int) $_POST['place_id'];
				$result = $ws_serendip_event_manager->delete_place( $place_id );
				if ( $result ) {
					echo '<div class="updated"><p><strong>' . __( 'Place deleted succlessfully.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				} else {
					echo '<div class="error"><p><strong>' . __( 'Place coud not be deleted.', Serendip_Event_Manager::$domain ) . '</strong></p></div>';
				}
				$this->show_list_place_table();
			}
		}
	}

	/**
	 * Show place form fields
	 */
	private function show_place_form_fields( $mode = 'add', $place_id = -1 ) {
		global $ws_serendip_event_manager;
		$is_mode_add = $mode == 'add';
		$place;
		if ( $mode != 'add' ) {
			$place = $ws_serendip_event_manager->get_place( $place_id );
		}
?>
<div class="wrap nosubsub">
<?php screen_icon( 'edit' ); ?>
<?php if ( $mode == 'add' ) : ?>
	<h2><?php _e( 'Add New Place', Serendip_Event_Manager::$domain ); ?></h2>
<?php elseif ( $mode == 'delete' ) : ?>
	<h2><?php _e( 'Delete Place', Serendip_Event_Manager::$domain ); ?></h2>
<?php else: ?>
	<h2><?php _e( 'Edit Place', Serendip_Event_Manager::$domain ); ?></h2>
<?php endif; ?>

<div class="postbox-container" style="width: 90%">
<div class="metabox-holder">

<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<h3><?php _e( 'Place Editor', Serendip_Event_Manager::$domain ); ?></h3>
		<div class="inside">
			<form id="place-edit" method="post" action="<?php echo admin_url( 'edit.php?post_type=event_manager&page=list_place' ); ?>">
				<input type="hidden" name="place-nonce" value="<?php echo wp_create_nonce( 'place-nonce' ); ?>" />
				<input type="hidden" name="post_type" value="event_manager" />
				<?php if ( $mode == 'add' ) : ?>
					<input type="hidden" name="mode" value="add" />
				<?php elseif ( $mode == 'delete' ) : ?>
					<input type="hidden" name="mode" value="delete" />
					<input type="hidden" name="place_id" value="<?php echo $place_id; ?>" />
				<?php else : ?>
					<input type="hidden" name="mode" value="edit" />
					<input type="hidden" name="place_id" value="<?php echo $place_id; ?>" />
				<?php endif; ?>
				<table>
					<tr>
						<th><label for="place_name"><?php _e( 'Name', Serendip_Event_Manager::$domain ); ?></label></th>
						<td>
							<input type="text" id="place_name" name="place_name" class="input" size="60" maxlength="255" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->name ) ); ?>" required />
							(<?php _e( 'Add anchor tag, if web url field were filled.', Serendip_Event_Manager::$domain ); ?>)
						</td>
					</tr>
					<tr>
						<th><label for="place_address"><?php _e( 'Address', Serendip_Event_Manager::$domain ); ?></label></th>
						<td>
							<input type="text" id="place_address" name="place_address" class="input" size="60" maxlength="255" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->address ) ); ?>" />
							(<?php _e( 'Add anchor tag, if map url field were filled.', Serendip_Event_Manager::$domain ); ?>)
						</td>
					</tr>
					<tr>
						<th><label for="place_tel"><?php _e( 'Tel', Serendip_Event_Manager::$domain ); ?></label></th>
						<td><input type="tel" id="place_tel" name="place_tel" class="input" size="30" maxlength="20" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->tel ) ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="place_zip"><?php _e( 'Zip', Serendip_Event_Manager::$domain ); ?></label></th>
						<td><input type="text" id="place_zip" name="place_zip" class="input" size="30" maxlength="10" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->zip ) ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="place_web_url"><?php _e( 'Web Url', Serendip_Event_Manager::$domain ); ?></label></th>
						<td><input type="url" id="place_web_url" name="place_web_url" class="input" size="80" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->web_url ) ); ?>" pattern="https?://.+" /></td>
					</tr>
					<tr>
						<th><label for="place_map_url"><?php _e( 'Map Url', Serendip_Event_Manager::$domain ); ?></label></th>
						<td><input type="url" id="place_map_url" name="place_map_url" class="input" size="80" value="<?php if ( !empty( $place ) ) echo esc_attr( stripslashes( $place->map_url ) ); ?>" pattern="https?://.+" /></td>
					</tr>
					<tr>
						<th><label for="place_desc"><?php _e( 'Description', Serendip_Event_Manager::$domain ); ?></label></th>
						<td>
							<textarea  id="place_desc" name="place_desc" class="input" cols="80" rows="10"><?php if ( !empty( $place ) ) echo esc_textarea( stripslashes( $place->desc ) ); ?></textarea>
							&nbsp;(<?php _e( 'Description can use &lt;a&gt; tag. New line is auto replaced to &lt;br /&gt; tag.', Serendip_Event_Manager::$domain ); ?>)
						</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td><input type="submit" name="save" class="button-primary" value="<?php if ( $mode == 'add' ) { _e( 'Add New Place', Serendip_Event_Manager::$domain ); } elseif ( $mode == 'delete' ) { _e( 'Delete Place', Serendip_Event_Manager::$domain ); } else { _e( 'Save Changes', Serendip_Event_Manager::$domain ); } ?> &raquo;" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

</div>
</div>
</div>

<?php
	}

}

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
