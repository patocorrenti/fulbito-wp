<?php
namespace PCorrenti\Fulbito;

if ( ! defined( 'ABSPATH' ) ) exit;

class Commons {

    // DEPRECATED, don't use
    public function ft_get_template($templatePath, $templateArgs = []) {
        // 3rd party theme template path
        $themePath = get_template_directory() . '/fulbito/' . $templatePath . '.php';
        // In house template path
        $fullPath =  __DIR__ . '/../templates/' . $templatePath . '.php';
        include( file_exists($themePath) ? $themePath : $fullPath );
    }

    public function ft_template( $path, $templateArgs = [] ) {
        ob_start();
        // 3rd party theme template path
        $themePath = get_template_directory() . '/fulbito/' . $path . '.php';
        // In house template path
        $fullPath =  __DIR__ . '/../templates/' . $path . '.php';
        include( file_exists($themePath) ? $themePath : $fullPath );
        return ob_get_clean();
    }

}
