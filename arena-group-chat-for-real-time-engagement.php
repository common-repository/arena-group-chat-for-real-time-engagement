<?php

/**
 * Plugin Name: Arena - Group Chat for Real-Time Engagement
 * Plugin URI: https://arena.im/group-chat
 * Description: Arena Group Chat for Real-Time Engagement
 * Version: 1.0.5
 * Author: Arena.im
 * Author URI: https://arena.im
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: arena-group-chat-for-real-time-engagement
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin version constant
define('AGCFRE_VERSION', '1.0.1');

define('AGCFRE_PLUGIN_MAIN_FILE', __FILE__);
define('AGCFRE_SELECTED_SITE_OPTION', 'agcfre_selected_site');
define('AGCFRE_DEFAULT_CHAT_OPTION', 'agcfre_default_chat');
define('AGCFRE_DISPLAY_OPTIONS', 'agcfre_display_options');
define('AGCFRE_POSITION_OPTION', 'agcfre_position');
define('AGCFRE_CHATS_OPTION', 'agcfre_chats');
define('AGCFRE_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/core/util/AgcfreIcon.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/AgcfreMenu.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/AgcfreMetabox.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/AgcfreShortcode.php';

use AGCFRE\GroupChat\Includes\Admin\AgcfreMenu;

/**
 * Main plugin class
 */
class Agcfre
{
    private $api_base_url = 'https://api.arena.im/v3';

    const AGCFRE_SELECTED_ORGANIZATION_OPTION = 'agcfre_selected_organization';
    const AGCFRE_ARENA_TOKEN_OPTION = 'agcfre_arena_token';
    const AGCFRE_VERIFICATION_TOKEN_OPTION = 'agcfre_verification_token';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->init_hooks();
        add_action('wp_ajax_fetch_arena_organizations', [$this, 'fetch_arena_organizations']);
        add_action('wp_ajax_save_selected_organization', [$this, 'save_selected_organization']);
        add_action('wp_ajax_get_selected_organization', [$this, 'get_selected_organization']);
        add_action('wp_ajax_fetch_arena_sites', [$this, 'fetch_arena_sites']);
        add_action('wp_ajax_fetch_arena_chats', [$this, 'fetch_arena_chats']);
        add_action('wp_ajax_save_arena_configuration', [$this, 'save_arena_configuration']);
        add_action('wp_ajax_get_arena_configuration', [$this, 'get_arena_configuration']);
    }

    /**
     * Initialize hooks
     */
    private function init_hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('plugin_action_links_' . AGCFRE_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
        add_action('admin_notices', [$this, 'show_admin_notices']);

        // Initialize the admin menu
        AgcfreMenu::init();

        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function show_admin_notices()
    {
        global $hook_suffix;
        if ($hook_suffix === 'plugins.php' && !get_option(self::AGCFRE_ARENA_TOKEN_OPTION)) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<div style="padding: 24px;">
    <div>
        <div>
            <div><svg width="100" height="34" viewBox="0 0 75 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.1025 19.737V10.9051C17.1025 8.65465 17.7215 6.96029 18.9685 5.81288C20.1982 4.69197 22.2123 4.11852 25.0026 4.10986V7.12427C25.0026 7.66971 24.5918 8.12296 24.0507 8.18968C23.2679 8.28644 22.6714 8.50339 22.2647 8.83391C21.689 9.30449 21.4015 9.99761 21.4015 10.9046V18.6456C21.4015 19.2476 20.9138 19.7365 20.3112 19.7365L17.1025 19.737Z" fill="#9124FF"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M39.867 7.48137C38.2021 5.05059 35.4276 3.8268 32.6832 4.03255C31.3191 4.13491 29.9733 4.58766 28.7665 5.41523C25.1308 7.91731 24.2009 12.8924 26.7015 16.5292C29.2027 20.1664 34.175 21.0969 37.8107 18.5943C39.2537 17.6027 40.2651 16.2323 40.8107 14.6988L38.3406 13.497C37.7949 13.2316 37.1495 13.4547 36.8558 13.9859C36.5596 14.5221 36.1382 14.9998 35.5981 15.3726C34.318 16.2491 32.7101 16.301 31.4183 15.643L40.7771 9.21291C40.5516 8.61349 40.2513 8.03546 39.867 7.48137ZM29.223 12.43C29.0719 10.9837 29.6282 9.54445 30.978 8.62826C32.2617 7.75689 33.8798 7.73397 35.1721 8.39196L29.223 12.43Z" fill="#9124FF"/>
                <path d="M57.9525 18.6423V11.9188C57.9525 7.56244 54.4354 3.98578 50.0815 4.00004C45.7617 4.0143 42.2756 7.52322 42.2756 11.849V19.7291L45.5749 19.7367C46.177 19.7382 46.6657 19.2503 46.6657 18.6479V11.8979C46.6657 9.92395 48.2298 8.40834 50.0815 8.40834C52.0319 8.40834 53.5508 10.0019 53.5508 11.849V19.7291H56.8668C57.4664 19.7291 57.9525 19.2422 57.9525 18.6423Z" fill="#9124FF"/>
                <path d="M71.8676 4.11377C71.3378 4.11377 70.8975 4.49471 70.8033 4.99737C69.7248 4.4341 68.4996 4.11377 67.1992 4.11377C62.8911 4.11377 59.3989 7.60843 59.3989 11.9184C59.3989 16.229 62.8916 19.7231 67.1992 19.7231C68.5002 19.7231 69.7258 19.4028 70.8038 18.839C70.9011 19.3386 71.3398 19.716 71.8676 19.716C71.8722 19.716 71.8768 19.7155 71.8814 19.7155H74.9999V4.11377H71.8676ZM67.1992 15.9865C64.9535 15.9865 63.1339 14.1654 63.1339 11.9184C63.1339 9.67151 64.954 7.85084 67.1992 7.85084C69.4448 7.85084 71.2645 9.67202 71.2645 11.9184C71.265 14.1654 69.4448 15.9865 67.1992 15.9865Z" fill="#9124FF"/>
                <path d="M12.4468 4.13281C11.9271 4.13281 11.4782 4.49032 11.3581 4.99094C10.2495 4.42157 9.05339 4.13281 7.8028 4.13281C3.50032 4.13281 0 7.63511 0 11.94C0 16.2449 3.50032 19.7472 7.8028 19.7472C9.05389 19.7472 10.2505 19.4585 11.3591 18.8886C11.4818 19.3851 11.9302 19.7406 12.4468 19.7406L15.6051 19.7396V4.13281H12.4468ZM12.2972 16.1604C12.2203 16.2938 12.0569 16.3473 11.9185 16.2903L11.9078 16.2857C11.1621 16.003 10.3645 15.8543 9.52013 15.8543C9.1842 15.8543 8.86862 15.8767 8.54592 15.9241C8.54592 15.9241 8.0517 15.9918 7.80331 15.9918C5.56987 15.9918 3.75888 14.1803 3.75888 11.9451C3.75888 9.7099 5.56936 7.89841 7.80331 7.89841C10.0373 7.89841 11.8477 9.7099 11.8477 11.9451C11.8477 12.1442 11.8335 12.3398 11.8055 12.5313H11.806C11.7699 12.7976 11.7515 13.0696 11.7515 13.3461C11.7515 14.2511 11.9516 15.1103 12.3104 15.8798C12.3572 15.9771 12.3486 16.0708 12.2972 16.1604Z" fill="#9124FF"/>
                </svg>
</div>
            <h3 style="margin: 10px 0 20px; font-size: 22px; font-weight: 400;">Congratulations, the Arena Group Chat plugin is
                now activated</h3><a
                href="' . admin_url('admin.php?page=agcfre_group_chat_settings') . '"
                style="background: none; border: none; text-decoration: none; background-color: #9124ff; color: white; margin-right: 8px; padding: 12px 16px; border-radius: 100px; cursor: pointer; display: inline-block;"
                 target="_self"
                id="arena-start-setup-link"><span>Start setup</span></a>
        </div>
    </div>
</div>';
            echo '</div>';
        }
    }

    /**
     * Add plugin action links
     */
    public function plugin_action_links($links)
    {
        $links[] = '<a href="' . admin_url('admin.php?page=agcfre_group_chat_settings') . '">Settings</a>';
        return $links;
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook)
    {
        if ('arena_page_agcfre_group_chat_settings' !== $hook) {
            return;
        }
        wp_enqueue_script(
            'agcfre-admin',
            plugins_url('build/agcfre_admin.tsx.js', __FILE__),
            ['wp-element', 'wp-components', 'wp-api-fetch'],
            filemtime(plugin_dir_path(__FILE__) . 'build/agcfre_admin.tsx.js'),
            true
        );
        wp_localize_script('agcfre-admin', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_localize_script('agcfre-admin', 'agcfre_data', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('agcfre_ajax_nonce')
        ));

        wp_enqueue_style('agcfre-admin', plugins_url('build/agcfre_admin.tsx.css', __FILE__), array(), '1.0.1');
    }

    /**
     * Register settings
     */
    public function register_settings()
    {
        register_setting(
            'agcfre_live_chat_settings',
            'agcfre_live_chat_settings',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize_agcfre_live_chat_settings'],
            ]
        );
    }

    /**
     * Sanitize arena live chat settings
     */
    public function sanitize_agcfre_live_chat_settings($input)
    {
        $sanitized_input = [];

        if (is_array($input)) {
            foreach ($input as $key => $value) {
                switch ($key) {
                    case 'organization':
                    case 'position':
                        $sanitized_input[$key] = sanitize_text_field($value);
                        break;
                    case 'site':
                    case 'defaultChat':
                    case 'displayOptions':
                        $sanitized_input[$key] = $this->sanitize_json_object($value);
                        break;
                    // Add more cases as needed for other settings
                    default:
                        $sanitized_input[$key] = sanitize_text_field($value);
                }
            }
        }

        return $sanitized_input;
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes()
    {
        register_rest_route('arena-group-chat-for-real-time-engagement/v1', '/activate', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_activate_route']
        ]);

        register_rest_route('arena-group-chat-for-real-time-engagement/v1', '/generate-token', [
            'methods' => 'GET',
            'callback' => [$this, 'generate_verification_token_route'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('arena-group-chat-for-real-time-engagement/v1', '/connection-status', [
            'methods' => 'GET',
            'callback' => [$this, 'get_connection_status'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('arena-group-chat-for-real-time-engagement/v1', '/disconnect', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_disconnect_route'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);
    }

    /**
     * Handle the activate route
     */
    public function handle_activate_route($request)
    {
        $params = $request->get_params();
        $params = $this->sanitize_json_object($params);

        // Check if all required parameters are present
        if (
            !isset($params['verificationToken']) || !isset($params['arenaToken']) ||
            !isset($params['arenaToken']['clientId']) || !isset($params['arenaToken']['clientSecret'])
        ) {
            return new \WP_Error('missing_parameter', 'Missing required parameters', array('status' => 400));
        }

        $stored_verification_token = get_option(self::AGCFRE_VERIFICATION_TOKEN_OPTION);

        // Check if the verification token matches
        if ($params['verificationToken'] !== $stored_verification_token) {
            return new \WP_Error('invalid_token', 'Invalid verification token', array('status' => 401));
        }

        // Encrypt the Arena token before storing
        $arena_token = array(
            'clientId' => $this->encrypt_data($params['arenaToken']['clientId']),
            'clientSecret' => $this->encrypt_data($params['arenaToken']['clientSecret'])
        );

        update_option(self::AGCFRE_ARENA_TOKEN_OPTION, $arena_token);

        return new \WP_REST_Response(['success' => true, 'message' => 'Activation successful'], 200);
    }

    /**
     * Generate and store a verification token
     */
    public function generate_verification_token()
    {
        $verification_token = wp_generate_password(32, false);
        update_option(self::AGCFRE_VERIFICATION_TOKEN_OPTION, $verification_token);
        return $verification_token;
    }

    public function generate_verification_token_route()
    {
        $token = $this->generate_verification_token();
        return new \WP_REST_Response(['token' => $token], 200);
    }

    private function encrypt_data($data)
    {
        $key = $this->get_encryption_key();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    private function decrypt_data($encrypted_data)
    {
        $key = $this->get_encryption_key();
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }

    private function get_encryption_key()
    {
        if (defined('AGCFRE_LIVE_CHAT_ENCRYPTION_KEY') && AGCFRE_LIVE_CHAT_ENCRYPTION_KEY) {
            return AGCFRE_LIVE_CHAT_ENCRYPTION_KEY;
        }
        // the key is the verification token
        $key = get_option(self::AGCFRE_VERIFICATION_TOKEN_OPTION);
        return $key;
    }

    public function get_connection_status()
    {
        $arena_token = get_option(self::AGCFRE_ARENA_TOKEN_OPTION);
        $is_connected = !empty($arena_token);
        return new \WP_REST_Response(['isConnected' => $is_connected], 200);
    }

    public function handle_disconnect_route()
    {
        delete_option(self::AGCFRE_VERIFICATION_TOKEN_OPTION);
        delete_option(self::AGCFRE_ARENA_TOKEN_OPTION);
        delete_option(AGCFRE_CHATS_OPTION);
        delete_option(AGCFRE_SELECTED_SITE_OPTION);
        delete_option(AGCFRE_DEFAULT_CHAT_OPTION);
        delete_option(AGCFRE_DISPLAY_OPTIONS);
        delete_option(AGCFRE_POSITION_OPTION);
        return new \WP_REST_Response(['success' => true, 'message' => 'Disconnected successfully'], 200);
    }

    public function fetch_arena_organizations()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        // Get encrypted client ID and secret from WordPress options
        $arena_token = get_option(self::AGCFRE_ARENA_TOKEN_OPTION);

        if (!$arena_token || !isset($arena_token['clientId']) || !isset($arena_token['clientSecret'])) {
            wp_send_json_error('Client ID or Client Secret not set');
        }

        // Decrypt the client ID and secret
        $client_id = $this->decrypt_data($arena_token['clientId']);
        $client_secret = $this->decrypt_data($arena_token['clientSecret']);

        $token = $this->get_auth_token($client_id, $client_secret);
        if (!$token) {
            wp_send_json_error('Failed to obtain authentication token');
        }

        $organizations = $this->get_organizations($token);
        if (!$organizations) {
            wp_send_json_error('Failed to fetch organizations');
        }

        $selected_organization = get_option(self::AGCFRE_SELECTED_ORGANIZATION_OPTION, '');

        wp_send_json_success([
            'organizations' => $organizations,
            'selectedOrganization' => $selected_organization
        ]);
    }

    public function get_arena_configuration()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        $organization = get_option(self::AGCFRE_SELECTED_ORGANIZATION_OPTION, '');
        $site = get_option(AGCFRE_SELECTED_SITE_OPTION, new stdClass());
        $default_chat = get_option(AGCFRE_DEFAULT_CHAT_OPTION, new stdClass());
        $display_options = get_option(AGCFRE_DISPLAY_OPTIONS, new stdClass());
        $position = get_option(AGCFRE_POSITION_OPTION, 'bottom');

        wp_send_json_success([
            'organization' => $organization,
            'site' => $site,
            'defaultChat' => $default_chat,
            'displayOptions' => $display_options,
            'position' => $position
        ]);
    }

    public function save_arena_configuration()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $organization = isset($_POST['organization']) ? sanitize_text_field(wp_unslash($_POST['organization'])) : '';
        $site = isset($_POST['site']) ? $this->sanitize_json_object(json_decode(sanitize_text_field(wp_unslash($_POST['site'])), true)) : new stdClass();
        $default_chat = isset($_POST['default_chat']) ? $this->sanitize_json_object(json_decode(sanitize_text_field(wp_unslash($_POST['default_chat'])), true)) : new stdClass();
        $display_options = isset($_POST['display_options']) ? $this->sanitize_json_object(json_decode(sanitize_text_field(wp_unslash($_POST['display_options'])), true)) : new stdClass();
        $position = isset($_POST['position']) ? sanitize_text_field(wp_unslash($_POST['position'])) : '';

        update_option(self::AGCFRE_SELECTED_ORGANIZATION_OPTION, $organization);
        update_option(AGCFRE_SELECTED_SITE_OPTION, $site);
        update_option(AGCFRE_DEFAULT_CHAT_OPTION, $default_chat);
        update_option(AGCFRE_DISPLAY_OPTIONS, $display_options);
        update_option(AGCFRE_POSITION_OPTION, $position);

        wp_send_json_success('Configuration saved successfully');
    }

    public function fetch_arena_sites()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $organization_id = isset($_POST['organization_id']) ? sanitize_text_field(wp_unslash($_POST['organization_id'])) : '';

        $arena_token = get_option(self::AGCFRE_ARENA_TOKEN_OPTION);
        if (!$arena_token || !isset($arena_token['clientId']) || !isset($arena_token['clientSecret'])) {
            wp_send_json_error('Client ID or Client Secret not set');
        }

        // Decrypt the client ID and secret
        $client_id = $this->decrypt_data($arena_token['clientId']);
        $client_secret = $this->decrypt_data($arena_token['clientSecret']);

        $token = $this->get_auth_token($client_id, $client_secret);
        if (!$token) {
            wp_send_json_error('Failed to obtain authentication token');
        }

        if (empty($organization_id)) {
            wp_send_json_error('No organization selected');
        }

        $sites = $this->get_sites($token, $organization_id);
        if (!$sites) {
            wp_send_json_error('Failed to fetch sites');
        }

        wp_send_json_success(['sites' => $sites]);
    }

    public function fetch_arena_chats()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $site_id = isset($_POST['site_id']) ? sanitize_text_field(wp_unslash($_POST['site_id'])) : '';

        if (empty($site_id)) {
            wp_send_json_error('No site selected');
        }

        $arena_token = get_option(self::AGCFRE_ARENA_TOKEN_OPTION);
        if (!$arena_token || !isset($arena_token['clientId']) || !isset($arena_token['clientSecret'])) {
            wp_send_json_error('Client ID or Client Secret not set');
        }

        // Decrypt the client ID and secret
        $client_id = $this->decrypt_data($arena_token['clientId']);
        $client_secret = $this->decrypt_data($arena_token['clientSecret']);

        $token = $this->get_auth_token($client_id, $client_secret);
        if (!$token) {
            wp_send_json_error('Failed to obtain authentication token');
        }

        $chats = $this->get_chats($token, $site_id);
        if (!$chats) {
            wp_send_json_error('Failed to fetch chats');
        }

        // Save the chats to the database
        update_option(AGCFRE_CHATS_OPTION, $chats);

        wp_send_json_success(['chats' => $chats]);
    }

    private function get_auth_token($client_id, $client_secret)
    {
        $url = $this->api_base_url . '/oauth/tokens';
        $data = array(
            'clientId' => $client_id,
            'clientSecret' => $client_secret
        );

        $response = wp_remote_post($url, array(
            'body' => wp_json_encode($data),
            'headers' => array('Content-Type' => 'application/json'),
        ));

        if (is_wp_error($response)) {
            error_log('Failed to get auth token: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    private function get_organizations($token)
    {
        $url = $this->api_base_url . '/organizations';
        $response = wp_remote_get($url, array(
            'headers' => array('Authorization' => "Bearer $token"),
        ));

        if (is_wp_error($response)) {
            error_log('Failed to fetch organizations: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    private function get_sites($token, $organization_id)
    {
        $url = $this->api_base_url . '/organizations/' . $organization_id . '/sites';
        $response = wp_remote_get($url, array(
            'headers' => array('Authorization' => "Bearer $token"),
        ));

        if (is_wp_error($response)) {
            error_log('Failed to fetch sites: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    private function get_chats($token, $site_id)
    {
        $url = $this->api_base_url . '/sites/' . $site_id . '/chats';
        $response = wp_remote_get($url, array(
            'headers' => array('Authorization' => "Bearer $token"),
        ));

        if (is_wp_error($response)) {
            error_log('Failed to fetch chats: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    public function save_selected_organization()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $organization_id = isset($_POST['organization_id']) ? sanitize_text_field(wp_unslash($_POST['organization_id'])) : '';

        if (empty($organization_id)) {
            wp_send_json_error('Invalid organization ID');
        }

        // Save the selected organization ID
        $result = update_option(self::AGCFRE_SELECTED_ORGANIZATION_OPTION, $organization_id);

        if ($result) {
            wp_send_json_success('Organization saved successfully');
        } else {
            wp_send_json_error('Failed to save organization');
        }
    }

    public function get_selected_organization()
    {
        if (!check_ajax_referer('agcfre_ajax_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $organization_id = get_option(self::AGCFRE_SELECTED_ORGANIZATION_OPTION);
        if (!$organization_id) {
            wp_send_json_error('Organization not selected');
        } else {
            wp_send_json_success(['organization_id' => $organization_id]);
        }
    }

    private function sanitize_json_object($obj)
    {
        if (is_object($obj)) {
            foreach ($obj as $key => $value) {
                $obj->$key = is_string($value) ? sanitize_text_field($value) : $this->sanitize_json_object($value);
            }
        } elseif (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $obj[$key] = is_string($value) ? sanitize_text_field($value) : $this->sanitize_json_object($value);
            }
        }
        return $obj;
    }
}

// Initialize the plugin
global $agcfre;
$agcfre = new Agcfre();