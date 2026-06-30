<?php 
$post_type = 'event';

// Register the columns.
add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
	
  $defaults['title'] = 'Name';
  $defaults['dates'] = 'Dates';
  $defaults['venue'] = 'Venue';

	return $defaults;
} );

// Handle the value for each of the new columns.
add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {
	
	if ( $column_name == 'title' ) {
		echo esc_html( get_the_title( $post_id ) );
	}

   if ( $column_name == 'dates' ) {
		 $start = get_post_meta( $post_id, 'date_start', true );
		 if ( $start ) {
			 echo esc_html( ( new DateTime( $start ) )->format( 'F j, Y' ) );
		 }
	  $finish = get_post_meta($post_id,'date_finish',true);
	 	if (empty($finish)) {
		echo '';
		} 
		else {
		echo '<br>';
		echo esc_html( ( new DateTime( $finish ) )->format( 'F j, Y' ) );
		}
	}

  if ( $column_name == 'venue' ) {
		echo esc_html( (string) get_post_meta( $post_id, 'venue_name', true ) );
	}
	
}, 10, 2 );

$post_type = 'podcast';

// Register the columns.
add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
	
	$defaults['series'] = 'Series';
	$defaults['episode'] = 'Episode';
	$defaults['runtime'] = 'Runtime';

	return $defaults;
} );

$taxonomy = 'podcaster';

// Handle the value for each of the new columns.
add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {
	
	if ( $column_name == 'runtime' ) {
		echo esc_html( (string) get_field( 'runtime', $post_id ) );
	}

	if ( $column_name == 'series' ) {
		echo esc_html( (string) get_field( 'series', $post_id ) );
	}

	if ( $column_name == 'episode' ) {
		echo esc_html( (string) get_field( 'episode', $post_id ) );
	}
	
}, 10, 2 );


function add_thumbnail_columns($columns) {
	unset($columns['description']);
	unset($columns['slug']);
    $columns['podcaster_thumbnail'] = __('Thumbnail');
    return $columns;

    $new = array();
    foreach($columns as $key => $value) {
        if ($key=='name') 
            $new['podcaster_thumbnail'] = 'Thumbnail';
        $new[$key] = $value;
    }
    return $new;
}
add_filter('manage_edit-podcaster_columns', 'add_thumbnail_columns');


function thumbnail_columns_content($content, $column_name, $term_id) {
    if ('podcaster_thumbnail' == $column_name) {
        $term = get_term($term_id);
        if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
            $podcaster_thumbnail_var = get_field('cat_thumb', $term);
            if ( is_array( $podcaster_thumbnail_var ) && ! empty( $podcaster_thumbnail_var['url'] ) ) {
                $content = sprintf(
                    '<img src="%s" width="200" alt="" />',
                    esc_url( (string) $podcaster_thumbnail_var['url'] )
                );
            }
        }
        }
    return $content;
}
add_filter('manage_podcaster_custom_column' , 'thumbnail_columns_content' , 10 , 3);