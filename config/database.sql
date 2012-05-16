--
-- Table `tl_iso_setup`
--

CREATE TABLE `tl_iso_config` (
  `pricenote` int(10) unsigned NOT NULL default '0',
  `shippingnote` int(10) unsigned NOT NULL default '0',
  `orderbutton` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;