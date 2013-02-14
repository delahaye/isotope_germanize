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

$GLOBALS['TL_LANG']['tl_iso_config']['pageShipping']       = array('Seite mit den Versandkosten', 'Wählen Sie die Seite aus, die Angaben zu den Versandkosten enthält.');
$GLOBALS['TL_LANG']['tl_iso_config']['shippingTarget']     = array('In neuem Fenster öffnen', 'Den Link in einem neuen Browserfenster öffnen.');
$GLOBALS['TL_LANG']['tl_iso_config']['shippingRel']        = array('Lightbox', 'Hier können Sie ein rel-Attribut eingeben, um die Lightbox anzusteuern.');

$GLOBALS['TL_LANG']['tl_iso_config']['shippingNote']       = array('Artikel mit Versandkostenhinweis im Warenkorb', 'Wählen Sie den Artikel aus, der die im Warenkorb sichtbaren Hinweise zu Versandkosten enthält.');

$GLOBALS['TL_LANG']['tl_iso_config']['manualVatCheck']     = array('USt-ID-Prüfung nur manuell','Die Freigabe der USt-ID Nr. erfolgt ausschließlich manuell im Backend.');
$GLOBALS['TL_LANG']['tl_iso_config']['checkoutPages']      = array('Kassenseiten','Auf Kassenseiten sehen auch Gäste ggf. Nettopreise aufgrund Ihrer Landeszuordnung bzw. USt-ID Nr..');
$GLOBALS['TL_LANG']['tl_iso_config']['onlyMemberVatCheck'] = array('USt-ID-Prüfung nur für Mitglieder','Es sind keine USt.-freien Einkäufe für Gäste mit USt-ID möglich.');
$GLOBALS['TL_LANG']['tl_iso_config']['groupsVatCheck']     = array('Mitgliedergruppen mit USt-ID Überprüfung','Für Mitglieder dieser Gruppen wird die USt-iD automatisiert geprüft.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_config']['germanize_legend']   = 'Einstellungen für deutsche Shops';
