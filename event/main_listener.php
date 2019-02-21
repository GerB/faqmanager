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
		);
	}
    
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
}
