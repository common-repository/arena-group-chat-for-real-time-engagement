<?php
/**
 * Class AGCFRE\GroupChat\Includes\Admin\AgcfreMetabox
 *
 * @package   AGCFRE\GroupChat
 * @copyright 2024 Arena
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://arena.im
 */

namespace AGCFRE\GroupChat\Includes\Admin;

/**
 * Class for the Arena Group Chat Meta Box
 *
 * @since 1.28.0
 * @access private
 * @ignore
 */

if (!defined('ABSPATH'))
	exit;

if (!class_exists('AgcfreMetabox')):

	class AgcfreMetabox
	{
		/**
		 * add meta box
		 */
		function meta_box()
		{
			$post_types = get_post_types(array('public' => true));

			foreach ($post_types as $type) {
				if ('attachment' !== $type) {
					add_meta_box(
						'agcfre_live_chat_settings_meta_box',
						'Arena Group Chat',
						array($this, 'display_meta_box'),
						$type,
						'side',
						'default'
					);
				}
			}
		}

		/**
		 * render meta box content
		 */
		function display_meta_box($current_post)
		{
			wp_nonce_field('agcfre_live_chat_page_meta_box', 'agcfre_live_chat_page_meta_box_nonce');

			// get options from db
			$agcfre_live_chat_pagelevel = get_post_meta($current_post->ID, 'agcfre_live_chat_pagelevel', true);

			$arena_live_chat_chats = get_option(AGCFRE_CHATS_OPTION, array());
			$arena_live_chat_default_chat = get_option(AGCFRE_DEFAULT_CHAT_OPTION);
			$arena_live_chat_position = sanitize_text_field(get_option(AGCFRE_POSITION_OPTION));

			$arena_live_chat_default_chat_slug = '';
			if (is_object($arena_live_chat_default_chat) && isset($arena_live_chat_default_chat->slug)) {
				$arena_live_chat_default_chat_slug = esc_attr($arena_live_chat_default_chat->slug);
			}

			$show_settings = !empty($arena_live_chat_chats);
			if ($show_settings) {
				$all_chats_have_slug = true;
				for ($i = 0; $i < count($arena_live_chat_chats); $i++) {
					if (!isset($arena_live_chat_chats[$i]['slug'])) {
						$all_chats_have_slug = false;
						break;
					}
				}

				if (!$all_chats_have_slug) {
					$show_settings = false;
				}
			}

			if (!$show_settings) {
				?>
				<p class="description">Change values at <a href="/wp-admin/admin.php?page=agcfre_group_chat_settings">Arena Group
						ChatSettings</a></p><?php
			} else {
				// chat
				$chat_slug = isset($agcfre_live_chat_pagelevel['chat_slug']) ? esc_attr($agcfre_live_chat_pagelevel['chat_slug']) : $arena_live_chat_default_chat_slug;
				$show_hide = isset($agcfre_live_chat_pagelevel['show_hide']) ? esc_attr($agcfre_live_chat_pagelevel['show_hide']) : '';
				$position = isset($agcfre_live_chat_pagelevel['position']) ? esc_attr($agcfre_live_chat_pagelevel['position']) : $arena_live_chat_position;
				?>
				<p class="description">Chat Settings:</p>
				<!-- group chat -->
				<div class="row">
					<label for="chat_slug" style="font-weight: bold;">Group Chat</label><br>
					<select name="agcfre_live_chat_pagelevel[chat_slug]" id="chat_slug" style="box-sizing: border-box !important;">
						<?php foreach ($arena_live_chat_chats as $chat): ?>
							<?php if (isset($chat['slug'])): ?>
								<option value="<?php echo esc_attr($chat['slug']); ?>" <?php selected($chat['slug'], $chat_slug); ?>>
									<?php echo esc_html($chat['name']); ?>
								</option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<p class="description">Select the group chat to display</p>
				</div>

				<!-- position -->
				<div class="row">
					<label for="position" style="font-weight: bold;">Position</label><br>
					<label for="bottom">
						<input name="agcfre_live_chat_pagelevel[position]" <?php checked('bottom', $position); ?> value="bottom"
							id="bottom" type="radio">
						Bottom
					</label>
					<br>
					<label for="aside">
						<input name="agcfre_live_chat_pagelevel[position]" <?php checked('aside', $position); ?> value="aside"
							id="aside" type="radio">
						Aside
					</label>
					<br>
					<label for="overlay">
						<input name="agcfre_live_chat_pagelevel[position]" <?php checked('overlay', $position); ?> value="overlay"
							id="overlay" type="radio">
						Overlay
					</label>
					<p class="description">Select the position of the chat</p>
				</div>

				<!-- show/hide -->
				<div class="row">
					<label for="show_hide" style="font-weight: bold;">Display Settings</label><br>
					<label for="show">
						<input name="agcfre_live_chat_pagelevel[show_hide]" <?php checked('show', $show_hide); ?> value="show" id="show"
							type="radio">
						Show
					</label>
					<br>
					<label for="hide">
						<input name="agcfre_live_chat_pagelevel[show_hide]" <?php checked('hide', $show_hide); ?> value="hide" id="hide"
							type="radio">
						Hide
					</label>
					<br>
					<label for="default">
						<input name="agcfre_live_chat_pagelevel[show_hide]" <?php checked('', $show_hide); ?> value="" id="default"
							type="radio">
						Default
					</label>
					<p class="description">Select the display settings for the chat</p>
				</div>
				<?php
			}

			do_action('agcfre_live_chat_ah_admin_chat_bottom_meta_box', $current_post);
		}

		/**
		 * save meta box
		 */
		function save_meta_box($post_id)
		{
			// Check if our nonce is set and verify it.
			if (
				!isset($_POST['agcfre_live_chat_page_meta_box_nonce']) ||
				!wp_verify_nonce(
					sanitize_text_field(wp_unslash($_POST['agcfre_live_chat_page_meta_box_nonce'])),
					'agcfre_live_chat_page_meta_box'
				)
			) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			// Check the user's permissions.
			if (!current_user_can('edit_post', $post_id)) {
				return;
			}

			error_log('Saving meta box: ' . print_r($_POST, true));

			if (isset($_POST['agcfre_live_chat_pagelevel']) && is_array($_POST['agcfre_live_chat_pagelevel'])) {
				$agcfre_live_chat_pagelevel = array_map('sanitize_text_field', wp_unslash($_POST['agcfre_live_chat_pagelevel']));

				// Sanitize the fields
				$sanitized_data = array();

				if (isset($agcfre_live_chat_pagelevel['chat_slug'])) {
					$sanitized_data['chat_slug'] = $agcfre_live_chat_pagelevel['chat_slug'];
				}

				if (isset($agcfre_live_chat_pagelevel['position'])) {
					$sanitized_data['position'] = $agcfre_live_chat_pagelevel['position'];
				}

				if (isset($agcfre_live_chat_pagelevel['show_hide'])) {
					$sanitized_data['show_hide'] = $agcfre_live_chat_pagelevel['show_hide'];
				}

				// Remove any empty values
				$sanitized_data = array_filter($sanitized_data);

				error_log('Saving sanitized data before update: ' . print_r($sanitized_data, true));
				if (!empty($sanitized_data)) {
					error_log('Saving sanitized data: ' . print_r($sanitized_data, true));
					update_post_meta($post_id, 'agcfre_live_chat_pagelevel', $sanitized_data);
				} else {
					delete_post_meta($post_id, 'agcfre_live_chat_pagelevel');
				}
			}
		}
	}

	$agcfre_live_chat_metabox = new AgcfreMetabox();

	add_action('add_meta_boxes', array($agcfre_live_chat_metabox, 'meta_box'));
	add_action('save_post', array($agcfre_live_chat_metabox, 'save_meta_box'));

endif; // END class_exists check