<?php

/**
*
* @package phpBB Extension - Introduciator Extension
* @author Feneck91 (Stéphane Château) feneck91@free.fr
* @copyright (c) 2019 Feneck91
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace feneck91\introduciator\migrations;

class introduciator_migration_2_0_0 extends \phpbb\db\migration\migration
{
	/**
	 * Add the table schema to the database
	 *
	 * Only add the introduciator group table is added
	 *
	 * Return an array of table schema to create / update
	 *
	 * @return array
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			// Add Groups list table
			'add_tables' => array(
				$this->table_prefix . 'introduciator_groups' => array(
					'COLUMNS' => array(
						'fk_group'			=> array('UINT', null),
					),
				),
				$this->table_prefix . 'introduciator_explanation' => array(
					'COLUMNS'		=> array(
						'id'							=> array('UINT', null, 'auto_increment'),
						'lang'							=> array('VCHAR:30', ''),
						'message_title'					=> array('MTEXT_UNI', ''),
						'message_title_uid'				=> array('VCHAR:8', ''),
						'message_title_bitfield'		=> array('VCHAR:255', ''),
						'message_title_bbcode_options'	=> array('VCHAR:255', ''),
						'message_text'					=> array('MTEXT_UNI', ''),
						'message_text_uid'				=> array('VCHAR:8', ''),
						'message_text_bitfield'			=> array('VCHAR:255', ''),
						'message_text_bbcode_options'	=> array('VCHAR:255', ''),
						'rules_title'					=> array('MTEXT_UNI', ''),
						'rules_title_uid'				=> array('VCHAR:8', ''),
						'rules_title_bitfield'			=> array('VCHAR:255', ''),
						'rules_title_bbcode_options'	=> array('VCHAR:255', ''),
						'rules_text'					=> array('MTEXT_UNI', ''),
						'rules_text_uid'				=> array('VCHAR:8', ''),
						'rules_text_bitfield'			=> array('VCHAR:255', ''),
						'rules_text_bbcode_options'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),
		);
	}

	/**
	 * Drop the Introduciator groups table schema from the database.
	 *
	 * @return array Array of table schema to revert
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			// Remove table
			'drop_tables' => array(
				$this->table_prefix . 'introduciator_groups',
				$this->table_prefix . 'introduciator_explanation',
			),
		);
	}

	/**
	 * Update data of the databse.
	 *
	 * @return array Array of elements to update.
	 * @access public
	 */
	public function update_data()
	{
		return array(
			// Introduciator Settings
			array('config.add', array('introduciator_posting_approval_level', 0)),
			array('config.add', array('introduciator_allow', '0')),
			array('config.add', array('introduciator_fk_forum_id', 0)),
			array('config.add', array('introduciator_is_introduction_mandatory', true)),
			array('config.add', array('introduciator_is_check_delete_first_post', true)),
			array('config.add', array('introduciator_is_explanation_enabled', false)),
			array('config.add', array('introduciator_is_use_permissions', true)),
			array('config.add', array('introduciator_is_include_groups', true)),
			array('config.add', array('introduciator_ignored_users', '')),
			array('config.add', array('introduciator_is_explanation_display_rules', true)),

			// Misc Settings
			array('config.add', array('introduciator_install_date', time())),

			// Add admin permissions
			array('permission.add', array('a_introduciator_manage', true)),

			// Add user permissions
			array('permission.add', array('u_must_introduce', true)),

			// Set permissions users
			array('permission.permission_set', array('ADMINISTRATORS', 'u_must_introduce', 'group', false)), // Set to never for adminitrators
			array('permission.permission_set', array('GLOBAL_MODERATORS', 'u_must_introduce', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_must_introduce', 'group')),
			array('permission.permission_set', array('NEWLY_REGISTERED', 'u_must_introduce', 'group')),

			// Set permissions administration
			array('permission.permission_set', array('ADMINISTRATORS', 'a_introduciator_manage', 'group')),

			// Global user role permissions for user mask
			array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_must_introduce', 'role')),
			array('permission.permission_set', array('ROLE_USER_LIMITED', 'u_must_introduce', 'role')),
			array('permission.permission_unset', array('ROLE_USER_FULL', 'u_must_introduce', 'role')),	// Set to no for adminitrators
			array('permission.permission_set', array('ROLE_USER_NOPM', 'u_must_introduce', 'role')),
			array('permission.permission_set', array('ROLE_USER_NOAVATAR', 'u_must_introduce', 'role')),
			array('permission.permission_set', array('ROLE_USER_NEW_MEMBER', 'u_must_introduce', 'role')),

			// Global admin role permissions for admin
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_introduciator_manage', 'role')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_introduciator_manage', 'role')),

			//===============================================================================
			// Add the module in ACP under the customise tab

			// Add a new category named ACP_INTRODUCIATOR_EXTENSION to ACP_CAT_DOT_MODS (under tab 'extensions' in ACP)
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_INTRODUCIATOR_EXTENSION')),

			array('module.add', array(
					'acp',
					'ACP_INTRODUCIATOR_EXTENSION',
					array(
						'module_basename'	=> '\feneck91\introduciator\acp\introduciator_module',
						'modes'	  			=> array(
							//---------------------------------------------------------------------
							// Creation of ACP sub caterories under Introduciator extension into Extensions tab
							'general',
							'configuration',
							'explanation',
							'statistics',
							// Creation of ACP sub caterories under Introduciator extension into Extensions tab
							//---------------------------------------------------------------------
							),
					),
				)),

			// Add the module in ACP under the customise tab
			//===============================================================================
		);
	}
}
