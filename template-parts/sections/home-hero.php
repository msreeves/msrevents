<?php
/**
 * Programme home hero — image, scrim, facts, CTAs.
 *
 * @package msrevents
 */

if ( ! msrevents_should_show_home_hero() ) {
	return;
}

$hero_bg = msrevents_hero_background_url( get_field( 'image', 'option' ) );
$hero_attachment_id = function_exists( 'msrevents_acf_attachment_id' )
	? msrevents_acf_attachment_id( get_field( 'image', 'option' ) )
	: 0;
$venue   = get_field( 'venue', 'option' );
$date    = get_field( 'date', 'option' );
$time    = get_field( 'time', 'option' );
if ( ! is_array( $venue ) ) {
	$venue = array();
}
if ( ! is_array( $date ) ) {
	$date = array();
}
if ( ! is_array( $time ) ) {
	$time = array();
}

$hero_name = trim( (string) get_field( 'name', 'option' ) );
if ( '' === $hero_name ) {
	$hero_name = __( 'Programmes, awards, and seminars — built for live audiences.', 'msrevents' );
}

$venue_name  = isset( $venue['name'] ) ? (string) $venue['name'] : '';
$venue_addr  = isset( $venue['address'] ) ? (string) $venue['address'] : '';
$date_start  = isset( $date['start'] ) ? (string) $date['start'] : '';
$date_finish = isset( $date['finish'] ) ? (string) $date['finish'] : '';
$time_start  = isset( $time['start'] ) ? (string) $time['start'] : '';
$time_finish = isset( $time['finish'] ) ? (string) $time['finish'] : '';
$has_facts   = $venue_addr || $date_start || $date_finish || $time_start || $time_finish;
$hero_lead   = $venue_name ? $venue_name : __( 'MSR Awards for recognition. MSR Seminars for delegate learning.', 'msrevents' );
?>
<section class="msr-events-hero<?php echo ( $hero_bg || $hero_attachment_id ) ? '' : ' msr-events-hero--no-image'; ?>">
	<?php
	if ( $hero_attachment_id ) {
		echo wp_get_attachment_image(
			$hero_attachment_id,
			'medium_large',
			false,
			array(
				'class'          => 'msr-events-hero__bg',
				'fetchpriority'  => 'high',
				'loading'        => 'eager',
				'decoding'       => 'async',
				'alt'            => '',
			)
		);
	} elseif ( $hero_bg ) {
		printf(
			'<img class="msr-events-hero__bg" src="%s" alt="" fetchpriority="high" loading="eager" decoding="async" />',
			esc_url( $hero_bg )
		);
	}
	?>
	<div class="msr-events-hero__scrim" aria-hidden="true"></div>
	<div class="msr-events-hero__inner">
		<div class="msr-events-hero__content msr-reveal">
			<p class="msr-events-hero__eyebrow"><?php esc_html_e( 'Events hub', 'msrevents' ); ?></p>
			<?php if ( function_exists( 'msrevents_get_programme_format_label' ) ) : ?>
			<p class="msr-events-hero__format-badge"><?php echo esc_html( msrevents_get_programme_format_label() ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( $hero_name ); ?></h1>
			<p class="msr-events-hero__lead"><?php echo esc_html( $hero_lead ); ?></p>
			<?php if ( $has_facts ) : ?>
			<ul class="msr-events-hero__facts">
				<?php if ( $date_start || $date_finish ) : ?>
				<li class="msr-events-hero__fact">
					<?php if ( $date_start ) : ?><i class="fa-solid fa-calendar" aria-hidden="true"></i><?php endif; ?>
					<span><?php echo esc_html( trim( $date_start . ( $date_finish ? ' - ' . $date_finish : '' ) ) ); ?></span>
				</li>
				<?php endif; ?>
				<?php if ( $time_start || $time_finish ) : ?>
				<li class="msr-events-hero__fact">
					<?php if ( $time_start ) : ?><i class="fa-solid fa-clock" aria-hidden="true"></i><?php endif; ?>
					<span><?php echo esc_html( trim( $time_start . ( $time_finish ? ' - ' . $time_finish : '' ) ) ); ?></span>
				</li>
				<?php endif; ?>
				<?php if ( $venue_addr ) : ?>
				<li class="msr-events-hero__fact msr-events-hero__fact--address">
					<i class="fa fa-map-marker" aria-hidden="true"></i>
					<span class="msr-events-hero__fact-text"><?php msrevents_render_rich_text( $venue_addr ); ?></span>
				</li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
			<div class="events-ctas ctas">
				<?php msrevents_render_cta_link( get_field( 'link1', 'option' ) ); ?>
				<?php msrevents_render_cta_link( get_field( 'link2', 'option' ), 'btn btn-outline-primary' ); ?>
				<p class="msr-events-hero__cta-note"><?php esc_html_e( 'Preview — ticketing connects at launch', 'msrevents' ); ?></p>
			</div>
		</div>
	</div>
</section>
