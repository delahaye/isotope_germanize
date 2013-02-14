<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * Check the VAT-Id with the external service vatid.eu
 *
 * @package	   isotope_germanize
 * @author     Christian de la Haye <service@delahaye.de>
 */
class IsotopeVatId_Vatideu extends IsotopeFrontend
{
	// Array to be returned
	protected $arrResponseData = array
		(
		'server' => 'vatid.eu',
		'url' => 'http://vatid.eu',
		'original' => '',
		'request_id' => '',
		'check_company' => '',
		'check_address' => ''
		);

	/**
	 * Check the VAT-Id
	 * @param object
	 * @param array
	 * @param array
	 * @return array
	 */
	public function checkIt($objThis, $arrAddress, $arrOwn)
	{
		// Verfiy VAT-Id
		$objXml = simplexml_load_file('http://vatid.eu/check/'.$arrAddress['vatNoCountry'].'/'.$arrAddress['vatNoNumber'].'/'.$arrOwn['country'].'/'.$arrOwn['no']);

		// Build return array
		$this->arrResponseData['original']      = print_r($objXml, true);
		$this->arrResponseData['request_id']    = $objXml->{'request-identifier'};
		$this->arrResponseData['check_company'] = $objXml->name;
		$this->arrResponseData['check_address'] = $objXml->address;

		return array
			(
				'confirmed'  => ($objXml->valid == 'true' ? true : false),
				'error'      => ($objXml->error ? $objXml->text : ($objXml->valid == 'true' ? false : $GLOBALS['TL_LANG']['iso_germanize']['vat_no_notconfirmed'])),
				'response'   => $this->arrResponseData
			);
	}
}