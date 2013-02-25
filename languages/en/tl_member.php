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

$GLOBALS['TL_LANG']['tl_member']['vat_no']    = array('Ust-ID Nr.','Bitte geben Sie ihre Umsatzsteuer-ID Nummer ein.');
$GLOBALS['TL_LANG']['tl_member']['vat_no_ok'] = array('Status der USt-ID Nr.','Die USt-ID Nr. wurde ggf. bestätigt und berechtigt dann zum Steuerabzug.');

$GLOBALS['TL_LANG']['tl_member']['nok']            = 'nicht freigeschaltet - ungeprüft';
$GLOBALS['TL_LANG']['tl_member']['nok_invalid']    = 'nicht freigeschaltet - nicht verfizierbar';
$GLOBALS['TL_LANG']['tl_member']['nok_simple']     = 'nicht freigeschaltet - gültig';
$GLOBALS['TL_LANG']['tl_member']['nok_qualified']  = 'nicht freigeschaltet - verifiziert';
$GLOBALS['TL_LANG']['tl_member']['ok_qualified']   = 'automatisch freigeschaltet';
$GLOBALS['TL_LANG']['tl_member']['ok_manual']      = 'manuell freigeschaltet';