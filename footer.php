<?php
/**
 * The template for displaying the footer
 *
 * @package msrevents
 */

?>
<?php
if ( function_exists( 'msrevents_show_leaderboard_ads' ) && msrevents_show_leaderboard_ads() ) {
	get_template_part( 'templates/partials/leaderboard/footer' );
}
?>
<?php
if ( function_exists( 'msrevents_render_site_footer' ) ) {
	msrevents_render_site_footer();
}
?>
<?php wp_footer(); ?>
</body>
</html>
