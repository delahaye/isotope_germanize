isotope_germanize
=================

Enable German settings for Isotope

-

TODO: English text version contains German text. Translators needed please.

-


Spezifikationen
===============

Produkt-Ansicht oder Warenkorb
------------------------------

1. Mitglied + !EU: STEUERFREI `(c)`
2. Mitglied + EU-Land + nicht DE + VAT-NR bestätigt: STEUERFREI `(d)`
3. Mitglied "Nettopreise": Produktpreise NETTO, Steuer DAZU
	- `(a)` wenn DE
    - `(e)` wenn VAT-NR unbestätigt und EU
	- `(b)` sonst
4. Alle anderen: Produktpreise BRUTTO, Steuer INKL
	- `(a)` wenn kein Land oder DE
	- `(f)` wenn Land innerhalb EU
	- `(b)` sonst



Kasse
-----

1. (EU-Land + !DE + VAT-NR bestätigt) || !EU: STEUERFREI
	- `(c)` ausserhalb EU
	- `(d)` sonst
2. EU-Land + Mitglied "Nettopreise": dann NETTOPREISE, Steuer DAZU
	- `(a)` wenn DE
	- `(e)` wenn VAT-NR unbestätigt
	- `(a)` sonst
3. Alle andere: BRUTTOPREISE, Steuer INKL
	- `(a)` wenn DE
	- `(e)` wenn VAT-NR unbestätigt
	- `(a)` sonst



Sätze
-----

- `(a)` = kein Satz
- `(b)` = Die Preise werden unabhängig vom Lieferland %s inkl. MwSt. angezeigt. Bei Lieferung in nicht-EU-Länder wird diese in der Bestellübersicht nicht berücksichtigt.
- `(c)` = Als Lieferung an einen Leistungsempfänger in dem nicht-EU-Land %s ist der Umsatz nicht steuerbar. Es wird daher keine MwSt. berechnet.
- `(d)` = Die USt.-Id %s ist bestätigt. Der Leistungsempfänger entspricht der Lieferadresse, daher wird bei dieser innergemeinschaftlichen Leistung keine MwSt in Rechnung gestellt.
- `(e)` = Die USt.-Id %s wurde bisher leider noch nicht bestätigt. Daher wird unabhängig davon in das Lieferland %s inkl. MwSt. berechnet.
- `(f)` = Als Gast sehen Sie immer Bruttopreise. Die effektive Steuer sehen sie bei der Bestellung...




Insert-Tags
===============

{{isotopeGerman::noteShipping}}
- liefert den Inhalt des Artikels, der in der Konfiguration zur Anzeige von Versandinfos im Warenkorb festgelegt ist.


{{isotopeGerman::noteVat}}
- liefert je nach ermitteltem Status einen der o.g. Sätze a)-f). Kann z.B. am Fuß von Produktlisten genutzt werden.


{{isotopeGerman::notePricing}}
- ist für die Preis- und Steuerangaben am Produkt zuständig. Mehrere Varianten zur Nutzung in eigenen Templates:

  {{isotopeGerman::notePricing::1}} : baut die %-Angabe der Steuerklasse ID 1 ein

  {{isotopeGerman::notePricing::1::true}} : baut die %-Angabe der Steuerklasse ID 1 ein, kennzeichnet als Nicht-Versandprodukt
  
Das Einfügen von {{isotopeGerman::notePricing::<?php echo $this->raw['tax_class']; ?>}} in das Produkt-Template iso_reader_default.html5
fügt also den Preis-Hinweis mit der für dieses Produkt geltenden Steuer- und Versandoption in die Website ein.


Automatische Insert-Tags: 
---------
In die Standardtemplates für die Produkte, den Warenkorb und den Checkout werden die o.g. Angaben automatisch eingebaut. 
Allerdings nur, solange das Template nicht umbeannt wurde und auch den jeweiligen Isert-Tag noch nicht enthält.
Hier stehen aber für notePricing keine erweiterten Möglichkeiten zur Verfügung. 

