<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Provides several functionality for German shops:
 * VAT-handling, gross- and net-prices, tax-notes at several places
 *
 * This extension depends on the Contao-Extension Isotope eCommerce
 *
 * @copyright  2013 de la Haye Kommunikationsdesign <http://www.delahaye.de>
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @package    isotope_germanize
 * @license    LGPL
 * @filesource
 */


/**
 * German shop functionality
 *
 * @package	   isotope_germanize
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 */
class IsotopeGermanize extends IsotopeFrontend
{

    protected static $arrEuCountries = array('at', 'be', 'bg', 'cy', 'cz', 'de', 'dk', 'es', 'fi', 'fr', 'gb', 'gr', 'hu', 'ie', 'it', 'je', 'lt', 'lu', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'se', 'si', 'sk');

	protected static $strHost = 'evatr.bff-online.de';

	protected $arrCheckData = array();


    /**
     * Overwrite the default Isotope tax calculation if german store config is active
     * @param  Database_Result
     * @param  float
     * @param  bool
     * @param  array
     * @return mixed
     */
    public function calculateTax($objTaxClass, $fltPrice, $blnAdd, $arrAddresses)
    {
        // Trigger default tax calculation
        if (!IsotopeGermanize::isActive()) {
            return false;
        }

        // Calculate a product price (add or remove tax)
        if (!$blnAdd) {

            if ($objTaxClass->germanize_price == 'gross' && (self::hasTaxFreePrices() || self::hasNetPrices())) {

                return $fltPrice - $this->calculateTaxIncluded($fltPrice, $objTaxClass->germanize_rate);

            } elseif ($objTaxClass->germanize_price == 'net' && self::hasGrossPrices()) {

                return $fltPrice + $this->calculateTaxSurcharge($fltPrice, $objTaxClass->germanize_rate);

            }

            return $fltPrice;
        }

        // Calculate tax surcharges
        else {

            if (self::hasNetPrices()) {

                $strPercent = $this->getTaxPercentForRate($objTaxClass->germanize_rate);
                $fltTax = $this->calculateTaxSurcharge($fltPrice, $objTaxClass->germanize_rate);

                return array($objTaxClass->id => array
    			(
    				'label'			=> $GLOBALS['TL_LANG']['iso_germanize']['vatCart']['net'],
    				'price'			=> $strPercent,
    				'total_price'	=> Isotope::getInstance()->roundPrice($fltTax, $objTaxClass->applyRoundingIncrement),
    				'add'			=> true,
    			));

    		} elseif (self::hasGrossPrices()) {

    		    $strPercent = $this->getTaxPercentForRate($objTaxClass->germanize_rate);
    		    $fltTax = $this->calculateTaxIncluded($fltPrice, $objTaxClass->germanize_rate);

        		return array($objTaxClass->id => array
    			(
    				'label'			=> $GLOBALS['TL_LANG']['iso_germanize']['vatCart']['gross'],
    				'price'			=> $strPercent,
    				'total_price'	=> Isotope::getInstance()->roundPrice($fltTax, $objTaxClass->applyRoundingIncrement),
    				'add'			=> false,
    			));

    		}

    		return array();
        }
    }


    /**
     * Return true if german tax management is active
     * @return bool
     */
    public static function isActive()
    {
        return (bool) Isotope::getInstance()->Config->germanize;
    }


    /**
     * Return true if user is on a checkout page
     * @return bool
     */
    public static function isOnCheckoutPage()
    {
        global $objPage;

        $arrPages = deserialize(Isotope::getInstance()->Config->checkout_pages, true);

        return in_array($objPage->id, $arrPages);
    }


    /**
     * Return true if user is in germany or country is unknown
     * @return bool
     */
    public static function isGermany()
    {
        $strCountry = Isotope::getInstance()->Cart->shippingAddress->country;

        return ($strCountry == 'de' || $strCountry == '');
    }


    /**
     * Return true if address is in the EU
     * @return bool
     */
    public static function isEuropeanUnion()
    {
        return in_array(Isotope::getInstance()->Cart->shippingAddress->country, self::$arrEuCountries);
    }


    /**
     * Return true if user is in a group with net prices
     * @return bool
     */
    public static function hasNetPriceGroup()
    {
        if (FE_USER_LOGGED_IN !== true) {
            return false;
        }

        $arrUserGroups = FrontendUser::getInstance()->groups;
        $arrNetGroups = deserialize(Isotope::getInstance()->Config->netprice_groups);

        if (!is_array($arrUserGroups) || !is_array($arrNetGroups)) {
            return false;
        }

        return count(array_intersect($arrUserGroups, $arrNetGroups)) > 0;
    }


    /**
     * Return true if a vat-no exists
     * @return bool
     */
    public static function hasVatNo()
    {
        return Isotope::getInstance()->Cart->shippingAddress->vat_no != '';
    }


    /**
     * Return true if a valid vat-no exists
     * @return bool
     */
    public static function hasValidVatNo()
    {
		// in test mode we always have a valif vat no
		if($GLOBALS['isotope_germanize']['testmode'])
		{
	        return true;
		}
		
        if (!self::hasVatNo() 
        	|| (FE_USER_LOGGED_IN !== true && !Isotope::getInstance()->Config->vatcheck_guests)
        	|| (Isotope::getInstance()->Cart->shippingAddress->vat_no_ok != 'ok_qualified' && Isotope::getInstance()->Cart->shippingAddress->vat_no_ok != 'ok_manual')) {
            return false;
        }

        return true;
    }


    /**
     * Return true if all prices have to be tax free
     * @return bool
     */
    public static function hasTaxFreePrices()
    {
        $blnGuestCheck = (FE_USER_LOGGED_IN === true || self::isOnCheckoutPage());

        // Situation 1
        if ($blnGuestCheck && !self::isEuropeanUnion()) {
            return true;
        }

        // Situation 2
        if ($blnGuestCheck && self::isEuropeanUnion() && !self::isGermany() && self::hasValidVatNo()) {
            return true;
        }

        return false;
    }


    /**
     * Return true if net prices are active
     * @return bool
     */
    public static function hasNetPrices()
    {
        if (self::hasTaxFreePrices() || !self::hasNetPriceGroup()) {
            return false;
        }

        if (!self::isOnCheckoutPage() || self::isEuropeanUnion()) {
            return true;
        }

        return false;
    }


    /**
     * Return true if gross prices are active
     * @return bool
     */
    public function hasGrossPrices()
    {
        return (!self::hasTaxFreePrices() && !self::hasNetPrices());
    }


    /**
     * Return a nice formatted string with a tax rate
     * @return string
     */
    public static function getTaxPercentForRate($fltRate)
    {
		$arrFormat = $GLOBALS['ISO_NUM'][Isotope::getInstance()->Config->currencyFormat];
    	
		return number_format($fltRate, (floor($fltRate)==$fltRate ? 0 : 1), $arrFormat[1], $arrFormat[2]).'%';
    }


    /**
     * Return gross price
     * @return float
     */
    protected function calculateTaxIncluded($fltPrice, $fltRate)
    {
        return $fltPrice - ($fltPrice / (1 + ($fltRate / 100)));
    }


    /**
     * Return net price
     * @return float
     */
    protected function calculateTaxSurcharge($fltPrice, $fltRate)
    {
        return ($fltPrice * (1 + ($fltRate / 100))) - $fltPrice;
    }


    /**
     * Return a notice about the vat status of the order, is displayed in the cart and checkout
     * @return string
     */
    public static function getTaxNotice()
    {
        $objAddress = Isotope::getInstance()->Cart->shippingAddress;
        $arrCountries = Isotope::getInstance()->call('getCountries');
        $strCountry = $arrCountries[$objAddress->country];

        $b = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEuGuest'], $strCountry);
        $c = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEu'], $strCountry);
        $d = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['confirmedVatNo'], $objAddress->vat_no);
        $e = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['unconfirmedVatNo'], $objAddress->vat_no, $strCountry);
        $f = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['noVatNo'], $strCountry);

        if (self::isGermany()) {
            return '';
        }

        if (!self::isOnCheckoutPage()) {

            if (FE_USER_LOGGED_IN === true && !self::isEuropeanUnion()) {
                return $c;
            } elseif (FE_USER_LOGGED_IN === true && self::isEuropeanUnion() && self::hasValidVatNo()) {
                return $d;
            } elseif (self::hasNetPriceGroup()) {

                if (self::isEuropeanUnion() && self::hasVatNo() && !self::hasValidVatNo()) {
                    return $e;
                } else {
                    return $b;
                }

            } elseif (self::isEuropeanUnion()) {
                return $f;
            } else {
                return $b;
            }

        } else {

            if (!self::isEuropeanUnion()) {
                return $c;
            } elseif (self::isEuropeanUnion() && self::hasValidVatNo()) {
                return $d;
            } elseif (self::isEuropeanUnion() && self::hasVatNo()) {
                return $e;
            } else {
                return '';
            }
        }
    }



	/**
	 * Inject notes in default templates automatically
	 * @param string
	 * @param string
	 * @return string
	 */
	public function injectNotes($strBuffer, $strTemplate)
	{
        // Use only if the shop is defined as German
        if (!IsotopeGermanize::isActive()) {
            return $strBuffer;
        }

		switch($strTemplate)
		{
			case 'iso_list_default':
			case 'iso_list_variants':
			case 'iso_reader_default':
				// Pricing note at the product
				if(strpos($strBuffer,'isotopeGerman::notePricing')===false)
				{
					// Inject the pricing note after the baseprice or the price
					$strSearchTag = 'price';
					if(strpos($strBuffer,'class="baseprice')!==false)
					{
						$strSearchTag = 'baseprice';
					}
					return preg_replace('#\<div class="'.$strSearchTag.'">(.*)\</div>(.*)#Uis', '<div class="'.$strSearchTag.'">\1</div>'.$this->isotopeGermanizeInsertTags('isotopeGerman::notePricing').'\2', $strBuffer);
				}
				break;

			case 'iso_cart_full':
				// VAT note in the main cart
				if(strpos($strBuffer,'isotopeGerman::noteVat')===false)
				{
					$strBuffer = str_replace('<div class="submit_container">',$this->isotopeGermanizeInsertTags('isotopeGerman::noteVat').'<div class="submit_container">',$strBuffer);
				}
				// shipping note in the main cart
				if(strpos($strBuffer,'isotopeGerman::noteShipping')===false)
				{
					$strBuffer = str_replace('<div class="submit_container">',$this->isotopeGermanizeInsertTags('isotopeGerman::noteShipping').'<div class="submit_container">',$strBuffer);
				}
				return $strBuffer;
				break;

			case 'iso_checkout_shipping_method':
			case 'iso_checkout_payment_method':
				// VAT note in the other checkout steps
				if(strpos($strBuffer,'isotopeGerman::noteVat')===false)
				{
					return $strBuffer.$this->isotopeGermanizeInsertTags('isotopeGerman::noteVat');
				}
				break;

			case 'iso_checkout_order_products':
				// VAT note in the checkout product overview (has to be above the products/total)
				if(strpos($strBuffer,'isotopeGerman::noteVat')===false)
				{
					return $this->isotopeGermanizeInsertTags('isotopeGerman::noteVat').$strBuffer;
				}
				break;

		}

		return $strBuffer;
	}


	/**
	 * Inject notes via insert tags
	 * @param string
	 * @return string
	 */
	public function isotopeGermanizeInsertTags($strTag)
	{
        // Use only if the shop is defined as German
        if (!IsotopeGermanize::isActive()) {
            return $strTag;
        }

		$arrTag = trimsplit('::', $strTag);

		if($arrTag[0] == 'isotopeGerman')
		{
			switch($arrTag[1])
			{
				case 'noteShipping':
					return '<div class="noteShipping">'.self::getShippingNotice().'</div>';
					break;
				case 'notePricing':
					// [2]: # tax class, [3]: shipping exempt, [4]: txt for text version
					return '<div class="notePricing">'.$this->getPriceNotice($arrTag[2], $arrTag[3], $arrTag[4]).'</div>';
					break;
				case 'noteVat':
					return '<div class="noteVat">'.self::getTaxNotice().'</div>';
					break;
			}
		}

		return false;
	}



    /**
     * Return the shipping notice built from an article
     * @return string
     */
    protected function getShippingNotice()
    {
	    return Isotope::getInstance()->Config->shipping_note ? Controller::replaceInsertTags('{{insert_article::'.Isotope::getInstance()->Config->shipping_note.'}}') : false;
    }


    /**
     * Return a price notice to be displayed at a single product
     * @return string
     */
    protected function getPriceNotice($intTaxClass=false, $blnShippingExempt=false, $txt=false)
    {
		if ((FE_USER_LOGGED_IN === true && !self::isEuropeanUnion()) || (FE_USER_LOGGED_IN === true && !self::isGermany() && self::isEuropeanUnion() && self::hasValidVatNo())) {
			if (!$blnShippingExempt)
			{
				$strNote = $GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['taxfree_shipping'];

				$strShippingLink = $this->getShippingLink();
				
				$note = $strShippingLink ? str_replace('<a>',$strShippingLink, $strNote) : str_replace('<a>','',str_replace('</a>','', $strNote));
			} else {
				$strNote = $GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['taxfree_noShipping'];
			}
		} elseif (self::hasNetPriceGroup()) {

			$strTax = $this->getTaxRate($intTaxClass);
			$strTax .= $strTax ? ' ' : '';

			if (!$blnShippingExempt)
			{
				$strNote = $GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['net_shipping'];

				$strShippingLink = $this->getShippingLink();
				
				$strNote = sprintf(($strShippingLink ? str_replace('<a>',$strShippingLink, $strNote) : str_replace('<a>','',str_replace('</a>','', $strNote))), $strTax);
			} else {

				$strNote = sprintf($GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['net_noShipping'], $strTax);
			}
		} else {

			$strTax = $this->getTaxRate($intTaxClass);
			$strTax .= $strTax ? ' ' : '';

			if (!$blnShippingExempt)
			{
				$strNote = $GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross_shipping'];

				$strShippingLink = $this->getShippingLink();
				
				$strNote = sprintf(($strShippingLink ? str_replace('<a>',$strShippingLink, $strNote) : str_replace('<a>','',str_replace('</a>','', $strNote))), $strTax);
			} else {
				$strNote = sprintf($GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross_noShipping'], $strTax);
			}
		}

		// Optional parameter txt for text only
		if($txt)
		{
			$strNote = trim(strip_tags($strNote));
		}
		
		return $strNote;
    }


    /**
     * Return text for a tax rate
     * @return string
     */
    protected function getTaxRate($intTaxClass)
    {
		if(!is_numeric($intTaxClass))
		{
			return false;
		}

		$objRate = Database::getInstance()->prepare("SELECT germanize_rate FROM tl_iso_tax_class WHERE id=?")
			->limit(1)
			->execute($intTaxClass);

		if($objRate->numRows)
		{
			return $this->getTaxPercentForRate($objRate->germanize_rate);
		}
		
		return false;
	}


    /**
     * Return a link to the shipping costs page
     * @return string
     */
    protected function getShippingLink()
    {
        global $objPage;

		if(Isotope::getInstance()->Config->shipping_page < 1)
		{
			return false;
		}

		// Build link to the shipping costs page
		$objTarget = $this->getPageDetails(Isotope::getInstance()->Config->shipping_page);

		if ($GLOBALS['TL_CONFIG']['addLanguageToUrl'])
		{
			$strUrl = $this->generateFrontendUrl($objTarget->row(), null, $objTarget->rootLanguage);
		}
		else
		{
			$strUrl = $this->generateFrontendUrl($objTarget->row());
		}

		$strLink = '<a href="'.$strUrl.'"';

		if (strncmp(Isotope::getInstance()->Config->shipping_rel, 'lightbox', 8) !== 0 || $objPage->outputFormat == 'xhtml')
		{
			$strLink .= ' rel="'. Isotope::getInstance()->Config->shipping_rel .'"';
		}
		else
		{
			$strLink .= ' data-lightbox="'. Isotope::getInstance()->Config->shipping_rel .'"';
		}

		if(Isotope::getInstance()->Config->shipping_target)
		{
			$strLink .= ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
		}

		$strLink .= '>';

		return $strLink;
	}


	/**
	 * Check vat no and update the status
	 */
	public function updateVatCheck()
    {
		// Set the data to check as it is not yet stored in the object
		$addrType = (Isotope::getInstance()->Cart->shippingAddress_id != -1 ? 'shipping_address' : 'billing_address');

		$this->arrCheckData = array(
			'addr_type'  => $addrType,
			'company'    => Input::getInstance()->post($addrType.'_company'),
			'street'     => Input::getInstance()->post($addrType.'_street_1'),
			'postal'     => Input::getInstance()->post($addrType.'_postal'),
			'city'       => Input::getInstance()->post($addrType.'_city'),
			'country'    => Input::getInstance()->post($addrType.'_country'),
			'vat_no'     => Input::getInstance()->post($addrType.'_vat_no')
			);

    	// check the vat no
		if($arrCheck = $this->checkVatNo())
		{
			// update member data
			$this->updateMember($arrCheck['status']);

			// mails for the vendor
			$this->sendMails($arrCheck);

			// log the action
			$this->logIt($arrCheck['status']);

			// update the session data
			Input::getInstance()->setPost($this->arrCheckData['addr_type'].'_vat_no_ok',$arrCheck['status']);
		}
	}


    /**
     * Return array with results of the online check
     * @return array
     */
    protected function checkVatNo()
    {
		if(!Isotope::getInstance()->Config->vat_no
			|| self::isGermany()
			|| !self::isEuropeanUnion()
			|| (!FE_USER_LOGGED_IN && !Isotope::getInstance()->Config->vatcheck_guests)
			|| (FE_USER_LOGGED_IN && !Isotope::getInstance()->Config->vatcheck_member)
			|| !$this->addressHasbeenModified()
			)
		{
			return false;
		}

		// Formal check vat_no
	   	if(!$strCustomerVatNo = self::preCheckVatNo($this->arrCheckData['vat_no']))
		{
			return array(
				'status' => 'nok',
				'error'  => 'vat_no'
				);
		}

		// Formal check own vat_no
	   	if(!$strOwnVatNo = self::preCheckVatNo(Isotope::getInstance()->Config->vat_no))
	   	{
			return array(
				'status' => 'nok_invalid',
				'error'  => 'own_vat_no'
				);
	   	}

		// Server is online
		if(!self::testConnection())
		{
			return array(
				'status' => 'nok_invalid',
				'error'  => 'server'
				);
		}

		// Verify the vat no online
		$arrCheck = $this->verifyVatNo($strCustomerVatNo, $strOwnVatNo);

		// don't activate if no member groups shall be auto-activated
		$arrCheck['status'] = ($arrCheck['status'] == 'ok_qualified' && FE_USER_LOGGED_IN && count(array_intersect(Isotope::getInstance()->Config->vatcheck_groups, FrontendUser::getInstance()->groups)) < 1) ? 'nok_qualified' : $arrCheck['status'];

		return $arrCheck;
	}


    /**
     * Verify the vat no online
     * @return array
     */
    protected function verifyVatNo($strCustomerVatNo, $strOwnVatNo)
    {
		require_once(TL_ROOT.'/system/modules/isotope_germanize/IXR_Library.php');

    	$client = new IXR_Client('https://'.self::$strHost);
		$UstId_1    = strtoupper($strOwnVatNo);
		$UstId_2    = strtoupper($strCustomerVatNo);
		$Firmenname = $this->arrCheckData['company'];
		$Ort        = $this->arrCheckData['city'];
		$PLZ        = $this->arrCheckData['postal'];
		$Strasse    = $this->arrCheckData['street'];
		$Druck      = $GLOBALS['isotope_germanize']['order_printed_verification'] ? 'ja' : 'nein';

		if (!$client->query('evatrRPC',
			$UstId_1,
			$UstId_2,
			$Firmenname,
			$Ort,
			$PLZ,
			$Strasse,
			$Druck))
		{
			return array(
				'status'     => 'nok_invalid',
				'error'      => 'server',
				'check_code' => $client->getErrorCode().': '.$client->getErrorMessage()
				);
		}

		$xml = $client->getResponse();

		preg_match('#(?<=ErrorCode</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrErrorCode);
		preg_match('#(?<=Datum</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrDatum);
		preg_match('#(?<=Uhrzeit</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrUhrzeit);
		preg_match('#(?<=Erg_Name</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrErg_Name);
		preg_match('#(?<=Erg_Str</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrErg_Str);
		preg_match('#(?<=Erg_PLZ</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrErg_PLZ);
		preg_match('#(?<=Erg_Ort</string></value>\n<value><string>).*(?=</string.*)#', $xml, $arrErg_Ort);

		$arrResponse = array(
			'check_date'    => $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrDatum[0]],
			'check_time'    => $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrUhrzeit[0]],
			'check_company' => $arrErg_Name[0] ? $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrErg_Name[0]] : '',
			'check_street'  => $arrErg_Str[0] ? $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrErg_Str[0]] : '',
			'check_postal'  => $arrErg_PLZ[0] ? $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrErg_PLZ[0]] : '',
			'check_city'    => $arrErg_Ort[0] ? $GLOBALS['TL_LANG']['iso_germanize']['bff'][$arrErg_Ort[0]] : '',
			'check_code'    => $arrErrorCode[0]
		);

		switch($arrErrorCode[0])
		{
			// ok
			case '200':
				if(($arrErg_Name[0] != 'A' && $arrErg_Name[0] != 'D')
					|| ($arrErg_Str[0] != 'A' && $arrErg_Str[0] != 'D' && !$GLOBALS['isotope_germanize']['loose_verification_street'])
					|| ($arrErg_PLZ[0] != 'A' && $arrErg_PLZ[0] != 'D' && !$GLOBALS['isotope_germanize']['loose_verification_postal'])
					|| ($arrErg_Ort[0] != 'A' && $arrErg_Ort[0] != 'D')
					)
				{
					// the vat no doesn't fit the address
					return array_merge($arrResponse, array(
						'status'        => 'nok_invalid',
						'error'         => 'general'
						));
				}

				// everything is fine
				return array_merge($arrResponse, array(
					'status'        => 'ok_qualified'
					));
				break;

			// only simple verification available
			case '216':
			case '218':
			case '219':
				return array_merge($arrResponse, array(
					'status'        => 'nok_simple',
					'error'         => 'general'
					));
				break;

			// (technical) error
			default:
				return array_merge($arrResponse, array(
					'status'        => 'nok_invalid',
					'error'         => 'general'
					));
				break;
		}
	}


    /**
     * Test connection to authority server (is only available 5:00 - 22:00
     * @return array
     */
    public static function testConnection()
    {
    	return is_array(gethostbynamel(self::$strHost)) ? true : false;
	}


    /**
     * Log the action 
     */
    protected function logIt($strStatus)
    {
		switch($strStatus)
		{
			case 'ok_qualified':
			case 'nok_qualified':
			case 'nok_simple':
				$this->log('VAT-ID '.$this->arrCheckData['vat_no'].' ('.(FE_USER_LOGGED_IN ? 'User '.FrontendUser::getInstance()->id : 'Guest').'): '.$GLOBALS['TL_LANG']['iso_germanize'][$strStatus],'updateVatCheck','VAT-CHECK');
				break;
			default:
				$this->log('VAT-ID '.$this->arrCheckData['vat_no'].' ('.(FE_USER_LOGGED_IN ? 'User '.FrontendUser::getInstance()->id : 'Guest').'): '.$GLOBALS['TL_LANG']['iso_germanize'][$strStatus],'updateVatCheck','ERROR');
				break;
		}
	}


    /**
     * Update the member data if address and vat_no match the cart version 
     */
    protected function updateMember($strStatus)
    {
    	if(!FE_USER_LOGGED_IN)
    	{
    		return false;
    	}

		$arrMember = array(
			FrontendUser::getInstance()->company,
			FrontendUser::getInstance()->street,
			FrontendUser::getInstance()->postal,
			FrontendUser::getInstance()->city,
			FrontendUser::getInstance()->country
			);
		
		$arrAddress = array(
			$this->arrCheckData['company'],
			$this->arrCheckData['street'],
			$this->arrCheckData['postal'],
			$this->arrCheckData['city'],
			$this->arrCheckData['country']
			);

    	if($arrMember != $arrAddress)
    	{
    		return false;
    	}

		Database::getInstance()->prepare("UPDATE tl_member SET vat_no=?, vat_no_ok=? WHERE id=?")
			->executeUncached($this->arrCheckData['vat_no'], $strStatus, FrontendUser::getInstance()->id);

   		return true;
	}


    /**
     * Confirmation mails
     */
    protected function sendMails($arrCheck)
    {
    	$arrCountries = $this->getCountries();

		$arrMailfields = array(
			'host'          => self::$strHost,
			'status'        => $GLOBALS['TL_LANG']['iso_germanize'][$arrCheck['status']],
			'error'         => $GLOBALS['TL_LANG']['iso_germanize']['error'][$arrCheck['error']],
			'vat_no'        => $this->arrCheckData['vat_no'],
			'date'          => date($GLOBALS['TL_CONFIG']['datimFormat'],time()),
			'company'       => $this->arrCheckData['company'],
			'street'        => $this->arrCheckData['street'],
			'postal'        => $this->arrCheckData['postal'],
			'city'          => $this->arrCheckData['city'],
			'country'       => $arrCountries[$this->arrCheckData['country']],
			'member_id'     => (FE_USER_LOGGED_IN ? FrontendUser::getInstance()->id : $GLOBALS['TL_LANG']['iso_germanize']['guest_order']),
			'address_id'    => (Isotope::getInstance()->Cart->shippingAddress_id > 0 ? Isotope::getInstance()->Cart->shippingAddress_id : ''),
			'check_date'    => $arrCheck['check_date'],
			'check_time'    => $arrCheck['check_time'],
			'check_company' => $arrCheck['check_company'],
			'check_street'  => $arrCheck['check_street'],
			'check_postal'  => $arrCheck['check_postal'],
			'check_city'    => $arrCheck['check_city'],
			'check_code'    => $arrCheck['check_code']
			);

		if($arrCheck['status'] == 'ok_qualified' || $arrCheck['status'] == 'nok_qualified')
		{
			$arrMailfields['inactive'] = $arrCheck['status'] == 'nok_qualified' ? $GLOBALS['TL_LANG']['iso_germanize']['inactive'] : '';

			$objEmail          = new Email();
			$objEmail->subject = $GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_subject'];
			$objEmail->text    = $GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_text'];

			foreach($arrMailfields as $k=>$v)
			{
				$objEmail->subject = str_replace('##'.$k.'##', $v, $objEmail->subject);
				$objEmail->text    = str_replace('##'.$k.'##', $v, $objEmail->text);
			}

			$objEmail->sendTo(Isotope::getInstance()->Config->email);
		}
		else
		{
			$objEmail = new Email();

			// Reminding e-mail to the vendor for manual cheack after salte, only for members
			if(1==1 || FE_USER_LOGGED_IN)
			{
				$objEmail->subject = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_subject'];
				$objEmail->text    = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_text'];

				$objEmail->subject = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_subject'];
				$objEmail->text    = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_text'];

				foreach($arrMailfields as $k=>$v)
				{
					$objEmail->subject = str_replace('##'.$k.'##', $v, $objEmail->subject);
					$objEmail->text    = str_replace('##'.$k.'##', $v, $objEmail->text);
				}

				$objEmail->sendTo(Isotope::getInstance()->Config->email);
			}
		}
	}


    /**
     * Return array with country-code and formally checked number
     * @return array
     */
    public static function preCheckVatNo($strVatNo)
    {
    	$strVatNo = str_replace(array(
			chr(0),
			chr(9),
			chr(10),
			chr(11),
			chr(13),
			chr(23),
			chr(92),
			' ',
			'.',
			'-',
			'_',
			'/',
			'>',
			'<',
			','
			), '', $strVatNo);

		if(strlen($strVatNo) < 8 || strlen($strVatNo) > 12)
		{
			return false;
		}

		return $strVatNo;
	}


    /**
     * Return true if an relevant address has been modified
     * @return bool
     */
    protected function addressHasbeenModified()
    {
    	if(Input::getInstance()->post('FORM_SUBMIT') != 'iso_mod_checkout_address')
    	{
    		return false;
    	}

		foreach(array('vat_no','company','street_1','postal','city','country') as $strRelevant)
		{
			if(Input::getInstance()->post($this->arrCheckData['addr_type'].'_'.$strRelevant) && Input::getInstance()->post($this->arrCheckData['addr_type'].'_'.$strRelevant) != Isotope::getInstance()->Cart->shippingAddress->$strRelevant)
			{
				$hasChanged = true;
			}
		}
		
		return $hasChanged;
    }
    
    /**
	 * Inject data via mail tags
	 * @param string
	 * @return string
	 */
	public function isotopeGermanizeOrderEmailData(IsotopeOrder $objOrder, $arrData)
	{
		return = array_merge($arrData, array
		(
			'germ_shipping_note'		=> Isotope::getInstance()->Config->shipping_note,
			'germ_shipping_page'		=> $this->getShippingLink(),
			'germ_vat_no_ok'			=> Isotope::getInstance()->Cart->shippingAddress->vat_no_ok,
			'germ_vat_no'				=> $this->arrCheckData['vat_no'],
		));
	}

}
