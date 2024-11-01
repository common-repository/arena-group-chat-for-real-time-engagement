<?php

/**
 * Class AGCFRE\GroupChat\Includes\Admin\AgcfreMenu
 *
 * @package   AGCFRE\GroupChat
 * @copyright 2024 Arena
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://arena.im
 */

namespace AGCFRE\GroupChat\Includes\Admin;

/**
 * Class for the Arena Group Chat Menu
 *
 * @since 1.28.0
 * @access private
 * @ignore
 */

use AGCFRE\GroupChat\Core\Util\AgcfreIcon;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Menu
 */
class AgcfreMenu
{

    /**
     * Initialize the menu
     */
    public static function init()
    {
        add_action('admin_menu', [self::class, 'add_admin_menu']);
    }

    /**
     * Add menu item
     */
    public static function add_admin_menu()
    {
        $hook_suffix = add_menu_page(
            'Arena Group Chat', // Page title
            'Arena', // Menu title
            'manage_options', // Capability required to see the page
            'agcfre_settings', // Menu slug
            [self::class, 'render_settings_page'], // Function to render the settings page
            'data:image/svg+xml;base64,' . AgcfreIcon::to_base64()
        );

        // Rename the first submenu item to "Group Chat"
        add_submenu_page(
            'agcfre_settings', // Parent menu slug
            'Arena Group Chat', // Page title
            'Group Chat', // Menu title
            'manage_options', // Capability required to see the page
            'agcfre_group_chat_settings', // Menu slug (same as parent to replace default submenu)
            [self::class, 'render_settings_page'] // Function to render the settings page
        );

        // Remove the default submenu item
        remove_submenu_page('agcfre_settings', 'agcfre_settings');
    }

    /**
     * Render the settings page
     */
    public static function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="agcfre-settings-root"></div>
        </div>
        <?php
    }
}
