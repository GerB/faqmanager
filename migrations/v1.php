<?php
/**
 *
 * FAQ manager reloaded. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Ger, https://github.com/gerb
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\faqmanager\migrations;

class v1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'faqmgr');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v322');
	}
    
    public function update_data()
	{
		return array(
//			array('module.add', array(
//				'acp',
//				'ACP_CAT_DOT_MODS',
//				'FM_FAQ_MANAGER'
//			)),
			array('module.add', array(
				'acp',
				'ACP_BOARD_CONFIGURATION',
				array(
					'module_basename'	=> '\ger\faqmanager\acp\main_module',
					'modes'				=> array('manage'),
				),
			)),
		);
	}
    
	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'faqmgr'	=> array(
					'COLUMNS'		=> array(
						'faq_id'			=> array('UINT', null, 'auto_increment'),
						'faq_question'		=> array('VCHAR_UNI', ''),
						'faq_answer'		=> array('MTEXT_UNI', ''),
						'lang'              => array('VCHAR:30', ''),
						'cat_id'			=> array('USINT', '0'),
						'sort_order'        => array('USINT', '0'),
					),
					'PRIMARY_KEY'	=> 'faq_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'faqmgr',
			),
		);
	}
}
