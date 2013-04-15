<?php
/**
 * View class for Serendip Event Manager Plugin
 *
 * @package Serendip Event Manager
 * @since 0.1
 *
 * file created in 2013/03/21 08:54:27.
 * LastUpdated :2013/03/31 23:12:47.
 * author iNo
 */

class Serendip_Event_Manager_View {
	/**
	 * Add event post menu
	 * @return void
	 */
	public function add_event_post() {
		$labels = array(
			'name' => __( 'Events', Serendip_Event_Manager::$domain ),
			'singular_name' => __( 'Event', Serendip_Event_Manager::$domain ),
			'add_new' => __( 'Add new event', Serendip_Event_Manager::$domain ),
			'add_new_item' => __( 'Add new event', Serendip_Event_Manager::$domain ),
			'edit_item' => __( 'Edit event', Serendip_Event_Manager::$domain ),
			'new_item' => __( 'New event', Serendip_Event_Manager::$domain ),
			'view_item' => __( 'View event', Serendip_Event_Manager::$domain ),
			'search_items' => __( 'Search event', Serendip_Event_Manager::$domain ),
			'not_found' => __( 'Posted events not found.', Serendip_Event_Manager::$domain ),
			'not_found_in_trash' => __( 'Events are not found in trash.', Serendip_Event_Manager::$domain ),
			'parent_item_colon' => ''
		);
		register_post_type(
			'event_manager',
			array(
				'label' => __( 'Event', Serendip_Event_Manager::$domain ),
				'labels' => $labels,
				'public' => true,
				'menu_position' => 5,
				'capability_type' => 'post',
				'supports' => array( 'title', 'editor' ),
				'taxonomies' => array( 'event_category', 'post_tag' ),
				'has_archive' => 'event'
			)
		);
	}

	/**
	 * Add event category menu
	 * @return void
	 */
	public function event_category_taxonomy() {
		$labels = array(
			'name' => __( 'Event category', Serendip_Event_Manager::$domain ),
			'singular_name' => __( 'Event category', Serendip_Event_Manager::$domain ),
			'search_items' =>  __( 'Search event categories', Serendip_Event_Manager::$domain ),
			'popular_items' => __( 'Popular event categories', Serendip_Event_Manager::$domain ),
			'all_items' => __( 'All event categories', Serendip_Event_Manager::$domain ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit event category', Serendip_Event_Manager::$domain ),
			'update_item' => __( 'Update', Serendip_Event_Manager::$domain ),
			'add_new_item' => __( 'Add new event category &raquo;', Serendip_Event_Manager::$domain ),
			'new_item_name' => __( 'New event category', Serendip_Event_Manager::$domain )
		);
		register_taxonomy( 'event_category', 'event_manager', array(
			'label' => __( 'Event category', Serendip_Event_Manager::$domain ),
			'labels' => $labels,
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'event-category' ),
		));
	}

	/**
	 * Event manager post edit field page
	 * @return void
	 */
	public function add_field_event_manager() {
		global $post;
		$custom = get_post_custom( $post->ID );
		if ( !empty( $custom ) ) {
			$event_start_date = isset( $custom['event_start_date'] ) ? $custom['event_start_date'][0] : null;
			$event_end_date = isset( $custom['event_end_date'] ) ? $custom['event_end_date'][0] : null;
			$event_undecided = isset( $custom['event_undecided'] ) ? $custom['event_undecided'][0] : null;
			$event_time = isset( $custom['event_time'] ) ? $custom['event_time'][0] : null;
			$event_place = isset( $custom['event_place'] ) ? $custom['event_place'][0] : null;
		}
		echo '<input type="hidden" name="events-nonce" id="evnets-nonce" value="' . wp_create_nonce( 'events-nonce' ) . '" />';
// display post edit fields
?>
<div id="events-meta">
	<table>
		<tr>
			<th><?php _e( 'Start date', Serendip_Event_Manager::$domain ); ?></th>
			<td>
				<input type="text" name="event_start_date" class="event_start_date" id="datepicker-start-date" value="<?php if ( isset( $event_start_date ) ) echo esc_attr( stripslashes( $event_start_date ) ); ?>" required pattern="\d{4}-\d{2}-\d{2}" />
				<label for="event_undecided"><?php _e( '*Undecided', Serendip_Event_Manager::$domain ); ?></label><input type="checkbox" name="event_undecided" class="event_undecided" id="event_undecided" value="event_undecided"<?php if ( isset( $event_undecided ) && $event_undecided == 'event_undecided' ) echo ' checked="checked"' ?> />
				&nbsp;(<?php _e( 'Date format: 2013-01-01', Serendip_Event_Manager::$domain ); ?>)
			</td>
		</tr>
		<tr>
			<th><?php _e( 'End date', Serendip_Event_Manager::$domain ); ?></th>
			<td>
				<input type="text" name="event_end_date" class="event_end_date" id="datepicker-end-date" value="<?php if ( isset( $event_end_date ) ) echo esc_attr( stripslashes( $event_end_date ) ); ?>" required pattern="\d{4}-\d{2}-\d{2}" />
				&nbsp;(<?php _e( 'Date format: 2013-01-01', Serendip_Event_Manager::$domain ); ?>)
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Time', Serendip_Event_Manager::$domain ); ?></th>
			<td>
				<input type="text" name="event_time" class="event_time" id="event_time" value="<?php if ( isset( $event_time ) ) echo esc_attr( stripslashes( $event_time ) ); ?>" size="50" />
				&nbsp;(<?php _e( 'Time can be given in free format.', Serendip_Event_Manager::$domain ); ?>)
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Place', Serendip_Event_Manager::$domain ); ?></th>
			<td>
				<textarea name="event_place" class="event_place" id="event_place" cols="80" rows="10"><?php if ( isset( $event_place ) ) echo esc_attr( stripslashes( $event_place ) ); ?></textarea>
				&nbsp;(<?php _e( 'Place can use &lt;a&gt;,&ltdl&gt;,&ltdt&gt;,&ltdd&gt; tag.', Serendip_Event_Manager::$domain ); ?>)
				<br />
				<select id="select-place">
					<option value="0" selected="selected">-</option>
				</select>&nbsp;<input type="button" id="input-place" value="<?php _e( 'Autocomplete place', Serendip_Event_Manager::$domain ); ?>" />
			</td>
		</tr>
	</table>
</div>
<?php
	}

	/**
	 * Add event list column item
	 * @param array $columns
	 * @return array
	 */
	public function manage_event_manager_post_columns( $columns ) {
		global $post_type;
		if ( 'event_manager' == $post_type ) {
			$columns['event_undecided'] = __( 'Undecided', Serendip_Event_Manager::$domain );
			$columns['event_start_date'] = __( 'Start date', Serendip_Event_Manager::$domain );
			$columns['event_end_date'] = __( 'End date', Serendip_Event_Manager::$domain );
			$columns['event_time'] = __( 'Time', Serendip_Event_Manager::$domain );
			$columns['ecategory'] = __( 'Categories', Serendip_Event_Manager::$domain );
		}
		return $columns;
	}

	/**
	 * Add event list column value
	 * @param string $column_name
	 * @param int $post_id
	 * @return void
	 */
	public function add_column( $column_name, $post_id ) {
		if ( $column_name == 'event_undecided' ) {
			$event_undecided = get_post_meta( $post_id, 'event_undecided', true );
			if ( isset( $event_undecided ) && 'event_undecided' == $event_undecided ) {
				echo __( 'Undecided', Serendip_Event_Manager::$domain );
			}
		}
		if ( $column_name == 'event_start_date' ) {
			$event_start_date = get_post_meta( $post_id, 'event_start_date', true );
			if ( isset( $event_start_date ) && strlen( $event_start_date ) > 0 ) {
				echo date( get_option( 'date_format' ), strtotime( $event_start_date ) );
			}
		}
		if ( $column_name == 'event_end_date' ) {
			$event_end_date = get_post_meta( $post_id, 'event_end_date', true );
			if ( isset( $event_end_date ) && strlen( $event_end_date ) > 0 ) {
				echo date( get_option( 'date_format' ), strtotime( $event_end_date ) );
			}
		}
		if ( $column_name == 'event_time' ) {
			$event_time = get_post_meta( $post_id, 'event_time', true );
			if ( isset( $event_time ) && strlen( $event_time ) > 0 ) {
				echo esc_attr($event_time);
			}
		}
		if ( $column_name == 'ecategory' ) {
			$terms = get_the_terms( $post_id, 'event_category' );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $key => $value ) {
					echo esc_attr( $value->name );
					if ( end( array_keys( $terms ) ) != $key ) {
						echo ', ';
					}
				}
			}
		}
	}

	/**
	 * Display new event list (for shortcode)
	 * @return void
	 */
	public function load_new_event_list() {
		$this->load_event_list();
	}

	/**
	 * Display old event list (for shortcode)
	 * @return void
	 */
	public function load_old_event_list() {
		$this->load_event_list( true );
	}

	/**
	 * Display event list
	 * @return void
	 */
	private function load_event_list( $is_archive = false ) {
		global $ws_serendip_event_manager;
		$count_days_from_posts = get_option( 'ws_serendip_event_manager_count_days_from_posts', Serendip_Event_Manager::COUNT_DAYS_FROM_POSTS );
		$count_seconds_from_posts = 60 * 60 * 24 * $count_days_from_posts;
		$WEEK_NAME = array(
			__( 'Sun', Serendip_Event_Manager::$domain ),
			__( 'Mon', Serendip_Event_Manager::$domain ),
			__( 'Tue', Serendip_Event_Manager::$domain ),
			__( 'Wed', Serendip_Event_Manager::$domain ),
			__( 'Thu', Serendip_Event_Manager::$domain ),
			__( 'Fri', Serendip_Event_Manager::$domain ),
			__( 'Sat', Serendip_Event_Manager::$domain )
		);
		$result = '';
		$paged = $is_archive ? ( ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ) : 1; // get current page num
		$query = $ws_serendip_event_manager->get_query( $is_archive, $paged );
?>
<dl id="ws-serendip-event-manager-list">
<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
<?php
	$event_start_date = post_custom( 'event_start_date' );
	$event_end_date = post_custom( 'event_end_date' );
	$event_place = post_custom( 'event_place' );
	$event_undecided = post_custom( 'event_undecided' );
	$is_undecided = $event_undecided == 'event_undecided';
	$date_estart = strtotime( $event_start_date );
	$month_class = ( $is_archive ? 'finished-' : '' ) . ( ( date( 'm', $date_estart ) % 2 ) == 0 ? 'even-month' : 'odd-month' );
	$start_week_index = date( 'w', strtotime( $event_start_date ) );
	$end_week_index = date( 'w', strtotime( $event_end_date ) );
?>
	<dt class="<?php echo esc_attr( $month_class ); ?>">
		<?php if ( !$is_archive ) : ?><span class="title-date"><?php echo ( ( $is_undecided || strlen( $event_start_date ) <= 0 ) ? __( 'TBD', Serendip_Event_Manager::$domain ) : date( 'n/j', $date_estart ) ); ?></span><?php endif; ?><span class="event-title"><?php the_title(); ?></span>
	</dt>
	<dd>
<?php
		if ( date( 'Y-m-d' ) > date( $event_end_date ) ) : ?>
		<div class="finished-message"><p><?php _e( 'Finished', Serendip_Event_Manager::$domain ); ?></p></div>
		<?php endif; ?>
<?php
		if ( strtotime( date( 'Y-m-d' ) ) - strtotime( date( get_the_time( 'Y-m-d' ) ) ) < $count_seconds_from_posts ) : ?>
		<div class="new-event"><p><?php _e( 'New!', Serendip_Event_Manager::$domain ); ?></p></div>
		<?php endif; ?>
		<dl class="ws-serendip-event-manager-item">
			<dt><?php _e( 'Date', Serendip_Event_Manager::$domain ); ?></dt>
			<dd>
				<?php if ( !$is_undecided ) : ?>
					<?php if ( strlen( $event_start_date ) > 0 ) : ?>
						<?php echo date( __( 'Y/n/j', Serendip_Event_Manager::$domain ), strtotime( $event_start_date ) ); ?>
						(<span class="week-<?php echo $start_week_index; ?>"><?php echo $WEEK_NAME[ $start_week_index ]; ?></span>)
						<?php $event_end_date = post_custom( 'event_end_date' ); if ( strlen( $event_end_date ) > 0 && $event_start_date != $event_end_date ) : ?>
							〜
							<?php echo date( __( 'Y/n/j', Serendip_Event_Manager::$domain ), strtotime( $event_end_date ) ); ?>
							(<span class="week-<?php echo $end_week_index; ?>"><?php echo $WEEK_NAME[ $end_week_index ]; ?></span>)
						<?php endif; ?>
						<br />
						<?php $event_time = post_custom( 'event_time' ); if ( strlen( $event_time ) > 0 ) : ?>
							<?php echo $event_time; ?>
						<?php endif; ?>
					<?php else : ?>
						<?php _e( 'Undecided', Serendip_Event_Manager::$domain ); ?>
					<?php endif; ?>
				<?php else : ?>
					<?php _e( 'Undecided', Serendip_Event_Manager::$domain ); ?>
				<?php endif; ?>
			</dd>
			<?php if ( strlen( $event_place ) > 0 ) : ?>
			<dt><?php _e( 'Place', Serendip_Event_Manager::$domain ); ?></dt>
			<dd>
				<?php echo strip_tags ( $event_place, '<dl><dt><dd><br><a>' ); ?>
			</dd>
			<?php endif; ?>
			<?php $_content = get_the_content(); if ( strlen( $_content ) > 0 ) : ?>
			<dt><?php _e( 'Details', Serendip_Event_Manager::$domain ); ?></dt>
			<dd><?php
				$_content = apply_filters('the_content', $_content);
				$_content = str_replace(']]>', ']]&gt;', $_content);
 				echo $_content; ?></dd>
			<?php endif; ?>
			<?php if ( is_user_logged_in() ) : ?>
				<p class="post-edit"><?php edit_post_link(); ?></p>
			<?php endif; ?>
		</dl>
	</dd>
<?php endwhile; endif; ?>
</dl>
<div class="ws-serendip-event-manager-page-nav">
<?php
if ( $is_archive ) {
	// Display pagenate links
	global $wp_rewrite;
	$paginate_base = get_pagenum_link( 1 );
	if ( strpos( $paginate_base, '?' ) || ! $wp_rewrite->using_permalinks() ) {
		$paginate_format = '';
		$paginate_base = add_query_arg( 'paged', '%#%' );
	} else {
		$paginate_format = ( substr( $paginate_base, -1 ,1 ) == '/' ? '' : '/' ) . user_trailingslashit( 'page/%#%/', 'paged' );
		$paginate_base .= '%_%';
	}
	echo paginate_links(
		array(
			'base' => $paginate_base,
			'format' => $paginate_format,
			'total' => $query->max_num_pages,
			'mid_size' => 5,
			'current' => ( $paged ? $paged : 1 )
		)
	);
}
?>
</div>
<?php
	wp_reset_query();
	}
}

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
