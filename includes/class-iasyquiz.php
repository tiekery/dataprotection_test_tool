<?php
/**
 *  Base functions for Quiz app
 *
 */


class Iasy_Quiz {
    private static $cptName = 'Quiz';
    public $post_type_name;

    function __construct() {
        $this->load_dependencies();

        add_action( 'add_meta_boxes', array( $this,'quiz_meta_box'));
        add_shortcode( 'iasy-quiz', array( $this, 'do_quiz' ));
        add_shortcode( 'tietosuojakysely', array( $this, 'do_quiz' )); // alternative shortcode
        add_action( 'save_post', array($this, 'save_global_notice_meta_box_data' ));
        add_action( 'wp_ajax_nopriv_iasy_print_pdf', array($this, 'iasy_print_pdf' ));
        add_action( 'wp_ajax_iasy_print_pdf', array($this, 'iasy_print_pdf' ) );
        add_action( 'init', array($this, 'iasy_output_pdf' ) );
    }

    /**
     * PDF output
     */

    public function iasy_output_pdf(){

        if ( !isset($_GET["pdf"]) ) return;

        $pdf = (int)$_GET["pdf"];
        $pdf_name = "pdf_name_" . $pdf;

        $report = get_transient( $pdf );
        $report_name = get_transient( $pdf_name );

        if ( !$report || !$report_name ) return;

        include dirname(__DIR__) . '/vendor/autoload.php';
        ob_start();
        include "views/report.css";
        $stylesheet = ob_get_contents();
        ob_end_clean();

        ob_start();
        include "views/report-pdf.php";
        $report = ob_get_contents();
        ob_end_clean();

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($report,\Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output( $report_name . '.pdf', 'D');
        exit;

    }

    public function iasy_print_pdf(){

        $quizname = sanitize_text_field($_POST['quizname']);

        $message = "<h3>$quizname</h3>";
        $message .= wp_kses_post( stripslashes($_POST['markup']) );

        $time = time() . rand(5, 500);
        $name = sanitize_title($_POST['quizname']) ?: 'tietosuojakysely';
        set_transient( "pdf_name_" . $time, $name, 1800 );
        set_transient( $time, $message, 1800 );

        $data = array( "pdf" => $time );

        // returns pfd file to user
        die(json_encode($data));
    }

    /**
     * Wordpress admin metabox
     */

    public function quiz_meta_box() {
        global $pagenow, $post_type, $post;
        add_meta_box(
            'quiz-settings',
            __( 'Settings', 'iasyquiz' ),
            array($this, 'global_notice_meta_box_callback'),
            $this->post_type_name,
        );

        if (($post_type == $this->post_type_name) && ($pagenow == 'post-new.php' || $pagenow == 'post.php')) {
            wp_enqueue_script( 'quizIndexjs', PLUGIN_URL  . 'dist/assets/index.js', array ( 'jquery' ), PLUGIN_VERSION, true);
            wp_enqueue_style( 'AdminAppcss', PLUGIN_URL  . 'dist/assets/AdminApp.css');
            add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);

            $quizjson = get_post_meta( $post->ID, '_quiz_json', true );
            wp_localize_script( 'quizIndexjs', 'quiz_data', array(
                'id' => get_the_ID(),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'quiztitle' => get_the_title(get_the_ID()),
                'nonce' => wp_create_nonce('iasy_ajax_nonce'),
                'quizdata' => $quizjson,
            ));
        }
    }

    public function global_notice_meta_box_callback( $post ) {
        echo '<div id="quizAdmin"></div>';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id
     */
    public function save_global_notice_meta_box_data( $post_id ) {

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'quiz' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        }
        else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        // Make sure that it is set.
        if ( ! isset( $_POST['quiz_json'] ) ) {
            return;
        }

        $quiz_data = $_POST['quiz_json'];

        // Update the meta field in the database.
        update_post_meta( $post_id, '_quiz_json', $quiz_data );
    }

    public function load_dependencies(){
        require_once plugin_dir_path( __FILE__ ) . 'class-custom-post-type.php';

        $args = array(
            'show_in_rest' => true,
            'supports' => array( 'title' ),
            'capability-type' => 'page',
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'menu_icon' => 'dashicons-welcome-write-blog'
        );
        $labels = array(
            'name' => 'Survey',
            'singular_name'         => 'Survey',
            'add_new'               => 'Add new survey',
            'add_new_item'          => 'Add new survey',
            'edit_item'             => 'Edit survey',
            'new_item'              => 'New survey',
            'all_items'             => 'All surveys',
            'menu_name'             => 'Surveys',
        );
        $quizcpt = new Iasy_Custom_Post_Type( self::$cptName, $args, $labels );
        $this->post_type_name = strtolower( str_replace( ' ', '_', self::$cptName ) );


        add_filter('manage_quiz_posts_columns', function($columns) {
            return array_merge($columns, ['shortcode' => 'Shortcode']);
        });

        add_action('manage_quiz_posts_custom_column', function($column_key, $post_id) {
            if ($column_key == 'shortcode') {
                echo '[iasy-quiz id="'.get_the_ID().'"]';
            }
        }, 10, 2);

    }

    public function do_quiz( $atts ) {
        wp_enqueue_script( 'quizIndexjs', PLUGIN_URL  . 'dist/assets/index.js', array ( 'jquery' ), PLUGIN_VERSION, true);
        add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);
        wp_enqueue_style( 'quizAppcss', PLUGIN_URL . 'dist/assets/quizApp.css', array(), PLUGIN_VERSION);
        wp_enqueue_style( 'quizIndexcss', PLUGIN_URL  . 'dist/assets/index.css', array(), PLUGIN_VERSION);

        $quizid = $atts['id'];
        $quizjson = get_post_meta( $quizid, '_quiz_json', true );
        $quizSettings = get_option('quiz_settings');
        wp_localize_script( 'quizIndexjs', 'quiz_data', array(
            'lang' => pll_current_language(),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('iasy_ajax_nonce'),
            'quizFormInfotext' => $quizSettings['settings_form_infotext'] ?: '',
            'quizjson' => $quizjson
        ));
        return '<div id="quizApp"></div>';
    }

    # add type="module" to loaded
    public function add_type_attribute($tag, $handle, $src) {
        // if not your script, do nothing and return original $tag
        if ( 'quizAppjs' == $handle  || 'quizIndexjs' == $handle) {
            $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
            return $tag;
        }
        return $tag;
    }

}