<?php
/**
 *
 * FAQ manager reloaded. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Ger, https://github.com/gerb
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\faqmanager\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * FAQ manager reloaded Event listener.
 */
class main_listener implements EventSubscriberInterface
{
    public $template;
    public $request;
    public $helper;
    
    static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after'	=> 'change_faqurl',
			'core.help_manager_add_block_before'	=> 'add_blocks_before',
			'core.help_manager_add_block_after'	=> 'add_blocks_after',
			'core.help_manager_add_question_before'	=> 'add_questions_before',
			'core.help_manager_add_question_after'	=> 'add_questions_after',
		);
	}

    
    /*
     * Structuur:
     * 1. Inlezen huidige FAQ
     * 2. Overschrijven/toevoegen in ACP
     * 3. Nieuw bestand wegschrijven in ext dir
     * 4. geheel weergeven dmv events   
     * 
     */
    
    
    public function __construct( \phpbb\template\template $template, \phpbb\request\request $request, \phpbb\controller\helper $helper) {
            $this->template = $template;
            $this->request = $request;
            $this->helper = $helper;
            
            
    }

    /**
     * Alter url in template var and redirect any misguided souls
     * @param \phpbb\event\data	$event	Event object
     */
    public function change_faqurl($event)
    {
        if ($this->request->server('PATH_INFO') == '/help/faq')
        {
            header('Location: ' . $this->helper->route('ger_faqmanager_faqpage'), true, 301);
            exit_handler();
        }
        // If not on the wrong FAQ page, just change the template var
        $this->template->assign_var('U_FAQ', $this->helper->route('ger_faqmanager_faqpage'));
    }
    
    /**
	 * Add blocks before
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_blocks_before($event)
	{
		/**
		 * You can use this event to add a block before the current one.
		 *
		 * @event core.help_manager_add_block_before
		 * @var	string	block_name		Language key of the block headline
		 * @var	bool	switch_column	Should we switch the menu column before this headline
		 * @var	array	questions		Array with questions
		 * @since 3.2.0-a1
		 */
	}
    
	/**
	 * Add blocks after
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_blocks_after($event)
	{
		/**
		 * You can use this event to add a block after the current one.
		 *
		 * @event core.help_manager_add_block_after
		 * @var	string	block_name		Language key of the block headline
		 * @var	bool	switch_column	Should we switch the menu column before this headline
		 * @var	array	questions		Array with questions
		 * @since 3.2.0-a1
		 */
	}
	/**
	 * Add questions before
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_questions_before($event)
	{
		/**
         *  You can use this event to add a question before the current one.*
		 *
		 * @event core.help_manager_add_question_before
		 * @var	string	question	Language key of the question
		 * @var	string	answer		Language key of the answer
		 * @since 3.2.0-a1
		 */
	}
    
	/**
	 * Add questions after
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_questions_after($event)
	{
		/**
		 * You can use this event to add a question after the current one.
		 *
		 * @event core.help_manager_add_question_after
		 * @var	string	question	Language key of the question
		 * @var	string	answer		Language key of the answer
		 * @since 3.2.0-a1
		 */
	}
}
