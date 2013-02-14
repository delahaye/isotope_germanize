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

$GLOBALS['TL_DCA']['tl_iso_addresses']['palettes']['default'] = str_replace('vat_no','vat_no,vat_no_confirmed,vat_no_check',$GLOBALS['TL_DCA']['tl_iso_addresses']['palettes']['default']);


/**
 * Add fields
 */

$GLOBALS['TL_DCA']['tl_iso_addresses']['fields']['vat_no_confirmed'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no_confirmed'],
	'exclude'				=> true,
	'filter'				=> true,
	'inputType'				=> (TL_MODE=='BE' ? 'checkbox':'hidden'),
	'eval'					=> array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal', 'tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_iso_addresses']['fields']['vat_no_check'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no_check'],
	'exclude'				=> true,
	'inputType'				=> (TL_MODE=='BE' ? 'textarea':'hidden'),
	'eval'					=> array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal', 'tl_class'=>'clr long')
);