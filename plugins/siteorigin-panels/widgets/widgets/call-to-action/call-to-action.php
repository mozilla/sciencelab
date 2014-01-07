<?php

class SiteOrigin_Panels_Widget_Call_To_Action extends SiteOrigin_Panels_Widget  {
	function __construct() {
		parent::__construct(
			__('Call To Action (PB)', 'so-panels'),
			array(
				'description' => __('A Call to Action block', 'so-panels'),
				'default_style' => 'simple',
			),
			array(),
			array(
				'title' => array(
					'type' => 'text',
					'label' => __('Title', 'so-panels'),
				),
				'subtitle' => array(
					'type' => 'text',
					'label' => __('Sub Title', 'so-panels'),
				),
				'button_text' => array(
					'type' => 'text',
					'label' => __('Button Text', 'so-panels'),
				),
				'button_url' => array(
					'type' => 'text',
					'label' => __('Button URL', 'so-panels'),
				),
				'button_new_window' => array(
					'type' => 'checkbox',
					'label' => __('Open In New Window', 'so-panels'),
				),
			)
		);

		// We need the button style
		$this->add_sub_widget('button', __('Button', 'so-panels'), 'SiteOrigin_Panels_Widget_Button');
	}
}