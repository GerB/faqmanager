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
    'FM_ACP_SAVED'                  => 'FAQ saved',
    'FM_ANSWER'                     => 'Answer',
    'FM_CAT_ADD'                    => 'Add category',
    'FM_CAT_DELETED'                => 'Category with children deleted',
    'FM_CAT_TITLE'                  => 'Category title',
    'FM_CHILDREN_WILL_BE_DELETED'	=> 'All entries within this category will also be deleted!',
    'FM_DEFAULTS_IMPORT'            => 'Read default language files into FAQ database',
    'FM_DEFAULTS_EXPLAIN'           => 'If you continue, all existing FAQ entries will be purged from the database. The default <code>./language/**/help/faq.php</code> file will be imported for each enabled language.<br>',
	'FM_FAQ_MANAGER'                => 'FAQ Manager',
	'FM_FAQ_DELETED'                => 'FAQ entry deleted',
	'FM_LANG'                       => 'Language for this category and its entries',
	'FM_NO_CATEGORIES'              => 'No categories found',
	'FM_QUESTION'                   => 'Question',
	'FM_QUESTION_ADD'               => 'Add new question to this category',
));
