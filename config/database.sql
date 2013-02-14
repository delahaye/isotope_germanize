--
-- Table `tl_iso_setup`
--

CREATE TABLE `tl_iso_tax_rate` (
  `excludeFromVatHandling` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_iso_setup`
--

CREATE TABLE `tl_iso_config` (
  `shippingNote` int(10) unsigned NOT NULL default '0',
  `pageShipping` int(10) unsigned NOT NULL default '0',
  `shippingTarget` char(1) NOT NULL default '',
  `shippingRel` varchar(255) NOT NULL default '',
  `checkoutPages` blob NULL,
  `manualVatCheck` char(1) NOT NULL default '',
  `onlyMemberVatCheck` char(1) NOT NULL default '',
  `groupsVatCheck` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_iso_addresses`
-- 

CREATE TABLE `tl_iso_addresses` (
  `vat_no_confirmed` char(1) NOT NULL default '',
  `vat_no_check` text NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_member`
--

CREATE TABLE `tl_member` (
  `vat_no` varchar(255) NOT NULL default '',
  `vat_no_confirmed` char(1) NOT NULL default '',
  `vat_no_check` text NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;