<?php
/*
Plugin Name: Qwardo Insights
Plugin URI: http://qwardo.com/
Description: Qwardo Insights captures valuable insights from anonymous visitors, uncovers their Interests and identifies high quality prospects by adding Qwardo tracking code to a Wordpress website.
Version: 1.0
Author: Qwardo
Author URI: http://qwardo.com/
*/

if( is_admin() )
    $alertme_page = new Qwardo();
   

register_deactivation_hook( __FILE__, 'remove_qwardo_smartbar_script' );
register_uninstall_hook( __FILE__, 'remove_qwardo_script_options' );
add_action('wp_enqueue_scripts', 'add_qwardo_script_code');
/*This funciton will add the script to <head> tag of all the pages of the 
wordpress site*/
function add_qwardo_script_code()
{ 
	$plugin_options = get_option('qwardo_options');
	$qwardoIdNumber=$plugin_options['qwardo_id_number'];
	if(!empty(	$qwardoIdNumber))
	{
		wp_enqueue_script( 'qwardo_script_file', 'https://dyr0l27y3r6fr.cloudfront.net/service/js/trackingscript.js',array(),false,true);
		wp_add_inline_script( 'qwardo_script_file', 'var qwardoWebSiteId="'.$qwardoIdNumber.'"','before' );
	}
}
 function caption_shortcode( $atts, $content = null ) {
	 
	if(!empty($atts) && strlen(trim($atts['code'])) > 0)
	{
		return '<div qwardo-cta="'.$atts['code'].'" >' . $content . '</div>';
	}
		return '<div qwardo-cta>' . $content . '</div>';
 }
add_shortcode( 'qwardo-cta', 'caption_shortcode' );
function remove_qwardo_script_options()
{
	delete_option('qwardo_options');
}

function remove_qwardo_smartbar_script()  
    { 
		
       remove_action('wp_footer','add_qwardo_script_code');
		update_option('qwardo_plugin_notice', 'false');
	}

class Qwardo
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
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action('admin_notices', array($this,'qwardo_plugin_admin_notices'));
		add_action( 'admin_print_styles', array($this,'add_qwardo_plugin_styles'));
		add_action( 'admin_print_scripts', array($this,'add_qwardo_plugin_scripts'));
    }

	public function add_qwardo_plugin_scripts()
	{
		wp_enqueue_script( 'qwardo_insights_js', plugins_url('/js/qwardoInsight.js',__FILE__));
	}
	public function add_qwardo_plugin_styles()
	{
		wp_enqueue_style( 'qwardo_insights_css',plugins_url('/css/qwardoInsights.css',__FILE__));
		
	}
	public function qwardo_plugin_admin_notices() {
		
		$isMessageShown = get_option('qwardo_plugin_notice');
		if ((!$isMessageShown || $isMessageShown == 'false') && !is_plugin_active('plugin-directory/plugin-file.php')) {
			echo "<div class='updated'><p>Your Qwardo plugin is successfully installed. Please go to Settings for the <b>Qwardo</b> plugin to add your Site Id to run Qwardo on your website.</p></div>";
			update_option('qwardo_plugin_notice', 'true');
	}
}
	
    /**
     * Add options page
     */
	 
    public function add_plugin_page()
    {
      
		 add_menu_page( 'Qwardo Setting Page', 'Qwardo Insights', 'manage_options', 'qwardo-plugin', array( $this, 'create_alertme_admin_page'),plugins_url( '/img/fav16.png', __FILE__ ));
    }

    /**
     * Options page callback
     */
    public function create_alertme_admin_page()
    {
        // Set class property
        $this->options = get_option( 'qwardo_options' );
        ?>
		
		 <div class="outer">
		 <div class="middle">
        <div class="wrap" style="background-color:white;margin: 200px auto;border:1px solid lightgray;max-width: 500px;">
            <?php screen_icon(); ?>
            <h2 class="qwardosection-header"><a href="http://qwardo.com/" target="_blank" style="vertical-align: middle;display: inline-block;margin-right: 20px;"><img width="40%" src="<?php echo esc_url( plugins_url( '/img/qwardoLogo.png', __FILE__ ) ); ?>"/></a></h2>           
            <form method="post" id="trackingidform" class="qwardosection" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'alertme_option_group' );   
                do_settings_sections( 'alertme-settings-admin' );
				?>
				<div id="site-id-not-valid" class="qwardo-siteid-info site-id-error site-id-not-valid">Please enter valid site ID</div>
				<div id="site-id-null" class="qwardo-siteid-info site-id-error site-id-null">Please enter Qwardo site ID</div>
				 <?php
						if ($_GET['settings-updated'])
 {
    echo "<div id='site-id-added' class='qwardo-siteid-info site-id-successfully-added site-id-added'>Thank You! Qwardo script is successfully added to your website.</div>";
}
                submit_button(); 
			?>
		
            </form>
        </div>
		</div>
		</div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'alertme_option_group', // Option group
            'qwardo_options', // Option name
            array( $this, 'use_field_value' ) // use_field_value
        );

        add_settings_section(
            'setting_section_id', // ID
            'Tracking Setting', // Title
            array( $this, 'print_section_info' ), // Callback
            'alertme-settings-admin' // Page
        );

        add_settings_field(
            'qwardo_id_number', // ID
            'Qwardo Site ID :', // Title 
            array( $this, 'id_number_callback' ), // Callback
            'alertme-settings-admin', // Page
            'setting_section_id' // Section           
        );      
         
         
    }

    /**
     * Use each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function use_field_value( $input )
    {
        $new_input = array();
        if( isset( $input['qwardo_id_number'] ) )
        {   
            if (!empty($input['qwardo_id_number'])) 
            {
                $new_input['qwardo_id_number'] =  $input['qwardo_id_number'] ;
                return $new_input;
            } 
            else 
            {
                $new_input['qwardo_id_number']="";
                return $new_input;
            }
            
        }
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {

		echo 'Go to <a href="https://app.qwardo.com/register?utm_source=wordpress"><b>app.qwardo.com</b></a> and register your website. Then go to <a href="https://app.qwardo.com/#/app/smartbarsetting"><b>Settings</b></a> on your dashboard, copy Qwardo Site ID and add it here. 
		';

    }

    /** 
     * Get the settings option array and print one of its values
     */
   
	 public function id_number_callback()    
    {
        printf(
            '<input type="text" id="qwardo_id_number" name="qwardo_options[qwardo_id_number]" value="%s" />',
            isset( $this->options['qwardo_id_number'] ) ? esc_attr( $this->options['qwardo_id_number']) : ''
        );
    } 
}