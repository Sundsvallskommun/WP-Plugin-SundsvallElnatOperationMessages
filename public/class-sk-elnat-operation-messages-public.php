<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Elnat_Operation_Messages
 * @subpackage Sk_Elnat_Operation_Messages/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sk_Elnat_Operation_Messages
 * @subpackage Sk_Elnat_Operation_Messages/public
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Elnat_Operation_Messages_Public {

	private $hash = 'a95c530a7af5f492a74499e70578d150';
	private $html_file = 'http://sundsvallelnat.se/default.aspx?id=1055';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;


	/**
	 * Register short code.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function add_shortcode(){
		add_shortcode( 'elnat-operation-messages', array( $this, 'output' ) );
	}

	/**
	 * Adding custom action for cron schedule to be fired every minute.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return mixed
	 */
	public function add_cron_interval() {
		// add an event hook every minute
		$schedules['minute'] = array(
			'interval' => 60,
			'display' => __('Once a minute')
		);
		return $schedules;
	}


	/**
	 * Render the html for short code.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function output( $atts, $content ) {
		wp_enqueue_style( $this->plugin_name );
		ob_start();

		$messages = get_transient( 'elnat_operation_messages' );

		if( !empty($messages)) {
			require_once plugin_dir_path( __FILE__ ) . '/partials/sk-elnat-operation-messages-public-display.php';
		}

		return ob_get_clean();
	}

	/**
	 * Manual trigger for import by querystring.
	 * example.com/?import_operation_messages={$hash-variable}
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function import_manual(){

		if ( isset( $_GET['import_operation_messages'] ) && $_GET['import_operation_messages'] === $this->hash ) {
			$this->import_messages();
		}
	}


	/**
	 * Method to handle the import trigger.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function import_messages() {
		$messages = $this->parse_html();
		$this->save_messages( $messages );

		//sk_log('Unable to get json', $url);
	}

	/**
	 * Parse the html from a given webpage.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return array
	 */
	public function parse_html() {

		$doc = new DOMDocument();
		$request = $doc->loadHTMLFile( $this->html_file );

		if(! $request ){
			sk_log('Requested file for operation messages does not exists', $this->html_file );
			return false;
		}

		$temp = $doc->getElementById( 'tab3' );

		$test = $temp->getElementsByTagName( 'div' );

		$messages = array();

		foreach ( $test as $t ) {

			foreach ( $t->childNodes as $d ) {

				$i = 0;
				$key = 0;
				foreach ( $d->childNodes as $c ) {

					if ( $c->tagName === 'div' ) {
						$i++;
						switch ( $i ){
							case 1:
								$messages[$key]['desc'] = $c->nodeValue;
								break;

							case 2:
								$messages[$key]['start'] = $c->nodeValue;
								break;

							case 3:
								$messages[$key]['end'] = $c->nodeValue;
								$ended = explode( ': ', $messages[$key]['end'] );
								$messages[ $key ]['icon'] = 'flash on';
								if( empty( $ended[1] )) {
									$messages[ $key ]['icon'] = 'flash off';
									unset($messages[$key]['end']);
								}

								break;

						}



					}

					if ( $c->nodeName === 'hr' ) {
						$key++;
					}


					if ( $i === 3 ) {
						$i = 0;
					}


				}

			}

		}

		return $messages;

	}


	/**
	 * Save messages to transient.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param array $messages
	 *
	 * @return bool
	 */
	public function save_messages( $messages = array() ) {
		$messages = false;
		if(empty( $messages )){
			sk_log('Empty result when parsing html for operation messages', $messages );
			return false;
		}

		set_transient('elnat_operation_messages', $messages, false );

	}


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sk_Elnat_Operation_Messages_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Elnat_Operation_Messages_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sk-elnat-operation-messages-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sk_Elnat_Operation_Messages_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Elnat_Operation_Messages_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sk-elnat-operation-messages-public.js', array( 'jquery' ), $this->version, false );

	}

}
