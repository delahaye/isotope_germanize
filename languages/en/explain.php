<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Provides several functionality for German shops:
 * VAT-handling, gross- and net-prices, tax-notes at several places
 *
 * This extension depends on the Contao-Extension Isotope eCommerce
 *
 * @copyright  2013 de la Haye Kommunikationsdesign <http://www.delahaye.de>
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @package    isotope_germanize
 * @license    LGPL
 * @filesource
 */


/**
 * German shop functionality
 *
 * @package	   isotope_germanize
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 */


/**
 * Help Wizard explanations
 */
$GLOBALS['TL_LANG']['XPL']['isoMailTokens'][]	= array('##germ_shipping_note##', 'German Shipping Note');
$GLOBALS['TL_LANG']['XPL']['isoMailTokens'][]	= array('##germ_shipping_page##', 'Shipping page');
$GLOBALS['TL_LANG']['XPL']['isoMailTokens'][]	= array('##germ_vat_no_ok##', 'Validate Ust-ID');
$GLOBALS['TL_LANG']['XPL']['isoMailTokens'][]	= array('##germ_vat_no##', 'Member Ust-ID');
