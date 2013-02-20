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

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace('country','country,vat_no,vat_no_ok',$GLOBALS['TL_DCA']['tl_member']['palettes']['default']);


/**
 * Add fields
 */

$GLOBALS['TL_DCA']['tl_member']['fields']['vat_no'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_member']['vat_no'],
	'exclude'				=> true,
	'search'				=> true,
	'inputType'				=> 'text',
	'eval'					=> array('maxlength'=>255, 'feEditable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_member']['fields']['vat_no_ok'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_member']['vat_no_ok'],
	'exclude'				=> true,
	'filter'				=> true,
	'inputType'				=> (TL_MODE=='BE' ? 'select':'hidden'),
	'options'				=> array('nok', 'nok_invalid', 'nok_simple', 'nok_qualified', 'ok_qualified', 'ok_manual'),
	'reference'				=> &$GLOBALS['TL_LANG']['tl_member'],
	'default'				=> 'nok',
	'eval'					=> array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal', 'tl_class'=>'w50')
);