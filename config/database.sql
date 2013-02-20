-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


--
-- Table `tl_iso_config`
--

CREATE TABLE `tl_iso_config` (
  `germanize` char(1) NOT NULL default '',
  `shipping_note` int(10) unsigned NOT NULL default '0',
  `shipping_page` int(10) unsigned NOT NULL default '0',
  `shipping_target` char(1) NOT NULL default '',
  `shipping_rel` varchar(255) NOT NULL default '',
  `checkout_pages` blob NULL,
  `netprice_groups` blob NULL,
  `vatcheck_guests` char(1) NOT NULL default '',
  `vatcheck_member` char(1) NOT NULL default '',
  `vatcheck_groups` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table `tl_iso_tax_class`
--

CREATE TABLE `tl_iso_tax_class` (
  `germanize_price` varchar(5) NOT NULL default '',
  `germanize_rate` decimal(12,2) NOT NULL default '0.00',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_iso_addresses`
--

CREATE TABLE `tl_iso_addresses` (
  `vat_no_ok` varchar(16) NOT NULL default 'nok'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table `tl_member`
--

CREATE TABLE `tl_member` (
  `vat_no` varchar(255) NOT NULL default '',
  `vat_no_ok` varchar(16) NOT NULL default 'nok'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;