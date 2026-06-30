<?php
/**
 * ACF: Flexible Content > Layouts > Event (hub programme carousel)
 *
 * @package msrevents
 */

$slider = isset( $args['event'] ) && is_array( $args['event'] ) ? $args['event'] : array();
if ( ! $slider ) {
	return;
}

$carousel_id = 'msr-hub-event-carousel';
?>
<div id="<?php echo esc_attr( $carousel_id ); ?>" class="carousel slide msr-hero-carousel" data-bs-ride="carousel" data-bs-interval="6000" role="region" aria-label="<?php esc_attr_e( 'Programme highlights', 'msrevents' ); ?>">
	<?php if ( count( $slider ) > 1 ) : ?>
	<div class="carousel-indicators msr-hero-carousel__indicators">
		<?php foreach ( $slider as $idx => $slide ) : ?>
			<button type="button" data-bs-target="#<?php echo esc_attr( $carousel_id ); ?>" data-bs-slide-to="<?php echo (int) $idx; ?>" class="<?php echo 0 === (int) $idx ? 'active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'msrevents' ), (int) $idx + 1 ) ); ?>"></button>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<div class="carousel-inner">
		<?php
		$count = 0;
		foreach ( $slider as $slide ) :
			$slide_id = is_object( $slide ) && isset( $slide->ID ) ? (int) $slide->ID : (int) $slide;
			if ( $slide_id <= 0 ) {
				continue;
			}
			$thumb    = get_the_post_thumbnail_url( $slide_id, 'large' );
			$venue    = get_field( 'venue', $slide_id );
			$venue    = is_array( $venue ) ? $venue : array();
			$date     = get_field( 'date', $slide_id );
			$date     = is_array( $date ) ? $date : array();
			$time     = get_field( 'time', $slide_id );
			$time     = is_array( $time ) ? $time : array();
			$date_start  = isset( $date['start'] ) ? (string) $date['start'] : '';
			$date_finish = isset( $date['finish'] ) ? (string) $date['finish'] : '';
			$time_start  = isset( $time['start'] ) ? (string) $time['start'] : '';
			$time_finish = isset( $time['finish'] ) ? (string) $time['finish'] : '';
			$venue_name  = isset( $venue['name'] ) ? (string) $venue['name'] : '';
			$venue_addr  = isset( $venue['address'] ) ? (string) $venue['address'] : '';
			?>
		<div class="carousel-item<?php echo 0 === $count ? ' active' : ''; ?>">
			<div class="row g-0">
				<div class="background-image msr-hero-carousel__bg"<?php echo $thumb ? ' style="background-image: url(' . esc_url( $thumb ) . ');"' : ''; ?>>
					<div class="mask p-4 p-md-5 msr-hero-carousel__mask" style="background-color: rgba(0, 0, 0, 0.45);">
						<div class="d-flex justify-content-center align-items-center h-100">
							<div class="text-white text-center msr-hero-carousel__inner">
								<h2 class="carousel-hero-title msr-reveal"><?php echo esc_html( get_the_title( $slide_id ) ); ?></h2>
								<?php if ( $venue_name ) : ?>
								<p class="h4 mb-1"><?php echo esc_html( $venue_name ); ?></p>
								<?php endif; ?>
								<?php if ( $venue_addr ) : ?>
								<p class="mb-2"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html( $venue_addr ); ?></p>
								<?php endif; ?>
								<?php if ( $date_start || $date_finish ) : ?>
								<p class="mb-2">
									<i class="fa-solid fa-calendar" aria-hidden="true"></i>
									<?php echo esc_html( trim( $date_start . ( $date_finish ? ' - ' . $date_finish : '' ) ) ); ?>
								</p>
								<?php endif; ?>
								<?php if ( $time_start || $time_finish ) : ?>
								<p class="mb-3">
									<i class="fa-solid fa-clock" aria-hidden="true"></i>
									<?php echo esc_html( trim( $time_start . ( $time_finish ? ' - ' . $time_finish : '' ) ) ); ?>
								</p>
								<?php endif; ?>
								<div class="ctas d-flex flex-wrap justify-content-center gap-2">
									<?php
									foreach ( array( 'link1', 'link2' ) as $link_key ) :
										$link = get_field( $link_key, $slide_id );
										if ( ! is_array( $link ) || empty( $link['url'] ) ) {
											continue;
										}
										$link_target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
										$btn_class   = 'link2' === $link_key ? 'btn btn-outline-light' : 'btn btn-primary';
										?>
									<a class="<?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link['title'] ?? __( 'Learn more', 'msrevents' ) ); ?></a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php
			++$count;
		endforeach;
		?>
	</div>
	<?php if ( count( $slider ) > 1 ) : ?>
	<button class="carousel-control-prev msr-hero-carousel__control" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id ); ?>" data-bs-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="visually-hidden"><?php esc_html_e( 'Previous', 'msrevents' ); ?></span>
	</button>
	<button class="carousel-control-next msr-hero-carousel__control" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id ); ?>" data-bs-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="visually-hidden"><?php esc_html_e( 'Next', 'msrevents' ); ?></span>
	</button>
	<?php endif; ?>
</div>
