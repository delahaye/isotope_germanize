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
 * Settings
 */
$GLOBALS['isotope_germanize']['testmode'] = false; // true always verifies the VAT id. Only for testing!

$GLOBALS['isotope_germanize']['order_printed_verification'] = false; // auto-order a print document at the German authorities (beware in test cases!!)
$GLOBALS['isotope_germanize']['loose_verification_street']  = false; // state a verfication as 'qualified' even if the street is not verified
$GLOBALS['isotope_germanize']['loose_verification_postal']  = false; // state a verfication as 'qualified' even if the postal is not verified


/**
 * Set VAT-Id check as first checkout step for addresses
 */
array_insert($GLOBALS['ISO_CHECKOUT_STEPS']['address'], 0, array(array('IsotopeGermanize', 'updateVatCheck')));


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['calculateTax'][]         = array('IsotopeGermanize', 'calculateTax');
$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('IsotopeGermanize', 'injectNotes');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('IsotopeGermanize', 'isotopeGermanizeInsertTags');
$GLOBALS['ISO_HOOKS']['getOrderEmailData'][]    = array('IsotopeGermanize', 'isotopeGermanizeOrderEmailData'); 
