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
$GLOBALS['TL_LANG']['tl_iso_config']['germanize']        = array('Dies ist ein deutscher Shop', 'ACHTUNG! Hiermit aktivieren Sie die automatische Steuerberechnung für Deutschland! Einstellungen der Steuerklassen und -sätze werden ggf. ignoriert!');

$GLOBALS['TL_LANG']['tl_iso_config']['shipping_page']    = array('Seite mit den Versandkosten', 'Wählen Sie die Seite aus, die Angaben zu den Versandkosten enthält.');
$GLOBALS['TL_LANG']['tl_iso_config']['shipping_target']  = array('In neuem Fenster öffnen', 'Den Link in einem neuen Browserfenster öffnen.');
$GLOBALS['TL_LANG']['tl_iso_config']['shipping_rel']     = array('Lightbox', 'Hier können Sie ein rel-Attribut eingeben, um die Lightbox anzusteuern.');

$GLOBALS['TL_LANG']['tl_iso_config']['shipping_note']    = array('Artikel mit Versandkostenhinweis im Warenkorb', 'Wählen Sie den Artikel aus, der die im Warenkorb sichtbaren Hinweise zu Versandkosten enthält.');

$GLOBALS['TL_LANG']['tl_iso_config']['checkout_pages']   = array('Kassenseiten','Auf Kassenseiten sehen auch Gäste ggf. Nettopreise aufgrund Ihrer Landeszuordnung bzw. USt-ID Nr..');
$GLOBALS['TL_LANG']['tl_iso_config']['netprice_groups']  = array('Mitgliedergruppen mit Nettopreisen', 'Wählen Sie welche Mitglieder Nettopreise sehen dürfen.');
$GLOBALS['TL_LANG']['tl_iso_config']['vatcheck_guests']  = array('USt-ID-Prüfung bei Gästen','Die USt-ID soll auch bei Gästen automatisch geprüft werden, und somit ggf. beim ersten Einkauf die Steuerfreiheit ermöglichen. In den Adressfeldern müssen die Felder ´USt-ID-Nr.´ und ´Status der USt-ID Nr.´ aktiviert sein.');
$GLOBALS['TL_LANG']['tl_iso_config']['vatcheck_member']  = array('USt-ID-Prüfung bei Mitgliedern','Die USt-ID soll bei Mitgliedern automatisch geprüft werden. In den Adressfeldern müssen die Felder ´USt-ID-Nr.´ und ´Status der USt-ID Nr.´ aktiviert sein.');
$GLOBALS['TL_LANG']['tl_iso_config']['vatcheck_groups']  = array('Automatische USt-ID-Freischaltung','Wählen Sie die Mitgliedergruppen aus, bei denen die USt-ID bei erfolgreicher Prüfung auch automatisch freigeschaltet werden soll.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_config']['germanize_legend'] = 'Einstellungen für deutsche Shops';