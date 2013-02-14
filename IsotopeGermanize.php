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
 * German shop functionality
 *
 * @package	   isotope_germanize
 * @author     Christian de la Haye <service@delahaye.de>
 */
class IsotopeGermanize extends IsotopeFrontend
{

    protected static $arrEuCountries = array('at', 'be', 'bg', 'cy', 'cz', 'de', 'dk', 'es', 'fi', 'fr', 'gb', 'gr', 'hu', 'ie', 'it', 'je', 'lt', 'lu', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'se', 'si', 'sk');


    public static function isActive()
    {
        return (bool) Isotope::getInstance()->Config->germanize;
    }

    public static function isOnCheckoutPage()
    {
        global $objPage;

        $arrPages = deserialize(Isotope::getInstance()->Config->checkoutPages, true);

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

    public static function isEuropeanUnion()
    {
        return in_array(Isotope::getInstance()->Cart->shippingAddress->country, self::$arrEuCountries);
    }

    public static function hasNetPriceGroup()
    {
        if (FE_USER_LOGGED_IN !== true) {
            return false;
        }

        $arrUserGroups = FrontendUser::getInstance()->groups;
        $arrNetGroups = deserialize(Isotope::getInstance()->Config->netPriceGroups);

        if (!is_array($arrUserGroups) || !is_array($arrNetGroups)) {
            return false;
        }

        return count(array_intersect($arrUserGroups, $arrNetGroups)) > 0;
    }

    public static function hasVatNo()
    {
        return Isotope::getInstance()->Cart->shippingAddress->vat_no != '';
    }

    public static function hasValidVatNo()
    {
        if (!self::hasVatNo()) {
            return false;
        }

        // TODO: implement VAT check here

        return true;
    }

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

    public function hasGrossPrices()
    {
        return (!self::hasTaxFreePrices() && !self::hasNetPrices());
    }

    public static function getTaxPercentForRate($strRate)
    {
        switch ($strRate)
        {
            case 'regular':
                return 19;

            case 'reduced':
                return 7;

            case 'excepted':
                return 0;

            default:
                throw new InvalidArgumentException('Unknown german tax rate "' . $strRate . '"');
        }
    }

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
    				'label'			=> 'Steuer hinzu', //$this->translate($objRates->label ? $objRates->label : $objRates->name),
    				'price'			=> $strPercent . '%',
    				'total_price'	=> Isotope::getInstance()->roundPrice($fltTax, $objTaxClass->applyRoundingIncrement),
    				'add'			=> true,
    			));

    		} elseif (self::hasGrossPrices()) {

    		    $strPercent = $this->getTaxPercentForRate($objTaxClass->germanize_rate);
    		    $fltTax = $this->calculateTaxIncluded($fltPrice, $objTaxClass->germanize_rate);

        		return array($objTaxClass->id => array
    			(
    				'label'			=> 'Steuer enthalten', //$this->translate($objRates->label ? $objRates->label : $objRates->name),
    				'price'			=> $strPercent . '%',
    				'total_price'	=> Isotope::getInstance()->roundPrice($fltTax, $objTaxClass->applyRoundingIncrement),
    				'add'			=> false,
    			));

    		}

    		return array();
        }
    }


    protected function calculateTaxIncluded($fltPrice, $strRate)
    {
        $intPercent = self::getTaxPercentForRate($strRate);

        return $fltPrice - ($fltPrice / (1 + ($intPercent / 100)));
    }

    protected function calculateTaxSurcharge($fltPrice, $strRate)
    {
        $intPercent = self::getTaxPercentForRate($strRate);

        return ($fltPrice * (1 + ($intPercent / 100))) - $fltPrice;
    }


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
	 * Inject notes in default templates and set certain variables
	 * @param string
	 * @param string
	 * @return string
	 */
	public function injectNotes($strBuffer, $strTemplate)
	{
		// VAT note in the main cart
		if($strTemplate == 'iso_cart_full' && strpos($strBuffer,'isotopeGerman::noteVat')===false)
		{
			$strBuffer = str_replace('<div class="submit_container">',$this->isotopeGermanizeInsertTags('isotopeGerman::noteVat').'<div class="submit_container">',$strBuffer);
		}

		// VAT note in the checkout
		if(($strTemplate == 'iso_checkout_order_products'
			|| $strTemplate == 'iso_checkout_shipping_method'
			|| $strTemplate == 'iso_checkout_payment_method'
			) && strpos($strBuffer,'isotopeGerman::noteVatCheckout')===false)
		{
			$strBuffer .= $this->isotopeGermanizeInsertTags('isotopeGerman::noteVatCheckout');
		}

		// Pricing note at the product
		if(strpos($strBuffer,'isotopeGerman::notePricing')===false && ($strTemplate == 'iso_list_default' || $strTemplate == 'iso_list_variants' || $strTemplate == 'iso_reader_default'))
		{
			// Inject the pricing note after the baseprice or the price
			$strSearchTag = 'price';
			if(strpos($strBuffer,'class="baseprice')!==false)
			{
				$strSearchTag = 'baseprice';
			}

			$strBuffer = preg_replace('#\<div class="'.$strSearchTag.'">(.*)\</div>(.*)#Uis', '<div class="'.$strSearchTag.'">\1</div>'.$this->isotopeGermanizeInsertTags('isotopeGerman::notePricing').'\2', $strBuffer);
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
		global $objPage;

		$this->import('FrontendUser', 'User');

		$arrTag = trimsplit('::', $strTag);

		if($arrTag[0] == 'isotopeGerman')
		{
			switch($arrTag[1])
			{
				case 'noteShipping':
					$arrAddresses = array('billing'=>$this->Isotope->Cart->billingAddress, 'shipping'=>$this->Isotope->Cart->shippingAddress);

					// Shipping note
					if($this->Isotope->Config->shippingNote)
					{
						$return = $this->replaceInsertTags('{{insert_article::'.$this->Isotope->Config->shippingNote.'}}');
					}
					break;

				case 'notePricing':
					// Build link to the shipping costs page
					if($this->Isotope->Config->pageShipping)
					{
						$objTarget = $this->getPageDetails($this->Isotope->Config->pageShipping);

						if ($GLOBALS['TL_CONFIG']['addLanguageToUrl'])
						{
							$strUrl = $this->generateFrontendUrl($objTarget->row(), null, $objTarget->rootLanguage);
						}
						else
						{
							$strUrl = $this->generateFrontendUrl($objTarget->row());
						}

						$strLink = '<a href="'.$strUrl.'"';

						if (strncmp($this->Isotope->Config->shippingRel, 'lightbox', 8) !== 0 || $objPage->outputFormat == 'xhtml')
						{
							$strLink .= ' rel="'. $this->Isotope->Config->shippingRel .'"';
						}
						else
						{
							$strLink .= ' data-lightbox="'. substr($this->Isotope->Config->shippingRel, 9, -1) .'"';
						}

						if($this->Isotope->Config->shippingTarget)
						{
							$strLink .= ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
						}

						$strLink .= '>';
					}

					// Get tax rate of the product if the second parameter in the insert tag is numeric
					if(is_numeric($arrTag[2]))
					{
						$arrFormat = $GLOBALS['ISO_NUM'][$this->Isotope->Config->currencyFormat];

						$objRate = $this->Database->prepare("SELECT rate FROM tl_iso_tax_rate WHERE id=(SELECT includes FROM tl_iso_tax_class WHERE id=?)")
							->limit(1)
							->execute($arrTag[2]);

						if($objRate->numRows)
						{
							$arrRate = unserialize($objRate->rate);
							$arrTag[2] = number_format($arrRate['value'], (floor($arrRate['value'])==$arrRate['value'] ? 0 : 1), $arrFormat[1], $arrFormat[2]).$arrRate['unit'];
						}
						else
						{
							$arrTag[2] = false;
						}
					}

					//Build the pricing note
					$return = '<div class="notePricing">';

					switch($_SESSION['CHECKOUT_DATA']['vatManagement']['vatStatus'])
					{
						case 'nonEu':
						case 'confirmedVatNo':
							$return .= str_replace('</a>',($strLink ? '</a>':''),str_replace('<a>',$strLink,sprintf($GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['netWithoutVat'],($arrTag[2] ? $arrTag[2].' ' : '')))).'</div>';
							break;

						default:
							$arrGroupsNet = array();

							// Get possible net price groups from tax rates that are added in a tax class
							if(FE_LOGGED_IN)
							{
								$objClasses = $this->Database->prepare("SELECT rates FROM tl_iso_tax_class")
									->execute();

								$arrRates = array();

								while($objClasses->next())
								{
									$tmp = unserialize($objClasses->rates);
									if(is_array($tmp))
									{
										$arrRates = array_unique(array_merge($arrRates,$tmp));
									}
								}

								$objRates = $this->Database->prepare("SELECT groups FROM tl_iso_tax_rate WHERE id IN(?)")
									->execute($arrRates);

								while($objRates->next())
								{
									$tmp = unserialize($objRates->groups);
									if(is_array($tmp))
									{
										$arrGroupsNet = array_unique(array_merge($arrGroupsNet,$tmp));
									}
								}
							}

							if(FE_LOGGED_IN && is_array($this->User->groups) && count(array_intersect($arrGroupsNet, $this->User->groups)))
							{
								$return .= str_replace('<a>',$strLink,sprintf($GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['netWithVat'],($arrTag[2] ? $arrTag[2].' ' : ''))).'</div>';
							}
							else
							{
								$return .= str_replace('<a>',$strLink,sprintf($GLOBALS['TL_LANG']['iso_germanize']['priceNotes']['gross'],($arrTag[2] ? $arrTag[2].' ' : ''))).'</div>';
							}
							break;
					}
					break;

				case 'noteVat':
				case 'noteVatCheckout':
					// Build a note in the cart and the checkout depending on the VAT state of the customer
					$return = '<div class="noteVat '.$arrTag[1].'">';

					switch($_SESSION['CHECKOUT_DATA']['vatManagement']['vatStatus'])
					{
						case 'nonEuGuest':
							$return .= sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes'][($arrTag[1]=='noteVatCheckout' ? 'nonEu' : 'nonEuGuest')],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatCountry']).'</div>';
							break;

						case 'nonEu':
							$return .= sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['nonEu'],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatCountry']).'</div>';
							break;

						case 'confirmedVatNo':
							$return .= sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['confirmedVatNo'],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatNo'],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatCountry']).'</div>';
							break;

						case 'confirmedVatNo':
							$return .= sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['unconfirmedVatNo'],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatNo'],$_SESSION['CHECKOUT_DATA']['vatManagement']['vatCountry']).'</div>';
							break;

						default:
							// Return nothing if there is no VAT note
							$return = '';
							break;
					}
					break;
			}

			// Optional parameter txt for text only
			if($arrTag[2] == 'txt' || $arrTag[3] == 'txt')
			{
				$return = trim(strip_tags($return));
			}

			return $return;
		}

		return false;
	}


	/*
	* Decides on the VAT status of the customer depending on country, EU and VAT-Id
	 * @param object
	 * @param float
	 * @param array
	 * @return string
	*/
	public function handleVat($objRate, $fltPrice, $arrAddresses)
	{
		global $objPage;

		$this->import('FrontendUser', 'User');

		// Define tye of address
		$strAddrType = $this->Isotope->Cart->shippingAddress_id != -1 ? 'shipping' : 'billing';

		// Skip hook functionality if there is no action to take
		$return = 'skip';

		// Only affects taxes that are a VAT and only if the shipping goes to another country
		if ($objRate->excludeFromVatHandling || $arrAddresses[$strAddrType]['country'] == $this->Isotope->Config->shipping_country)
		{
			return $return;
		}

		// EU country and VAT-Id present
		if ($arrAddresses[$strAddrType]['vat_no'] && in_array($arrAddresses[$strAddrType]['country'], $GLOBALS['iso_germanize']['eu']))
		{
			if($arrAddresses[$strAddrType]['vat_no_confirmed']) // VAT-Id confirmed
			{
				// Don't calculate tax, no further checking
				$return = false;

				$status = 'confirmedVatNo';
			}
			else // VAT-Id present, but unconfirmed
			{
				$status = 'unconfirmedVatNo';
			}
		}

		// Non-EU
		elseif(!in_array($arrAddresses[$strAddrType]['country'], $GLOBALS['iso_germanize']['eu']))
		{
			// Look for tax rates that may be added to the actual tax rate
			$objClasses = $this->Database->prepare("SELECT id, rates FROM tl_iso_tax_class WHERE includes=?")
				->execute($objRate->id);

			$arrNetRates = array();

			while($objClasses->next())
			{
				$arrNetRates = array_unique(array_merge($arrNetRates, unserialize($objClasses->rates)));
			}

			// Only affects guests
			if (FE_USER_LOGGED_IN !== true)
			{
				$status = 'nonEuGuest';

				if(count($arrNetRates)>0 // Only take included tax rates, but net prices were possible if user was logged in
					&& !in_array($objPage->id,$this->Isotope->Config->checkoutPages) // On defined (checkout)pages prices are net instead of gross
					)
				{
					// Calculate tax, stop checking
					$return = true;
				}
			}
			else
			{
				$status = 'nonEu';
			}
		}

		// We need some notes in the FE depending on the relevant address

		$arrCountries = $this->getCountries();

		$_SESSION['CHECKOUT_DATA']['vatManagement']['vatStatus']   = $status;
		$_SESSION['CHECKOUT_DATA']['vatManagement']['vatCountry']  = $arrCountries[$arrAddresses[$strAddrType]['country']];
		$_SESSION['CHECKOUT_DATA']['vatManagement']['vatNo']       = $arrAddresses[$strAddrType]['vat_no'];

		return $return;
	}


	/*
	* Sets the VAT state after every modification of the shipping target
	* @param array
	* @param string
	* @param integer
	* @param object
	* @return array
	*/
	public function setVatStatus($arrOptions, $strField, $intDefaultValue, $objThis)
	{
		$this->import('FrontendUser', 'User');

		// Address data can be edited in relevant ways -> new check of the VAT-id
		if($this->Input->post('FORM_SUBMIT')=='iso_mod_checkout_address')
		{

			// Always reset session data
			unset($_SESSION['CHECKOUT_DATA']['vatManagement']);

			if($this->Isotope->Cart->shippingAddress_id != -1)
			{
				$arrAddressData = $this->Isotope->Cart->shippingAddress_data;
				$strAddrType = 'shipping_address';
			}
			else
			{
				$arrAddressData = $this->Isotope->Cart->billingAddress_data;
				$strAddrType = 'billing_address';
			}

			// Something relevant has changed
			if($strAddrType== $strField && $this->relevantModifications($strAddrType, $arrAddressData))
			{
				$arrCountries = $this->getCountries();

				// Update of the address data for the check
				foreach($GLOBALS['iso_germanize']['relevantData'] as $strRelevant)
				{
	    	    	$arrAddressData[$strRelevant] = $this->Input->post($strAddrType.'_'.$strRelevant);
				}

				// Never within the own country
				if($arrAddressData['country'] == $this->Isotope->Config->shipping_country)
				{
					$this->Input->setPost($strAddrType.'_vat_no_confirmed',false);
					$this->Input->setPost($strAddrType.'_vat_no_check',false);

					return $arrOptions;
				}

				// If only manually confirmed VAT-ids are used
				if($this->Isotope->Config->manualVatCheck)
				{
	        		// No guest order without VAT-id on manual confirmation
					$this->Input->setPost($strAddrType.'_vat_no_confirmed',false);

					// VAT-id was confirmed in the member data, given address fits it
					if(FE_USER_LOGGED_IN && $this->User->vat_no_confirmed && !$this->relevantModifications($strAddrType, $this->User, true))
					{
						$this->Input->setPost($strAddrType.'_vat_no_confirmed',true);
					}

					$this->Input->setPost($strAddrType.'_vat_no_check',false);

					return $arrOptions;
				}

				// If only members can order without VAT
				if($this->Isotope->Config->onlyMemberVatCheck)
				{
					if (!FE_USER_LOGGED_IN || !is_array($this->Isotope->Config->groupsVatCheck) || empty($this->Isotope->Config->groupsVatCheck) || !count(array_intersect($this->Isotope->Config->groupsVatCheck, $this->User->groups)))
					{
		        		// No guest ordewr without VAT
						$this->Input->setPost($strAddrType.'_vat_no_confirmed',false);
						$this->Input->setPost($strAddrType.'_vat_no_check',false);

						return $arrOptions;
					}
				}

				// Still unconfirmed

				// Automatic check
				// Format:
				// - confirmed
				// - responseData
				//   - check_company
				//   - check_address
				//   - url
				//   - server
				//   - request_id

				$arrCheck = $this->checkVat($arrAddressData);

				// Fields returned by the check
				$arrMailfields = array(
					'vat_no'        => $arrAddressData['vat_no'],
					'date'          => date($GLOBALS['TL_CONFIG']['datimFormat'],time()),
					'company'       => $arrAddressData['company'],
					'street'        => $arrAddressData['street'],
					'postal'        => $arrAddressData['postal'],
					'city'          => $arrAddressData['city'],
					'country'       => $arrCountries[$arrAddressData['country']],
					'member_id'     => (FE_USER_LOGGED_IN ? $this->User->id : $GLOBALS['TL_LANG']['iso_germanize']['guest_order']),
					'address_id'    => ($arrAddressData['id'] > 0 ? $arrAddressData['id'] : ''),
					'server'        =>  $arrCheck['response']['server'],
					'request_id'    => $arrCheck['response']['request_id'],
					'check_company' => $arrCheck['response']['check_company'],
					'check_address' => $arrCheck['response']['check_address'],
					'url'           =>  $arrCheck['response']['url'],
					'original'      =>  $arrCheck['response']['original'],
					'error'         =>  $arrCheck['error']
					);

				// Log the result, mail it
				if($arrCheck['confirmed'])
				{
					$_SESSION['CHECKOUT_DATA']['vatManagement']['noteCheckoutVatId'] = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['confirmedVatNo'],$arrAddressData['vat_no'],$arrCountries[$arrAddressData['country']]);
					$_SESSION['CHECKOUT_DATA']['vatManagement']['noteCartVatId']     = $_SESSION['CHECKOUT_DATA']['vatManagement']['noteCheckoutVatId'];

					// Confirmation e-mail if the VAT-id was verified
					$objEmail          = new Email();
					$objEmail->subject = $GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_subject'];
					$objEmail->text    = $GLOBALS['TL_LANG']['iso_germanize']['mail_verfication_text'];

					foreach($arrMailfields as $k=>$v)
					{
						$objEmail->subject = str_replace('##'.$k.'##', $v, $objEmail->subject);
						$objEmail->text    = str_replace('##'.$k.'##', $v, $objEmail->text);
					}

					$objEmail->sendTo($this->Isotope->Config->email);

					// Log entry, both members and guests
					$this->log('VAT-ID '.$arrAddressData['vat_no'].' ('.(FE_USER_LOGGED_IN ? 'User '.$this->User->id : 'Guest').'): '.$GLOBALS['TL_LANG']['iso_germanize']['vat_no_confirmed'],'checkVat','VAT-CHECK');
				}
				else
				{
					// Set the VAT state as unconfirmed
					$_SESSION['CHECKOUT_DATA']['vatManagement']['noteCheckoutVatId'] = sprintf($GLOBALS['TL_LANG']['iso_germanize']['notes']['unconfirmedVatNo'],$arrAddressData['vat_no'],$arrCountries[$arrAddressData['country']]);
					$_SESSION['CHECKOUT_DATA']['vatManagement']['noteCartVatId']     = $_SESSION['CHECKOUT_DATA']['vatManagement']['noteCheckoutVatId'];

					$objEmail = new Email();

					// Reminding e-mail to the vendor for manual cheack after salte, only for members
					if(FE_USER_LOGGED_IN)
					{
						$objEmail->subject = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_subject'];
						$objEmail->text    = $GLOBALS['TL_LANG']['iso_germanize']['mail_reminder_text'];

						foreach($arrMailfields as $k=>$v)
						{
							$objEmail->subject = str_replace('##'.$k.'##', $v, $objEmail->subject);
							$objEmail->text    = str_replace('##'.$k.'##', $v, $objEmail->text);
						}

						$objEmail->sendTo($this->Isotope->Config->email);
					}

				}

				// Confirm VAT-id in the member data if the main address fits the used one
				if(FE_USER_LOGGED_IN && $arrCheck['confirmed'] && !$this->relevantModifications($strAddrType, $this->User, true))
				{
					$this->Database->prepare("UPDATE tl_member SET vat_no_confirmed=?, vat_no_check=? WHERE id=?")
						->execute(1,($objEmail->text ? $objEmail->text : $arrCheck['responseData']['original']),$this->User->id);
				}

				// Set confirmation state in the Isotope object data
				$this->Input->setPost($strAddrType.'_vat_no_confirmed',$arrCheck['confirmed']);
				$this->Input->setPost($strAddrType.'_vat_no_check',$arrCheck['response']['original']);

		   	}
		}

		return $arrOptions;
	}


	/*
	* Checks changes in the VAT-relevat data
	* @param string
	* @param mixed
	* @param boolean
	* @return boolean
	*/
	public function relevantModifications($addrType, $varData, $userData=false)
	{
		foreach($GLOBALS['iso_germanize']['relevantData'] as $strRelevant)
		{
			if($strRelevant == 'street_1' && $userData)
			{
				$strRelevant == 'street';
			}

			if(!$userData || $strRelevant == 'street_2' || $strRelevant == 'street_3')
			{
				if($this->Input->post($addrType.'_'.$strRelevant) && $this->Input->post($addrType.'_'.$strRelevant) != (is_object($varData) ? $varData->$strRelevant : $varData[$strRelevant]))
				{
					$hasChanged = true;
				}
			}
		}

		return $hasChanged;
	}


	/*
	* Check the VAT-id formally
	* @param string
	* @param string
	* @return array
	*/
	protected function prepareVatNo($strVatNo, $strCheckCountry)
	{
		// Filter characters
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

		// Country codes from addresses
		$strCountry = strtoupper(trim(($strCheckCountry=='gr' ? 'el' : $strCheckCountry)));

		// VAT-ids without country codes
		$strNo = substr($strVatNo,2);

		// Fits to extracted country of the ID?
		if(strtoupper(substr($strVatNo,0,2)) != $strCountry || strlen($strNo) < 8 || strlen($strNo) > 12)
		{
			return false;
		}

		return array
			(
			'country' => $strCountry,
			'no'      => $strNo
			);
	}


	/*
	* Automatic check of the VAT-id
	* @param array
	* @return array
	*/
	protected function checkVat($arrAddress)
	{
		// Pre-check own VAT-id
		if(!$arrOwn = $this->prepareVatNo($this->Isotope->Config->vat_no, $this->Isotope->Config->country))
		{
			// Log errors
			$this->log('VAT-ID '.$arrAddress['vat_no'].': '.$GLOBALS['TL_LANG']['iso_germanize']['error']['own_vat_no'],'checkVat','ERROR');

			return false;
		}

		// Pre-check VAT-id
		if(!$arrCustomer = $this->prepareVatNo($arrAddress['vat_no'], $arrAddress['country']))
		{
			// Log errors
			$this->log('VAT-ID '.$arrAddress['vat_no'].': '.$GLOBALS['TL_LANG']['iso_germanize']['error']['vat_no'],'checkVat','ERROR');

			return false;
		}

		$arrAddress['vatNoCountry'] = $arrCustomer['country'];
		$arrAddress['vatNoNumber']  = $arrCustomer['no'];

		// !HOOK: Check-routines - enables more than one check routine and flexible extension
		if (isset($GLOBALS['ISO_HOOKS']['checkVatNo']) && is_array($GLOBALS['ISO_HOOKS']['checkVatNo']))
		{
			foreach ($GLOBALS['ISO_HOOKS']['checkVatNo'] as $callback)
			{
				$this->import($callback[0]);
				$arrReturn = $this->$callback[0]->$callback[1]($this, $arrAddress, $arrOwn);

				if($arrReturn['confirmed'])
				{
					return array
						(
						'confirmed' => true,
						'response'  => $arrReturn['response']
						);
				}
				else
				{
					// Log errors
					$this->log('VAT-ID '.$arrAddress['vat_no'].': '.$arrReturn['error'],'checkVat','ERROR');

					// Collect error history, if none of the verfications success
					$strError .= ($strError ? '
--------------------
' : '').$arrReturn['error'];
				}
			}
		}

		// No verfication at all
		return array
			(
			'confirmed' => false,
			'error'     => $strError
			);
	}
}