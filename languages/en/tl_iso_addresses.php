<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
 * Fields
 */

$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no']    = array('Ust-ID Nr.','Bitte geben Sie ihre Umsatzsteuer-ID Nummer ein.');
$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no_ok'] = array('Status der USt-ID Nr.','Die USt-ID Nr. wurde ggf. bestätigt und berechtigt dann zum Steuerabzug.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_addresses']['nok']            = 'nicht freigeschaltet - ungeprüft';
$GLOBALS['TL_LANG']['tl_iso_addresses']['nok_invalid']    = 'nicht freigeschaltet - nicht verfizierbar';
$GLOBALS['TL_LANG']['tl_iso_addresses']['nok_simple']     = 'nicht freigeschaltet - gültig';
$GLOBALS['TL_LANG']['tl_iso_addresses']['nok_qualified']  = 'nicht freigeschaltet - verifiziert';
$GLOBALS['TL_LANG']['tl_iso_addresses']['ok_qualified']   = 'automatisch freigeschaltet';
$GLOBALS['TL_LANG']['tl_iso_addresses']['ok_manual']      = 'manuell freigeschaltet';