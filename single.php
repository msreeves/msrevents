<?php
/**
 * The template for displaying all single posts
 *
 *
 * @package msrevents
 */

get_header();
?>

<main id="site-content">
    <div class="container">

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );
		}
	}

	?>
</div>
</main><!-- #site-content -->

<?php
get_footer();
