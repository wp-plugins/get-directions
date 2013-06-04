<?php
/*
Plugin Name: getDirections
Plugin URI: http://llocally.com/wordpress-plugins/get-directions
Description: Get Directions knows when your site is being viewed on a mobile or desktop. When it is on a desktop if displays a map and directions, when it is viewed on a mobile it passes the co-ordinates through to the mobiles google maps so the mobile can be used for bnavifgation. the direction map can be utilised through a shortcode or widget.
Version: 1.21
Author: llocally
Author URI: http://llocally.com/wordpress-plugins/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1'); 

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//
//
$getdirections_current_version = '1.0';
require_once 'includes/ll_is_mobile.php'; //mobile browsers and devices detection function

//------------------------------------------------------------------------//
//---Hooks-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action( 'wp_enqueue_scripts', 'getdirections_init' );
add_action( 'widgets_init', create_function( '', 'register_widget( "getdirections" );' ) );
add_shortcode( 'get-directions', 'getdirections_render' );

//
if ( is_admin() ){ // admin actions
	add_action( 'admin_print_styles', 'getdirections_admin_styles' );  // add javascipt for admin 
	}
	

function getdirections_admin_styles() {
	wp_register_script( 'getdirectionadminScripts', plugins_url('js/admin-script.js', __FILE__) );
	wp_enqueue_script( 'getdirectionadminScripts',array('jquery') );
}  
//
// check to see if key is defined in WP Config otherwise add to general settings

$bizoptions=get_option('ll_bizprofile');    // find bizprofile settings
if (defined('MAPQUEST_LLOCALLY_BRANDING')) {
   $bizoptions['branding']=MAPQUEST_LLOCALLY_BRANDING;
   update_option('ll_bizprofile',$bizoptions);
   }
   
   
if (defined('GETDIRECTIONS_HIDEMOBILE_MAP')) {
   $bizoptions['hidemobile']=GETDIRECTIONS_HIDEMOBILE_MAP;
   update_option('hidemobile',$bizoptions);
   }
if (defined('MAPQUEST_API_KEY')) {
   $bizoptions['api_key']=MAPQUEST_API_KEY;
   update_option('ll_bizprofile',$bizoptions);
} else {
   $bizoptions['api_key']="Fmjtd%7Cluub2g6b2g%2Crw%3Do5-9ualqu";
   update_option('ll_bizprofile',$bizoptions);
}

// end of initial processes ----------------------------------------------

//     end of settings  -----------------------



// do all the get-directions shortcode processes ---------------------------------//
function getdirections_render( $atts, $content = null ){

extract(shortcode_atts(array(
    'postcode' => '',
	'country' => '',
	'latlong' => '',
	'hideroute' => '',
	'height' => '',
	'width' => '',
	'zoom' => '',
	'controls' => '',
	'rotitle'=>'',
	'rocontent'=>'',
	'radius'=>''
  ), $atts));

global $bizoptions;
// map will auto zoom to route but we still have zoom option incase route not shown
        $html="";
		
        $height = preg_replace("/\D/", "", $height);  // only numbers
		$width = preg_replace("/\D/", "", $width);
		$zoom = preg_replace("/\D/", "", $zoom);

		$postcode = preg_replace('/\s+/', '', $postcode); // strip whitespace
		$country = preg_replace('/\s+/', '', $country);
		$radius = preg_replace('/\s+/', '', $radius);
		$radiusint = preg_replace("/\D/", "", $radius);
		$radiusunits='MI';
		if (preg_match('/KM/i', $radius) ) $radiusunits='KM';
		
		
		$posarray = explode(',',$latlong);
     
		if (!empty($postcode)) {   // so we want to geo code end location
		    if (empty($country)) {
			   $html=_e('get-directions shortcode: Error: Country must be specified if post code is specified - see documentation!','llgetdirections');
			} else  {
			$url = "http://www.mapquestapi.com/geocoding/v1/address?key=" . $bizoptions['api_key'] . "&inFormat=kvp&outFormat=json&postalCode=" . $postcode . "&country=". $country ."&maxResults=1";
		    $georesponse = file_get_contents($url);

			$mapdata = json_decode($georesponse);

			if ($mapdata->info->statuscode!=0)  {
			   foreach ($mapdata->info->messages as $message)   {
			       _e('get-directions shortcode: Error: geoencoding:','llgetdirections');
			       echo $message . "<br>";
				   }
			   }
			if ($mapdata->results[0]->locations[0]->geocodeQuality=='COUNTRY') _e('get-directions shortcode: Error: Your postcode was not found, try latitude and longtitude for a more accurate position','llgetdirections');
			$posarray[0]=$mapdata->results[0]->locations[0]->latLng->lat;
			$posarray[1]=$mapdata->results[0]->locations[0]->latLng->lng;

			}
		}
	// if over riding end point not provided, check to see if there is some in bizprofile, otherwise error
	if (empty($rotitle)) $rotitle=ll_format_rotitle($bizoptions);
	if (empty($rocontent)) $rocontent=ll_format_rocontent($bizoptions);
	
	if ( (empty($postcode)) && empty($latlong) ) {
		if (empty($bizoption['lat']) && empty($bizoption['lng']) ) {
		   _e('get-directions shortcode: Error: No destination available, please refer to documentation!','llgetdirections');
		} else {
		    $posarray[0]=$bizoption['lat'];
			$posarray[1]=$bizoption['lng'];
		}
	}	
	if (empty($hideroute)) $showroute=(empty($bizoptions['option_filed_mapshowroute']))?'1':$bizoptions['option_filed_mapshowroute'];
	if (empty($height)) $height=(empty($bizoptions['option_filed_mapheight']))?300:$bizoptions['option_filed_mapheight'];
	if (empty($zoom)) $zoom=(empty($bizoptions['option_filed_mapzoom']))?10:$bizoptions['option_filed_mapzoom'];
	if (empty($controls)) $controls=(empty($bizoptions['option_filed_mapcontrols']))?'largezoom':$bizoptions['option_filed_mapcontrols'];
	
	
	return ll_render_directions ($posarray[0], $posarray[1], $showroute, $height, $width, $zoom, $controls, $rotitle, $rocontent, 'get-directions-map-sc', $radiusint, $radiusunits);

}

//------------------------ Render Route Map -----------------------------//
function ll_render_directions( $lat, $lng, $showroute, $height, $width, $zoom, $controls, $rotitle, $rocontent, $mapid, $radiusint=0, $radiusunits=""  ) {
	if (ll_is_mobile()) {
// force a pin only map	with 100% width
global $bizoptions;

	    if (empty($bizoptions['hidemobile'])) $html = ll_render_map( $lat, $lng, 0, $height, '', $zoom, $controls, $rotitle, $rocontent, $mapid , $radiusint, $radiusunits );
		$html .= '<form  class="gdform" action="http://maps.google.com/" method="get">';
		$html .= '<input type="hidden" name="daddr"value="'.$lat.','.$lng.'" />';
		$html .= '<button class="gdbutton" type="submit">Get Directions</button>';
		$html .= '</form>';

        } else {	
     //  ok do a route or pin map
	    $html = ll_render_map( $lat, $lng, $showroute, $height, $width, $zoom, $controls, $rotitle, $rocontent, $mapid , $radiusint, $radiusunits );
	 }
	 return $html;
}

function ll_render_map( $lat, $lng, $showroute, $height, $width, $zoom, $controls, $rotitle, $rocontent, $mapid, $radiusint, $radiusunits  ) 		{
global $bizoptions;
           if (empty($radiusint)) $radiusint=0;


		    $icon['path'] = plugins_url('images/map-pin.png', __FILE__);
		    $icon['width'] =18;
			$icon['height'] =26;
			$icon = apply_filters ('get_directions_map_pin', $icon );
			
		  	$html = '<script type="text/javascript">';
		  	
			$html .= 'var gdstartlat='.$lat.';';
			$html .= 'var gdstartlong='.$lng.';';
			$html .= "var gdshowroute='".$showroute."';";
			$html .= "var gdzoom=".$zoom.";";
			$html .= "var gdcontrols='".$controls."';";
			$html .= "var gdtitle='".addslashes_gpc($rotitle)."';";
			$html .= "var gdcontent='".addslashes_gpc($rocontent)."';";
			$html .= "var gdmapid='".$mapid."';"; 
			$html .= "var gdiconpath='".$icon['path']."';";
			$html .= "var gdiconwidth=".$icon['width'].";";
			$html .= "var gdiconheight=".$icon['height'].";";
			$html .= "var gdradiusunits='".$radiusunits."';";
			$html .= 'var gdradiusint='.$radiusint.';';
			
			
			$html .= "</script>";
			
		
		
		
			$html .= '<div ';
			if (!empty($width)) $html .= 'style="width:'.$width.'px;"';
			$html .= '><div class="gdmap "id="'.$mapid.'" style="';
            if (!empty($height)) $html .= 'height:'.$height.'px; ';
			if (!empty($width)) $html .= 'width:'.$width.'px; ';
			$html .= '"></div>';
			if (!$bizoptions['branding']) { 
		       $html .= '<div class="gdbranding" class="ll_branding">a <a href="http://llocally.com/wordpress-plugins" rel="no_follow" target="_blank">llocally</a> plugin</div>';
		     }
			$html .= '</div>';
			if ($showroute=='1') {  // check if route needs to be shown ok do a table
				$html .= '<div class="gdroute" id="'.$mapid.'-route" style="';
				if (!empty($width)) $html .= 'width:'.$width.'px; ';
				$html .= '"></div>';
	// put out a printer button for the route
				$html .= '<input type="image" value="Print" src="'.plugins_url( 'images/printer.png' , __FILE__ ).'" onclick="window.print()" />';
			}
		
		return $html;
	
}
function ll_format_rotitle($options) {  //format output for title when hovering over map pin

	  $html = (empty($options['option_field_bizname']))?"":'<span class="gdrotitle">'.$options['option_field_bizname']."</span>";
      return $html;
}

function ll_format_rocontent($options) {  //format output for title when click on map pin

	$html='';
	  
	  $html .= (empty($options['option_field_bizname']))?"":'<p class="gdwp">'.__('Company  :','getdirections'). $options['option_field_bizname']."</p>";
	  $html .= (empty($options['option_field_bizbuilding']))?"":'<p class="gdwp">'.__('Building :','getdirections'). $options['option_field_bizbuilding']."</p>";
	  $html .= (empty($options['option_field_bizstreet']))?"":'<p class="gdwp">'.__('Street   :','getdirections'). $options['option_field_bizstreet']."</p>";
	  $html .= (empty($options['option_field_bizstreet2']))?"":'<p class="gdwp">'.__('         :','getdirections'). $options['option_field_bizstreet2']."</p>";
	  $html .= (empty($options['option_field_bizcity']))?"":'<p class="gdwp">'.__('City     :','getdirections'). $options['option_field_bizcity']."</p>";
	  $html .= (empty($options['option_field_bizcounty']))?"":'<p class="gdwp">'.__('County   :','getdirections'). $options['option_field_bizcounty']."</p>";
	  $html .= (empty($options['option_field_bizphone']))?"":'<p class="gdwp">'.__('Phone    :','getdirections'). $options['option_field_bizphone']."</p>";



      return $html;

}


//------------------------------------------------------------------------//
//---Sidebar Widget Map-------------//
//------------------------------------------------------------------------//
function getdirections_init() {
global $bizoptions;



/* Register our stylesheet. */
        wp_register_style( 'GDwidgetStylesheet', plugins_url('css/style.css', __FILE__) );
	    wp_register_style( 'GDprintStylesheet', plugins_url('css/print.css', __FILE__),'','','print' );
/* Register our script. */
        wp_register_script( 'bizprofileMapQuestScript','http://www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key='.$bizoptions['api_key']);
        wp_register_script( 'GDwidgetScript', plugins_url('js/script.js', __FILE__) );
		wp_enqueue_style( 'GDwidgetStylesheet' );
		wp_enqueue_style( 'GDprintStylesheet' );
	   // enqueue our script here
	    wp_enqueue_script( 'bizprofileMapQuestScript' );
	    wp_enqueue_script( 'GDwidgetScript', array('bizprofileMapQuestScript','jquery')  );
}


/**
 * Adds getdirections widget.
 */
class getdirections extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'getdirections', // Base ID
			'getDirections', // Name
			array( 'description' => __( 'Get Directions displays a map with a route to your business. It knows when your site is being viewed on a mobile or desktop.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

    	extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$posarray=explode(',',$instance['latlng']);
		$showroute = $instance['showroute'];
		$height = $instance['height'];
		$zoom = $instance['zoom'];
		$controls = $instance['controls'];
		$rotitle = $instance['rotitle'];
		$rocontent = $instance['rocontent'];
		$height = $instance['height'];
		

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo ll_render_directions ($posarray[0], $posarray[1], $showroute, $height, '',$zoom, $controls, $rotitle, $rocontent, 'gd-'.$widget_id);
		
		echo $after_widget; 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
        $instance['latlng'] = preg_replace('~[[:cntrl:]]~', '', $new_instance['latlng']);  //needs validation numeric pair @todo
		$instance['showroute'] = $new_instance['showroute'];
		$instance['height'] = $new_instance['height'];
		// not width as 100% or as per css for widget
		$instance['zoom'] = $new_instance['zoom'];
		$instance['controls'] = $new_instance['controls'];
		$instance['rotitle'] =  preg_replace('~[[:cntrl:]]~', '', $new_instance['rotitle']); 
		$instance['rocontent'] =  preg_replace('~[[:cntrl:]]~', '', $new_instance['rocontent']);
		$instance['bizoptiondefault'] = strip_tags( $new_instance['bizoptiondefault'] );
		
		return $instance; 
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

        global $bizoptions;
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( '', 'text_domain' );
		}
		if ( isset( $instance[ 'latlng' ] ) ) {
			$latlng = $instance[ 'latlng' ];
		}
		else {
		   if (!empty($bizoptions['option_field_bizlat']) && !empty($bizoptions['option_field_bizlong'])) {
		       $latlng = $bizoptions['option_field_bizlat'].','.$bizoptions['option_field_bizlong'];
			} else {
			   $latlng ='0,0';
			}
		}
		if ( isset( $instance[ 'showroute' ] ) ) {
			$showroute = $instance[ 'showroute' ];
		}
		else {
			if (!empty($bizoptions['installed']))  {
			    $showroute = (!empty($bizoptions['option_field_mapshowroute']))?$bizoptions['option_field_mapshowroute']:'1';
			} 
		}
		
		if ( isset( $instance[ 'height' ] ) ) {
			$height = $instance[ 'height' ];
		}
		else {
			$height = (!empty($bizoptions['option_field_mapheight']))?$bizoptions['option_field_mapheight']:325;
		}
		if ( isset( $instance[ 'zoom' ] ) ) {
			$zoom = $instance[ 'zoom' ];
		}
		else {
			$zoom = (!empty($bizoptions['option_field_mapzoom']))?$bizoptions['option_field_mapzoom']:12;
		}
	//
		if ( isset( $instance[ 'controls' ] ) ) {
			$controls = $instance[ 'controls' ];
		}
		else {
			$controls = (!empty($bizoptions['option_field_mapcontrols']))?$bizoptions['option_field_mapcontrols']:'smallzoom';
		}

		if ( isset( $instance[ 'rotitle' ] ) ) {
			$rotitle = $instance[ 'rotitle' ];
		}
		else {
			$rotitle = addslashes_gpc(ll_format_rotitle($bizoptions));
		}
		if ( isset( $instance[ 'rocontent' ] ) ) {
			$rocontent = $instance[ 'rocontent' ];
		}
		else {
			$rocontent = addslashes_gpc(ll_format_rocontent($bizoptions));
		}

		if ( isset( $instance[ 'bizoptiondefault' ] ) ) {
			$bizoptiondefault = $instance[ 'bizoptiondefault' ];
		}
		else {
			$bizoptiondefault = (empty($bizoptions['installed']))?false:true;
		}
		
		// if biz profile turned on ask if you want to use profile defaults
		if (!empty($bizoptions['installed'])) {
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'bizoptiondefault' ); ?>"><?php _e( 'Use defaults from business profile?' ); ?></label> 
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'bizoptiondefault' ); ?>-true"><?php _e( 'Yes:' ); ?></label> 
		<input  type='radio' class='usedefault' id='usedefaulttrue'  name="<?php echo $this->get_field_name( 'bizoptiondefault' ); ?>" value='1' <?php checked( $bizoptiondefault, '1' ); ?> />
		<label for="<?php echo $this->get_field_id( 'bizoptiondefault' ); ?>-false"><?php _e( 'No:' ); ?></label> 
		<input  type='radio' class='usedefault' id='usedefaultfalse' name="<?php echo $this->get_field_name( 'bizoptiondefault' ); ?>" value='0' <?php checked( $bizoptiondefault, '0' ); ?> />
		</p>
		<?php
		
		}
		if (!empty($bizoptiondefault))  {
			$bizreadonly='readonly';
			$bizdisabled="disabled";
			$latlng = $bizoptions['option_field_bizlat'].','.$bizoptions['option_field_bizlong'];
			$showroute = (!empty($bizoptions['option_field_mapshowroute']))?$bizoptions['option_field_mapshowroute']:'1';
			$height = (!empty($bizoptions['option_field_mapheight']))?$bizoptions['option_field_mapheight']:325;
			$zoom = (!empty($bizoptions['option_field_mapzoom']))?$bizoptions['option_field_mapzoom']:12;
			$controls = (!empty($bizoptions['option_field_mapcontrols']))?$bizoptions['option_field_mapcontrols']:'smallzoom';
			$rotitle = addslashes_gpc(ll_format_rotitle($bizoptions));
			$rocontent = addslashes_gpc(ll_format_rocontent($bizoptions));
			}
			
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'latlng' ); ?>"><?php _e( 'Lat / Long: e.g. 51.23,1.12' ); ?></label> 
		<input class="defaultinput" <?php echo $bizreadonly; ?> size="8" id="<?php echo $this->get_field_id( 'latlng' ); ?>" name="<?php echo $this->get_field_name( 'latlng' ); ?>" type="text" value="<?php echo esc_attr( $latlng ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'showroute' ); ?>"><?php _e( 'Show directions:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizdisabled; ?> type="checkbox" id="<?php echo $this->get_field_id( 'showroute' ); ?>" name="<?php echo $this->get_field_name( 'showroute' ); ?>" type="text"  value="1"  <?php checked( $showroute, '1' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Map height in px:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizreadonly; ?> type="number" step="1" min="150" max="900" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'controls' ); ?>"><?php _e( 'Show zoom controls?' ); ?></label> 
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'controls' ); ?>"><?php _e( 'Small:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizdisabled; ?> type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" type="text" value="smallzoom" <?php checked( $controls, 'smallzoom' ); ?> />
		<label for="<?php echo $this->get_field_id( 'controls' ); ?>"><?php _e( 'Large:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizdisabled; ?> type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" type="text" value="largezoom" <?php checked( $controls, 'largezoom' ); ?> />
		<label for="<?php echo $this->get_field_id( 'controls' ); ?>"><?php _e( 'None:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizdisabled; ?> type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" type="text" value="" <?php checked( $controls, '' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _e( 'Initial map zoom level 1-16:' ); ?></label> 
		<input class="defaultinput" <?php echo $bizreadonly; ?> type="number" step="1" min="1" max="16" id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>" type="text" value="<?php echo esc_attr( $zoom ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'rotitle' ); ?>"><?php _e( 'Pin Hover Title' ); ?></label> 
		<input class="defaultinput" <?php echo $bizreadonly; ?> class="widefat" id="<?php echo $this->get_field_id( 'rotitle' ); ?>" name="<?php echo $this->get_field_name( 'rotitle' ); ?>" type="text" value="<?php echo esc_attr( $rotitle ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'roontent' ); ?>"><?php _e( 'Pin Click Content (html allowed)' ); ?></label> 
		<textarea class="defaultinput" <?php echo $bizreadonly; ?> class="widefat" id="<?php echo $this->get_field_id( 'rocontent' ); ?>" name="<?php echo $this->get_field_name( 'rocontent' ); ?>" ><?php echo esc_attr( $rocontent ); ?></textarea>
		</p>
		
		
		
		
	
		<?php 
	}
	
	

} // class getdirections

