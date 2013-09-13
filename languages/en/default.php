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
 * Order button
 */

$GLOBALS['TL_LANG']['MSC']['confirmOrder']                          = 'Order';


/**
 * Notes at the products, in the cart etc
 */

$GLOBALS['TL_LANG']['iso_germanize']['vatCart']['gross']            = 'incl. VAT';
$GLOBALS['TL_LANG']['iso_germanize']['vatCart']['net']              = 'plus VAT';

$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross_shipping']     = 'incl. %sVAT plus <a>Shipping</a>';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross_noShipping']   = 'incl. %sMVAT, no shipping article';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['net_shipping']       = 'plus %sVAT plus <a>shipping</a>';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['net_noShipping']     = 'plus %sVAT, no shipping article';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['taxfree_shipping']   = 'plus <a>Shipping</a>';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['taxfree_noShipping'] = 'no shipping article';

$GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEuGuest']        = 'The prices shown are incl. VAT irrespective of the country of delivery %s. In case of delivery within non-EU-Countries this will not be considered in the order form.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEu']             = 'Will the shipping address be in a non EU-country %s the sale will not be taxed. Therefore no VAT will be charged.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['confirmedVatNo']    = 'VAT identification number %s is confirmed. The consignee corresponds to the address of delivery, therefore no VAT will be charged for the intra-community supply.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['unconfirmedVatNo']  = 'Unfortunately the VAT identification number %s is not confirmed yet. Therefore VAT will be charged irrespective of the country of delivery %s.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['noVatNo']           = 'The prices shown are including VAT, this is irrespective of the country of delivery %s. If you indicate a valid value added tax identification number within your order, there will not be charged any VAT accordingly an intra-community supply.';

/**
 * VAT-Id check legends
 */

$GLOBALS['TL_LANG']['iso_germanize']['error']['vat_no']            = 'Fehler in der USt.-ID';
$GLOBALS['TL_LANG']['iso_germanize']['error']['own_vat_no']        = 'Fehler in der eigenen USt.-ID';
$GLOBALS['TL_LANG']['iso_germanize']['error']['server']            = 'Server nicht erreichbar';
$GLOBALS['TL_LANG']['iso_germanize']['error']['general']           = 'Fehler in der Abfrage, siehe Fehlercodes';

$GLOBALS['TL_LANG']['iso_germanize']['vat_no_confirmed']           = 'bestätigt';
$GLOBALS['TL_LANG']['iso_germanize']['vat_no_notconfirmed']        = 'USt.-ID nicht bestätigt';

$GLOBALS['TL_LANG']['iso_germanize']['bff']['A']                   = 'stimmt überein';
$GLOBALS['TL_LANG']['iso_germanize']['bff']['B']                   = 'stimmt nicht überein';
$GLOBALS['TL_LANG']['iso_germanize']['bff']['C']                   = 'nicht angefragt';
$GLOBALS['TL_LANG']['iso_germanize']['bff']['D']                   = 'vom EU-Mitgliedsstaat nicht mitgeteilt';


/**
 * Notification mails
 */

$GLOBALS['TL_LANG']['iso_germanize']['nok']            = 'nicht freigeschaltet - ungeprüft';
$GLOBALS['TL_LANG']['iso_germanize']['nok_invalid']    = 'nicht freigeschaltet - nicht verfizierbar';
$GLOBALS['TL_LANG']['iso_germanize']['nok_simple']     = 'nicht freigeschaltet - gültig';
$GLOBALS['TL_LANG']['iso_germanize']['nok_qualified']  = 'nicht freigeschaltet - verifiziert';
$GLOBALS['TL_LANG']['iso_germanize']['ok_qualified']   = 'automatisch freigeschaltet';
$GLOBALS['TL_LANG']['iso_germanize']['ok_manual']      = 'manuell freigeschaltet';

$GLOBALS['TL_LANG']['iso_germanize']['guest_order']                 = 'Gast-Bestellung';
$GLOBALS['TL_LANG']['iso_germanize']['inactive']                    = 'INAKTIV: ';

$GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_subject']   = '##inactive##USt.-ID Verifizierung ##vat_no## (##company##)';

$GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_text']      = '
Automatisierte USt.-ID-Prüfung
=======================================

Status       : ##status##

USt.-ID-Nr.  : ##vat_no##
Datum        : ##date##
Server       : ##host##

Kundendaten des Leistungsempfängers
--------------------------------------
Firma        : ##company##
Straße       : ##street##
PLZ          : ##postal##
Ort          : ##city##
Land         : ##country##

Empfangene Server-Antwort
--------------------------------------
Code         : ##check_code##
Datum        : ##check_date##
Zeit         : ##check_time##
Firma        : ##check_company##
Straße       : ##check_street##
PLZ          : ##check_postal##
Ort          : ##check_city##

(aufgrund länderspezifischer Regelungen sind ggf. nicht alle Angaben abrufbar)

Speicherung in der Contao-Installation
--------------------------------------
Mitglieds-ID : ##member_id##
Adressen-ID  : ##address_id##
';

$GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_subject']      = 'Fehlende USt.-ID Verifizierung ##vat_no## (##company##)';

$GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_text']         = '
Fehlerhafte USt.-ID-Prüfung
=======================================

Status       : ##status##
Fehler       : ##error##

USt.-ID-Nr.  : ##vat_no##
Mitglieds-ID : ##member_id##
Adressen-ID  : ##address_id##

Datum Versuch: ##date##
Server       : ##host##

Kundendaten des Leistungsempfängers
--------------------------------------
Firma        : ##company##
Straße       : ##street##
PLZ          : ##postal##
Ort          : ##city##
Land         : ##country##

Empfangene Server-Antwort
--------------------------------------
Code         : ##check_code##
Datum        : ##check_date##
Zeit         : ##check_time##
Firma        : ##check_company##
Straße       : ##check_street##
PLZ          : ##check_postal##
Ort          : ##check_city##
';
