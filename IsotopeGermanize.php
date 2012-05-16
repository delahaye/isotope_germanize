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
 * Class IsotopeGermanize
 * 
 * Provide methods to optimize Isotope for Germany.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Christian de la Haye <service@delahaye.de>
 */
class IsotopeGermanize extends IsotopeFrontend
{

	/**
	 * Inject notes in default templates and set certain variables
	 * @param string
	 * @param string
	 * @return string
	 */
	public function injectNotes($strBuffer, $strTemplate)
	{
		// set order button label
		if(is_array($this->Isotope->Config->orderbutton))
		{
			foreach($this->Isotope->Config->orderbutton as $orderbut)
			{
				if($orderbut['default'])
				{
					$GLOBALS['TL_LANG']['MSC']['confirmOrder'] = $orderbut['label'];
				}
			}
			foreach($this->Isotope->Config->orderbutton as $orderbut)
			{
				if($orderbut['value'] == $GLOBALS['TL_LANGUAGE'])
				{
					$GLOBALS['TL_LANG']['MSC']['confirmOrder'] = $orderbut['label'];
					continue;
				}
			}
		}

		// price note at the product
		if(strpos($strBuffer,'isotopeGerman::priceNote')===false && ($strTemplate == 'iso_list_default' || $strTemplate == 'iso_list_variants' || $strTemplate == 'iso_reader_default'))
		{
			$strBuffer = preg_replace('#\<div class="price">(.*)\</div>(.*)#Uis', '<div class="price">\1</div>'.$this->isotopeGermanizeInsertTags('isotopeGerman::priceNote').'\2', $strBuffer);
		}

		// shipping note in the main cart
		if(strpos($strBuffer,'isotopeGerman::shippingNote')===false && $strTemplate == 'iso_cart_full')
		{
			$strBuffer = str_replace('<div class="submit_container">',$this->isotopeGermanizeInsertTags('isotopeGerman::shippingNote').'<div class="submit_container">',$strBuffer);
		}

		return $strBuffer;
	}


	/**
	 * Inject notes via insert tags
	 * @param string
	 * @return string
	 */
	public function isotopeGermanizeInsertTags($strTag)
	{
		$arrTag = trimsplit('::', $strTag);
		if($arrTag[0] == 'isotopeGerman')
		{
			switch($arrTag[1])
			{
				case 'shippingNote':
					$arrAddresses = array('billing'=>$this->Isotope->Cart->billingAddress, 'shipping'=>$this->Isotope->Cart->shippingAddress);

					// shipping note
					if($this->Isotope->Config->shippingnote)
					{
						$return = $this->replaceInsertTags('{{insert_article::'.$this->Isotope->Config->shippingnote.'}}');
					}
					break;

				case 'priceNote':
					if($this->Isotope->Config->pricenote)
					{
						$return = str_replace('~isotopeVat~',($arrTag[2] ? $arrTag[2] : ''),$this->replaceInsertTags('{{insert_article::'.$this->Isotope->Config->pricenote.'}}'));
					}
					break;
			}

			// optional parameter txt for text only
			if($arrTag[2] == 'txt')
			{
				$return = trim(strip_tags($return));
			}

			return $return;
		}

		return false;
	}

}