<?php

namespace NPW;
use NPW_Widget;

class Widgetize{
    function __construct(){
        add_action('widgets_init', array($this, 'register_widget'));
    }

    /**
    * Registers the Widget
    * @todo figure out why I can't namespace a widget
    */
    function register_widget(){
        require_once('NPW_Widget.php');
        register_widget('NPW_Widget');
    }
}

