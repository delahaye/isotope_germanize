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



// Experimentell!!

class IsotopeVatId_Bff extends IsotopeFrontend
{
	
	protected $arrResponseData = array
		(
		'server' => 'evatr.bff-online.de',
		'url' => 'https://evatr.bff-online.de/eVatR/index_html',
		'original' => '',
		'request_id' => '',
		'check_company' => '',
		'check_address' => ''
		);
	
	public function checkIt($objThis, $arrAddress, $arrOwn)
	{
    	require_once(TL_ROOT.'/system/modules/isotope_test/IXR_Library.php');

$client     = new IXR_Client('https://evatr.bff-online.de/');

$UstId_1    = $arrOwn['country'].$arrOwn['no'];
$UstId_2    = $arrAddress['vatNoCountry'].$arrAddress['vatNoNumber'];
$Firmenname = $arrAddress['company'];
$Ort        = $arrAddress['city'];
$PLZ        = $arrAddress['postal'];
$Strasse    = $arrAddress['street'];
$Druck      = 'nein';

if (!$client->query('evatrRPC',
                    $UstId_1,
                    $UstId_2,
                    $Firmenname,
                    $Ort,
                    $PLZ,
                    $Strasse,
                    $Druck))
  {
    die('Ein Fehler ist aufgetreten -
    '.$client->getErrorCode().":".$client->getErrorMessage());
  }

$outString=$client->getResponse();

echo $outString;
die('---');

		return array
			(
				'confirmed' => ($objXml->valid == 'true' ? true : false),
				'error' => ($objXml->error ? $objXml->text : ($objXml->valid == 'true' ? false : $GLOBALS['TL_LANG']['iso_eutax']['vat_no_notconfirmed'])),
				'response' => $this->arrResponseData
			);
	}
}