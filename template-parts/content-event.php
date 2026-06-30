<?php
/**
 * Event single content.
 *
 * @package msrevents
 */

$post_id  = get_the_ID();
$schedule = function_exists( 'msrevents_get_event_schedule_fields' )
	? msrevents_get_event_schedule_fields( $post_id )
	: array(
		'date'       => array( 'start' => '', 'finish' => '' ),
		'time'       => array( 'start' => '', 'finish' => '' ),
		'venue_name' => '',
	);
$venue = get_field( 'venue' );
if ( ! is_array( $venue ) ) {
	$venue = array();
}
?>
<article <?php post_class( 'events-single-article' ); ?>>
<div class="row g-0 events-single-article__hero">
	<div class="col-xl-6 col-lg-6">
		<?php msrevents_render_event_single_media( $post_id ); ?>
	</div>
	<div class="col-xl-6 col-lg-6">
		<div class="panel">
			<div class="my-auto text-center">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<?php msrevents_render_event_meta_chips( $post_id ); ?>
				<?php msrevents_render_event_lifecycle_badge(); ?>
				<?php
				$event_phase = msrevents_get_event_lifecycle_phase();
				$event_copy  = msrevents_get_lifecycle_phase_description( $event_phase );
				if ( $event_copy ) :
					?>
					<p class="events-lifecycle-single__copy"><?php echo esc_html( $event_copy ); ?></p>
				<?php endif; ?>
				<?php msrevents_render_event_registration_countdown( $post_id ); ?>
				<?php if ( $schedule['date']['start'] || $schedule['date']['finish'] ) : ?>
					<p class="mb-2">
						<i class="fa-solid fa-calendar" aria-hidden="true"></i>
						<span><?php echo esc_html( msrevents_get_event_date_chip_label( $post_id ) ); ?></span>
					</p>
				<?php endif; ?>
				<?php if ( $schedule['time']['start'] || $schedule['time']['finish'] ) : ?>
					<p class="mb-2">
						<i class="fa-solid fa-clock" aria-hidden="true"></i>
						<span>
							<?php echo esc_html( trim( $schedule['time']['start'] . ( $schedule['time']['finish'] ? ' – ' . $schedule['time']['finish'] : '' ) ) ); ?>
						</span>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $venue['name'] ) ) : ?>
					<p class="mb-2">
						<i class="fa fa-map-marker" aria-hidden="true"></i>
						<span><?php echo esc_html( (string) $venue['name'] ); ?></span>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $venue['address'] ) ) : ?>
					<div class="msr-rich-text mb-3"><?php msrevents_render_rich_text( $venue['address'] ); ?></div>
				<?php endif; ?>
				<div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
					<?php msrevents_render_cta_link( get_field( 'link1', $post_id ) ); ?>
					<?php msrevents_render_cta_link( get_field( 'link2', $post_id ), 'btn btn-outline-primary' ); ?>
				</div>
				<?php msrevents_render_event_calendar_demo( $post_id ); ?>
			</div>
		</div>
	</div>
</div>
<?php msrevents_render_event_mini_agenda( $post_id ); ?>
<?php msrevents_render_event_partners_strip( $post_id ); ?>
<?php if ( get_field( 'information' ) ) : ?>
	<div class="entry-content msr-rich-text">
		<?php msrevents_render_rich_text( get_field( 'information' ) ); ?>
	</div>
<?php endif; ?>
<?php if ( get_field( 'image_gallery' ) ) : ?>
	<?php get_template_part( 'templates/partials/gallery' ); ?>
<?php endif; ?>
<?php msrevents_render_event_related_programmes( $post_id ); ?>
<?php
if ( ( is_single() || is_page() ) && ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
	?>
	<div class="comments-wrapper section-inner">
		<?php comments_template(); ?>
	</div>
	<?php
}
?>
</article>
