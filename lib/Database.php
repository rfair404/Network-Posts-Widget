<?php

namespace NPW;

class Database{
    protected $table;

    function __construct(){
        add_action('init', array($this, 'set_table'), 10);
        add_action('init', array($this, 'check_db'), 10);
    }

    function set_table(){
        $this->table = 'network_posts';
    }
    //checks the database
    //would be better if we only hooked this check on our settings page, or only ran on activation but for now this is it
    function check_db(){
        if( ! $this->database_exists() ){
            $this->create_table();
        }
    }

    /**
    * conditional check if our custom db exists or not.
    * @return bool false if the table isn't found, true if it is
    */
    function database_exists(){
        global $wpdb;

        $table_exists = (bool) $wpdb->query(  "SHOW TABLES LIKE '$wpdb->base_prefix$this->table';"  );

       return $table_exists;
    }
    /**
    * As much as possible I've attempted to mirror the WP db schema so that the data can be copied over neatly
    */
    function create_table(){
        global $wpdb;

        require_once(ABSPATH . "wp-admin" . '/includes/upgrade.php');

        $table_name = $wpdb->base_prefix . $this->table;

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          post_id bigint(20) unsigned,
          blog_id mediumint(9) NOT NULL,
          post_author bigint(20) unsigned,
          post_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          post_date_gmt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          post_content longtext,
          post_title text,
          post_excerpt text,
          post_status varchar(20) DEFAULT 'post_status',
          post_type varchar(20) DEFAULT 'post',
          post_modified_gmt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          post_modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          guid varchar(255),
          UNIQUE KEY id (id)
        );";

        dbDelta( $sql );
    }
}
