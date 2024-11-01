<?php
/**
 * Class AGCFRE\GroupChat\Core\Util\AgcfreIcon
 *
 * @package   AGCFRE\GroupChat
 * @copyright 2024 Arena
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://arena.im
 */

namespace AGCFRE\GroupChat\Core\Util;

/**
 * Class for the Arena SVG Icon
 *
 * @since 1.28.0
 * @access private
 * @ignore
 */
final class AgcfreIcon
{

	/**
	 * We use fill="white" as a placeholder attribute that we replace in with_fill()
	 * to match the colorscheme that the user has set.
	 *
	 * See the comment in includes/Core/Admin/Screen.php::add() for more information.
	 */
	const XML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.9522 0C15.2862 0 14.7109 0.457926 14.5569 1.09915C13.1361 0.369863 11.6031 0 10.0003 0C4.48612 0 0 4.48598 0 10C0 15.514 4.48612 20 10.0003 20C11.6038 20 13.1374 19.6301 14.5582 18.9002C14.7154 19.5362 15.2901 19.9915 15.9522 19.9915L20 19.9902V0H15.9522ZM15.7605 15.4057C15.662 15.5766 15.4526 15.6451 15.2751 15.5721L15.2614 15.5662C14.3057 15.2042 13.2835 15.0137 12.2013 15.0137C11.7708 15.0137 11.3663 15.0424 10.9527 15.1031C10.9527 15.1031 10.3193 15.1898 10.001 15.1898C7.13852 15.1898 4.81751 12.8695 4.81751 10.0065C4.81751 7.14351 7.13787 4.82322 10.001 4.82322C12.8641 4.82322 15.1844 7.14351 15.1844 10.0065C15.1844 10.2616 15.1662 10.5121 15.1303 10.7573H15.131C15.0846 11.0985 15.0612 11.4468 15.0612 11.801C15.0612 12.9602 15.3175 14.0607 15.7774 15.0463C15.8374 15.1709 15.8263 15.2909 15.7605 15.4057Z" fill="white"/></svg>';

	/**
	 * Returns a base64 encoded version of the SVG.
	 *
	 * @since 1.28.0
	 *
	 * @param string $source SVG icon source.
	 * @return string Base64 representation of SVG
	 */
	public static function to_base64($source = self::XML)
	{
		return base64_encode($source);
	}

	/**
	 * Returns SVG XML with fill color replaced.
	 *
	 * @since 1.28.0
	 *
	 * @param string $color Any valid color for css, either word or hex code.
	 * @return string SVG XML with the fill color replaced
	 */
	public static function with_fill($color)
	{
		return str_replace('white', esc_attr($color), self::XML);
	}
}
