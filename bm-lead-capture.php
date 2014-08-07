<?php
/**
 * Plugin Name: BM Lead Capture
 * Plugin URI: https://github.com/bricks-mortar/bm-lead-capture
 * Description: A popup plugin for WordPress to capture leads
 * Version: 0.1.0
 * Author: Dane Grant
 * Author URI: http://www.bricksandmortarweb.com
 * License: MIT
 */

// Helper functions
require_once('lib/helpers.php');

// Admin
require_once('admin/OptionsPage.php');
require_once('admin/LeadsPage.php');

// Global vars
define('PLUGIN_ASSETS', plugins_url('assets', __FILE__));

// Setup database table
require_once('lib/db.php');
register_activation_hook( __FILE__, 'jal_install' );

// check cookie or else ignore loading
// Load plugin assets
function lc_enqueue_scripts() {
    wp_enqueue_style('lc_css', PLUGIN_ASSETS . '/css/lead-capture.css');
    wp_enqueue_script('lc_js', PLUGIN_ASSETS . '/js/lead-capture.js', array('jquery'), '1', true);
}
add_action( 'wp_enqueue_scripts', 'lc_enqueue_scripts' );

// Load admin pages
function lc_add_pages() {
    $options_page = new OptionsPage();
    add_menu_page('Options', 'Lead Capture', 'manage_options', 'lc-options-page', array($options_page, 'create_admin_page'));
    $leads_page = new LeadsPage();
    add_submenu_page('lc-options-page', 'Lead List', 'Lead List', 'manage_options', 'lead-list', array($leads_page, 'create_lead_page'));
}
add_action('admin_menu', 'lc_add_pages');

// This function is called on email submission
function captureLead() {
    global $wpdb;

    $email = strip_tags($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid email address");
    }

    // if email already exists in database just send another email
    if ($wpdb->get_results("SELECT * FROM wp_leads WHERE email = '$email'")) {
        $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

        function sendMail($email, $config) {
            wp_mail( $email, $config['email_subject'], $config['email_message'], '', getAttachmentPath($config['offer_download']));
        }
        sendMail($email, $config);
        die($config['submission_msg'] . $config['offer_download']);
    }

    // store lead in database
    if (!$wpdb->insert('wp_leads',
        array(
            'time' => current_time('mysql'),
            'email'=> $email))
    ) {
        die("There was an error with your submission");

    } else {
        $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
        function sendMail($email, $config) {
            wp_mail( $email, $config['email_subject'], $config['email_message'], '', $config['offer_download']);
        }
        sendMail($email, $config);
        die($config['submission_msg']);
    }

    // something went wrong
    die("There was a problem processing your registration");
}
add_action('wp_ajax_nopriv_captureLead', 'captureLead');


