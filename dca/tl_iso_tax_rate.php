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

$GLOBALS['TL_DCA']['tl_iso_tax_rate']['palettes']['default'] = str_replace(',guests',',excludeFromVatHandling,guests',$GLOBALS['TL_DCA']['tl_iso_tax_rate']['palettes']['default']);


/**
 * Add fields
 */

$GLOBALS['TL_DCA']['tl_iso_tax_rate']['fields']['excludeFromVatHandling'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_tax_rate']['excludeFromVatHandling'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr m12')
);