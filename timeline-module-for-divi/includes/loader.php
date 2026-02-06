<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
if (!class_exists('ET_Builder_Element')) {
    return;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound	
$module_files = glob(__DIR__ . '/modules/*/*.php');

// Load custom Divi Builder modules
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound	
foreach ((array) $module_files as $module_file) {
    if ($module_file && preg_match("/\/modules\/\b([^\/]+)\/\\1\.php$/", $module_file)) {
        require_once $module_file;
    }
}

require_once plugin_dir_path(__FILE__) . 'modules/ModulesCore/ModulesHelper.php';