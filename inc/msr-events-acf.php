<?php
/**
 * ACF options page and local fields — MSR Events hub site copy.
 *
 * @package msrevents
 */

/**
 * @return void
 */
function msrevents_register_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'MSR Events settings', 'msrevents' ),
			'menu_title' => __( 'MSR Events', 'msrevents' ),
			'menu_slug'  => 'msr-events-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'icon_url'   => 'dashicons-calendar-alt',
			'position'   => 58,
		)
	);
}
add_action( 'acf/init', 'msrevents_register_acf_options_page' );

/**
 * @return void
 */
function msrevents_register_acf_options_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_events_programme_urls',
			'title'  => 'Programme URLs',
			'fields' => array(
				array(
					'key'   => 'field_msr_evt_opt_awards_url',
					'label' => 'MSR Awards URL',
					'name'  => 'msr_programme_awards_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_msr_evt_opt_seminars_url',
					'label' => 'MSR Seminars URL',
					'name'  => 'msr_programme_seminars_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_msr_evt_opt_publishing_url',
					'label' => 'Atlas Briefing URL',
					'name'  => 'msr_programme_publishing_url',
					'type'  => 'url',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-events-settings',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_events_site_copy',
			'title'  => 'Site copy',
			'fields' => array(
				array(
					'key'   => 'field_msr_evt_ecosystem_title',
					'label' => 'Ecosystem band title',
					'name'  => 'ecosystem_band_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_evt_ecosystem_lead',
					'label' => 'Ecosystem band lead',
					'name'  => 'ecosystem_band_lead',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_archive_intro',
					'label' => 'Our Events archive intro',
					'name'  => 'events_archive_intro',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_publications_lead',
					'label' => 'Publications page lead',
					'name'  => 'publications_page_lead',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_about_lead',
					'label' => 'About page lead',
					'name'  => 'about_page_lead',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_about_programmes',
					'label' => 'About programme map intro',
					'name'  => 'about_programmes_intro',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_evt_about_disclaimer',
					'label' => 'About demonstration disclaimer',
					'name'  => 'about_disclaimer',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'           => 'field_msr_evt_footer_demo_toggle',
					'label'         => 'Show footer demo disclaimer',
					'name'          => 'show_footer_demo_note',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'   => 'field_msr_evt_footer_demo_text',
					'label' => 'Footer demo disclaimer text',
					'name'  => 'footer_demo_note',
					'type'  => 'text',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-events-settings',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_events_seo_copy',
			'title'  => 'SEO descriptions',
			'fields' => array(
				array(
					'key'   => 'field_msr_evt_seo_home',
					'label' => 'Home meta description',
					'name'  => 'seo_home_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_seo_events_archive',
					'label' => 'Events archive meta description',
					'name'  => 'seo_events_archive_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_evt_seo_search',
					'label' => 'Search meta description',
					'name'  => 'seo_search_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-events-settings',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'msrevents_register_acf_options_fields' );
