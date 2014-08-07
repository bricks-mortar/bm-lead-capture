<?php

class OptionsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'lc_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>BM Lead Capture Plugin</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'lc_option_group' );
                do_settings_sections( 'lc-options-page' );
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        if (isset($_GET['settings-updated'])) {

            require_once(__DIR__ . '/../lib/helpers.php');
            $updated_options = get_option('lc_options');

            $config = array(
                'cookie_length' => 30,
                'popup_heading' => $updated_options['popup_heading'],
                'popup_subheading' => $updated_options['popup_subheading'],
                'popup_background' => $updated_options['popup_background'],
                'yes_message' => $updated_options['yes_message'],
                'no_message' => $updated_options['no_message'],
                'submission_msg' => $updated_options['submission_msg'],
                'offer_heading' => $updated_options['offer_heading'],
                'offer_message' => $updated_options['offer_message'],
                'offer_download' => $updated_options['offer_download'],
                'email_subject' => $updated_options['email_subject'],
                'email_message' => $updated_options['email_message']
            );

            // update json config with new options
            writeConfig($config);
        }

        register_setting(
            'lc_option_group', // Option group
            'lc_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        /**
         * Popup settings
         */
        add_settings_section(
            'popup_options_section', // ID
            'Popup Options', // Title
            array( $this, 'popup_section_info' ), // Callback
            'lc-options-page' // Page
        );

        add_settings_field(
            'cookie_duration', // ID
            'Cookie Duration in Days', // Title
            array( $this, 'cookie_duration_callback' ), // Callback
            'lc-options-page', // Page
            'popup_options_section' // Section
        );

        add_settings_field(
            'popup_heading', // ID
            'Popup Heading', // Title
            array( $this, 'popup_heading_callback' ), // Callback
            'lc-options-page', // Page
            'popup_options_section' // Section
        );

        add_settings_field(
            'popup_subheading',
            'Popup Sub Heading',
            array($this, 'popup_subheading_callback'),
            'lc-options-page',
            'popup_options_section'
        );

        add_settings_field(
            'popup_background',
            'Popup Background URL',
            array($this, 'popup_background_callback'),
            'lc-options-page',
            'popup_options_section'
        );

        add_settings_field(
            'yes_message',
            'Yes Message',
            array($this, 'yes_message_callback'),
            'lc-options-page',
            'popup_options_section'
        );

        add_settings_field(
            'no_message',
            'No Message',
            array($this, 'no_message_callback'),
            'lc-options-page',
            'popup_options_section'
        );

        add_settings_field(
            'submission_msg',
            'Submission Success Message',
            array($this, 'submission_msg_callback'),
            'lc-options-page',
            'popup_options_section'
        );

        /**
         * File settings
         */
        add_settings_section(
            'free_offer_settings', // ID
            'Free Offer', // Title
            array( $this, 'free_gift_info' ), // Callback
            'lc-options-page' // Page
        );

        add_settings_field(
            'offer_heading',
            'Offer Heading',
            array( $this, 'offer_heading_callback' ),
            'lc-options-page',
            'free_offer_settings'
        );

        add_settings_field(
            'offer_message',
            'Offer Message',
            array( $this, 'offer_message_callback' ),
            'lc-options-page',
            'free_offer_settings'
        );

        add_settings_field(
            'offer_download',
            'Offer Download',
            array( $this, 'offer_download_callback' ),
            'lc-options-page',
            'free_offer_settings'
        );

        add_settings_field(
            'email_subject',
            'Email Subject',
            array( $this, 'email_subject_callback' ),
            'lc-options-page',
            'free_offer_settings'
        );

        add_settings_field(
            'email_message',
            'Email Message',
            array( $this, 'email_message_callback' ),
            'lc-options-page',
            'free_offer_settings'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array $new_input
     */
    public function sanitize( $input )
    {
        $new_input = array();

        /**
         * Popup options
         */
        if (isset( $input['cookie_duration']))
            $new_input['cookie_duration'] = sanitize_text_field($input['cookie_duration']);
        if (isset( $input['popup_heading']))
            $new_input['popup_heading'] = sanitize_text_field($input['popup_heading']);
        if (isset( $input['popup_subheading']))
            $new_input['popup_subheading'] = sanitize_text_field($input['popup_subheading']);
        if (isset( $input['popup_background']))
            $new_input['popup_background'] = sanitize_text_field($input['popup_background']);
        if (isset( $input['submission_msg']))
            $new_input['submission_msg'] = sanitize_text_field($input['submission_msg']);
        if (isset( $input['yes_message']))
            $new_input['yes_message'] = sanitize_text_field($input['yes_message']);
        if (isset( $input['no_message']))
            $new_input['no_message'] = sanitize_text_field($input['no_message']);

        /**
         * Offer options
         */
        if( isset( $input['offer_heading'] ) )
            $new_input['offer_heading'] = sanitize_text_field( $input['offer_heading'] );
        if( isset( $input['offer_message'] ) )
            $new_input['offer_message'] = sanitize_text_field( $input['offer_message'] );
        if( isset( $input['offer_download'] ) )
            $new_input['offer_download'] = sanitize_text_field( $input['offer_download'] );
        if( isset( $input['email_subject'] ) )
            $new_input['email_subject'] = sanitize_text_field( $input['email_subject'] );
        if( isset( $input['email_message'] ) )
            $new_input['email_message'] = sanitize_text_field( $input['email_message'] );

        return $new_input;
    }

    /**
     * Popup option fields
     */
    public function popup_section_info()
    {
        print 'Options specific to popup styles and functions';
    }

    public function cookie_duration_callback()
    {
        printf(
            '<select id="cookie-duration" name="lc_options[cookie-duration]">' .
            '<option value="5">5 Days</option>' .
            '<option value="10">10 Days</option>' .
            '<option value="15">15 Days</option>' .
            '<option value="30">30 Days</option>' .
            '</select>'
        );
    }

    public function popup_heading_callback()
    {
        printf(
            '<input type="text" id="popup-heading" name="lc_options[popup_heading]" value="%s" />',
            isset( $this->options['popup_heading'] ) ? esc_attr( $this->options['popup_heading']) : ''
        );
    }

    public function popup_subheading_callback()
    {
        printf(
            '<input type="text" id="popup-subheading" name="lc_options[popup_subheading]" value="%s" />',
            isset( $this->options['popup_subheading'] ) ? esc_attr( $this->options['popup_subheading']) : ''
        );
    }

    public function popup_background_callback()
    {
        printf(
            '<input type="text" id="popup-background" name="lc_options[popup_background]" value="%s" />',
            isset( $this->options['popup_background'] ) ? esc_attr( $this->options['popup_background']) : ''
        );
    }

    public function yes_message_callback()
    {
        printf(
            '<input type="text" id="yes-message" name="lc_options[yes_message]" value="%s" />',
            isset( $this->options['yes_message'] ) ? esc_attr( $this->options['yes_message']) : ''
        );
    }

    public function no_message_callback()
    {
        printf(
            '<input type="text" id="no-message" name="lc_options[no_message]" value="%s" />',
            isset( $this->options['no_message'] ) ? esc_attr( $this->options['no_message']) : ''
        );
    }

    public function submission_msg_callback()
    {
        printf(
            '<input type="text" id="submission-msg" name="lc_options[submission_msg]" value="%s" />',
            isset( $this->options['submission_msg'] ) ? esc_attr( $this->options['submission_msg']) : ''
        );
    }


    /**
     * Offer Options
     */
    public function free_gift_info()
    {
        print 'Free offering incentive information';
    }

    public function offer_heading_callback()
    {
        printf(
            '<input type="text" id="title" name="lc_options[offer_heading]" value="%s" />',
            isset( $this->options['offer_heading'] ) ? esc_attr( $this->options['offer_heading']) : ''
        );
    }

    public function offer_message_callback()
    {
        printf(
            '<input type="text" id="title" name="lc_options[offer_message]" value="%s" />',
            isset( $this->options['offer_message'] ) ? esc_attr( $this->options['offer_message']) : ''
        );
    }

    public function offer_download_callback()
    {
        printf(
            '<input type="text" id="title" name="lc_options[offer_download]" value="%s" />',
            isset( $this->options['offer_download'] ) ? esc_attr( $this->options['offer_download']) : ''
        );
    }

    public function email_subject_callback()
    {
        printf(
            '<input type="text" id="title" name="lc_options[email_subject]" value="%s" />',
            isset( $this->options['email_subject'] ) ? esc_attr( $this->options['email_subject']) : ''
        );
    }

    public function email_message_callback()
    {
        printf(
            '<input type="text" id="title" name="lc_options[email_message]" value="%s" />',
            isset( $this->options['email_message'] ) ? esc_attr( $this->options['email_message']) : ''
        );
    }
}