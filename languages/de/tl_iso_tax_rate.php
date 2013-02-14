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

$GLOBALS['TL_LANG']['tl_iso_tax_rate']['excludeFromVatHandling'] = array('Keine MwSt.-Behandlung','Diese Steuer wird bei der erweiterten MwSt.-Behandlung ignoriert.');