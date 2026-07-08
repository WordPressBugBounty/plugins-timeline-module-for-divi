<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class TMDIVI_Builder_Module extends ET_Builder_Module{

    /**
     * Render prop value
     * Some prop value needs to be parsed before can be used
     * This method is added to display how to parse certain saved value
     *
     */
    public function render_prop($value = '', $field_type = ''){
        $output = '';

        if ('' === $value) {
            return $output;
        }
   
        switch ($field_type) {
            case 'select_icons':
                $output = sprintf(
                    '<i class="et-tmdivi-icon">%1$s</i>',
                    esc_attr(et_pb_process_font_icon($value))
                );
                break;

            default:
                $output = $value;
                break;
        }

        return $output;
    }

    /**
     * Configuring Advanced field for Divi builder.
     */
    public function get_advanced_fields_config(){
        return array(
            'text' => false,
            'fonts' => array(),
            'max_width' => false,
            'margin_padding' => false,
            'border' => false,
            'box_shadow' => false,
            'filters' => false,
            'transform' => false,
            'animation' => false,
            'background' => false
        );
    }

    /**
     *  Credit information for divi module
     */
    protected $module_credits = array(
        'module_uri' => 'https://coolplugins.net/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard',
        'author' => 'Cool Plugins',
        'author_uri' => 'https://coolplugins.net/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard',
    );

}
