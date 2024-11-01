<?php
/**
 * Class AGCFRE\GroupChat\Includes\Admin\AgcfreShortcode
 *
 * @package   AGCFRE\GroupChat
 * @copyright 2024 Arena
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://arena.im
 */

namespace AGCFRE\GroupChat\Includes\Admin;

/**
 * Class for the Arena Group Chat Shortcode
 *
 * @since 1.28.0
 * @access private
 * @ignore
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('AgcfreShortcode')):

    class AgcfreShortcode
    {
        //  Register shortcode
        public function shortcodes_init()
        {
            error_log('shortcodes_init called');
            error_log('function_exists add_shortcode: ' . (function_exists('add_shortcode') ? 'true' : 'false'));
            error_log('function_exists add_filter: ' . (function_exists('add_filter') ? 'true' : 'false'));
            if (function_exists('add_shortcode') && function_exists('add_filter')) {
                add_shortcode('agcfre-group-chat', [$this, 'shortcode']);
                
                add_filter('the_content', [$this, 'insert_shortcode']);
                add_filter('the_excerpt', [$this, 'insert_shortcode']);
                
                add_action('wp_footer', [$this, 'maybe_append_to_footer']);
            }
        }

        public function insert_shortcode($content)
        {
            $shortcode = '[agcfre-group-chat]';
            $page_id = get_the_ID();

            error_log('=== Starting insert_shortcode ===');
            error_log('Page ID: ' . $page_id);

            // get site level options
            $arena_live_chat_display_options = get_option(AGCFRE_DISPLAY_OPTIONS, array());
            $arena_live_chat_selected_site = get_option(AGCFRE_SELECTED_SITE_OPTION);
            error_log('Display options: ' . json_encode($arena_live_chat_display_options));
            error_log('Selected site: ' . json_encode($arena_live_chat_selected_site));

            if (!is_array($arena_live_chat_selected_site) || !isset($arena_live_chat_selected_site['slug'])) {
                error_log('Returning early: Selected site is not an array or missing slug');
                return $content;
            }

            $display_options = $this->get_display_options($arena_live_chat_display_options);
            error_log('Processed display options: ' . json_encode($display_options));

            $agcfre_live_chat_pagelevel = get_post_meta($page_id, 'agcfre_live_chat_pagelevel', true);
            error_log('Page level options: ' . json_encode($agcfre_live_chat_pagelevel));

            $show_hide = (isset($agcfre_live_chat_pagelevel['show_hide']) && !empty($agcfre_live_chat_pagelevel['show_hide'])) 
                ? sanitize_text_field($agcfre_live_chat_pagelevel['show_hide']) 
                : '';
            error_log('Show/Hide setting: ' . $show_hide);

            $insert = $this->should_insert_shortcode($show_hide, $display_options);
            error_log('Should insert shortcode: ' . ($insert ? 'true' : 'false'));

            if ($insert) {
                error_log('Adding shortcode to content');
                $content .= wp_kses_post(do_shortcode($shortcode));
            }

            error_log('=== Ending insert_shortcode ===');
            return $content;
        }

        private function get_display_options($arena_live_chat_display_options)
        {
            $display_options = [
                'global' => '',
                'home' => 'global',
                'posts' => 'global',
                'pages' => 'global',
                'archive' => 'global',
                'category' => 'global',
                'notFound' => 'global'
            ];

            foreach ($display_options as $key => $default) {
                if (isset($arena_live_chat_display_options[$key])) {
                    $display_options[$key] = $arena_live_chat_display_options[$key];
                }
            }

            return $display_options;
        }

        // Public method for testing purposes
        public function test_should_insert_shortcode($show_hide, $display_options)
        {
            return $this->should_insert_shortcode($show_hide, $display_options);
        }

        protected function should_insert_shortcode($show_hide, $display_options)
        {
            error_log('should_insert_shortcode called with show_hide: ' . $show_hide . ' and display_options: ' . json_encode($display_options));
            if ($show_hide === 'hide') {
                return false;
            }

            if ($show_hide === 'show') {
                return true;
            }

            $page_conditions = [
                'single' => 'posts',
                'page' => 'pages',
                'home' => 'home',
                'archive' => 'archive',
                'category' => 'category',
                '404' => 'notFound'
            ];

            foreach ($page_conditions as $condition => $option) {
                $is_condition = "is_$condition";
                if (function_exists($is_condition) && $is_condition()) {
                    if (isset($display_options[$option])) {
                        if ($display_options[$option] === 'show') {
                            return true;
                        } elseif ($display_options[$option] === 'global') {
                            break;  // Check global setting
                        } else {
                            error_log('Returning false: Display option is not show or global');
                            return false;
                        }
                    }
                }
            }

            // Check global setting
            if (isset($display_options['global']) && $display_options['global'] === 'show') {
                return true;
            }

            error_log('Returning false: No conditions met');
            return false;
        }

        // call back function - shortcode 
        public function shortcode($atts = [], $content = null, $shortcode = '')
        {
            $page_id = get_the_ID() ?: 0;

            $arena_live_chat_selected_site = get_option(AGCFRE_SELECTED_SITE_OPTION);
            $arena_live_chat_default_chat = get_option(AGCFRE_DEFAULT_CHAT_OPTION);
            $arena_live_chat_position = get_option(AGCFRE_POSITION_OPTION);

            $publisher_slug = '';
            if (is_array($arena_live_chat_selected_site) && isset($arena_live_chat_selected_site['slug'])) {
                $publisher_slug = sanitize_text_field($arena_live_chat_selected_site['slug']);
            }

            $chat_slug = '';
            if (is_array($arena_live_chat_default_chat) && isset($arena_live_chat_default_chat['slug'])) {
                $chat_slug = sanitize_text_field($arena_live_chat_default_chat['slug']);
            }

            $position = sanitize_text_field($arena_live_chat_position);

            $agcfre_live_chat_pagelevel = get_post_meta($page_id, 'agcfre_live_chat_pagelevel', true);

            $chat_slug = (isset($agcfre_live_chat_pagelevel['chat_slug'])) ? sanitize_text_field($agcfre_live_chat_pagelevel['chat_slug']) : $chat_slug;
            $position = (isset($agcfre_live_chat_pagelevel['position'])) ? sanitize_text_field($agcfre_live_chat_pagelevel['position']) : $position;

            $script_url = add_query_arg(
                array(
                    'p' => $publisher_slug,
                    'e' => $chat_slug,
                ),
                'https://go.arena.im/public/js/arenachatlib.js'
            );

            // Use a constant or option for the version instead of get_plugin_data()
            $plugin_version = AGCFRE_VERSION;

            // Enqueue the script with version and in_footer set to true
            wp_enqueue_script('agcfre-arena-chat-lib', esc_url($script_url), array(), $plugin_version, array(
                'in_footer' => true,
                'strategy' => 'async',
            ));

            return '<div class="arena-chat" data-publisher="' . esc_js($publisher_slug) . '" data-chatroom="' . esc_js($chat_slug) . '" data-position="' . esc_js($position) . '"></div>';
        }

        public function maybe_append_to_footer() {
            if (is_home() || is_front_page()) {
                $page_id = get_the_ID();
                // If we're on blog page, get the posts page ID
                if (empty($page_id) && is_home()) {
                    $page_id = get_option('page_for_posts');
                }
                // If we're on front page, get the front page ID
                if (empty($page_id) && is_front_page()) {
                    $page_id = get_option('page_on_front');
                }

                error_log("page id: " . $page_id);
                error_log('=== Running maybe_append_to_footer on home/front page ===');

                $agcfre_live_chat_pagelevel = get_post_meta($page_id, 'agcfre_live_chat_pagelevel', true);
                error_log('Page level options: ' . json_encode($agcfre_live_chat_pagelevel));

                $show_hide = (isset($agcfre_live_chat_pagelevel['show_hide']) && !empty($agcfre_live_chat_pagelevel['show_hide'])) 
                    ? sanitize_text_field($agcfre_live_chat_pagelevel['show_hide']) 
                    : '';
                error_log('Show/Hide setting: ' . $show_hide);
                
                // Get display options and check if we should insert
                $arena_live_chat_display_options = get_option(AGCFRE_DISPLAY_OPTIONS, array());
                $display_options = $this->get_display_options($arena_live_chat_display_options);
                
                $insert = $this->should_insert_shortcode($show_hide, $display_options);
                
                if ($insert) {
                    echo do_shortcode('[agcfre-group-chat]');
                }
            }
        }
    }

    $shortcode = new AgcfreShortcode();

    add_action('init', array($shortcode, 'shortcodes_init'));

endif; // END class_exists check
