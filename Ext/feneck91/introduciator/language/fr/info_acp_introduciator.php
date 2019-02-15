<?php
/**
 * info_acp_introduciator.php [Français]
 *
 * @package phpBB Extension - Introduciator Extension (Présentation forcée)
 * @author Feneck91 (Stéphane Château) feneck91@free.fr
 * @copyright (c) 2019 Feneck91
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

/**
* mode: main : le nom de l'extension
*/
$lang = array_merge($lang, array(
	'ACP_INTRODUCIATOR_EXTENSION'					=> 'Présentation forcée',

/**
* Titres présents dans la partie gauche de l'onglet extensions de l'ACP sous l'item INTRODUCIATOR
*/
	'INTRODUCIATOR_GENERAL'							=> 'Général',
	'INTRODUCIATOR_CONFIGURATION'					=> 'Configuration',
	'INTRODUCIATOR_EXPLANATION'						=> 'Explication',
	'INTRODUCIATOR_STATISCICS'						=> 'Statistiques',

/**
* mode: general
* Info: Les clefs de langages sont préfixés avec 'INTRODUCIATOR_GP_' pour 'INTRODUCIATOR_GENERAL_PAGES_'
*/
	'INTRODUCIATOR_GP_TITLE'						=> 'Informations générales',
	'INTRODUCIATOR_GP_TITLE_EXPLAIN'				=> 'Donne la version courante de cette extension.',

	'INTRODUCIATOR_GP_VERSION_NOT_UP_TO_DATE_TITLE'	=> 'Votre installation de l’extension Présentation forcée n’est pas à jour.',
	'INTRODUCIATOR_GP_STATS'						=> 'Statistiques de l’extension Présentation forcée',
	'INTRODUCIATOR_GP_INSTALL_DATE'					=> 'Date d’installation de l’extension <strong>Présentation forcée</strong> :',
	'INTRODUCIATOR_GP_VERSION'						=> 'Version de l’extension <strong>Présentation forcée</strong> :',
	'INTRODUCIATOR_GP_UPDATE_VERSION_TITLE'			=> 'Dernière version :',
	'INTRODUCIATOR_GP_UPDATE_URL_TITLE'				=> 'Lien de téléchargement :',
	'INTRODUCIATOR_GP_UPDATE_INFOS_TITLE'			=> 'Information de mise à jour :',

/**
* mode: configuration
* Info: Les clefs de langages sont préfixés avec 'INTRODUCIATOR_CP_' pour 'INTRODUCIATOR_CONFIGURATION_PAGES_'
*/
	'INTRODUCIATOR_CP_TITLE'						=> 'Configuration de la Présentation forcée',
	'INTRODUCIATOR_CP_TITLE_EXPLAIN'				=> 'Permet de configurer le fonctionnement de l’extension.',

/**
* mode: statistics
* Info: Les clefs de langages sont préfixés avec 'INTRODUCIATOR_ST_' pour 'INTRODUCIATOR_STATISTICS_PAGES_'
*/
	'INTRODUCIATOR_ST_TITLE'						=> 'Statistiques et vérifications sur les présentations des membres',
	'INTRODUCIATOR_ST_TITLE_EXPLAIN'				=> 'Permet d’afficher les informations de la base de données :
														<ul>
														<li>Les statistiques sur les présentations.</li>
														<li>La vérification de la base de données concernant les présentations (vérification que les utilisateurs n’ont pas postés plus d’une seule présentation).</li>
														</ul>',
	
/**
* mode: configuration : Edit
* Info: Les clefs de langages sont préfixés avec 'INTRODUCIATOR_CP_ED_' pour 'INTRODUCIATOR_CONFIGURATION_PAGES_EDIT_'
*/
	// Titres
	'GENERAL_OPTIONS_MANAGE_GROUPS_AND_USERS'						=> 'Configuration des groupes et des utilisateurs',
	'GENERAL_OPTIONS_EXPLANATION_SETTINGS'							=> 'Configuration de la page d’explications',
	'GENERAL_OPTIONS_EXPLANATION_TEXTS'								=> 'Configuration des textes de la page d’explications',
	'GENERAL_OPTIONS_EXPLANATION_TEXTS_EXPLAIN'						=> 'Pour tous les champs textes suivants, vous pouvez utiliser :<br/>
																		<ul>
																		<li><b>%forum_name%</b> : nom du forum de présentation</li>
																		<li><b>%forum_url%</b> : url vers le forum de présentation</li>
																		<li><b>%forum_post%</b> : url pour l’écriture d’un nouveau post dans le forum de présentation</li>
																		</ul>
																		Vous pouvez utiliser les BBcodes pour créer les messages.<br/>
																		<br/>
																		<u>Exemples :</u>
																		<ul>
																		<li>Créer un lien vers le forum de présentation : <i>[url=<b>%forum_url%</b>]Cliquez ici pour aller au forum ’<b>%forum_name%</b>’[/url]</i>
																		<li>Créer un lien de création du sujet dans le forum de présentation : <i>[url=<b>%forum_post%</b>]Cliquez ici pour créer un nouveau sujet dans le forum ’<b>%forum_name%</b>’[/url]</i>
																		</ul>
																		<br/>',
	// Sous-titres
	'INTRODUCIATOR_CP_ED_EXTENSION_ACTIVATED'						=> 'Activer l’extension',
	'INTRODUCIATOR_CP_ED_EXTENSION_ACTIVATED_EXPLAIN'				=> 'Est utilisé pour activer ou désactiver cette extension.',
	'INTRODUCIATOR_CP_ED_MANDATORY_INTRODUCE'						=> 'Forcer l’utilisateur à se présenter',
	'INTRODUCIATOR_CP_ED_MANDATORY_INTRODUCE_EXPLAIN'				=> 'Quand cet option est activée, l’extension force l’utilisateur à poster sa propre présentation avant d’être autorisé à poster dans les autres sujets.
																		<br/>Si cette fonctionalité n’est pas activée, toutes les autres options restent actives.',
	'INTRODUCIATOR_CP_ED_CHECK_DEL_1ST_POST'						=> 'Autorise l’extension à vérifier la suppression du premier message d’un sujet dans le forum de présentation',
	'INTRODUCIATOR_CP_ED_CHECK_DEL_1ST_POST_EXPLAIN'				=> 'Lorsque cette option est activée, l’extension empèche la suppression du premier message qui a créé le sujet dans le forum de présentation.
																		<br/>Même les modérateurs et les administrateurs n’ont pas cette permission pour être certain que le premier message du sujet est la présentation du membre. Il reste toutefois possible de supprimer le sujet si les permissions le permettent.
																		<br/>Vous pouvez désactiver cette option mais dans ce cas un membre peut avoir plusieurs présentations. Il est recommandé d’activer cette option.',
	'INTRODUCIATOR_CP_ED_FORUM_CHOICE'								=> 'Choix du forum où l’utilisateur doit se présenter',
	'INTRODUCIATOR_CP_ED_FORUM_CHOICE_EXPLAIN'						=> 'Est utilisé pour connaître quel forum doit être testé pour savoir si un utilisateur s’est déjà présenté ou pas.',
	'INTRODUCIATOR_CP_ED_POSTING_APPROVAL_LEVEL'					=> 'Options d’approbation de la présentation',
	'INTRODUCIATOR_CP_ED_POSTING_APPROVAL_LEVEL_EXPLAIN'			=> 'Est utilisé pour forcer l’approbation de la présentation par un modérateur :<br/>
																		<ul>
																		<li><b>Pas d’approbation</b> : ne force pas l’approbation de la présentation, il laisse le traitement par défaut.</li>
																		<li><b>Approbation simple</b> : force l’approbation de la présentation. L’utilisateur ne voit pas sa présentation jusqu’à ce qu’elle soit validée par un modérateur (traitement normal de tous les messages nécessitants une approbation).</li>
																		<li><b>Approbation avec édition</b> : force l’approbation de la présentation. L’utilisateur voit sa présentation immédiatement et peut la modifier. Il ne peut pas poster ailleurs tant qu’elle n’est pas validée par un modérateur. Ceci permet aux modérateurs et à l’utilisateur d’échanger afin que ce dernier puisse mettre son message en conformité avant validation par le modérateur (traitement différent des messages nécessitant une approbation). Seule l’édition est autorisée. Répondre et citer sont interdit.</li>
																		</ul>',
	'INTRODUCIATOR_CP_ED_TEXT_POSTING_NO_APPROVAL'					=> 'Pas d’approbation',
	'INTRODUCIATOR_CP_ED_TEXT_POSTING_APPROVAL'						=> 'Approbation simple',
	'INTRODUCIATOR_CP_ED_TEXT_POSTING_APPROVAL_WITH_EDIT'			=> 'Approbation avec édition',
	'INTRODUCIATOR_CP_ED_DISPLAY_EXPLANATION_PAGE'					=> 'Afficher la page d’explications',
	'INTRODUCIATOR_CP_ED_DISPLAY_EXPLANATION_PAGE_EXPLAIN'			=> 'Est utilisé pour afficher la page d’explications si l’utilisateur tente de poster dans un autre forum que celui des présentations.',

	'INTRODUCIATOR_CP_ED_USE_PERMISSIONS'							=> 'Utiliser les permissions de phbBB',
	'INTRODUCIATOR_CP_ED_USE_PERMISSIONS_EXPLAIN'					=> 'Vous pouvez utiliser les permissions de phpBB pour indiquer si un utilisateur doit se présenter ou utiliser la configuration de l’extension (plus simple mais moins performant).<br/><br/>Lorsque l’option « Utiliser les permissions du forums » est sélectionné, la configuration ci-dessous est ignorée.',
	'INTRODUCIATOR_CP_ED_USE_PERMISSION_OPTION'						=> 'Utiliser les permissions du forum',
	'INTRODUCIATOR_CP_ED_NOT_USE_PERMISSION_OPTION'					=> 'Utiliser la configuration de l’extension',
	'INTRODUCIATOR_CP_ED_INCLUDE_EXCLUDE_GROUPS'					=> 'Groupes inclus ou groupes exclus',
	'INTRODUCIATOR_CP_ED_INCLUDE_EXCLUDE_GROUPS_EXPLAIN'			=> 'Lorsque les groupes inclus sont sélectionnés, seuls les utilisateurs des groupes sélectionnés doivent se présenter.<br/>Lorsque les groupes exclus sont sélectionnés, seuls les utilisateurs ne faisant pas parti des groupes sélectionnés doivent se présenter.',
	'INTRODUCIATOR_CP_ED_INCLUDE_GROUPS_OPTION'						=> 'Groupes inclus',
	'INTRODUCIATOR_CP_ED_EXCLUDE_GROUPS_OPTION'						=> 'Groupes exclus',
	'INTRODUCIATOR_CP_ED_SELECTED_GROUPS'							=> 'Sélection des groupes',
	'INTRODUCIATOR_CP_ED_SELECTED_GROUPS_EXPLAIN'					=> 'Sélectionne les groupes qui doivent être inclus ou exclus.',
	'INTRODUCIATOR_CP_ED_IGNORED_USERS'								=> 'Utilisateurs ignorés',
	'INTRODUCIATOR_CP_ED_IGNORED_USERS_EXPLAIN'						=> 'Liste des utilisateurs qui ne sont pas obligés de se présenter.<br/>Entrez un utilisateur par ligne.<br/>Utilisé pour les comptes d’administrations ou de tests par exemple.',

	'INTRODUCIATOR_CP_ED_EXPLANATION_MESSAGE_TITLE'					=> 'Titre de la page d’explications',
	'INTRODUCIATOR_CP_ED_EXPLANATION_MESSAGE_TITLE_EXPLAIN'			=> 'Défaut = <b>%explanation_title%</b><br/>Vous pouvez changer le texte pour mettre celui de votre choix.',
	'INTRODUCIATOR_CP_ED_EXPLANATION_MESSAGE_TEXT'					=> 'Texte de la page d’explications',
	'INTRODUCIATOR_CP_ED_EXPLANATION_MESSAGE_TEXT_EXPLAIN'			=> 'Défaut = <b>%explanation_text%</b><br/>Vous pouvez changer le texte pour mettre celui de votre choix.',
	'INTRODUCIATOR_CP_ED_EXPLANATION_DISPLAY_RULES_ENABLED'			=> 'Activer l’affichage des règles du forum de présentation',
	'INTRODUCIATOR_CP_ED_EXPLANATION_DISPLAY_RULES_ENABLED_EXPLAIN'	=> 'Permet d’afficher les règles du forum de présentation dans la page d’explication.',
	'INTRODUCIATOR_CP_ED_EXPLANATION_RULES_TITLE'					=> 'Titre de la présentation des règles',
	'INTRODUCIATOR_CP_ED_EXPLANATION_RULES_TITLE_EXPLAIN'			=> 'Défaut = <b>%rules_title%</b><br/>Vous pouvez changer le texte pour mettre celui de votre choix.',
	'INTRODUCIATOR_CP_ED_EXPLANATION_RULES_TEXT'					=> 'Texte des règles du forum de présentation',
	'INTRODUCIATOR_CP_ED_EXPLANATION_RULES_TEXT_EXPLAIN'			=> 'Défaut = <b>%rules_text%</b><br/>Par défaut %rules_text% est remplacé par le texte des règles du forum de présentation.<br/>Vous pouvez changer le texte pour mettre celui de votre choix.',

	'INTRODUCIATOR_ST_USER'											=> 'UTILISATEUR',
	'INTRODUCIATOR_ST_DATE'											=> 'DATE',
	'INTRODUCIATOR_ST_INTRODUCE'									=> 'PRÉSENTATIONS',
/**
* Autres
*/
	'INTRODUCIATOR_NO_FORUM_CHOICE'							=> '',
	'INTRODUCIATOR_NO_FORUM_CHOICE_TOOLTIP'					=> 'Aucun forum sélectionné, à utiliser uniquement lorsque l’extension est désactivé',
	'INTRODUCIATOR_ERROR_MUST_SELECT_FORUM'					=> 'Lorsque cette extension est activé vous devez choisir un forum !',
	'INTRODUCIATOR_NO_UPDATE_INFO_FOUND'					=> 'Aucune information de mise à jour disponible',
	'INTRODUCIATOR_CHECK'									=> 'Vérifier',
	'INTRODUCIATOR_NOT_ENABLED_FOR_STATISTICS'				=> 'Pour voir les statistiques vous devez activer et configurer l’extension Présentation forcée !',

/**
* logs
*/
	//logs
	'LOG_INTRODUCIATOR_UPDATED'				=> '<strong>Présentation forcée : configuration mise à jour.</strong>',
	'LOG_INTRODUCIATOR_EXPLANATION_UPDATED'	=> '<strong>Présentation forcée : configuration des explications mise à jour.</strong>',

	// Confirm box
	'INTRODUCIATOR_CP_UPDATED'				=> 'La configuration a été mise à jour',
));