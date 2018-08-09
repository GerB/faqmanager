<?php
/**
 *
 * FAQ manager reloaded. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Ger, https://github.com/gerb
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'FM_NO_DATA'			=> 'No FAQ entries found',
));
