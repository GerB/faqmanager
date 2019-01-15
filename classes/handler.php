<?php

/**
 *
 * FAQ Manager handler class
 *
 * @copyright (c) 2018 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\faqmanager\classes;

class handler
{
    
    public $db;
    public $faq_table;


    public function __construct(\phpbb\db\driver\driver_interface $db, $faq_table) 
    {
        $this->db = $db;
        $this->faq_table = $faq_table;
    }
    
    /**
     * Get structured FAQ data
     * @param string $langcode    iso lang identifier
     */
    public function get_faq_data($langcode)
    {
        if (!$this->validate_langcode($langcode))
        {
            return false;
        }
        $total = 0;
        $sql_array = [
            'SELECT'    => 'fm.*',
            'FROM'      => [$this->faq_table => 'fm'],
            'WHERE'     => 'lang = "' . $this->db->sql_escape($langcode) . '" AND cat_id = 0',
            'ORDER_BY'  => 'fm.sort_order ASC, fm.faq_question ASC',  
        ];
        
        $result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_array));
        while ($row = $this->db->sql_fetchrow($result))
        {
            $total++;
            $structure[$row['faq_id']]['title'] = $row['faq_question'];
            $sql_ary = [
                'SELECT'    => 'fm.*',
                'FROM'      => [$this->faq_table => 'fm'],
                'WHERE'     => 'lang = "' . $this->db->sql_escape($langcode) . '" AND cat_id = ' . $row['faq_id'],
                'ORDER_BY'  => 'fm.sort_order ASC, fm.faq_question ASC',  
            ];
            $qa = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));
            while ($faq = $this->db->sql_fetchrow($qa))
            {
                $structure[$row['faq_id']]['faq'][] = $faq;
                $total++;
            }
            $this->db->sql_freeresult($qa);
            $structure[$row['faq_id']]['cnt'] = $total;
        }
        $this->db->sql_freeresult($result);
        
        return isset($structure) ?  $structure : false;
    }
    
    
    /**
     * Get single entry
     * @param int $faq_id
     * @return array
     */
    public function get_single($faq_id)
    {
        $sql = 'SELECT * FROM ' . $this->faq_table . ' WHERE faq_id = ' . (int) $faq_id;
        $result = $this->db->sql_query($sql);
        $entry = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        return $entry;
    }
    
    /**
     * Get ACP category listing
     * @param int $cat_id
     * @param string $langcode
     * @return array 
     */
    public function get_cat_children($cat_id, $langcode = null)
    {
        if ($langcode && !$this->validate_langcode($langcode))
        {
            return false;
        }
        $where = 'cat_id = ' . (int) $cat_id;
        if (!empty($langcode))
        {
            $where.= ' AND lang = "' . $langcode . '"';
        }
        $sql_array = [
            'SELECT'    => 'fm.*',
            'FROM'      => [$this->faq_table => 'fm'],
            'WHERE'     => $where,
            'ORDER_BY'  => 'lang, fm.sort_order ASC, fm.faq_question ASC',  
        ];
        
        $result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_array));
        while ($row = $this->db->sql_fetchrow($result))
        {
            $return[] = $row;
        }
        $this->db->sql_freeresult($result);
        return isset($return) ? $return : false;
    }
    
    /**
     * Check requested lang against installed language code
     * @param string $langcode
     * @return boolean
     */
    public function validate_langcode($langcode)
    {
        if (empty($langcode))
        {
            return false;
        }
        $installed = $this->get_board_langs();
        foreach ($installed as $lang)
        {
            if ($langcode == $lang['lang_iso'])
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all installed languages
     * @return array
     */
    public function get_board_langs()
    {
        $sql = 'SELECT *
		FROM ' . LANG_TABLE . '
		ORDER BY lang_english_name';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $return[$row['lang_iso']] = $row;
        }
        $this->db->sql_freeresult($result);

        return $return;
    }
    
    /**
     * Insert or update entry in DB
     * @param array $data
     * @return int
     */
    public function store_faq($data)
    {
        
        if (isset($data['faq_id']))
		{
			$faq_id = $data['faq_id'];
			unset($data['faq_id']);
			$action = 'UPDATE ' . $this->faq_table . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE faq_id =  ' . (int) $faq_id;
		}
		else
		{
			$action = 'INSERT INTO ' . $this->faq_table . ' ' . $this->db->sql_build_array('INSERT', $data);
		}
                
        if (!$this->db->sql_query($action))
		{
			return false;
		}
		else
		{
			return isset($faq_id) ? $faq_id : $this->db->sql_nextid();
		}
    }
    
    /**
     * Move a an entry up or down within a categoru=y
     * @param int $cat_id
     * @param int $faq_id
     * @param string $order     either of up or down
     * @return boolean
     */
    public function move_up_down($cat_id, $faq_id, $order)
    {
        // Look what we have
        $select_array = [
            'SELECT'    => 'sort_order AS current_order, lang',
            'FROM'      => [$this->faq_table => 'fm'],
            'WHERE'     => 'faq_id = ' . (int) $faq_id,
            'ORDER_BY'  => 'lang, fm.sort_order ASC, fm.faq_question ASC',  
        ];
        $result = $this->db->sql_query($this->db->sql_build_query('SELECT', $select_array));
        $faq = $this->db->sql_fetchrow($result);
        $current_order = (int) $faq['current_order'];
        $lang =  $faq['lang'];
        $this->db->sql_freeresult($result);

        // Decide what to switch with
        $look = ($order == 'down') ? ' > ' : ' < ';
        $sort = ($order == 'down') ? 'ASC' : 'DESC';
        
        $select_array = [
            'SELECT'    => 'faq_id, sort_order',
            'FROM'      => [$this->faq_table => 'fm'],
            'WHERE'     => 'cat_id = ' . (int) $cat_id . ' AND sort_order ' . $look . $current_order . ' AND lang = "' . $this->db->sql_escape($lang) . '"', 
            'ORDER_BY'  => 'fm.sort_order '. $sort,  
        ];
        $result = $this->db->sql_query($this->db->sql_build_query('SELECT', $select_array));
        $switch = $this->db->sql_fetchrow($result);        
        $this->db->sql_freeresult($result);
        
        // Switch the switch
        $action =  'UPDATE ' . $this->faq_table . 
                ' SET sort_order = ' . $current_order .
                ' WHERE faq_id =  ' . (int) $switch['faq_id'] .
                ' AND cat_id = ' . (int) $cat_id;
        $this->db->sql_query($action);

        $move_executed = (bool) $this->db->sql_affectedrows();
        if ($move_executed)
        {
            // Switch given faq as well
            $sql =  'UPDATE ' . $this->faq_table . 
                    ' SET sort_order = ' . $switch['sort_order'] .
                    ' WHERE faq_id =  ' . (int) $faq_id .
                    ' AND cat_id = ' . (int) $cat_id;
            $this->db->sql_query($sql); 
        }
        return $move_executed;
    }
    
    /**
     * Fetch highest sort order in cat/lang
     * @param int $cat_id
     * @param string $langcode
     * @return int
     */
    public function get_max_order($cat_id, $langcode)
    {
        if (!$this->validate_langcode($langcode))
        {
            return false;
        }
        $select_array = [
            'SELECT'    => 'sort_order',
            'FROM'      => [$this->faq_table => 'fm'],
            'WHERE'     => 'cat_id = ' . (int) $cat_id . ' AND lang = "' . $this->db->sql_escape($langcode) . '"',
            'ORDER_BY'  => 'sort_order DESC',  
        ];
        $result = $this->db->sql_query($this->db->sql_build_query('SELECT', $select_array));
        $sort_order = (int) $this->db->sql_fetchfield('sort_order');
        $this->db->sql_freeresult($result);
        return $sort_order;
        
    }

    /**
     * Delete entry with any children
     * @param int $faq_id
     * @return bool
     */
    public function delete_faq($faq_id)
    {
        // Check for children, delete those as well if applicable
        $children = $this->get_cat_children($faq_id);
        if ($children)
        {
            foreach ($children as $child)
            {
                $this->delete_faq($child['faq_id']);
            }
               
        }
        $sql = 'DELETE FROM ' . $this->faq_table . ' WHERE faq_id = ' . (int) $faq_id;
        return $this->db->sql_query($sql);
    }

    /**
     * Delete all entries for given language
     * @param string $langcode
     * @return bool
     */
    public function truncate_faq($langcode)
    {
        if (!$this->validate_langcode($langcode))
        {
            return false;
        }
        $action = 'DELETE FROM ' . $this->faq_table. ' WHERE lang = "' . $this->db->sql_escape($langcode) . '"';
        return $this->db->sql_query($action);
    }
}