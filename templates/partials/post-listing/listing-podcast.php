<?php
$podcaster_terms = get_terms( 'podcaster' );
?>
<?php
foreach ( $podcaster_terms as $podcaster_term ) {
    $podcaster_query = new WP_Query( array(
        'post_type' => 'podcast',
        'tax_query' => array(
            array(
                'taxonomy' => 'podcaster',
                'field' => 'slug',
                'terms' => array( $podcaster_term->slug ),
                'operator' => 'IN'
            )
        )
    ) );
    ?>
    <section class="panel">
            <div class="row g-0">
     <div class="col-md-4"> 
   <?php
$image = get_field('cat_thumb', $podcaster_term);
if( $image ):

    // Image variables.
    $url = $image['url'];
    $title = $image['title'];
    $alt = $image['alt'];

    // Thumbnail size attributes.
    $size = 'large';
    $thumb = $image['sizes'][ $size ];
    $width = $image['sizes'][ $size . '-width' ];
    $height = $image['sizes'][ $size . '-height' ];

 ?>

        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($alt); ?>" />
<?php endif; ?>
    </div>
         <div class="col-md-8">
            <div class="d-flex flex-column h-100">
                <div class="m-auto p-3">
            <h2><?php echo $podcaster_term->name; ?></h2>
           <?php echo $podcaster_term->description; ?>
</div>
</div>
        </div>
        <div class="col-sm-12">
    <?php
    if ( $podcaster_query->have_posts() ) : while ( $podcaster_query->have_posts() ) : $podcaster_query->the_post(); ?>
        <div class="text-center p-5">
                	<h2><?php the_title(); ?> </h2>  
                     <p><span> Series <?php print get_field('series') ?> </span><span> Episode <?php print get_field('episode') ?></span> <i class="fa fa-clock p-1" aria-hidden="true"></i><?php print get_field('runtime') ?> </p>
                     <?php print get_field('summary') ?>
                <p> <audio controls src="<?php echo get_field('file') ?>"><a href="<?php echo get_field('file') ?>"> Download audio </a> </audio></p>
    </div>
    <?php endwhile; endif; ?>
        </div>
    </div>
</section>
            <?php
    // Reset things, for good measure
    $podcaster_query = null;
    wp_reset_postdata();
}
?>