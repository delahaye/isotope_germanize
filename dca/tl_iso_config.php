<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Christian de la Haye <service@delahaye.de>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Modify palettes
 */

$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{germanize_legend:hide},pricenote,shippingnote,orderbutton';


/**
 * Add fields
 */

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['pricenote'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['pricenote'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_iso_germanize', 'getArticleAlias'),
	'eval'                    => array('mandatory'=>false)
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['shippingnote'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['shippingnote'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_iso_germanize', 'getArticleAlias'),
	'eval'                    => array('mandatory'=>false)
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['orderbutton'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['orderbutton'],
	'exclude'                 => true,
	'inputType'               => 'optionWizard',
	'eval'                    => array('mandatory'=>false)
);


/**
 * Class tl_iso_germanize
 * 
 * Provide additional methods for DCA.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Christian de la Haye <service@delahaye.de>
 */
class tl_iso_germanize extends Backend
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