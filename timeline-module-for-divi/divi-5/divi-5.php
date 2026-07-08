<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'TMDIVI_JSON_PATH' ) ) {
	define( 'TMDIVI_JSON_PATH', TMDIVI_DIR . 'divi-5/modules-json/' );
}

// Require php files.
require_once TMDIVI_MODULE_DIR . '/ModulesCore/Helper.php';
require_once TMDIVI_DIR . 'divi-5/vendor/autoload.php';
require_once TMDIVI_DIR . 'divi-5/server/Conversion/FontFieldConversion.php';
require_once TMDIVI_DIR . 'divi-5/server/Modules/Modules.php';

/**
 * Enqueue Divi 5 Visual Builder Assets
 *
 * @since 1.0.0
 */

class Divi5_Visual_Builder_Assets {

  public function __construct(){
    add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array($this,'tmdivi_divi5_enqueue_visual_builder_assets') );
  }

  public function tmdivi_divi5_enqueue_visual_builder_assets() {
    if ( et_core_is_fb_enabled() && et_builder_d5_enabled() ) {
      ?>
      <style>
        .tmdivi-wrapper .tmdivi-story .tmdivi-arrow-line{
          z-index: -1;
        }
      </style>
      <?php
      \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
        [
          'name'    => 'timeline-module-for-divi-visual-builder',
          'version' => TMDIVI_V,
          'script'  => [
            'src'                => TMDIVI_URL . 'divi-5/visual-builder/build/tmdivi-timeline-module-for-divi-conversion.js',
            'deps'               => [
              'divi-module-library',
              'divi-vendor-wp-hooks',
            ],
            'enqueue_top_window' => false,
            'enqueue_app_window' => true,
          ],
        ]
      );

      if (!wp_style_is('tmdivi-fontawesome-css', 'enqueued')) {
        wp_enqueue_style('tmdivi-fontawesome-css');
      }
      if (!wp_style_is('d5-timeline-helper-style', 'enqueued')) {
        wp_enqueue_style('d5-timeline-helper-style');
      }
    }
  }
}
