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
		echo get_title( $post_id, true);
	}

   if ( $column_name == 'dates' ) {
		 echo (new DateTime(get_post_meta($post_id,'date_start',true)))->format('F j, Y');
	  $finish = get_post_meta($post_id,'date_finish',true);
	 	if (empty($finish)) {
		echo '';
		} 
		else {
		echo '<br>';
		echo (new DateTime($finish))->format('F j, Y');
		}
	}

  if ( $column_name == 'venue' ) {
		echo get_post_meta( $post_id, 'venue_name', true);
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
		echo get_field( 'runtime', $post_id );
	}

	if ( $column_name == 'series' ) {
		echo get_field( 'series', $post_id );
	}

	if ( $column_name == 'episode' ) {
		echo get_field( 'episode', $post_id );
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
        $podcaster_thumbnail_var = get_field('cat_thumb', $term);
        $content = '<img src="'.$podcaster_thumbnail_var['url'].'" width="200" />';
        }
    return $content;
}
add_filter('manage_podcaster_custom_column' , 'thumbnail_columns_content' , 10 , 3);