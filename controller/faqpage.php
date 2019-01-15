<?php

/**
 *
 * Faq Manager
 *
 * @copyright (c) 2018 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\faqmanager\controller;

class faqpage
{
    
    public $helper;
    public $template;
    public $user;
    public $language;
    public $help_manager;
    public $handler;
    public $root_path;
    public $php_ext;
    
    public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\language\language $language, \phpbb\help\manager $help_manager, \ger\faqmanager\classes\handler $handler, $root_path, $php_ext) 
    {
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->language = $language;
        $this->help_manager = $help_manager;
        $this->handler = $handler;
        $this->root_path = $root_path;
		$this->php_ext = $php_ext;
    }
    
    
    public function handle()
    {
        $this->language->add_lang('common', 'ger/faqmanager');
        $faq = $this->handler->get_faq_data($this->language->get_used_language());
        if (!$faq)
        {
            return $this->helper->error('FM_NO_DATA');
        }
        
        $switch_column = $found_switch = false;
        $half_done = ceil((end($faq)['cnt'])/2);        
        reset($faq);
        
        // Grab necessary info and send to help manager
        foreach ($faq as $block)
        {
            $questions = array();
            if (isset($block['faq']))
            {
                foreach ($block['faq'] as $qa) 
                {
                    $questions[$qa['faq_question']] = $qa['faq_answer'];    
                }
                $this->help_manager->add_block($block['title'], $switch_column, $questions);
                if (!$found_switch && $block['cnt'] > $half_done)
                {
                    $switch_column = $found_switch = true;
                }
            }
        }
        
        // Generic page stuff
		$title = $this->language->lang('FAQ_EXPLAIN');
		$this->template->assign_vars(array(
            'L_FAQ_TITLE'       => $this->language->lang('FAQ_EXPLAIN'),
			'S_IN_FAQ'			 => true,

		));
        make_jumpbox(append_sid("{$this->root_path}viewforum.{$this->php_ext}"));

		return $this->helper->render('/faq_body.html', $title);
    }
}