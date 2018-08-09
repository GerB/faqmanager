<?php
/**
 *
 * FAQ manager reloaded. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Ger, https://github.com/gerb
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\faqmanager\acp;

/**
 * FAQ manager reloaded ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\ger\faqmanager\acp\main_module',
			'title'		=> 'FM_FAQ_MANAGER',
			'modes'		=> array(
				'manage'	=> array(
					'title'	=> 'FM_FAQ_MANAGER',
					'auth'	=> 'ext_ger/faqmanager && acl_a_board',
					'cat'	=> array('ACP_BOARD_CONFIGURATION')
				),
			),
		);
	}
}
