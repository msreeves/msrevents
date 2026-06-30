<?php
/**
 * Media helpers — hero guards, card frames, ACF image/link sanitization.
 *
 * @package msrevents
 */

/**
 * Resolve a thumbnail attachment ID when the file exists and is an image.
 *
 * @param int $attachment_id Candidate attachment.
 * @return int
 */
function msrevents_sanitize_attachment_id( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id ) {
		return 0;
	}
	if ( ! wp_attachment_is_image( $attachment_id ) ) {
		return 0;
	}
	return $attachment_id;
}

/**
 * Allow only http(s) background image URLs for inline hero styles.
 *
 * @param mixed $url Raw URL.
 * @return string
 */
function msrevents_sanitize_background_url( $url ) {
	$url = esc_url_raw( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	$parts = wp_parse_url( $url );
	if ( empty( $parts['scheme'] ) || ! in_array( $parts['scheme'], array( 'http', 'https' ), true ) ) {
		return '';
	}
	return $url;
}

/**
 * Hero background URL from ACF image field (options or post).
 *
 * @param mixed $value Raw ACF image value.
 * @return string
 */
function msrevents_hero_background_url( $value ) {
	return msrevents_sanitize_background_url( msrevents_acf_image_url( $value ) );
}

/**
 * Normalize an ACF link field to url/title/target parts.
 *
 * @param mixed $link Raw ACF link field.
 * @return array{url: string, title: string, target: string}
 */
function msrevents_get_acf_link_parts( $link ) {
	$parts = array(
		'url'    => '',
		'title'  => '',
		'target' => '_self',
	);
	if ( ! is_array( $link ) ) {
		return $parts;
	}
	if ( ! empty( $link['url'] ) ) {
		$parts['url'] = esc_url_raw( (string) $link['url'] );
	}
	if ( ! empty( $link['title'] ) ) {
		$parts['title'] = (string) $link['title'];
	}
	if ( ! empty( $link['target'] ) ) {
		$parts['target'] = (string) $link['target'];
	}
	return $parts;
}

/**
 * Render a consistent card media frame (16:10) for archive cards.
 *
 * @param int|null $post_id Post ID; defaults to current post.
 * @param string   $size    Image size.
 * @param array    $args    Optional link_url, link_target, link_class.
 * @return void
 */
function msrevents_render_card_media( $post_id = null, $size = 'medium_large', $args = array() ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$thumb_id = msrevents_sanitize_attachment_id( (int) get_post_thumbnail_id( $post_id ) );
	if ( ! $thumb_id ) {
		return;
	}

	$link_url    = isset( $args['link_url'] ) ? esc_url_raw( (string) $args['link_url'] ) : '';
	$link_target = isset( $args['link_target'] ) ? (string) $args['link_target'] : '_self';
	$link_class  = isset( $args['link_class'] ) ? (string) $args['link_class'] : '';

	$image = wp_get_attachment_image(
		$thumb_id,
		$size,
		false,
		array(
			'class'   => 'msr-card-media__img',
			'loading' => 'lazy',
			'alt'     => trim( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) ?: get_the_title( $post_id ),
		)
	);

	if ( ! $image ) {
		return;
	}

	if ( $link_url ) {
		printf(
			'<a class="msr-card-media%s" href="%s" target="%s"%s>%s</a>',
			$link_class ? ' ' . esc_attr( $link_class ) : '',
			esc_url( $link_url ),
			esc_attr( $link_target ),
			'_blank' === $link_target ? ' rel="noopener noreferrer"' : '',
			$image // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image
		);
		return;
	}

	printf( '<div class="msr-card-media">%s</div>', $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render single portrait frame.
 *
 * @param int|null $post_id Post ID.
 * @param string   $size    Image size.
 * @return void
 */
function msrevents_render_single_media( $post_id = null, $size = 'large' ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$thumb_id = msrevents_sanitize_attachment_id( (int) get_post_thumbnail_id( $post_id ) );
	if ( ! $thumb_id ) {
		return;
	}

	echo '<div class="msr-single-media">';
	echo '<div class="msr-single-media__frame">';
	echo wp_get_attachment_image(
		$thumb_id,
		$size,
		false,
		array(
			'class'   => 'msr-single-media__img',
			'loading' => 'eager',
			'alt'     => trim( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) ?: get_the_title( $post_id ),
		)
	);
	echo '</div>';
	$caption = wp_get_attachment_caption( $thumb_id );
	if ( $caption ) {
		printf( '<p class="msr-single-media__caption small text-muted">%s</p>', esc_html( $caption ) );
	}
	echo '</div>';
}
