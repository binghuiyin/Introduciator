<?php
/**
 *
 * @package phpBB Extension - Introduciator Extension
 * @author Feneck91 (Stéphane Château) feneck91@free.fr
 * @copyright (c) 2019 Feneck91
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace feneck91\introduciator\controller;

use feneck91\introduciator\helper\introduciator_helper;
use phpbb\config\config;
use phpbb\auth\auth;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class used to display introduciator explanation page.
 *
 * This page is displayed when user try to post and have not yet written it's introduce.
 * It explain that the introduce is mandatory and display links to help user to begin it's introduce.
 */
class introduciator_explain_controller
{
	/**
	 * @var helper Introduciator helper. The important code is into this helper.
	 */
	protected $helper;

	/**
	 * @var \phpbb\config\config Current configuration (config table).
	 */
	protected $config;

	/**
	 * @var \phpbb\auth\auth Current authorization.
	 */
	protected $auth;

	/**
	 * @var \phpbb\template\template Template object
	 */
	protected $template;

	/**
	 * @var \phpbb\user Current connected user.
	 */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \feneck91\introduciator\helper\introduciator_helper   $helper         Extension helper
	 * @param \phpbb\config\config                                  $config         Current configuration (config table).
	 * @param \phpbb\auth\auth                                      $auth           Current authorization.
	 * @param \phpbb\template\template                              $template       Template object
	 * @param \phpbb\user                                           $user           User object
	 *
	 * @access public
	 */
	public function __construct(introduciator_helper $helper, config $config, auth $auth, template $template, user $user)
	{
		//global $phpbb_container;
		// Get Introduciator class helper
		//$this->helper = $phpbb_container->get('feneck91.introduciator.helper');
		$this->helper = $helper;
		$this->config = $config;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
	}

	public function handle($forum_id)
	{
		// If user not connected, go to login page
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			// In case of introduciator_getparams is not called, I must load the introduciator language file
			$this->helper->load_language_if_needed();
			login_box('', $this->helper->get_language()->lang('LOGIN'));
		}

		if ($this->config['introduciator_allow'])
		{	// Title messagte
			// Load extension configuration + language
			$params = $this->helper->introduciator_getparams(false);

			$message = $this->helper->get_language()->lang('INTRODUCIATOR_EXT_MUST_INTRODUCE_INTO_FORUM', $params['forum_name']);
			page_header($message);

			$this->template->set_filenames(array(
				'body' => 'introduciator_explain.html',
			));

			$this->template->assign_vars(array(
				'S_EXT_ACTIVATED'					=> true,
				'INTRODUCIATOR_EXT_EXPLAIN_TITLE'	=> $params['explanation_message_title'],
				'INTRODUCIATOR_EXT_EXPLAIN_TEXT'	=> $params['explanation_message_text'],
				'S_RULES_ACTIVATED'					=> $params['is_explanation_display_rules'] && strlen($params['explanation_rules_text']) > 0,
				'S_RULES_TITLE_ACTIVATED'			=> (strlen($params['explanation_rules_title']) > 0),
				'INTRODUCIATOR_EXT_RULES_TITLE'		=> $params['explanation_rules_title'],
				'INTRODUCIATOR_EXT_RULES_TEXT'		=> $params['explanation_rules_text'],
				'INTRODUCIATOR_EXT_LINK_GOTO_FORUM'	=> $params['explanation_message_goto_forum'],
				'INTRODUCIATOR_EXT_LINK_POST_FORUM'	=> $params['explanation_message_post_forum'],
			));

			page_footer();
		}
		else
		{
			// In case of introduciator_getparams is not called, I must load the introduciator language file
			$this->helper->load_language_if_needed();

			page_header($this->helper->get_language()->lang('INTRODUCIATOR_EXT_DISABLED'));
			$this->template->set_filenames(array(
				'body' => 'introduciator_explain.html',
			));

			page_footer();
		}
	}
}
