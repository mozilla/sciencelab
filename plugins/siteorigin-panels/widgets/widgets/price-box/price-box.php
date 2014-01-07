<?php

class SiteOrigin_Panels_Widget_Price_Box extends SiteOrigin_Panels_Widget  {
	function __construct() {
		parent::__construct(
			__('Price Box (PB)', 'so-panels'),
			array(
				'description' => __('Displays a bullet list of elements', 'so-panels'),
				'default_style' => 'simple',
			),
			array(),
			array(
				'title' => array(
					'type' => 'text',
					'label' => __('Title', 'so-panels'),
				),
				'price' => array(
					'type' => 'text',
					'label' => __('Price', 'so-panels'),
				),
				'per' => array(
					'type' => 'text',
					'label' => __('Per', 'so-panels'),
				),
				'information' => array(
					'type' => 'text',
					'label' => __('Information Text', 'so-panels'),
				),
				'features' => array(
					'type' => 'textarea',
					'label' => __('Features Text', 'so-panels'),
					'description' => __('Start each new point with an asterisk (*)', 'so-panels'),
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

		$this->add_sub_widget('button', __('Button', 'so-panels'), 'SiteOrigin_Panels_Widget_Button');
		$this->add_sub_widget('list', __('Feature List', 'so-panels'), 'SiteOrigin_Panels_Widget_List');
	}
}