<?php
/**
 *  Helper functions for localization
 *
 */

class Iasy_Localization_Helper{

    function __construct() {
        add_action( 'init', array( $this, 'pll_fallback'));
    }

    /**
     *  Polylang fallback
     */
    public function pll_fallback(){

        if ( ! function_exists( 'pll__' ) ) {
            function pll__( $string ) {
                return $string;
            }

            function pll_e( $string ) {
                echo $string;
            }

            function pll_current_language() {
                return 'fi';
            }

            function pll_default_language( $value = 'slug' ) {
                return 'fi';
            }

            function pll_get_post_language( $id ) {
                return 'fi';
            }

            function pll_get_post( $post_id, $slug = '' ) {
                return $post_id;
            }

            function pll_get_term( $term_id, $slug = '' ) {
                return $term_id;
            }

            function pll_translate_string( $str, $lang = '' ) {
                return $str;
            }

            function pll_home_url( $slug = '' ) {
                return get_home_url();
            }
        }
    }
}