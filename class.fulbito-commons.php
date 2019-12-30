<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FulbitoCommons Class
 *
 *
 */

class FulbitoCommons {

    public function ft_get_template($templatePath, $templateArgs = []) {
        // 3rd party theme template path
        $themePath = get_template_directory() . '/fulbito/' . $templatePath . '.php';
        // In house template path
        $fullPath =  'templates/' . $templatePath . '.php';

        include( file_exists($themePath) ? $themePath : $fullPath );
    }

}
