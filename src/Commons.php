<?php
namespace PCorrenti\Fulbito;

if ( ! defined( 'ABSPATH' ) ) exit;

class Commons {

    public function ft_template( $path, $templateArgs = [] ) {
        ob_start();
        // 3rd party theme template path
        $themePath = get_template_directory() . '/fulbito/' . $path . '.php';
        // In house template path
        $fullPath =  __DIR__ . '/../templates/' . $path . '.php';
        include( file_exists($themePath) ? $themePath : $fullPath );
        return ob_get_clean();
    }

    public function ft_admin_template($templatePath, $templateArgs = []) {
        include( __DIR__ . '/../templates/' . $templatePath . '.php' );
    }

}
