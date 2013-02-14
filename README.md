isotope_germanize
=================

Enable German settings for Isotope

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
