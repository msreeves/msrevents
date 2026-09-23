<?php
/**
 * Estate partners aggregator — hub /partners/ pulls programme-site logos.
 *
 * Source of truth stays on each programme blog (partner CPT + featured image).
 * Hub renders sections by programme (Awards · Seminars · Hub), then by tier.
 *
 * @package msrevents
 */

/**
 * Resolve a multisite blog ID from its path slug (e.g. msrawards).
 *
 * @param string $slug Path slug.
 * @return int Blog ID or 0.
 */
function msrevents_get_blog_id_by_path_slug( $slug ) {
	$slug = sanitize_key( (string) $slug );
	if ( '' === $slug || ! is_multisite() ) {
		return is_multisite() ? 0 : (int) get_current_blog_id();
	}

	foreach ( get_sites( array( 'number' => 100 ) ) as $site ) {
		$path = trim( (string) $site->path, '/' );
		if ( $slug === $path || $slug === basename( $path ) ) {
			return (int) $site->blog_id;
		}
	}

	return 0;
}

/**
 * Programme sources for the estate partners page.
 *
 * @return array<int, array{key:string,label:string,blog_id:int,partners_url:string,logo_mimes:string[]}>
 */
function msrevents_get_estate_partner_programme_sources() {
	$hub_id = is_multisite() ? (int) get_main_site_id() : (int) get_current_blog_id();

	$sources = array(
		array(
			'key'          => 'awards',
			'label'        => __( 'MSR Awards', 'msrevents' ),
			'blog_id'      => msrevents_get_blog_id_by_path_slug( 'msrawards' ),
			'partners_url' => '',
			'logo_mimes'   => array( 'image/svg+xml', 'image/png', 'image/webp', 'image/jpeg', 'image/gif' ),
		),
		array(
			'key'          => 'seminars',
			'label'        => __( 'MSR Seminars', 'msrevents' ),
			'blog_id'      => msrevents_get_blog_id_by_path_slug( 'msrseminars' ),
			'partners_url' => '',
			'logo_mimes'   => array( 'image/svg+xml', 'image/png', 'image/webp', 'image/jpeg', 'image/gif' ),
		),
		array(
			'key'          => 'hub',
			'label'        => __( 'MSR Events hub', 'msrevents' ),
			'blog_id'      => $hub_id,
			'partners_url' => '',
			// Hub demo partners still share a magazine JPEG — only treat true logo mimes as logos here.
			'logo_mimes'   => array( 'image/svg+xml', 'image/png', 'image/webp' ),
		),
	);

	foreach ( $sources as &$source ) {
		$blog_id = (int) $source['blog_id'];
		if ( $blog_id <= 0 ) {
			continue;
		}
		if ( is_multisite() ) {
			switch_to_blog( $blog_id );
			$source['partners_url'] = home_url( '/partners/' );
			restore_current_blog();
		} else {
			$source['partners_url'] = home_url( '/partners/' );
		}
	}
	unset( $source );

	/**
	 * Filter estate partner programme sources.
	 *
	 * @param array $sources Programme source rows.
	 */
	return apply_filters( 'msrevents_estate_partner_programme_sources', $sources );
}

/**
 * Collect published partners for one programme blog (DTOs safe after restore_current_blog).
 *
 * @param int      $blog_id    Blog ID.
 * @param string[] $logo_mimes Allowed featured-image MIME types.
 * @return array<int, array{id:int,title:string,tier:string,logo_url:string,logo_alt:string,link_url:string,link_target:string}>
 */
function msrevents_collect_programme_partner_dtos( $blog_id, $logo_mimes = array() ) {
	$blog_id    = (int) $blog_id;
	$logo_mimes = array_filter( array_map( 'strval', (array) $logo_mimes ) );
	$out        = array();

	if ( $blog_id <= 0 ) {
		return $out;
	}

	$switched = false;
	if ( is_multisite() && get_current_blog_id() !== $blog_id ) {
		switch_to_blog( $blog_id );
		$switched = true;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'partner',
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $query->posts as $partner ) {
		if ( ! $partner instanceof WP_Post ) {
			continue;
		}

		$thumb_id = (int) get_post_thumbnail_id( $partner );
		if ( $thumb_id <= 0 ) {
			continue;
		}

		$mime = (string) get_post_mime_type( $thumb_id );
		if ( $logo_mimes && $mime && ! in_array( $mime, $logo_mimes, true ) ) {
			continue;
		}

		$logo_url = (string) wp_get_attachment_image_url( $thumb_id, 'medium_large' );
		if ( ! $logo_url ) {
			$logo_url = (string) wp_get_attachment_url( $thumb_id );
		}
		if ( ! $logo_url ) {
			continue;
		}

		// Hub theme helpers stay loaded after switch_to_blog; ACF fields are blog-local.
		if ( function_exists( 'msrevents_get_partner_tier' ) ) {
			$tier = msrevents_get_partner_tier( $partner->ID );
		} elseif ( function_exists( 'get_field' ) ) {
			$tier = sanitize_key( (string) get_field( 'sponsor_tier', $partner->ID ) );
		} else {
			$tier = sanitize_key( (string) get_post_meta( $partner->ID, 'sponsor_tier', true ) );
		}
		if ( '' === $tier ) {
			$tier = 'supporter';
		}

		$link = array(
			'url'    => '',
			'target' => '_self',
		);
		if ( function_exists( 'msrevents_get_acf_link_parts' ) && function_exists( 'get_field' ) ) {
			$link = msrevents_get_acf_link_parts( get_field( 'link', $partner->ID ) );
		} elseif ( function_exists( 'get_field' ) ) {
			$raw = get_field( 'link', $partner->ID );
			if ( is_array( $raw ) ) {
				$link['url']    = isset( $raw['url'] ) ? (string) $raw['url'] : '';
				$link['target'] = ! empty( $raw['target'] ) ? (string) $raw['target'] : '_self';
			} elseif ( is_string( $raw ) ) {
				$link['url'] = $raw;
			}
		}

		$alt = trim( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) );
		if ( '' === $alt ) {
			$alt = get_the_title( $partner );
		}

		$out[] = array(
			'id'          => (int) $partner->ID,
			'title'       => get_the_title( $partner ),
			'tier'        => $tier,
			'logo_url'    => $logo_url,
			'logo_alt'    => $alt,
			'link_url'    => (string) ( $link['url'] ?? '' ),
			'link_target' => (string) ( $link['target'] ?? '_self' ),
		);
	}

	wp_reset_postdata();

	if ( $switched ) {
		restore_current_blog();
	}

	return $out;
}

/**
 * Estate partners grouped by programme, then by sponsor tier.
 *
 * @return array<int, array{key:string,label:string,partners_url:string,tiers:array<string,array>}>
 */
function msrevents_get_estate_partners_by_programme() {
	$tier_keys = array_keys( msrevents_get_sponsor_tiers() );
	$sections  = array();

	foreach ( msrevents_get_estate_partner_programme_sources() as $source ) {
		$blog_id = (int) ( $source['blog_id'] ?? 0 );
		if ( $blog_id <= 0 ) {
			continue;
		}

		$partners = msrevents_collect_programme_partner_dtos(
			$blog_id,
			isset( $source['logo_mimes'] ) ? $source['logo_mimes'] : array()
		);
		if ( ! $partners ) {
			continue;
		}

		$tiers = array_fill_keys( $tier_keys, array() );
		foreach ( $partners as $row ) {
			$tier = isset( $tiers[ $row['tier'] ] ) ? $row['tier'] : 'supporter';
			$tiers[ $tier ][] = $row;
		}

		$sections[] = array(
			'key'          => (string) $source['key'],
			'label'        => (string) $source['label'],
			'partners_url' => (string) ( $source['partners_url'] ?? '' ),
			'tiers'        => $tiers,
		);
	}

	return $sections;
}

/**
 * Render one partner logo tile from a DTO (cross-blog safe).
 *
 * @param array $partner Partner DTO.
 * @return void
 */
function msrevents_render_estate_partner_logo_tile( $partner ) {
	if ( ! is_array( $partner ) || empty( $partner['logo_url'] ) ) {
		return;
	}

	$img = sprintf(
		'<img class="msr-logo-tile__img" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $partner['logo_url'] ),
		esc_attr( (string) ( $partner['logo_alt'] ?? $partner['title'] ?? '' ) )
	);

	$link_url    = isset( $partner['link_url'] ) ? (string) $partner['link_url'] : '';
	$link_target = isset( $partner['link_target'] ) ? (string) $partner['link_target'] : '_self';
	?>
	<div class="mx-auto mb-3 col-md-6 col-lg-4">
		<article class="partner-card panel msr-reveal msr-reveal--up">
			<div class="partner-listing-image events-logo-tile msr-logo-tile">
				<?php
				if ( $link_url ) {
					printf(
						'<a class="msr-logo-tile__link" href="%s" target="%s"%s>%s</a>',
						esc_url( $link_url ),
						esc_attr( $link_target ),
						'_blank' === $link_target ? ' rel="noopener noreferrer"' : '',
						$img // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				} else {
					echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<?php
			$tier_labels = msrevents_get_sponsor_tiers();
			$tier        = isset( $partner['tier'] ) ? (string) $partner['tier'] : '';
			$tier_label  = $tier_labels[ $tier ] ?? '';
			if ( $tier_label ) :
				?>
				<p class="partner-card__tier small text-center mb-2">
					<span class="partner-card__tier-badge"><?php echo esc_html( $tier_label ); ?></span>
				</p>
			<?php endif; ?>
		</article>
	</div>
	<?php
}

/**
 * Render hub /partners/ as programme sections (Awards · Seminars · Hub), each tier-grouped.
 *
 * @return void
 */
function msrevents_render_estate_partners_by_programme() {
	$sections = msrevents_get_estate_partners_by_programme();
	$labels   = msrevents_get_sponsor_tiers();

	if ( ! $sections ) {
		msrevents_render_empty_state(
			array(
				'context' => 'listing',
				'title'   => __( 'No partners published yet', 'msrevents' ),
				'message' => __( 'Partner logos appear here from each programme site when supporters are published.', 'msrevents' ),
			)
		);
		return;
	}

	foreach ( $sections as $section ) {
		$section_id = 'events-partner-programme-' . sanitize_html_class( $section['key'] );
		?>
		<section class="events-partner-programme" aria-labelledby="<?php echo esc_attr( $section_id ); ?>">
			<header class="events-partner-programme__header text-center mb-3">
				<h2 class="events-partner-programme__title h4 mb-2" id="<?php echo esc_attr( $section_id ); ?>">
					<?php echo esc_html( $section['label'] ); ?>
				</h2>
				<?php if ( ! empty( $section['partners_url'] ) && 'hub' !== $section['key'] ) : ?>
					<p class="events-partner-programme__link mb-0">
						<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $section['partners_url'] ); ?>">
							<?php
							printf(
								/* translators: %s: programme name */
								esc_html__( 'View %s partners', 'msrevents' ),
								esc_html( $section['label'] )
							);
							?>
						</a>
					</p>
				<?php endif; ?>
			</header>
			<?php
			foreach ( $labels as $tier => $tier_label ) {
				$rows = $section['tiers'][ $tier ] ?? array();
				if ( ! $rows ) {
					continue;
				}
				$tier_id = $section_id . '-' . sanitize_html_class( $tier );
				?>
				<section class="events-partner-tier" aria-labelledby="<?php echo esc_attr( $tier_id ); ?>">
					<h3 class="events-partner-tier__heading h5 text-center" id="<?php echo esc_attr( $tier_id ); ?>">
						<?php echo esc_html( $tier_label ); ?>
					</h3>
					<div class="row g-3 justify-content-center msr-card-grid align-items-stretch">
						<?php
						foreach ( $rows as $partner ) {
							msrevents_render_estate_partner_logo_tile( $partner );
						}
						?>
					</div>
				</section>
				<?php
			}
			?>
		</section>
		<?php
	}
}
