<?php

namespace NPW;
use NPW\Database as Database;
use NPW\Post as Post;
use NPW\Widgetize as Widgetize;

class Core{
    function __construct(){
        add_action('plugins_loaded', array($this, 'load'), 15);
    }

    /**
    * Loads classes as needed
    */
    function load(){

        //eventually give this a settings class

        if( ! get_site_option('network-post-widget-settings')){
            $default_options = array( 'num_posts' => 10 );
            update_site_option('network-post-widget-settings', $default_options);
        }

        if( is_admin() ){
            //admin only classes
            require_once('Database.php');
            new Database;

            require_once('Populate.php');
            new Populate;



        } else if ( ! is_admin() ){
            //the front end only type stuff
        }

        //all around

        require_once('Cache.php');
        new Cache;

        require_once('Widgetize.php');
        new Widgetize;
    }

}
