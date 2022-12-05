<?php
/**
 *  Settings for Quiz app
 *
 */


class Iasy_Quiz_Settings{
    function __construct() {
        add_action( 'admin_menu', array( $this, 'add_submenu_page_to_post_type' ) );
        add_action( 'admin_init', array( $this, 'sub_menu_page_init' ) );
    }

    public function add_submenu_page_to_post_type()
    {
        add_submenu_page(
            'edit.php?post_type=quiz',
            __('Settings', 'iasy'),
            __('Settings', 'iasy'),
            'manage_options',
            'settings',
            array($this, 'quiz_settings_options_display'));
    }

    /**
     * Options page callback
     */
    public function quiz_settings_options_display()
    {
        $this->options = get_option( 'quiz_settings' );
        echo '<div class="wrap">';
        printf( '<h1>%s</h1>', __('Settings', 'iasy' ) );
        echo '<form method="post" action="options.php">';
        settings_fields( 'quiz_settings' );
        do_settings_sections( 'settings-page' );
        submit_button();
        echo '</form></div>';
    }

    /**
     * Register and add settings
     */
    public function sub_menu_page_init()
    {
        register_setting(
            'quiz_settings', // Option group
            'quiz_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
            );

        add_settings_section(
            'quizform_settings_section', // ID
            __('Feedback form settings', 'iasy'), // Title
            array( $this, 'print_section_info' ), // Callback
            'settings-page' // Page
        );

        add_settings_field(
            'settings_form_email', // ID
            __('Recipient email', 'iasy'), // Title
            array( $this, 'settings_email_callback' ), // Callback
            'settings-page', // Page
            'quizform_settings_section' // Section
        );

        add_settings_field(
            'settings_form_infotext', // ID
            __('Feedback form infotext', 'iasy'), // Title
            array( $this, 'settings_form_infotext_callback' ), // Callback
            'settings-page', // Page
            'quizform_settings_section' // Section
        );

    }
    /**
     * Get the settings option array and print one of its values
     */
    public function settings_email_callback()
    {
        printf(
            '<input type="text" id="settings_form_email" name="quiz_settings[settings_form_email]" value="%s" />',
            isset( $this->options['settings_form_email'] ) ? esc_attr( $this->options['settings_form_email']) : ''
            );
    }
    public function settings_form_infotext_callback()
    {
        printf(
            '<textarea id="settings_form_infotext" name="quiz_settings[settings_form_infotext]" rows="8" cols="50">%s</textarea>',
            isset( $this->options['settings_form_infotext'] ) ? esc_attr( $this->options['settings_form_infotext']) : ''
            );
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print '';
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['settings_form_email'] ) )
            $new_input['settings_form_email'] = sanitize_text_field( $input['settings_form_email'] );

        if( isset( $input['settings_form_infotext'] ) )
            $new_input['settings_form_infotext'] = $input['settings_form_infotext'];

        return $new_input;
    }


}