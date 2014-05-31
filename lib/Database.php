<?php

namespace NPW;

class Database{
    protected $table;

    function __construct(){
        add_action('init', array($this, 'set_table'), 10);
        add_action('init', array($this, 'check_db'), 10);
    }

    /*
    * sets the table name to our custom table
    */
    function set_table(){
        $this->table = 'network_posts';
    }
    /**
    * Creates the database if it doesn't exist
    * @todo hook this into a more reasonable place than every init
    */
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
    * Creates the DB table, mirrors the wp_post schema
    * @todo verify this is indeed the correct schema and that the codex insn't out of date, update codex if applicable
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
