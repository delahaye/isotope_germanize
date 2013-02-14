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

$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no_confirmed']  = array('USt-ID Nr. bestätigt','Die USt-ID Nr. wurde bestätigt und berechtigt zum Steuerabzug.');
$GLOBALS['TL_LANG']['tl_iso_addresses']['vat_no_check']      = array('USt-ID Nr. Bestätigungsdaten','Hier wird die automatische Rückmeldung der Online-Bestätigung gespeichert.');