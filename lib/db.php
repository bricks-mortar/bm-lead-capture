<?php

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
    global $wpdb;
    global $jal_db_version;

    $table_name = $wpdb->prefix . 'leads';

    /*
    * We'll set the default character set and collation for this table.
    * If we don't do this, some characters could end up being converted
    * to just ?'s when saved in our table.
    */
    $charset_collate = '';

    if ( ! empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    }

    if ( ! empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
    }

    $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name varchar(40) NOT NULL,
            email varchar(60) NOT NULL,
            UNIQUE KEY id (id)
            ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'jal_db_version', $jal_db_version );
}

//function jal_install_data() {
//    global $wpdb;
//
//    $welcome_name = 'Lead Capture';
//    $welcome_text = 'Congratulations, you just completed the installation!';
//
//    $table_name = $wpdb->prefix . 'leads';
//
//    $wpdb->insert(
//        $table_name,
//        array(
//            'time' => current_time( 'mysql' ),
//            'name' => 'Dane Grant',
//            'email' => 'danecando@gmail.com',
//        )
//    );
//}