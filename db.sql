-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 10. Sep 2026 um 06:11
-- Server-Version: 10.11.18-MariaDB-cll-lve
-- PHP-Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `zq0s1ev_1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `amazon`
--

CREATE TABLE `amazon` (
  `id` int(64) NOT NULL,
  `ASIN` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `SmallImage` varchar(255) NOT NULL DEFAULT '',
  `MediumImage` varchar(255) NOT NULL DEFAULT '',
  `LargeImage` varchar(255) NOT NULL DEFAULT '',
  `Author` varchar(255) NOT NULL DEFAULT '',
  `Title` varchar(255) NOT NULL DEFAULT '',
  `LabelName` varchar(255) NOT NULL,
  `Publisher` varchar(255) NOT NULL DEFAULT '',
  `NumberOfPages` varchar(255) NOT NULL DEFAULT '',
  `Edition` varchar(255) NOT NULL DEFAULT '',
  `Manufacturer` varchar(255) NOT NULL DEFAULT '',
  `PublicationDate` varchar(255) NOT NULL DEFAULT '',
  `ISBN` varchar(255) NOT NULL DEFAULT '',
  `EAN` varchar(255) NOT NULL DEFAULT '',
  `Binding` varchar(255) NOT NULL DEFAULT '',
  `OfferListing` decimal(15,2) NOT NULL DEFAULT 0.00,
  `CurrencyCode` varchar(255) NOT NULL DEFAULT '',
  `ListPrice` decimal(15,2) NOT NULL DEFAULT 0.00,
  `LowestNewPrice` decimal(15,2) NOT NULL DEFAULT 0.00,
  `LowestUsedprice` decimal(15,2) NOT NULL DEFAULT 0.00,
  `LowestCollectiblePrice` decimal(15,2) NOT NULL DEFAULT 0.00,
  `TotalUsed` int(4) NOT NULL DEFAULT 0,
  `TotalNew` int(4) NOT NULL DEFAULT 0,
  `TotalCollectible` int(4) NOT NULL DEFAULT 0,
  `categories_id` int(11) NOT NULL DEFAULT 0,
  `AddToCartUrl` varchar(255) NOT NULL DEFAULT '',
  `MerchantId` varchar(255) NOT NULL DEFAULT '',
  `SubCondition` varchar(255) NOT NULL DEFAULT '',
  `ConditionNote` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `amazonCreator`
--

CREATE TABLE `amazonCreator` (
  `id` int(40) NOT NULL,
  `creator` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(255) NOT NULL DEFAULT '',
  `id_amazon` int(40) NOT NULL DEFAULT 0,
  `total` int(4) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `amazonOffers`
--

CREATE TABLE `amazonOffers` (
  `id` int(40) NOT NULL,
  `id_amazon` int(40) NOT NULL DEFAULT 0,
  `id_abebooks` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `MerchantId` varchar(255) NOT NULL DEFAULT '',
  `EurobuchID` int(56) NOT NULL,
  `Platform` varchar(255) NOT NULL,
  `OfferListingId` varchar(255) NOT NULL,
  `ExchangeId` varchar(255) NOT NULL,
  `oAmount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `oCurrencyCode` varchar(255) NOT NULL DEFAULT '',
  `SubCondition` varchar(255) NOT NULL DEFAULT '',
  `ConditionNote` longtext NOT NULL,
  `AddToCartUrl` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(255) NOT NULL,
  `versandkosten_eur` decimal(15,2) NOT NULL DEFAULT 3.00,
  `versandkosten_bem` varchar(255) NOT NULL,
  `picurl` varchar(255) NOT NULL,
  `del` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchfreund`
--

CREATE TABLE `buchfreund` (
  `id` int(24) NOT NULL,
  `date_added` date NOT NULL,
  `date_checked` date NOT NULL,
  `ASIN` varchar(255) NOT NULL,
  `ISBN` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL,
  `categories_domain` int(4) NOT NULL DEFAULT 0,
  `sql_special` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `categories_image` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `categories_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `categories_template` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `group_permission_0` tinyint(1) NOT NULL DEFAULT 0,
  `group_permission_1` tinyint(1) NOT NULL DEFAULT 0,
  `group_permission_2` tinyint(1) NOT NULL DEFAULT 0,
  `group_permission_3` tinyint(1) NOT NULL DEFAULT 0,
  `listing_template` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `sort_order` int(3) NOT NULL DEFAULT 0,
  `products_sorting` varchar(32) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_sorting2` varchar(32) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `total` int(24) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data`
--

CREATE TABLE `categories_data` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data1`
--

CREATE TABLE `categories_data1` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data2`
--

CREATE TABLE `categories_data2` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data3`
--

CREATE TABLE `categories_data3` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data4`
--

CREATE TABLE `categories_data4` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data5`
--

CREATE TABLE `categories_data5` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data6`
--

CREATE TABLE `categories_data6` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data7`
--

CREATE TABLE `categories_data7` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data_4th_copy`
--

CREATE TABLE `categories_data_4th_copy` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_data_copy`
--

CREATE TABLE `categories_data_copy` (
  `id` int(64) NOT NULL,
  `categories_id` int(64) NOT NULL,
  `totalResults` int(128) NOT NULL,
  `ItemPage` int(64) NOT NULL,
  `TotalPages` int(64) NOT NULL,
  `minimumPrice` decimal(15,4) NOT NULL,
  `maximumPrice` decimal(15,4) NOT NULL,
  `last_update` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories_description`
--

CREATE TABLE `categories_description` (
  `categories_id` int(11) NOT NULL DEFAULT 0,
  `language_id` int(11) NOT NULL DEFAULT 1,
  `categories_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `categories_heading_title` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `categories_description` text CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `categories_meta_title` varchar(100) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `categories_meta_description` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `categories_meta_keywords` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hcrondtab`
--

CREATE TABLE `hcrondtab` (
  `id` int(11) NOT NULL,
  `sec` varchar(255) DEFAULT '*',
  `min` varchar(255) DEFAULT '*',
  `hour` varchar(128) DEFAULT '*',
  `day` varchar(128) DEFAULT '*',
  `mon` varchar(128) DEFAULT '*',
  `dow` varchar(128) DEFAULT '*',
  `uid` varchar(128) DEFAULT NULL,
  `gid` varchar(128) DEFAULT NULL,
  `machine` varchar(128) DEFAULT NULL,
  `cmd` varchar(512) NOT NULL,
  `name` varchar(128) NOT NULL,
  `andor` enum('&','|') NOT NULL DEFAULT '|',
  `nice` int(11) DEFAULT 0,
  `lastrun` int(11) DEFAULT 0,
  `runonce` int(11) NOT NULL DEFAULT -1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `manufacturers`
--

CREATE TABLE `manufacturers` (
  `manufacturers_id` int(11) NOT NULL,
  `manufacturers_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `manufacturers_description` longtext NOT NULL,
  `manufacturers_public` tinyint(1) NOT NULL DEFAULT 1,
  `manufacturers_email_address` varchar(255) NOT NULL,
  `manufacturers_url` varchar(255) NOT NULL,
  `manufacturers_image` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `manufacturers_info`
--

CREATE TABLE `manufacturers_info` (
  `manufacturers_id` int(11) NOT NULL,
  `languages_id` int(11) NOT NULL,
  `manufacturers_meta_title` varchar(100) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `manufacturers_meta_description` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `manufacturers_meta_keywords` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `manufacturers_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `url_clicked` int(5) NOT NULL DEFAULT 0,
  `date_last_click` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE `products` (
  `products_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `date_checked` date NOT NULL,
  `products_ean` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_quantity` int(4) NOT NULL,
  `products_shippingtime` int(4) NOT NULL,
  `products_model` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `group_permission_0` tinyint(1) NOT NULL,
  `group_permission_1` tinyint(1) NOT NULL,
  `group_permission_2` tinyint(1) NOT NULL,
  `group_permission_3` tinyint(1) NOT NULL,
  `products_sort` int(4) NOT NULL DEFAULT 0,
  `products_image` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `SmallImage` varchar(255) NOT NULL,
  `MediumImage` varchar(255) NOT NULL,
  `LargeImage` varchar(255) NOT NULL,
  `products_price` decimal(15,4) NOT NULL,
  `minPrice` decimal(15,4) NOT NULL,
  `sumOffers` int(12) NOT NULL,
  `ASIN` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `Author` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `Title` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `Publisher` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `NumberOfPages` int(12) NOT NULL,
  `Edition` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `PublicationDate` datetime DEFAULT NULL,
  `ISBN` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `ISBN13` varchar(255) NOT NULL,
  `EAN` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `Binding` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `OfferListing` decimal(15,4) NOT NULL,
  `CurrencyCode` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `ListPrice` decimal(15,4) NOT NULL,
  `LowestNewPrice` decimal(15,4) NOT NULL,
  `LowestUsedprice` decimal(15,2) NOT NULL,
  `LowestCollectiblePrice` decimal(15,2) NOT NULL,
  `TotalUsed` int(12) NOT NULL,
  `TotalNew` int(12) NOT NULL,
  `TotalCollectible` int(12) NOT NULL,
  `products_discount_allowed` decimal(4,2) NOT NULL DEFAULT 0.00,
  `products_date_added` datetime NOT NULL,
  `products_last_modified` datetime DEFAULT NULL,
  `products_date_available` datetime DEFAULT NULL,
  `products_weight` decimal(5,2) NOT NULL,
  `products_status` tinyint(1) NOT NULL,
  `products_tax_class_id` int(11) NOT NULL,
  `product_template` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `options_template` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `manufacturers_id` int(11) DEFAULT NULL,
  `products_ordered` int(11) NOT NULL DEFAULT 0,
  `products_fsk18` int(1) NOT NULL DEFAULT 0,
  `products_vpe` int(11) NOT NULL,
  `products_vpe_status` int(1) NOT NULL DEFAULT 0,
  `products_vpe_value` decimal(15,4) NOT NULL,
  `products_startpage` int(1) NOT NULL DEFAULT 0,
  `products_startpage_sort` int(4) NOT NULL DEFAULT 0,
  `products_new_position` int(11) NOT NULL,
  `products_old_position` int(11) NOT NULL,
  `products_ordered_new` int(11) NOT NULL,
  `DetailPageURL` varchar(255) NOT NULL,
  `EditorialSource` varchar(255) NOT NULL,
  `EditorialReview` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_attributes`
--

CREATE TABLE `products_attributes` (
  `products_attributes_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `options_id` int(11) NOT NULL,
  `options_values_id` int(11) NOT NULL,
  `options_values_price` decimal(15,4) NOT NULL,
  `price_prefix` char(1) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `attributes_model` varchar(64) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `attributes_stock` int(4) DEFAULT NULL,
  `options_values_weight` decimal(15,4) NOT NULL,
  `weight_prefix` char(1) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `sortorder` int(11) DEFAULT NULL,
  `products_options_sort_order` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_description`
--

CREATE TABLE `products_description` (
  `products_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT 1,
  `products_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `products_subtitle` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `products_description` text CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_short_description` text CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_keywords` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_meta_title` text CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `products_meta_description` text CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `products_meta_keywords` text CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `products_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci DEFAULT NULL,
  `products_viewed` int(5) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_images`
--

CREATE TABLE `products_images` (
  `image_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `image_nr` smallint(6) NOT NULL,
  `image_name` varchar(254) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_to_categories`
--

CREATE TABLE `products_to_categories` (
  `products_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `src_category` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_xsell`
--

CREATE TABLE `products_xsell` (
  `ID` int(10) NOT NULL,
  `products_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `products_xsell_grp_name_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `xsell_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_xsell_grp_name`
--

CREATE TABLE `products_xsell_grp_name` (
  `products_xsell_grp_name_id` int(10) NOT NULL,
  `xsell_sort_order` int(10) NOT NULL DEFAULT 0,
  `language_id` smallint(6) NOT NULL DEFAULT 0,
  `groupname` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `amazon`
--
ALTER TABLE `amazon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `titel` (`Author`,`Title`),
  ADD KEY `asin` (`ASIN`),
  ADD KEY `isbn` (`ISBN`);

--
-- Indizes für die Tabelle `amazonCreator`
--
ALTER TABLE `amazonCreator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `titel` (`creator`,`role`),
  ADD KEY `asin` (`id_amazon`);

--
-- Indizes für die Tabelle `amazonOffers`
--
ALTER TABLE `amazonOffers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `amazon` (`id_amazon`),
  ADD KEY `date` (`date_added`),
  ADD KEY `merchant` (`MerchantId`);

--
-- Indizes für die Tabelle `buchfreund`
--
ALTER TABLE `buchfreund`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categories_id`),
  ADD KEY `idx_categories_parent_id` (`parent_id`);

--
-- Indizes für die Tabelle `categories_data`
--
ALTER TABLE `categories_data`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data1`
--
ALTER TABLE `categories_data1`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data2`
--
ALTER TABLE `categories_data2`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data3`
--
ALTER TABLE `categories_data3`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data4`
--
ALTER TABLE `categories_data4`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data5`
--
ALTER TABLE `categories_data5`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data6`
--
ALTER TABLE `categories_data6`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data7`
--
ALTER TABLE `categories_data7`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data_4th_copy`
--
ALTER TABLE `categories_data_4th_copy`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_data_copy`
--
ALTER TABLE `categories_data_copy`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories_description`
--
ALTER TABLE `categories_description`
  ADD PRIMARY KEY (`categories_id`,`language_id`),
  ADD KEY `idx_categories_name` (`categories_name`);

--
-- Indizes für die Tabelle `hcrondtab`
--
ALTER TABLE `hcrondtab`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`manufacturers_id`),
  ADD KEY `IDX_MANUFACTURERS_NAME` (`manufacturers_name`);

--
-- Indizes für die Tabelle `manufacturers_info`
--
ALTER TABLE `manufacturers_info`
  ADD PRIMARY KEY (`manufacturers_id`,`languages_id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`products_id`),
  ADD KEY `idx_products_date_added` (`products_date_added`);

--
-- Indizes für die Tabelle `products_attributes`
--
ALTER TABLE `products_attributes`
  ADD PRIMARY KEY (`products_attributes_id`),
  ADD KEY `products_id` (`products_id`),
  ADD KEY `options` (`options_id`,`options_values_id`);

--
-- Indizes für die Tabelle `products_description`
--
ALTER TABLE `products_description`
  ADD PRIMARY KEY (`products_id`,`language_id`),
  ADD KEY `products_name` (`products_name`);

--
-- Indizes für die Tabelle `products_images`
--
ALTER TABLE `products_images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indizes für die Tabelle `products_to_categories`
--
ALTER TABLE `products_to_categories`
  ADD PRIMARY KEY (`products_id`,`categories_id`),
  ADD KEY `idx` (`categories_id`);

--
-- Indizes für die Tabelle `products_xsell`
--
ALTER TABLE `products_xsell`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `product` (`products_id`),
  ADD KEY `xsell` (`xsell_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `amazon`
--
ALTER TABLE `amazon`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `amazonCreator`
--
ALTER TABLE `amazonCreator`
  MODIFY `id` int(40) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `amazonOffers`
--
ALTER TABLE `amazonOffers`
  MODIFY `id` int(40) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `buchfreund`
--
ALTER TABLE `buchfreund`
  MODIFY `id` int(24) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `categories_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data`
--
ALTER TABLE `categories_data`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data1`
--
ALTER TABLE `categories_data1`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data2`
--
ALTER TABLE `categories_data2`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data3`
--
ALTER TABLE `categories_data3`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data4`
--
ALTER TABLE `categories_data4`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data5`
--
ALTER TABLE `categories_data5`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data6`
--
ALTER TABLE `categories_data6`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data7`
--
ALTER TABLE `categories_data7`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data_4th_copy`
--
ALTER TABLE `categories_data_4th_copy`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `categories_data_copy`
--
ALTER TABLE `categories_data_copy`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hcrondtab`
--
ALTER TABLE `hcrondtab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturers_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products`
--
ALTER TABLE `products`
  MODIFY `products_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products_attributes`
--
ALTER TABLE `products_attributes`
  MODIFY `products_attributes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products_description`
--
ALTER TABLE `products_description`
  MODIFY `products_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products_images`
--
ALTER TABLE `products_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `products_xsell`
--
ALTER TABLE `products_xsell`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
