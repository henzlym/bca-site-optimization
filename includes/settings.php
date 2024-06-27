<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if (!class_exists('BCA_Site_Optimization_Settings')) :
	class BCA_Site_Optimization_Settings
	{

		private $settings_api;

		function __construct()
		{
			$this->settings_api = new BCA_Site_Optimization_Settings_API;

			add_action('admin_init', array($this, 'admin_init'));
			add_action('admin_menu', array($this, 'admin_menu'));
		}

		function admin_init()
		{

			//set the settings
			$this->settings_api->set_sections($this->get_settings_sections());
			$this->settings_api->set_fields($this->get_settings_fields());

			//initialize settings
			$this->settings_api->admin_init();
		}

		function admin_menu()
		{
			add_options_page('Site Optimization', 'Site Optimization', 'manage_options', 'bca_site_optimizations', array($this, 'plugin_page'));
		}

		function get_settings_sections()
		{
			$sections = array(
				array(
					'id'    => 'bca_site_optimizations_basics',
					'title' => __('Basic Settings', 'bca')
				)
			);
			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields()
		{
			$settings_fields = array(
				'bca_site_optimizations_basics' => array(
					array(
						'name'    => 'enable_cache',
						'label'   => __('Enable Cache', 'bca'),
						'desc'    => __('Enable site caching', 'bca'),
						'type'    => 'radio',
						'default' => 0,
						'options' => array(
							true => 'Yes',
							false  => 'No'
						)
					),
					array(
						'name'    => 'enable_gzip',
						'label'   => __('Gzip', 'bca'),
						'desc'    => __('Enable gzip file compression', 'bca'),
						'type'    => 'radio',
						'default' => 0,
						'options' => array(
							true => 'Yes',
							false  => 'No'
						)
					)
				)
			);

			return $settings_fields;
		}

		function plugin_page()
		{
			echo '<div class="wrap">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages()
		{
			$pages = get_pages();
			$pages_options = array();
			if ($pages) {
				foreach ($pages as $page) {
					$pages_options[$page->ID] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}
	new BCA_Site_Optimization_Settings;
endif;
