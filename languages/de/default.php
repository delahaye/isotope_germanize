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

$GLOBALS['TL_LANG']['MSC']['confirmOrder']                          = 'Kaufen';


/**
 * Notes at the products, in the cart etc
 */

$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross']         = 'Inkl. %sMwSt. zzgl. <a>Versand</a>';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['netWithVat']    = 'Zzgl. %sMwSt. zzgl. <a>Versand</a>';
$GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['netWithoutVat'] = 'Zzgl. <a>Versand</a>';

$GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEuGuest']        = 'Die Preise werden unabhängig vom Lieferland %s inkl. MwSt. angezeigt. Bei Lieferung in nicht-EU-Länder wird diese in der Bestellübersicht nicht berücksichtigt.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEu']             = 'Als Lieferung an einen Leistungsempfänger in dem nicht-EU-Land %s ist der Umsatz nicht steuerbar. Es wird daher keine MwSt. berechnet.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['confirmedVatNo']    = 'Die USt.-Id %s ist bestätigt. Der Leistungsempfänger entspricht der Lieferadresse, daher wird bei dieser innergemeinschaftlichen Leistung keine MwSt in Rechnung gestellt.';
$GLOBALS['TL_LANG']['iso_germanize']['notes']['unconfirmedVatNo']  = 'Die USt.-Id %s wurde bisher leider noch nicht bestätigt. Daher wird unabhängig davon in das Lieferland %s inkl. MwSt. berechnet.';

/**
 * VAT-Id check legends
 */

$GLOBALS['TL_LANG']['iso_germanize']['error']['vat_no']            = 'Fehler in der USt.-ID';
$GLOBALS['TL_LANG']['iso_germanize']['error']['own_vat_no']        = 'Fehler in der eigenen USt.-ID';

$GLOBALS['TL_LANG']['iso_germanize']['vat_no_confirmed']           = 'bestätigt';
$GLOBALS['TL_LANG']['iso_germanize']['vat_no_notconfirmed']        = 'USt.-ID nicht bestätigt';


/**
 * Notification mails
 */

$GLOBALS['TL_LANG']['iso_germanize']['guest_order']                 = 'Gast-Bestellung';

$GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_subject']   = 'USt.-ID Verifizierung ##vat_no## (##company##)';

$GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_text']      = '
Automatisierte USt.-ID-Prüfung
=======================================
Manuelle Wiederholung/Prüfung unter ##url##.


USt.-ID-Nr.  : ##vat_no##
Datum        : ##date##
Server       : ##server##
Abfrage-ID   : ##request_id##

(bei vorhandener Abfrage-ID kann die E-Mail als Nachweis beim Finanzamt genutzt werden) 

Kundendaten des Leistungsempfängers
--------------------------------------
Firma        : ##company##
Straße       : ##street##
PLZ          : ##postal##
Ort          : ##city##
Land         : ##country##

Empfangene Server-Antwort
--------------------------------------
Firma        : ##check_company##
Adresse      : ##check_address##

(aufgrund länderspezifischer Regelungen sind ggf. nicht alle Angaben abrufbar)

Speicherung in der Contao-Installation
--------------------------------------
Mitglieds-ID : ##member_id##
Adressen-ID  : ##address_id##


Original-Serverrückmeldung
--------------------------------------
##original##
';

$GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_subject']      = 'Fehlende USt.-ID Verifizierung ##vat_no## (##company##)';

$GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_text']         = '
Fehlende USt.-ID-Prüfung
=======================================

USt.-ID-Nr.  : ##vat_no##
Mitglieds-ID : ##member_id##
Adressen-ID  : ##address_id##

Datum Versuch: ##date##
Server       : ##server##

Kundendaten des Leistungsempfängers
--------------------------------------
Firma        : ##company##
Straße       : ##street##
PLZ          : ##postal##
Ort          : ##city##
Land         : ##country##

Manuelle Wiederholung/Prüfung unter ##url##.

Fehleraufzeichnung
--------------------------------------
##error##
';