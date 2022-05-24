<?php
class medigo_basic_addon extends \Elementor\Widget_Base  {

	public function get_name() {
		return 'Medigo_Theme';
	}

	public function get_title() {
		return esc_html__( 'Medigo Theme', 'medigo' );
	}

	public function get_icon() {
		return 'eicon-library-save';
	}

	public function get_custom_help_url() {
		return 'https://go.elementor.com/widget-name';
	}

	public function get_categories() {
		return [ 'basic', 'Medigo-category' ];
	}

	public function get_keywords() {
		return [ 'test', 'heading' ];
	}

}