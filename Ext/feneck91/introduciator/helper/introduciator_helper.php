<?php
/**
 *
 * @package phpBB Extension - Introduciator Extension
 * @author Feneck91 (Stéphane Château) feneck91@free.fr
 * @copyright (c) 2013 @copyright (c) 2014 Feneck91
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
namespace feneck91\introduciator\helper;

if (!function_exists('group_memberships'))
{
	global $phpbb_root_path, $phpEx;
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
}

/**
 * Class used to manage extension.
 *
 * Is used to manage ACP and check all needed information to known how the extension should work.
 */ 
class introduciator_helper
{
	const INTRODUCIATOR_POSTING_APPROVAL_LEVEL_NO_APPROVAL 			= 0; // No approval introduce
	const INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL				= 1; // Approval introduce : the user don't see his introduce and cannot edit it
	const INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT	= 2; // Approval introduce : the user see his introduce and can edit it

	/**
	 * @var string Table prefix.
	 */
	private $table_prefix;

	/**
	 * PhpBB Root path.
	 */
	private $root_path;
	
	/**
	 * phpBB Extention.
	 */
	private $php_ext;
	
	/**
	 * @var \phpbb\user Current connected user.
	 */
	private $user;

	/**
	 * @var \phpbb\db\driver\factory Database access.
	 */
	private $db;

	/**
	 * @var \phpbb\config\config Current configuration (config table).
	 */
	private $config;

	/**
	 * @var \phpbb\auth\auth Current authorization.
	 */
	private $auth;

	/**
	 * @var \phpbb\controller\helper Controller helper, used to generate links to explanation page.
	 */
	private $controller_helper;

	/**
	 * @var \phpbb\language\language Language manager, used to translate all messages.
	 */
	private $language;
	
	/**
	 * @var \phpbb\language\language Language manager, used to translate all messages.
	 */
	private $language_loaded;

	/**
	 * Current introduciator parameters.
	 */
	private $introduciator_params;

	/**
	 * Constructor
	 *
	 * @param string					$table_prefix Table prefix.
	 * @param string					$root_path phpBB root path.
	 * @param string					$php_ext phpBB Extention.
	 * @param \phpbb\user				$user Current connected user.
	 * @param \phpbb\db\driver\factory	$db Database access.
	 * @param \phpbb\config\config 		$config Current configuration (config table).
	 * @param \phpbb\auth\auth 			$auth Current authorizations.
	 * @param \phpbb\controller\helper  $controller_helper Controller helper, used to generate route.
	 * @param \phpbb\language\language  $language Language manager, used to translate all messages.
	 */
	public function __construct($table_prefix, $root_path, $php_ext, \phpbb\user $user, \phpbb\db\driver\factory $db, \phpbb\config\config $config, \phpbb\auth\auth $auth, \phpbb\controller\helper $controller_helper, \phpbb\language\language $language)
	{
		// Record parameters into this
		$this->table_prefix = $table_prefix;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->user = $user;
		$this->db = $db;
		$this->config = $config; 
		$this->auth = $auth;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->language_loaded = false;
	}

	/**
	 * Load language only if noyt already done.
	 */
	public function load_language_if_needed()
	{
		if (!$this->language_loaded)
		{
			$this->language->add_lang('introduciator', 'feneck91/introduciator');	// Add lang
			$this->language_loaded = true;
		}
	}
	
	/**
	 * Get the language instance.
	 *
	 * @return The private language instance.
	 */
	public function get_language()
	{
		return $this->language;
	}
	
	/**
	 * Get the introduciator groups table name (with prefix).
	 *
	 * It's not possible to create const INTRODUCIATOR_GROUPS_TABLE because it need table prefix.
	 * So, I use a method to get this name.
	 *
	 * @return The full group table name.
	 */
	public function Get_INTRODUCIATOR_GROUPS_TABLE()
	{
		return $this->table_prefix . 'introduciator_groups';
	}

	/**
	 * Is the introduciator enabled?
	 *
	 * Return the introduciator_allow's config field.
	 *
	 * @return true if the introduciator is allowed, false else.
	 */	
	public function is_introduciator_allowed()
	{
		return isset($this->config['introduciator_allow']) && $this->config['introduciator_allow'] != '' ? $this->config['introduciator_allow'] : false;
	}
	
	/**
	 * Check if a group is selected.
	 *
	 * @param $group_id Group's identifier.
	 * @return true if the group is selected, false else.
	 */
	public function is_group_selected($group_id)
	{
		$sql = 'SELECT COUNT(*) AS cnt
				FROM ' . $this->Get_INTRODUCIATOR_GROUPS_TABLE() . '
				WHERE fk_group = ' . (int) $group_id;

		$result = $this->db->sql_query($sql);
		$ret = false;
		$ret = (int) $this->db->sql_fetchfield('cnt') > 0;
		$this->db->sql_freeresult($result);

		return $ret;
	}
	
	/**
	 * Replace all variables with several values.
	 *
	 * Example :
	 * 	replace_all_by(
	 *		array(
	 *			&$var_1,
	 *			&$var_2
	 *			),
	 *		array(
	 *			'search1'	=> 'replaced by this text1',
	 *			'search2'	=> 'replaced by this text2',
	 *			'search3'	=> 'replaced by this text3',
	 *			));
	 *
	 * @param $arr_fields Array of variables to update
	 * @param $arr_replace_by Array of maps with key is the text to replace, value is the text to replace with
	 * @return None
	 */
	public function replace_all_by($arr_fields, $arr_replace_by)
	{
		foreach ($arr_fields as &$field)
		{
			foreach ($arr_replace_by as $arr_replace_by_key => $arr_replace_by_value)
			{
				$field = str_replace($arr_replace_by_key, $arr_replace_by_value, $field);
			}
		}
	}

	/**
	 * Get the introduciator parameters.
	 *
	 * @param $is_edit if true, return rules texts for editing
	 *                 if false, return rules texts for display
	 *                 if null, don't return rules texts (used only in the extension configuration and to display rules
	 * @return The introduciator parameters
	 */
	public function introduciator_getparams($is_edit = null)
	{
		$params = array(
			'introduciator_allow'					=> $this->is_introduciator_allowed(),
			'fk_forum_id'							=> isset($this->config['introduciator_fk_forum_id']) &&  $this->config['introduciator_fk_forum_id'] != '' ? $this->config['introduciator_fk_forum_id'] : 0,
			'is_check_delete_first_post'			=> isset($this->config['introduciator_is_check_delete_first_post']) && $this->config['introduciator_is_check_delete_first_post'] != '' ? $this->config['introduciator_is_check_delete_first_post'] : true,
			'is_explanation_enabled'				=> isset($this->config['introduciator_is_explanation_enabled']) && $this->config['introduciator_is_explanation_enabled'] != '' ? $this->config['introduciator_is_explanation_enabled'] : true,
			'is_use_permissions'					=> isset($this->config['introduciator_is_use_permissions']) && $this->config['introduciator_is_use_permissions'] != '' ? $this->config['introduciator_is_use_permissions'] : true,
			'is_include_groups'						=> isset($this->config['introduciator_is_include_groups']) && $this->config['introduciator_is_include_groups'] != '' ? $this->config['introduciator_is_include_groups'] : true,
			'ignored_users'							=> isset($this->config['introduciator_ignored_users']) && $this->config['introduciator_ignored_users'] != '' ? $this->config['introduciator_ignored_users'] : '',
			'is_explanation_display_rules'			=> isset($this->config['introduciator_is_explanation_display_rules']) && $this->config['introduciator_is_explanation_display_rules'] != '' ? $this->config['introduciator_is_explanation_display_rules'] : true,
			'posting_approval_level'				=> isset($this->config['introduciator_posting_approval_level']) && $this->config['introduciator_posting_approval_level'] != '' ? $this->config['introduciator_posting_approval_level'] : 0,
		);

		if ($is_edit === true || $is_edit === false)
		{
			$explanation_message_title						= isset($this->config['introduciator_explanation_message_title']) && $this->config['introduciator_explanation_message_title'] != '' ? $this->config['introduciator_explanation_message_title'] : '';
			$explanation_message_title_uid					= isset($this->config['introduciator_explanation_message_title_uid']) && $this->config['introduciator_explanation_message_title_uid'] != '' ? $this->config['introduciator_explanation_message_title_uid'] : '';
			$explanation_message_title_bitfield				= isset($this->config['introduciator_explanation_message_title_bitfield']) && $this->config['introduciator_explanation_message_title_bitfield'] != '' ? $this->config['introduciator_explanation_message_title_bitfield'] : '';
			$explanation_message_title_bbcode_options		= isset($this->config['introduciator_explanation_message_title_bbcode_options']) && $this->config['introduciator_explanation_message_title_bbcode_options'] != '' ? $this->config['introduciator_explanation_message_title_bbcode_options'] : '';
			$explanation_message_text						= isset($this->config['introduciator_explanation_message_text']) && $this->config['introduciator_explanation_message_text'] != '' ? $this->config['introduciator_explanation_message_text'] : '';
			$explanation_message_text_uid					= isset($this->config['introduciator_explanation_message_text_uid']) && $this->config['introduciator_explanation_message_text_uid'] != '' ? $this->config['introduciator_explanation_message_text_uid'] : '';
			$explanation_message_text_bitfield				= isset($this->config['introduciator_explanation_message_text_bitfield']) && $this->config['introduciator_explanation_message_text_bitfield'] != '' ? $this->config['introduciator_explanation_message_text_bitfield'] : '';
			$explanation_message_text_bbcode_options		= isset($this->config['introduciator_explanation_message_text_bbcode_options']) && $this->config['introduciator_explanation_message_text_bbcode_options'] != '' ? $this->config['introduciator_explanation_message_text_bbcode_options'] : '';
			$explanation_message_rules_title				= isset($this->config['introduciator_explanation_message_rules_title']) && $this->config['introduciator_explanation_message_rules_title'] != '' ? $this->config['introduciator_explanation_message_rules_title'] : '';
			$explanation_message_rules_title_uid			= isset($this->config['introduciator_explanation_message_rules_title_uid']) && $this->config['introduciator_explanation_message_rules_title_uid'] != '' ? $this->config['introduciator_explanation_message_rules_title_uid'] : '';
			$explanation_message_rules_title_bitfield		= isset($this->config['introduciator_explanation_message_rules_title_bitfield']) && $this->config['introduciator_explanation_message_rules_title_bitfield'] != '' ? $this->config['introduciator_explanation_message_rules_title_bitfield'] : '';
			$explanation_message_rules_title_bbcode_options	= isset($this->config['introduciator_explanation_message_rules_title_bbcode_options']) && $this->config['introduciator_explanation_message_rules_title_bbcode_options'] != '' ? $this->config['introduciator_explanation_message_rules_title_bbcode_options'] : '';
			$explanation_message_rules_text					= isset($this->config['introduciator_explanation_message_rules_text']) && $this->config['introduciator_explanation_message_rules_text'] != '' ? $this->config['introduciator_explanation_message_rules_text'] : '';
			$explanation_message_rules_text_uid				= isset($this->config['introduciator_explanation_message_rules_text_uid']) && $this->config['introduciator_explanation_message_rules_text_uid'] != '' ? $this->config['introduciator_explanation_message_rules_text_uid'] : '';
			$explanation_message_rules_text_bitfield		= isset($this->config['introduciator_explanation_message_rules_text_bitfield']) && $this->config['introduciator_explanation_message_rules_text_bitfield'] != '' ? $this->config['introduciator_explanation_message_rules_text_bitfield'] : '';
			$explanation_message_rules_text_bbcode_options	= isset($this->config['introduciator_explanation_message_rules_text_bbcode_options']) && $this->config['introduciator_explanation_message_rules_text_bbcode_options'] != '' ? $this->config['introduciator_explanation_message_rules_text_bbcode_options'] : '';
			
			$forum_name = '';
			$forum_rules = array();

			if ($params['introduciator_allow'])
			{	// Find Forum name
				$sql = 'SELECT forum_name, forum_rules, forum_rules_uid, forum_rules_bitfield, forum_rules_options
						FROM ' . FORUMS_TABLE . '
						WHERE forum_id = ' . (int) $params['fk_forum_id'];
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);

				if ($row)
				{
					$forum_name = $row['forum_name'];
					$forum_rules = array(
						'rules'				=> $row['forum_rules'],
						'rules_uid'			=> $row['forum_rules_uid'],
						'rules_bitfield'	=> $row['forum_rules_bitfield'],
						'rules_options'		=> $row['forum_rules_options'],
					);
				}
				$this->db->sql_freeresult($result);
			}

			if ($is_edit === true)
			{
				$explanation_message_title = generate_text_for_edit($explanation_message_title, $explanation_message_title_uid, $explanation_message_title_bbcode_options);
				$explanation_message_text = generate_text_for_edit($explanation_message_text, $explanation_message_text_uid, $explanation_message_text_bbcode_options);
				$explanation_message_rules_title = generate_text_for_edit($explanation_message_rules_title, $explanation_message_rules_title_uid, $explanation_message_rules_title_bbcode_options);
				$explanation_message_rules_text = generate_text_for_edit($explanation_message_rules_text, $explanation_message_rules_text_uid, $explanation_message_rules_text_bbcode_options);

				$explanation_message_title = $explanation_message_title['text'];
				$explanation_message_text = $explanation_message_text['text'];
				$explanation_message_rules_title = $explanation_message_rules_title['text'];
				$explanation_message_rules_text = $explanation_message_rules_text['text'];
				
				// Restore %forum_url% and %forum_post% tags because we must change them else the BBCode URL not work if the URL is not correct
				$this->replace_all_by(
					array(
						&$explanation_message_title,
						&$explanation_message_text,
						&$explanation_message_rules_title,
						&$explanation_message_rules_text,
					),
					array(
						'http&#58;//aghxkfps&#46;com'	=> '%forum_url%',
						'http&#58;//dqsdfzef&#46;com'	=> '%forum_post%',
					));
				
				$params = array_merge($params, array(
					'explanation_message_title'				=> $explanation_message_title,
					'explanation_message_text'				=> $explanation_message_text,
					'explanation_message_rules_title'		=> $explanation_message_rules_title,
					'explanation_message_rules_text'		=> $explanation_message_rules_text,
				));
			}
			else
			{
				// Load langage
				$this->load_language_if_needed();

				// Generate all string to be displayed
				$explanation_message_title = generate_text_for_display($explanation_message_title, $explanation_message_title_uid, $explanation_message_title_bitfield, $explanation_message_title_bbcode_options);
				$explanation_message_text = generate_text_for_display($explanation_message_text, $explanation_message_text_uid, $explanation_message_text_bitfield, $explanation_message_text_bbcode_options);
				$explanation_message_rules_title = generate_text_for_display($explanation_message_rules_title, $explanation_message_rules_title_uid, $explanation_message_rules_title_bitfield, $explanation_message_rules_title_bbcode_options);
				$explanation_message_rules_text = generate_text_for_display($explanation_message_rules_text, $explanation_message_rules_text_uid, $explanation_message_rules_text_bitfield, $explanation_message_rules_text_bbcode_options);
				$explanation_message_rules_text = str_replace('%rules_text%', generate_text_for_display($forum_rules['rules'], $forum_rules['rules_uid'], $forum_rules['rules_bitfield'], $forum_rules['rules_options']), $explanation_message_rules_text);
				$explanation_message_title = str_replace('%explanation_title%', $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_MESSAGE_TITLE'), $explanation_message_title);
				$explanation_message_text = str_replace('%explanation_text%', $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_MESSAGE_TEXT') . (($params['is_explanation_display_rules'] && strlen($explanation_message_text) > 0 && strlen($explanation_message_rules_text) > 0) ? $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_MESSAGE_TEXT_RULES') : ''), $explanation_message_text);
				$explanation_message_rules_title = str_replace('%rules_title%', $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_RULES_TITLE'), $explanation_message_rules_title);
				$link_goto_forum = $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_LINK_GOTO_FORUM');
				$link_post_forum = $this->language->lang('INTRODUCIATOR_EXT_DEFAULT_LINK_POST_FORUM');

				$forum_url = append_sid("{$this->root_path}viewforum.$this->php_ext", 'f=' . $params['fk_forum_id']);
				$forum_post = append_sid("{$this->root_path}posting.$this->php_ext", 'mode=post&amp;f=' . $params['fk_forum_id']);

				// Replace in each string the predefined fields
				$this->replace_all_by(
					array(
						&$explanation_message_title,
						&$explanation_message_text,
						&$explanation_message_rules_title,
						&$explanation_message_rules_text,
					),
					array(
						'%forum_name%'			=> $forum_name,
						'http://aghxkfps.com'	=> $forum_url,	// Restore correct link
						'http://dqsdfzef.com'	=> $forum_post,	// Restore correct link
					)
				);

				// Make links into $link_goto_forum / $link_post_forum
				$this->replace_all_by(
					array(
						&$explanation_message_title,		// if text is from $this->language->lang(xx),  
						&$explanation_message_text,
						&$explanation_message_rules_title,
						&$explanation_message_rules_text,
						&$link_goto_forum,
						&$link_post_forum,
					),
					array(
						'%forum_name%'	=> $forum_name,
						'%forum_url%'	=> $forum_url,
						'%forum_post%'	=> $forum_post,
					)
				);
						
				$params = array_merge($params, array(
					'explanation_message_title'				=> $explanation_message_title,
					'explanation_message_text'				=> $explanation_message_text,
					'explanation_message_rules_title'		=> $explanation_message_rules_title,
					'explanation_message_rules_text'		=> $explanation_message_rules_text,
					'explanation_message_goto_forum'		=> $link_goto_forum,
					'explanation_message_post_forum'		=> $link_post_forum,
					'forum_name'							=> $forum_name,
					'forum_url'								=> $forum_url,
					'forum_post'							=> $forum_post,
				));
			}
		}

		return $params;
	}

	/**
	 * Verify if the posting is allowed or not.
	 *
	 * If not allowed, it redirect the current page to the introduce forum or the explanation page
	 * or error message if action is not allowed.
	 *
	 * @param $user The connected user.
	 * @param $mode posting mode, could be 'reply' or 'quote' or 'post' or 'delete', etc.
	 * @param $forum_id Forum identifier where the user try to post.
	 * @param $post_id Post's id : it cannot be deleted if it is the first one and action is delete (used only for delete), pass 0 else.
	 * @param $post_data Informations about posting (used only for delete) pass null else.
	 * @param $redirect true if the function should redirect in case of the user is not allowed to make the action, else only return status.
	 * @return true if the user is allowed to make action,
	 *         false else, in this case, just check if allowed or not (remove quick reply if not allowed).
	 *         RedirectResponse if redirection is needed.
	 */
	public function introduciator_verify_posting($user, $mode, $forum_id, $post_id, $post_data, $redirect)
	{
		$poster_id = (int) $user->data['user_id'];
		$ret_allowed_action = true;

		if ($poster_id != ANONYMOUS)
		{	// User is logged and have user authorization
			if ($this->is_introduciator_allowed())
			{	// Extension is enabled and the user is not ignored, it can do all he wants
				// Force forum id because it be moved while user delete the message
				if (empty($this->introduciator_params))
				{
					$this->introduciator_params = $this->introduciator_getparams();
				}

				if (in_array($mode, array('delete', 'soft_delete')))
				{	// Check if the user don't try to remove the first message of it's OWN introduce
					// Don't care about is_user_ignored / is_user_must_introduce_himself => Administrator / Moderator cannot delete first posts of presentation
					// else he needs to delete all the topic
					$forum_id = (!empty($post_data['forum_id'])) ? (int) $post_data['forum_id'] : (int) $forum_id;
					$post_id  = (!empty($post_data['post_id'])) ? (int) $post_data['post_id'] : (int) $post_id;
					if (!empty($post_id) && !empty($post_data['topic_id']) && $this->introduciator_params['fk_forum_id'] == $forum_id && $this->introduciator_params['is_check_delete_first_post'] && $user->data['is_registered'] && $this->auth->acl_gets('f_delete', 'm_delete', $forum_id))
					{	// This post is into the introduce forum
						// Find the topic identifier
						$sql = 'SELECT topic_id, poster_id
								FROM ' . POSTS_TABLE . '
								WHERE post_id = ' . (int) $post_id;

						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						$topic_id = (int) $row['topic_id'];
						$first_poster_id = (int) $row['poster_id'];	// <-- $poster_id could be <> from current user id
																	// It's this case when moderator try to delete post of another user
						if (!empty($topic_id) && !empty($first_poster_id))
						{	// Check if this post is the first one, ie this is the post that created the Topic
							$topic_first_post_id = (int) $post_data['topic_first_post_id'];

							if (!empty($topic_first_post_id) && $topic_first_post_id == $post_id)
							{	// The user try to delete the first post of one introduce topic : may be not allowed
								// To finish, the $first_poster_id MUST BE not ignored
								if ($this->is_user_must_introduce_himself($first_poster_id, null, $user->data['username']))
								{
									$ret_allowed_action = false;
									if ($redirect)
									{
										// Load langage
										$this->user->setup(); // Mandatory here else all forum is not in same language as user's one
										$this->load_language_if_needed();
										
										$message = $this->language->lang(($first_poster_id == $poster_id && !$this->auth->acl_get('m_delete', $forum_id)) ? 'INTRODUCIATOR_EXT_DELETE_INTRODUCE_MY_FIRST_POST' : 'INTRODUCIATOR_EXT_DELETE_INTRODUCE_FIRST_POST');
										$meta_info = append_sid("{$this->root_path}viewtopic.$this->php_ext", "f=$forum_id&amp;t=$topic_id");
										$message .= '<br/><br/>' . sprintf($this->language->lang('RETURN_TOPIC'), '<a href="' . $meta_info . '">', '</a>');
										$message .= '<br/><br/>' . sprintf($this->language->lang('RETURN_FORUM'), '<a href="' . append_sid("{$this->root_path}viewforum.$this->php_ext", "f=$forum_id") . '">', '</a>');
										trigger_error($message, E_USER_NOTICE);
									}
								}
							}
						}
					}
				}
				else if ($this->is_user_must_introduce_himself($poster_id, $this->auth, $user->data['username']))
				{
					$topic_introduce_id = 0;
					$first_post_id = 0;
					$topic_approved = false;

					if (!$this->is_user_post_into_forum($this->introduciator_params['fk_forum_id'], $poster_id, $topic_introduce_id, $first_post_id, $topic_approved))
					{	// No post into the introduce topic
						if ((in_array($mode, array('reply', 'quote')) || ($mode == 'post' && $forum_id != $this->introduciator_params['fk_forum_id'])))
						{
							if ($redirect)
							{
								if ($this->introduciator_params['is_explanation_enabled'])
								{
									redirect($this->controller_helper->route('feneck91_introduciator_explain', array('forum_id' => (int) $this->introduciator_params['fk_forum_id'])));
								}
								else
								{
									redirect(append_sid("{$this->root_path}viewforum.$this->php_ext", 'f=' . $this->introduciator_params['fk_forum_id']));
								}
							}
						}
					}
					else if (!$topic_approved && in_array($mode, array('reply', 'quote', 'post')))
					{	// At least one post but not approved !
						if (!in_array($mode, array('reply', 'quote')) || !$this->auth->acl_get('m_approve', $forum_id) || $this->introduciator_params['fk_forum_id'] != $forum_id || $this->introduciator_params['posting_approval_level'] != $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT)
						{	// Can quote / reply if the user is allowed to approval this introduction (moderator) -> Right of reply or quote is done by the framework,
							// here we just test if right are approve to don't show next message : here, the right are not correct => display the message
							$ret_allowed_action = false;
						}

						if (!$ret_allowed_action && $redirect)
						{
							// Load langage
							$this->user->setup(); // Mandatory here else all forum is not in same language as user's one
							$this->load_language_if_needed();
							
							// Test : if the user try to quote / reply into his own introduction : change the message
							if (!empty($post_data['topic_id']) && $post_data['topic_id'] == $topic_introduce_id)
							{
								$message = $this->language->lang('INTRODUCIATOR_EXT_INTRODUCE_WAITING_APPROBATION_ONLY_EDIT');
							}
							else
							{
								$message = $this->language->lang('INTRODUCIATOR_EXT_INTRODUCE_WAITING_APPROBATION');
							}
							$message .= '<br /><br />' . sprintf($this->language->lang('RETURN_FORUM'), '<a href="' . append_sid("{$this->root_path}viewforum.$this->php_ext", 'f=' . $forum_id) . '">', '</a>');
							trigger_error($message, E_USER_NOTICE);
						}
					}
					else if ($forum_id == $this->introduciator_params['fk_forum_id'] && $mode == 'post')
					{	// User try to create more than one introduce post
						$ret_allowed_action = false;
						if ($redirect)
						{
							// Load langage
							$this->user->setup(); // Mandatory here else all forum is not in same language as user's one
							$this->load_language_if_needed();
							
							$message = $this->language->lang('INTRODUCIATOR_EXT_INTRODUCE_MORE_THAN_ONCE');
							$message .= '<br /><br />' . sprintf($this->language->lang('RETURN_FORUM'), '<a href="' . append_sid("{$this->root_path}viewforum.$this->php_ext", 'f=' . $forum_id) . '">', '</a>');
							trigger_error($message, E_USER_NOTICE);
						}
					}
				}
			}
		}

		return $ret_allowed_action;
	}

	/**
	 * Get informations about the user.
	 *
	 * Is used by several pages to display link to the member presentation. It indicate if the user has introduce himself or not,
	 * the text and tooltip info, etc.
	 *
	 * @param $poster_id The poster id
	 * @param $poster_name The poster name
	 * @return Array with :
	 * <ul>
	 *   <li>display : true if the user must introduce himself, false else.</li>
	 *   <li>url : url to member introduction, empty string if user has no presentation.</li>
	 *   <li>text : Text used to display the tooltip for the button.</li>
	 *   <li>class : class to use for the button.</li>
	 *   <li>pending : true if message is pending approval, false else.</li>
	 * </ul>.
	 */
	public function introduciator_get_user_infos($poster_id, $poster_name)
	{
		$display = false;
		$url = false;
		$text = '';
		$class = '';
		$pending = false;

		if ($this->is_introduciator_allowed())
		{
			if (empty($this->introduciator_params))
			{
				$this->introduciator_params = $this->introduciator_getparams();
			}

			if ($this->is_user_must_introduce_himself($poster_id, $this->auth, $poster_name))
			{
				$display = true;
				$topic_id = 0;
				$first_post_id = 0;
				$topic_approved = false;

				// Load langage
				$this->load_language_if_needed();
				
				if (!$this->is_user_post_into_forum($this->introduciator_params['fk_forum_id'], $poster_id, $topic_id, $first_post_id, $topic_approved))
				{	// No post into the introduce topic
					$text = $this->language->lang('INTRODUCIATOR_TOPIC_VIEW_NO_PRESENTATION');
					$class = 'introdno-icon';
				}
				else if ($topic_approved)
				{
					$text = $this->language->lang('INTRODUCIATOR_TOPIC_VIEW_PRESENTATION');
					$url = append_sid("{$this->root_path}viewtopic.$this->php_ext", 'f=' . $this->introduciator_params['fk_forum_id'] . '&amp;t=' . $topic_id . '#p' . $first_post_id);
					$class = 'introd-icon';
				}
				else
				{
					$text = $this->language->lang('INTRODUCIATOR_TOPIC_VIEW_APPROBATION_PRESENTATION');
					$pending = true;
					if ($this->auth->acl_get('m_approve', $this->introduciator_params['fk_forum_id']) || ($this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT && $poster_id == (int) $this->user->data['user_id']))
					{	// Display url if user can approve the introduction of this user
						// or if the current user is the poster (the user can see its own presentation) AND the extension configuration is INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT
						$url = append_sid("{$this->root_path}viewtopic.$this->php_ext", 'f=' . $this->introduciator_params['fk_forum_id'] . '&amp;t=' . $topic_id . '#p' . $first_post_id);
						$class = 'introdpu-icon';
					}
					else
					{
						$class = 'introdpd-icon';
					}
				}
			}
		}

		return array(
			'display'		=> $display,
			'url'			=> $url,
			'text'			=> $text,
			'class'			=> $class,
			'pending'		=> $pending,
		);
	}

	/**
	 * Verify if the posting is must be approved or not.
	 *
	 * If the user that post have right to approved it's own presentation,
	 * the function return always false: no need to make manage approval to a user
	 * that can approve himself its own message.
	 * 
	 * @param $user The user informations
	 * @param $mode posting mode, could be 'reply' or 'quote' or 'post' or 'delete', etc
	 * @param $forum_id Forum identifier where the user try to post
	 * @return true if the post must be approved, false else.
	 */
	public function introduciator_is_posting_must_be_approved($user, $mode, $forum_id)
	{
		return !$this->auth->acl_get('m_approve', $forum_id) && $this->introduciator_get_posting_approval_level($user, $mode, $forum_id) != $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_NO_APPROVAL;
	}

	/**
	 * Check if the user try to reply / quote to an unapproved message.
	 * 
	 * Usually, no user is able to reply / quote to an unapproved message. When trying,
	 * an error message indicate that the user is not able to do this action.
	 * This function test if the mode is INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT and
	 * if the logged user have the capability to approved the message, and the message is a presentation
	 * message. In this case, the error message is bypass.
	 * 
	 * @param type $user Logged user informations
	 * @param type $forum_id Forum's ID
	 * @param $mode Current reply mode (quote / bump / reply)
	 * @return true if the error message should be bypassed, false else.
	 */
	public function introduciator_ignore_topic_unapproved($user, $forum_id, $mode)
	{
		$ret = false;
		if ($this->is_introduciator_allowed())
		{	// Introduciator is activated and $sql_approved has filter
			if (empty($this->introduciator_params))
			{	// Retrieve extension parameters
				$this->introduciator_params = $this->introduciator_getparams();
			}

			if (in_array($mode, array('reply', 'quote')) && $this->auth->acl_get('m_approve', $forum_id) && $this->introduciator_params['fk_forum_id'] == $forum_id && $this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT)
			{	// Into the introduce forum, the moderator can approve this message and can edit / reply
				$ret = true;
			}
		}
		
		return $ret;
	}

	/**
	 * Generate the request to make topic visible to user when the topic owned by the user and is into
	 * approval state (only for INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT configuration).
	 *
	 * @param $user The user informations
	 * @param $forum_id Forum identifier to be displayed or null to don't filter on forum's id
	 * @param $sql_approved Current sql approved.
	 * @param $table_name Table name used for SQL request, it can be 't' ou 'p' or other. Empty if not needed.
	 * @param $approve_fid_ary Used to retrieve approve_fid_ary if needed, else pass null or ignore parameter.
	 * @return The SQL modified request to be able to see the unapproved user presentation.
	 */
	public function introduciator_generate_sql_approved_for_forum($user, $forum_id, $sql_approved, $table_name, &$approve_fid_ary = null)
	{
		if (!empty($sql_approved) && $this->is_introduciator_allowed())
		{	// Introduciator is activated and $sql_approved has filter
			if (empty($this->introduciator_params))
			{	// Retrieve extension parameters
				$this->introduciator_params = $this->introduciator_getparams();
			}
			
			if (($forum_id === null || $this->introduciator_params['fk_forum_id'] == $forum_id) && $this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT)
			{
				$poster_id = (int) $user->data['user_id'];
				if ($this->is_user_must_introduce_himself($poster_id, $this->auth, $user->data['username']))
				{
					$topic_id = 0;
					$first_post_id = 0;
					$topic_approved = false;

					if ($this->is_user_post_into_forum($this->introduciator_params['fk_forum_id'], $poster_id, $topic_id, $first_post_id, $topic_approved))
					{	// Post into this introduce topic
						if (!$topic_approved)
						{
							$sql_approved = str_replace('AND ', 'AND (', $sql_approved) . ' OR ' . (empty($table_name) ? '' : $table_name . '.') . 'topic_id = ' . (int) $topic_id . ')';
							if ($approve_fid_ary !== null)
							{
								$approve_fid_ary = array($topic_id);
							}
						}
					}
				}
			}
		}

		return $sql_approved;
	}

	/**
	 * Test if the topic into the forum is unapproved and contains current introduce of logged user.
	 *
	 * This function is used only for INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT level.
	 * 
	 * @param $user The user informations
	 * @param $forum_id Forum identifier
	 * @param $topic_id topic identifier
	 * @param $check_moderator_permissions If set to true, the function check moderator permissions to reply true or false.
	 * @return true if the topic_id is the presentation of the logged user and is not yet approved.
	 *         If should return true and check_moderator_permissions is set to true, this function also return false if the user has moderator privilege (to
	 *         let approval fields visible).
	 */
	public function introduciator_is_topic_in_forum_is_unapproved_for_introduction($user, $forum_id, $topic_id, $check_moderator_permissions)
	{
		$ret = false;
		if ($this->is_introduciator_allowed())
		{	// Introduciator is activated
			if (empty($this->introduciator_params))
			{	// Retrieve extension parameters
				$this->introduciator_params = $this->introduciator_getparams();
			}

			if ($this->introduciator_params['fk_forum_id'] == $forum_id && $this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT)
			{
				$poster_id = (int) $user->data['user_id'];
				if ($this->is_user_must_introduce_himself($poster_id, $this->auth, $user->data['username']))
				{
					$topic_introduce_id = 0;
					$first_post_id = 0;
					$topic_approved = false;

					if ($this->is_user_post_into_forum($this->introduciator_params['fk_forum_id'], $poster_id, $topic_introduce_id, $first_post_id, $topic_approved))
					{	// Post into this introduce forum, retrieve informations about topic_id and topic approved or not
						if (!$topic_approved && $topic_id == $topic_introduce_id)
						{	// This topic is unaproved and is the introduce of the current logged user
							if ($check_moderator_permissions)
							{
								$ret = !$this->auth->acl_get('m_approve', $forum_id);
							}
							else
							{
								$ret = true;
							}
						}
					}
				}
			}
		}
		
		return $ret;
	}

	/**
	 * Check if the user have already posted into this forum.
	 *
	 * It must be the creator of one topic into the configured forum.
	 *
	 * @param type $forum_id Forum's ID
	 * @param type $user_id User's ID
	 * @param $topic_id If this function returns true, it contains the Topic ID where the user hast post it's presentation
	 * @param $first_post_id If this function returns true, it contains the post ID of the post that has created the topic
	 * @param $topic_approved If this function returns true, it contains true / false if the topic is approved or not
	 * @return true if the user already post at least one message into this forum, false else
	 */
	protected function is_user_post_into_forum($forum_id, $user_id, &$topic_id, &$first_post_id, &$topic_approved)
	{
		// Visibility state : ITEM_UNAPPROVED / ITEM_APPROVED / ITEM_DELETED / ITEM_REAPPROVE
		$sql = 'SELECT topic_id, topic_first_post_id, topic_visibility
				FROM ' . TOPICS_TABLE . '
					WHERE topic_poster = ' . (int) $user_id . '
					 AND forum_id = ' . (int) $forum_id . '
					 AND topic_visibility <> ' . ITEM_DELETED . '
					 AND topic_first_post_id <> 0'; // PATCH : Sometimes, the topic_first_post_id is 0

		$result = $this->db->sql_query($sql);
		$topic_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if ($topic_row !== false)
		{
			$topic_id = $topic_row['topic_id'];
			$first_post_id = $topic_row['topic_first_post_id'];
			$topic_approved = $topic_row['topic_visibility']; // Change into phpBB 3.1.x => topic_approved replaced by topic_visibility
		}

		return $topic_row !== false; // Return true or false
	}

	/**
	 * Test if one of the user's groups has been selected into configuration.
	 *
	 * These groups are selected into ACP, recorded into INTRODUCIATOR_GROUPS_TABLE table.
	 * Call group_memberships function into includes/functions_user.php file.
	 *
	 * @param $user_id User identifier into database
	 * @return true if one of the user's group has been selected into configuration, false else
	 */
	protected function is_user_in_groups_selected($user_id)
	{
		$sql = 'SELECT *
				FROM ' . $this->Get_INTRODUCIATOR_GROUPS_TABLE();
		$result = $this->db->sql_query($sql);

		// Construct an array of group ID present into INTRODUCIATOR_GROUPS_TABLE table
		$arr_groups_id = array();
		while ($row = $this->db->sql_fetchrow($result))
		{	// Merge array
			array_push($arr_groups_id, $row['fk_group']);
		}
		$this->db->sql_freeresult($result);

		// Testing
		return group_memberships($arr_groups_id, (int) $user_id, true);
	}

	/**
	 * Check if the user is ignored or must introduce himself.
	 *
	 * Check if it contains include groups or if doesn't contains exclude group.
	 * Check if it doesn't contains name of ignored username list.
	 *
	 * @param $poster_id User's ID
	 * @param $poster_name User's name
	 * @return true if the user is ignored, false else
	 */
	protected function is_user_ignored($poster_id, $poster_name)
	{
		if (empty($this->introduciator_params))
		{
			$this->introduciator_params = $this->introduciator_getparams();
		}
		
		// Check if :
		//	1 : Include group is ON and the user is member of at least one group of the selected groups (include groups)
		//	2 : Include group is OFF (exclude) and the user is not member of one group of the selected groups (exclude groups)
		$is_in_group_selected = $this->is_user_in_groups_selected($poster_id);
		$user_ignored = true;

		// User is in selected group or out of selected group ?
		if (($this->introduciator_params['is_include_groups'] && $is_in_group_selected) || (!$this->introduciator_params['is_include_groups'] && !$is_in_group_selected))
		{
			$user_ignored = in_array(utf8_strtolower($poster_name), explode("\n", utf8_strtolower($this->introduciator_params['ignored_users'])));
		}

		return $user_ignored;
	}

	/**
	 * Check if the user is ignored or must introduce himself.
	 *
	 * Check if it contains include groups or if doesn't contains exclude group.
	 * Check if it doesn't contains name of ignored username list.
	 *
	 * @param $poster_id User's ID
	 * @param $authorisations User's authorisations
	 * @param $poster_name User's name
	 * @return true if the user must introduce himself pending of rights, false else
	 */
	protected function is_user_must_introduce_himself($poster_id, $authorisations, $poster_name)
	{
		$ret = false;

		if (empty($this->introduciator_params))
		{
			$this->introduciator_params = $this->introduciator_getparams();
		}
		
		if ($this->introduciator_params['is_use_permissions'])
		{
			if ($authorisations === null)
			{
				$sql = 'SELECT user_id, username, user_permissions, user_type
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . (int) $poster_id;
				$result = $this->db->sql_query($sql);
				$userdata = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$userdata)
				{
					$this->user->setup(); // Mandatory here else all forum is not in same language as user's one
					
					trigger_error('NO_USERS', E_USER_ERROR);
				}

				$authorisations = new auth();
				$authorisations->acl($userdata);
				$this->auth = $authorisations;
			}

			$ret = $authorisations->acl_get('u_must_introduce');
		}
		else
		{
			$ret = !$this->is_user_ignored($poster_id, $poster_name);
		}

		return $ret;
	}

	/**
	 * Get the approval level for the post using introduciator configuration.
	 *
	 * @param $user The user informations
	 * @param $mode posting mode, could be 'reply' or 'quote' or 'post' or 'delete', etc
	 * @param $forum_id Forum identifier where the user try to post
	 * @return The approval level for this post, depending of extension configuration.
	 */
	protected function introduciator_get_posting_approval_level($user, $mode, $forum_id)
	{
		$poster_id = (int) $user->data['user_id'];
		$ret_posting_approval_level = $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_NO_APPROVAL;

		if ($poster_id != ANONYMOUS)
		{	// User is logged and have user authorization
			if ($this->is_introduciator_allowed())
			{	// Extension is enabled and the user is not ignored, it can do all he wants
				// Force forum id because it be moved while user delete the message
				if (empty($this->introduciator_params))
				{
					$this->introduciator_params = $this->introduciator_getparams();
				}

				if ($this->is_user_must_introduce_himself($poster_id, $this->auth, $user->data['username']))
				{
					$topic_id = 0;
					$first_post_id = 0;
					$topic_approved = false;

					if (!$this->is_user_post_into_forum($this->introduciator_params['fk_forum_id'], $poster_id, $topic_id, $first_post_id, $topic_approved))
					{	// No post into the introduce topic
						if ($mode == 'post' && $forum_id == $this->introduciator_params['fk_forum_id'] && ($this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL || $this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT))
						{
							$ret_posting_approval_level = $this->introduciator_params['posting_approval_level'];
						}
					}
				}
			}
		}

		return $ret_posting_approval_level;
	}

	/**
	 * Get the approval level for the post using introduciator configuration.
	 *
	 * @param $user The user informations
	 * @param $forum_id Forum identifier where the user try to post
	 * @param $where_sql Current SQL WHERE used, must be concatenate with it.
	 * @param $mode topic or post.
	 * @param $table_alias alias table to use.
	 * @param $get_visibility_sql_overwrite Contains the SQL to send to get correct topic visibility if the function returns true.
	 * @return true if the sql visibility mmust be overwrite, false else.
	 */
	public function get_topic_sql_visibility($user, $forum_id, $where_sql, $mode, $table_alias, &$get_visibility_sql_overwrite)
	{
		$poster_id = (int) $user->data['user_id'];
		$ret = false;

		if ($poster_id != ANONYMOUS && !$this->auth->acl_get('m_approve', $forum_id))
		{	// User is logged and have user authorization
			// If the user has m_approve right, nothing to do, he will see the topic
			if ($this->is_introduciator_allowed())
			{	// Extension is enabled
				if (empty($this->introduciator_params))
				{
					$this->introduciator_params = $this->introduciator_getparams();
				}

				if ($forum_id == (int) $this->introduciator_params['fk_forum_id'] && $this->introduciator_params['posting_approval_level'] == $this::INTRODUCIATOR_POSTING_APPROVAL_LEVEL_APPROVAL_WITH_EDIT && $this->is_user_must_introduce_himself($poster_id, $this->auth, $user->data['username']))
				{	// It is the forum with approval level + edit and user should introduce himself
					$topic_id = 0;
					$first_post_id = 0;
					$topic_approved = false;

					if ($this->is_user_post_into_forum($forum_id, $poster_id, $topic_id, $first_post_id, $topic_approved))
					{	// Is is the introduce forum and he post into it
						if (!$topic_approved)
						{	// The topic is waiting approval: the user is allowed to see and modify it's own message into this mode
							$ret = true;
							$get_visibility_sql_overwrite = $where_sql . '(' . $table_alias . $mode . '_visibility = ' . ITEM_APPROVED . ' OR ' . $table_alias . 'topic_id = ' . $topic_id . ')';
						}
					}
				}
			}
		}

		return $ret;
	}
	
	/**
	 * Get the approval level for the post using introduciator configuration.
	 *
	 * @param $user The user informations
	 * @param $forum_id Forum identifier where the user try to post
	 * @param $where_sql Current SQL WHERE used, must be concatenate with it.
	 * @param $mode topic or post.
	 * @param $table_alias alias table to use.
	 * @param $get_visibility_sql_overwrite Contains the SQL to send to get correct topic visibility if the function returns true.
	 * @return true if the sql visibility mmust be overwrite, false else.
	 */
	public function introduciator_let_user_posting_or_editing($user, $mode, $forum_id, $topic_id, $post_data)
	{
		return $this->introduciator_verify_posting($user, $mode, $forum_id, 0, $post_data, true);
	}
}
?>