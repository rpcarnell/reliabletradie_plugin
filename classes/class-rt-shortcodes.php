<?php
class RT_Shortcodes {

	public function __construct() {
             add_shortcode( 'reliabletradie_lost_password', array( $this, 'lost_password' ) );
             add_shortcode( 'reliabletradie_find_tradie', array( $this, 'find_tradie' ) );
             add_shortcode( 'reliabletradie_tradie_setup', array( $this, 'tradie_setup' ) );
             add_shortcode( 'reliabletradie_tradie_found', array( $this, 'tradie_found' ) );
             add_shortcode( 'reliabletradie_tradie_showtra', array($this, 'tradie_show'));
            
        }
        public function lost_password( $atts ) {
		global $reliableTradie;
		return $reliableTradie->shortcode_wrapper( array( 'RT_Shortcode_Lost_Password', 'output' ), $atts );
	}
        public function find_tradie( $atts ) {
		global $reliableTradie;
		return $reliableTradie->shortcode_wrapper( array( 'RT_Shortcode_Find_Tradie', 'output' ), $atts );
	}
        public function tradie_setup( $atts ) { 
		global $reliableTradie;
		return $reliableTradie->shortcode_wrapper( array( 'RT_Shortcode_Tradie_Setup', 'output' ), $atts );
	}
        public function tradie_found( $atts ) {
		global $reliableTradie;
		return $reliableTradie->shortcode_wrapper( array( 'RT_Shortcode_Find_Tradie', 'tradiesfound' ), $atts );
	}
         public function tradie_show( $atts ) {
		global $reliableTradie;
		return $reliableTradie->shortcode_wrapper( array( 'RT_Shortcode_Find_Tradie', 'tradieshow' ), $atts );
	}
}
