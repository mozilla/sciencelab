<?php

class SiteOrigin_Panels_Widget_Button extends SiteOrigin_Panels_Widget  {
	function __construct() {
		parent::__construct(
			__('Button (PB)', 'so-panels'),
			array(
				'description' => __('A simple button', 'so-panels'),
				'default_style' => 'simple',
			),
			array(),
			array(
				'text' => array(
					'type' => 'text',
					'label' => __('Text', 'so-panels'),
				),
				'url' => array(
					'type' => 'text',
					'label' => __('Destination URL', 'so-panels'),
				),
				'new_window' => array(
					'type' => 'checkbox',
					'label' => __('Open In New Window', 'so-panels'),
				),
				'align' => array(
					'type' => 'select',
					'label' => __('Button Alignment', 'so-panels'),
					'options' => array(
						'left' => __('Left', 'so-panels'),
						'right' => __('Right', 'so-panels'),
						'center' => __('Center', 'so-panels'),
						'justify' => __('Justify', 'so-panels'),
					)
				),
			)
		);
	}

	function widget_classes($classes, $instance) {
		$classes[] = 'align-'.(empty($instance['align']) ? 'none' : $instance['align']);
		return $classes;
	}
}