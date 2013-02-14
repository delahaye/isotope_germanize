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
 * EU countries
 */

$GLOBALS['iso_germanize']['eu'] = array(
	'at',
	'be',
	'bg',
	'cy',
	'cz',
	'de',
	'dk',
	'es',
	'fi',
	'fr',
	'gb',
	'gr',
	'hu',
	'ie',
	'it',
	'je',
	'lt',
	'lu',
	'lv',
	'mt',
	'nl',
	'pl',
	'pt',
	'ro',
	'se',
	'si',
	'sk'
	);


/**
 * Data relevant for re-checking the VAT-Id
 */

$GLOBALS['iso_germanize']['relevantData'] = array('vat_no','company','street_1','postal','city','country');


/**
 * Hooks
 */

$GLOBALS['ISO_HOOKS']['force2TaxRate'][] = array('IsotopeGermanize', 'handleVat');
$GLOBALS['ISO_HOOKS']['addCustomAddress'][] = array('IsotopeGermanize','setVatStatus');

$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('IsotopeGermanize', 'injectNotes');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('IsotopeGermanize', 'isotopeGermanizeInsertTags');
// experimental: $GLOBALS['ISO_HOOKS']['checkVatNo'][] = array('IsotopeVatId_Bff', 'checkIt');
$GLOBALS['ISO_HOOKS']['checkVatNo'][]           = array('IsotopeVatId_Vatideu', 'checkIt');
