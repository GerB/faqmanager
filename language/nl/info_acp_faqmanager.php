<?php
/**
 *
 * FAQ manager reloaded. An extension for the phpBB Forum Software package.
 * [Dutch]
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
    'FM_ACP_SAVED'                  => 'V&A opgeslagen',
    'FM_ANSWER'                     => 'Antwoord',
    'FM_CAT_ADD'                    => 'Cetegorie toevoegen',
    'FM_CAT_DELETED'                => 'Categorie en onderliggende vragen verwijderd',
    'FM_CAT_TITLE'                  => 'Categorie kop',
    'FM_CHILDREN_WILL_BE_DELETED'	=> 'Alle vragen en antwoorden binnen deze categorie zullen eveneens worden verwijderd!',
    'FM_DEFAULTS_IMPORT'            => 'Standaard taalbestanden inlezen in de V&A database',
    'FM_DEFAULTS_EXPLAIN'           => 'Als je doorgaat worden alle aanwezige V&A gegevens uit de database verwijderd. Het standaard <code>./language/**/help/faq.php</code> bestand zal worden geïmporteerd voor iedere ingeschakelde taal op dit forum.<br>',
	'FM_FAQ_MANAGER'                => 'V&A Manager',
	'FM_FAQ_DELETED'                => 'Vraag en antwoord verwijderd',
	'FM_LANG'                       => 'Taal voor deze categorie en onderliggende vragen',
	'FM_NO_CATEGORIES'              => 'Geen categorieën gevonden',
	'FM_QUESTION'                   => 'Vraag',
	'FM_QUESTION_ADD'               => 'Nieuwe vraag toevoegen aan categorie',
));
