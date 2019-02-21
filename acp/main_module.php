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
 * FAQ manager reloaded ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;
    public $handler;
    public $template;
    public $request;
    public $user;
    public $form_key;
    public $link_hash;

    public function main($id, $mode)
	{
        // Grab all the basics
		global $request, $template, $user, $phpbb_container;
        $this->user = $user;
		$this->user->add_lang_ext('ger/faqmanager', 'common');
        
        $this->handler      = $phpbb_container->get('ger.faqmanager.classes.handler');
        $this->path_helper  = $phpbb_container->get('path_helper');
		$this->tpl_name     = 'acp_faqmanager_body';
		$this->template     = $template;
		$this->request      = $request;

		$this->page_title   = $this->user->lang('FM_FAQ_MANAGER');
        $action             = $this->request->variable('action', '');
        $cat_id             = $this->request->variable('cat_id', 0);
        $faq_id             = $this->request->variable('faq_id', 0);
        
        $this->link_hash    = 'ger_acp_faqmanager';
        $this->form_key     = 'ger/faqmanager';
		add_form_key($this->form_key);
        
        // Special action
		if ($this->request->is_set_post('read_defaults'))
		{
            if (confirm_box(true))
            {
                $this->read_defaults();
                trigger_error($this->user->lang('FM_ACP_SAVED') . adm_back_link($this->u_action)); 
            }    
            else
            {
                // Confirm
                confirm_box(false, $this->user->lang['FM_DEFAULTS_EXPLAIN'] . $this->user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
                    'read_defaults'    => '1',
                )));
            }
        }
        
        
        if ($this->request->is_ajax() && !check_link_hash($this->request->variable('hash', ''), $this->link_hash))
        {
            trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
        }
            
        // Regular management
        switch ($action) 
        {
            case 'cat_add':
                if (!check_form_key($this->form_key))
				{
					trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
                $this->category_add();
                break;
            
            case 'cat_edit':
                $this->category_edit($cat_id);
                break;
            
            case 'cat_save':
                if (!check_form_key($this->form_key))
				{
					trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
                $this->category_save($cat_id);
                break;
            
            case 'cat_del':
                $this->faq_delete($cat_id, $action); 
                break;
            
            case 'faq_del':
                $this->faq_delete($faq_id, $action);
                break;
            
            case 'cat_up':
                $ajax_result = $this->handler->move_up_down(0, $cat_id, 'up');
                break;
            
            case 'cat_down':
                $ajax_result = $this->handler->move_up_down(0, $cat_id, 'down');
                break;
            
            case 'faq_up':
                $ajax_result = $this->handler->move_up_down($cat_id, $faq_id, 'up');
                break;
            
            case 'faq_down':
                $ajax_result = $this->handler->move_up_down($cat_id, $faq_id, 'down');
                break;         
            // Default just lists all the categories
            default:
                $this->list_categories();
                break;
                
        }

        if ($this->request->is_ajax())
        {
            $json_response = new \phpbb\json_response;
            $json_response->send(['success' => $ajax_result]);
        }
  
        
		$this->template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
		));
	}
    
    /** 
     * List all main cat data and send to template
     * @return boolean
     */
    private function list_categories()
    {
        $categories = $this->handler->get_cat_children(0);
        $languages = $this->handler->get_board_langs();
        if (empty($categories))
        {
            $this->template->assign_vars(array(
                'S_DISPLAY_CAT_LIST' => true,
                'S_FIRST_LANG' => isset($languages[0]['lang_local_name']) ? $languages[0]['lang_local_name'] : '-'
                ));             
            return false;
        }
        else
        {
            $curlang = $categories[0]['lang'];
            $this->template->assign_vars(array(
                'S_DISPLAY_CAT_LIST' => true,
                'S_FIRST_LANG' => $languages[$curlang]['lang_local_name']
                ));     
        }

                
        foreach ($categories as $index => $cat)
        {
            $can_down = false;
            if (isset($categories[$index+1]['lang']) && ($categories[$index+1]['lang'] == $cat['lang']) )
            {
                $can_down = true;
            }
            
            if ($cat['lang'] != $curlang)
            {
                $newblock = true;
                $curlang = $cat['lang'];
            }
            else
            {
                $newblock = false;
            }
            
            $this->template->assign_block_vars('categories', array(
                'NEWBLOCK'      => $newblock,
                'ID'            => $cat['faq_id'],
                'TITLE'         => $cat['faq_question'],
                'LANG_ISO'         => $cat['lang'],
                'LANG'          => $languages[$cat['lang']]['lang_local_name'],
                'U_MOVE_UP'     => $newblock ? false : $this->u_action . '&amp;action=cat_up&amp;cat_id=' . $cat['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash),
                'U_MOVE_DOWN'   => $can_down ? $this->u_action . '&amp;action=cat_down&amp;cat_id=' . $cat['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash) : false,
                'U_EDIT'        => $this->u_action . '&amp;action=cat_edit&amp;cat_id=' . $cat['faq_id'],
                'U_DELETE'      => $this->u_action . '&amp;action=cat_del&amp;cat_id=' . $cat['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash),
            ));
        }       
        return true;
    }
    
    /**
     * Add new category
     */
    private function category_add()
    {
        $cur_max_order = $this->handler->get_max_order(0, $this->user->data['user_lang']);
        
        $insert = [
            'faq_question' => html_entity_decode($this->request->variable('faq_question', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8"),
            'faq_answer' => '',
            'lang' => $this->user->data['user_lang'],
            'cat_id' => 0,
            'sort_order' => $cur_max_order +1,
        ];
        $cat_id = $this->handler->store_faq($insert);
        $this->category_edit($cat_id);
    }
    
    
    /**
     * Edit category main data and underlying questions
     * @param type $cat_id
     */
    private function category_edit($cat_id)
    {
        $cat_data = $this->handler->get_single($cat_id);
        if (!$cat_data)
        {
            trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
        }
        $this->template->assign_vars(array(
            'S_DISPLAY_CATEGORY_CONTENT'    => true,
            'CAT_ID'                        => $cat_data['faq_id'],
            'CAT_TITLE'                     => $cat_data['faq_question'],
            'S_LANG_OPTIONS'                => language_select($cat_data['lang']),
            'CAT_LANG'                      => $cat_data['lang'],
        ));

        $faq = $this->handler->get_cat_children($cat_id, $cat_data['lang']);
        if ($faq)
        {
            foreach ($faq as $row)
            {
                $this->template->assign_block_vars('faq', array(
                    'ID'            => $row['faq_id'],
                    'FAQ_QUESTION'  => $row['faq_question'],
                    'FAQ_ANSWER'    => $row['faq_answer'],
                    'U_MOVE_UP'     => $this->u_action . '&amp;action=faq_up&amp;cat_id=' . $cat_id . '&amp;faq_id=' . $row['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash),
                    'U_MOVE_DOWN'   => $this->u_action . '&amp;action=faq_down&amp;cat_id=' . $cat_id . '&amp;faq_id=' . $row['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash),
                    'U_DELETE'      => $this->u_action . '&amp;action=faq_del&amp;faq_id=' . $row['faq_id'] . '&amp;hash=' . generate_link_hash($this->link_hash),
                ));
            }
        }
    }
    
    
    /**
     * Store category main data and underlying questions
     * @param int $cat_id
     */
    private function category_save($cat_id)
    {
        // Main data first
        $newlang = $this->request->variable('cat_lang', '');
        $main = [
            'faq_id' => $cat_id,
            'faq_question' => html_entity_decode($this->request->variable('cat_title', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8"),
            'lang' => $newlang,
            'cat_id' => 0,
        ];
        $this->handler->store_faq($main);    
        
        // Get current children
        $faq = $this->handler->get_cat_children($cat_id, $this->request->variable('curlang', ''));
        if ($faq)
        {
            foreach ($faq as $row)
            {
                // Update relevant changes
                $update = [
                    'faq_id' => $row['faq_id'],
                    'faq_question' => html_entity_decode($this->request->variable($row['faq_id'] . '_faq_question', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8"),
                    'faq_answer' => html_entity_decode($this->request->variable($row['faq_id'] . '_faq_answer', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8"),
                    'lang' => $newlang,
                ];
                $this->handler->store_faq($update);
            }
        }
        // Add new FAQ if provided
        $new_q = html_entity_decode($this->request->variable('new_faq_question', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8");
        $new_a = html_entity_decode($this->request->variable('new_faq_answer', '', true), ENT_QUOTES|ENT_HTML5, "UTF-8");
        if (!empty($new_q) && !empty($new_a))
        {
            $cur_max_order = $this->handler->get_max_order($cat_id, $newlang);
            $insert = [
                'faq_question' => $new_q,
                'faq_answer' => $new_a,
                'lang' => $newlang,
                'cat_id' => $cat_id,
                'sort_order' => $cur_max_order +1,
            ];
            $this->handler->store_faq($insert);
            
        }
        trigger_error($this->user->lang('FM_ACP_SAVED') . adm_back_link($this->u_action)); 
    }
    
        
    /**
     * Delete category or faq entry
     * @param type $faq_id
     * @param type $action
     */
    private function faq_delete($faq_id, $action)
    {
        $message_text = ($action == 'cat_del') ? 'FM_CAT_DELETED' : 'FM_FAQ_DELETED';
        $confirm_text = $this->user->lang['CONFIRM_OPERATION'] . (($action == 'cat_del') ? ' ' . $this->user->lang['FM_CHILDREN_WILL_BE_DELETED'] : '');
        if (confirm_box(true))
        {
            if ($this->handler->delete_faq($faq_id))
            {
                $json_response = new \phpbb\json_response;
                $json_response->send(array(
                    'MESSAGE_TITLE'	=> $this->user->lang['INFORMATION'],
                    'MESSAGE_TEXT'	=> $this->user->lang[$message_text],
                    'REFRESH_DATA'	=> array(
                        'time'	=> 3
                    )
                ));          
            }
        }
        else
        {
            // Confirm
            confirm_box(false, $confirm_text, build_hidden_fields(array(
                'faq_id'        => $faq_id,
                'action'        => $action,
            )));
        }
    }

    /**
     * Read language files into DB
     */
    private function read_defaults()
    {
        global $phpbb_root_path, $phpEx;
        $languages = $this->handler->get_board_langs();
        foreach ($languages as $language)
        {
            $filepath = $phpbb_root_path . 'language/' . $language['lang_dir'] . '/help/faq.' . $phpEx;
            $lang = [];
            include($filepath);
            
            // Define structure first
            $structure = $this->define_langfile_structure($lang);            
            if ($structure)
            {
                $block_order = $faq_order = 1;
                $this->handler->truncate_faq($language['lang_iso']);
                foreach ($structure as $block)
                {
                    // Create category first
                    $insert = [
                        'faq_question' => $block['block_title'],
                        'faq_answer' => '',
                        'lang' => $language['lang_iso'],
                        'cat_id' => 0,
                        'sort_order' => $block_order,
                    ];
                    $cat_id = $this->handler->store_faq($insert);
                    $block_order++;
                    // Add entries
                    foreach ($block['faq'] as $entry)
                    {
                        $insert = [
                            'faq_question' => $entry['faq_question'],
                            'faq_answer' => $entry['faq_answer'],
                            'lang' => $language['lang_iso'],
                            'cat_id' => $cat_id,
                            'sort_order' => $faq_order,
                        ];  
                        $this->handler->store_faq($insert);
                        $faq_order++;
                    }
                }
            }
        }
        return true;
    }
    
    
    /**
     * Build a structured array for FAQ blocks relying on validated language files
     * @param array $lang
     * @return array
     */
    private function define_langfile_structure($lang)
    {
        // Get blocks first
        foreach ($lang as $key => $val)
        {
            if (strpos($key, 'HELP_FAQ_BLOCK') !== false)
            {
                $identifier = str_replace('_BLOCK', '', $key);
                $blocks[$identifier]['block_title'] = $val;
                unset($lang[$key]);
            }
            else if (substr($key, '-9') == '_QUESTION')
            {
                $questions[$key] = $val;
            }
            else if (substr($key, '-7') == '_ANSWER')
            {
                $answers[$key] = $val;
            }
            
        }
        
        if ($blocks)
        {
            // Now add items to blocks
            foreach ($blocks as $identifier => $block)
            {
                foreach($questions as $langkey => $string)
                {
                    if (substr($langkey, 0, strlen($identifier)) == $identifier)
                    {
                        $answerkey = str_replace('_QUESTION', '_ANSWER', $langkey);                        
                        $blocks[$identifier]['faq'][] = ['faq_question' => $string, 'faq_answer' => isset($answers[$answerkey]) ? $answers[$answerkey] : ''];
                    }
                }   
            }
        }
        return isset($blocks) ? $blocks : false;
    }
}
