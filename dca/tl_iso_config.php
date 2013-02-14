<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Provides several functionality for German shops:
 * VAT-handling, gross- and net-prices, tax-notes at several places
 *
 * This extension depends on the Contao-Extension Isotope eCommerce
 *
 * @copyright  2013 de la Haye Kommunikationsdesign <http://www.delahaye.de>
 * @author     Christian de la Haye <service@delahaye.de>
 * @package    isotope_germanize
 * @license    LGPL
 * @filesource
 */


/**
 * Modify palettes
 */
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'onlyMemberVatCheck';

$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{germanize_legend:hide},pageShipping,shippingRel,shippingTarget,shippingNote,checkoutPages,manualVatCheck,onlyMemberVatCheck';

$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['onlyMemberVatCheck'] .= 'groupsVatCheck';


/**
 * Add fields
 */

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['shippingNote'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['shippingNote'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_iso_config_germanize', 'getArticleAlias'),
	'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['pageShipping'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['pageShipping'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array('fieldType'=>'radio')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['shippingTarget'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['shippingTarget'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['shippingRel'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['shippingRel'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>64, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['checkoutPages'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['checkoutPages'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array('fieldType'=>'checkbox')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['manualVatCheck'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['manualVatCheck'],
	'exclude'                 => true,
	'inputType'               => 'checkbox'
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['onlyMemberVatCheck'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['onlyMemberVatCheck'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['groupsVatCheck'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['groupsVatCheck'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkboxWizard',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('multiple'=>true)
);



/**
 * Adds backend functionality
 *
 * @package	   isotope_germanize
 * @author     Christian de la Haye <service@delahaye.de>
 */
class tl_iso_config_germanize extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Get all articles and return them as array (article alias)
	 * @param object
	 * @return array
	 */
	public function getArticleAlias(DataContainer $dc)
	{
		$arrPids = array();
		$arrAlias = array();

		if (!$this->User->isAdmin)
		{
			foreach ($this->User->pagemounts as $id)
			{
				$arrPids[] = $id;
				$arrPids = array_merge($arrPids, $this->getChildRecords($id, 'tl_page'));
			}

			if (empty($arrPids))
			{
				return $arrAlias;
			}

			$objAlias = $this->Database->prepare("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrPids))) .") ORDER BY parent, a.sorting")
									   ->execute();
		}
		else
		{
			$objAlias = $this->Database->prepare("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting")
									   ->execute();
		}

		if ($objAlias->numRows)
		{
			$this->loadLanguageFile('tl_article');

			while ($objAlias->next())
			{
				$key = $objAlias->parent . ' (ID ' . $objAlias->pid . ')';
				$arrAlias[$key][$objAlias->id] = $objAlias->title . ' (' . (strlen($GLOBALS['TL_LANG']['tl_article'][$objAlias->inColumn]) ? $GLOBALS['TL_LANG']['tl_article'][$objAlias->inColumn] : $objAlias->inColumn) . ', ID ' . $objAlias->id . ')';
			}
		}

		return $arrAlias;
	}

}