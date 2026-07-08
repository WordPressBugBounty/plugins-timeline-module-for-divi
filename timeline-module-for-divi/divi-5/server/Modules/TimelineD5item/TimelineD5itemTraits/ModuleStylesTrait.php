<?php
namespace TMDIVI\Modules\TimelineD5item\TimelineD5itemTraits;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Direct access forbidden.' );
}

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Layout\Components\StyleCommon\CommonStyle;

trait ModuleStylesTrait {

  use CustomCssTrait;


  public static function module_styles( $args ) {
    $attrs        = $args['attrs'] ?? [];
    $order_class  = $args['orderClass'];
    $elements     = $args['elements'];
    $settings     = $args['settings'] ?? [];
    $parent_attrs = $args['parentAttrs'] ?? [];

        Style::add(
            [
            'id'            => $args['id'],
            'name'          => $args['name'],
            'orderIndex'    => $args['orderIndex'],
            'storeInstance' => $args['storeInstance'],
            'styles'        => [
                $elements->style(
                    [
                        'attrName'   => 'module',
                        'styleProps' => [
                            'disabledOn' => [
                                'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                            ],
                        ],
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story .tmdivi-content div.tmdivi-title',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_heading_color'] ?? '';
                            return "color:{$data};";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story .tmdivi-content .tmdivi-description,' .
                                      $order_class . ' .tmdivi-story .tmdivi-content .tmdivi-description p',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_description_color'] ?? '';
                            return "color:{$data};";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story div.tmdivi-content, ' .
                                      $order_class . ' .tmdivi-story > div.tmdivi-arrow',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_background_color'] ?? '';
                            return "background:{$data};";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story div.tmdivi-label-big',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_label_color'] ?? '';
                            return "color:{$data};";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story div.tmdivi-label-small',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_sub_label_color'] ?? '';
                            return "color:{$data};";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector' => $order_class . ' .tmdivi-story .tmdivi-icon',
                        'attr'     => $parent_attrs['story_background_color']['advanced'] ?? $parent_attrs['timeline_layout']['advanced']['layout'] ?? '',
                        'declarationFunction' => function ( $declaration_function_args ) use ( $args ) {
                            $data = $args['attrs']['unknownAttributes']['child_story_icon_background_color'] ?? '';
                            return "background-color:{$data} !important;";
                        },
                    ]
                ),

                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story .tmdivi-content, '.$order_class . ' .tmdivi-story > .tmdivi-arrow',
                        'attr'                => $attrs['child_story_background_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "background: {$attr_value} !important;";
                        },
                    ]
                ),
                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story .tmdivi-content .tmdivi-title',
                        'attr'                => $attrs['child_story_heading_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "color: {$attr_value} !important;";
                        },
                    ]
                ),
                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story .tmdivi-content .tmdivi-description,'.$order_class . ' .tmdivi-story .tmdivi-content .tmdivi-description p',
                        'attr'                => $attrs['child_story_description_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "color:{$attr_value} !important;";
                        },
                    ]
                ),
                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story div.tmdivi-label-big',
                        'attr'                => $attrs['child_story_label_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "color: {$attr_value} !important;";
                        },
                    ]
                ),
                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story div.tmdivi-label-small',
                        'attr'                => $attrs['child_story_sub_label_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "color: {$attr_value} !important;";
                        },
                    ]
                ),
                CommonStyle::style(
                    [
                        'selector'            => $order_class . ' .tmdivi-story .tmdivi-icon',
                        'attr'                => $attrs['child_story_icon_background_color']['advanced'] ?? [],
                        'declarationFunction' => function ( $declaration_function_args ) {
                            $attr_value = $declaration_function_args['attrValue'] ?? [];
                            return "background-color: {$attr_value} !important;";
                        },
                    ]
                ),
                CssStyle::style(
                    [
                        'selector'  => $args['orderClass'],
                        'attr'      => $attrs['css'] ?? [],
                        'cssFields' => self::custom_css(),
                    ]
                ),

                $elements->style(
                    [
                        'attrName' => 'title',
                    ]
                ),

                $elements->style(
                    [
                        'attrName' => 'content',
                    ]
                ),

            ],
        ]
        );

    }
}
