<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package msrevents
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( '%s', 'msrevents' ), '<br>' . get_search_query() . '</br>' );
					?>
				</h1>
                    <h3 class="text-center"><?php
  global $wp_query;
  if($wp_query->found_posts < 2) {
    $result = "result";
  } else {
    $result = "results";
  }
    echo $wp_query->found_posts . " " . $result . " found.";
    ?></h3>
			</header><!-- .page-header -->
            <div class="container">
			<div class="row">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );
				

			endwhile;

			echo '<section>';
			the_posts_pagination( array(
'mid_size' => 2,
'prev_text' => __( 'Previous', 'textdomain' ),
'next_text' => __( 'Next', 'textdomain' ),
) );
			echo '</section>';

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
		</div>
				</div>
	</main><!-- #main -->

<?php
get_footer();
