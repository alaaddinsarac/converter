<?php
/*
Plugin Name: Euro to TL Live Converter
Plugin URI: 
Description: Live Euro to Turkish Lira converter using FXFeed API
Version: 1.0
Author: Your Name
Author URI: 
*/

if (!defined('ABSPATH')) {
    exit;
}

class EuroTLLiveConverter {
    private $api_key;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('euro_tl_converter', array($this, 'converter_shortcode'));
        add_action('wp_ajax_get_exchange_rate', array($this, 'get_exchange_rate'));
        add_action('wp_ajax_nopriv_get_exchange_rate', array($this, 'get_exchange_rate'));
        
        $this->api_key = get_option('fxfeed_api_key', 'demo');
    }

    public function add_admin_menu() {
        add_options_page(
            'Currency Converter Settings',
            'Currency Converter',
            'manage_options',
            'currency-converter',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('currency_converter_settings', 'fxfeed_api_key');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h2>Currency Converter Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('currency_converter_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">FXFeed API Key</th>
                        <td>
                            <input type="text" name="fxfeed_api_key" 
                                   value="<?php echo esc_attr(get_option('fxfeed_api_key', 'demo')); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'euro-tl-converter-style',
            plugins_url('css/style.css', __FILE__),
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'euro-tl-converter-script',
            plugins_url('js/script.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('euro-tl-converter-script', 'converter_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('convert_currency_nonce')
        ));
    }

    public function get_exchange_rate() {
        check_ajax_referer('convert_currency_nonce', 'nonce');

        $from = sanitize_text_field($_POST['from']);
        $to = sanitize_text_field($_POST['to']);
        $amount = floatval($_POST['amount']);

        $api_url = add_query_arg(array(
            'from' => $from,
            'to' => $to,
            'amount' => 1,
            'api_key' => $this->api_key
        ), 'https://api.fxfeed.io/v1/convert');

        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            wp_send_json_error('API request failed');
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (isset($data->result)) {
            wp_send_json_success(array(
                'rate' => $data->result,
                'converted' => $data->result * $amount
            ));
        } else {
            wp_send_json_error('Invalid API response');
        }
    }

    public function converter_shortcode() {
        ob_start();
        include(plugin_dir_path(__FILE__) . 'templates/converter.php');
        return ob_get_clean();
    }
}

new EuroTLLiveConverter();
?>
