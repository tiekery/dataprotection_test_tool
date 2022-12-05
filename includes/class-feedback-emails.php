<?php
/**
 *  Class for sending feedback emails
 *
 */


class Iasy_Feedback_Emails{
    function __construct() {
        add_action( 'wp_ajax_nopriv_iasy_form_response', array($this, 'iasy_form_response' ));
        add_action( 'wp_ajax_iasy_form_response', array($this, 'iasy_form_response' ) );
    }

    public function iasy_form_response(){
        $quizSettings = get_option('quiz_settings');
        $recipient = $quizSettings['settings_form_email'];
        $quizname = $_POST['quizname'];
        $question = $_POST['question'];
        $message = $_POST['message'];
        $multiple_recipients = array(
            $recipient
        );
        $subj = 'Palaute kyselystä: ' . $quizname;
        $body .= 'Kyselyn kohta: ' . $question .  "\r\n";
        $body .= '' . "\r\n";
        $body .= 'Palaute:' . "\r\n";
        $body .= $message . "\r\n";
        wp_mail( $multiple_recipients, $subj, $body, $headers );
        die(json_encode(array('status' => 'OK')));
    }
}