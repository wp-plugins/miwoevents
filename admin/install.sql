CREATE TABLE IF NOT EXISTS `#__miwoevents_attenders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` 			int(11) 	DEFAULT NULL,
  `user_id` 			int(11) 	DEFAULT NULL,
  `group_id` 			int(11) 	NULL DEFAULT '0',
  `order_id` 			int(11) 	DEFAULT '0',
  `fields` 				text 		NULL DEFAULT NULL,
  `number_attenders` 	int(11) 	DEFAULT '1',
  `register_date` 		datetime 	DEFAULT NULL,
  `payment_date` 		datetime 	DEFAULT NULL,
  `reminder_sent` 		tinyint(3) 	NOT NULL DEFAULT '0',
  `language` 			varchar(7) 	NULL DEFAULT '*',
  `status` 				int(5) 		DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__miwoevents_attenders_group` (
  `id` 			int(11) 		NOT NULL AUTO_INCREMENT,
  `event_id` 	int(11) 		NOT NULL,
  `name` 		varchar(250) 	NOT NULL,
  `attenders` int(5) 			NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__miwoevents_categories` (
  `id` 			int(11)			NOT NULL AUTO_INCREMENT,
  `parent` 		int(11) 		DEFAULT NULL,
  `title` 		varchar(255) 	DEFAULT NULL,
  `alias` 		varchar(255) 	DEFAULT NULL,
  `description` text 			DEFAULT NULL,
  `introtext`	mediumtext,
  `fulltext` 	mediumtext,
  `ordering` 	int(11) 		DEFAULT NULL,
  `access` 		tinyint(3) 		NOT NULL DEFAULT '0',
  `color_code` 	varchar(20) 	NULL,
  `language` 	varchar(7)		NULL DEFAULT '*',
  `meta_desc` 	varchar(1024) 	DEFAULT NULL,
  `meta_key` 	varchar(1024) 	DEFAULT NULL,
  `meta_author` varchar(255) 	DEFAULT NULL,
  `published` 	tinyint(3) 		unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__miwoevents_countries`;
CREATE TABLE IF NOT EXISTS `#__miwoevents_countries` (
  `country_id` 		int(11) 	NOT NULL AUTO_INCREMENT,
  `zone_id` 		int(11) 	NOT NULL DEFAULT '1',
  `name` 			varchar(64)	DEFAULT NULL,
  `country_3_code`	char(3) 	DEFAULT NULL,
  `country_2_code`	char(2) 	DEFAULT NULL,
  `published` 		tinyint(4) 	NOT NULL DEFAULT '0',
  PRIMARY KEY (`country_id`),
  KEY `name` (`name`)
) DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__miwoevents_countries` (`country_id`, `zone_id`, `name`, `country_3_code`, `country_2_code`, `published`) VALUES
( 1, 1, 'Afghanistan',										'AFG', 'AF', 1),
( 2, 1, 'Albania', 											'ALB', 'AL', 1),
( 3, 1, 'Algeria', 											'DZA', 'DZ', 1),
( 4, 1, 'American Samoa', 									'ASM', 'AS', 1),
( 5, 1, 'Andorra', 											'AND', 'AD', 1),
( 6, 1, 'Angola', 											'AGO', 'AO', 1),
( 7, 1, 'Anguilla', 										'AIA', 'AI', 1),
( 8, 1, 'Antarctica', 										'ATA', 'AQ', 1),
( 9, 1, 'Antigua and Barbuda', 								'ATG', 'AG', 1),
(10, 1, 'Argentina', 										'ARG', 'AR', 1),
(11, 1, 'Armenia', 											'ARM', 'AM', 1),
(12, 1, 'Aruba', 											'ABW', 'AW', 1),
(13, 1, 'Australia', 										'AUS', 'AU', 1),
(14, 1, 'Austria', 											'AUT', 'AT', 1),
(15, 1, 'Azerbaijan', 										'AZE', 'AZ', 1),
(16, 1, 'Bahamas',											'BHS', 'BS', 1),
(17, 1, 'Bahrain', 											'BHR', 'BH', 1),
(18, 1, 'Bangladesh', 										'BGD', 'BD', 1),
(19, 1, 'Barbados', 										'BRB', 'BB', 1),
(20, 1, 'Belarus', 											'BLR', 'BY', 1),
(21, 1, 'Belgium', 											'BEL', 'BE', 1),
(22, 1, 'Belize', 											'BLZ', 'BZ', 1),
(23, 1, 'Benin', 											'BEN', 'BJ', 1),
(24, 1, 'Bermuda', 											'BMU', 'BM', 1),
(25, 1, 'Bhutan', 											'BTN', 'BT', 1),
(26, 1, 'Bolivia', 											'BOL', 'BO', 1),
(27, 1, 'Bosnia and Herzegowina',							'BIH', 'BA', 1),
(28, 1, 'Botswana', 										'BWA', 'BW', 1),
(29, 1, 'Bouvet Island', 									'BVT', 'BV', 1),
(30, 1, 'Brazil', 											'BRA', 'BR', 1),
(31, 1, 'British Indian Ocean Territory',					'IOT', 'IO', 1),
(32, 1, 'Brunei Darussalam', 								'BRN', 'BN', 1),
(33, 1, 'Bulgaria', 										'BGR', 'BG', 1),
(34, 1, 'Burkina Faso', 									'BFA', 'BF', 1),
(35, 1, 'Burundi', 											'BDI', 'BI', 1),
(36, 1, 'Cambodia', 										'KHM', 'KH', 1),
(37, 1, 'Cameroon', 										'CMR', 'CM', 1),
(38, 1, 'Canada', 											'CAN', 'CA', 1),
(39, 1, 'Cape Verde', 										'CPV', 'CV', 1),
(40, 1, 'Cayman Islands', 									'CYM', 'KY', 1),
(41, 1, 'Central African Republic', 						'CAF', 'CF', 1),
(42, 1, 'Chad', 											'TCD', 'TD', 1),
(43, 1, 'Chile', 											'CHL', 'CL', 1),
(44, 1, 'China', 											'CHN', 'CN', 1),
(45, 1, 'Christmas Island', 								'CXR', 'CX', 1),
(46, 1, 'Cocos (Keeling) Islands', 							'CCK', 'CC', 1),
(47, 1, 'Colombia',											'COL', 'CO', 1),
(48, 1, 'Comoros', 											'COM', 'KM', 1),
(49, 1, 'Congo',											'COG', 'CG', 1),
(50, 1, 'Cook Islands', 									'COK', 'CK', 1),
(51, 1, 'Costa Rica', 										'CRI', 'CR', 1),
(52, 1, 'Cote D''Ivoire',									'CIV', 'CI', 1),
(53, 1, 'Croatia', 											'HRV', 'HR', 1),
(54, 1, 'Cuba', 											'CUB', 'CU', 1),
(55, 1, 'Cyprus', 											'CYP', 'CY', 1),
(56, 1, 'Czech Republic',									'CZE', 'CZ', 1),
(57, 1, 'Denmark', 											'DNK', 'DK', 1),
(58, 1, 'Djibouti', 										'DJI', 'DJ', 1),
(59, 1, 'Dominica', 										'DMA', 'DM', 1),
(60, 1, 'Dominican Republic', 								'DOM', 'DO', 1),
(61, 1, 'East Timor', 										'TMP', 'TP', 1),
(62, 1, 'Ecuador', 											'ECU', 'EC', 1),
(63, 1, 'Egypt', 											'EGY', 'EG', 1),
(64, 1, 'El Salvador', 										'SLV', 'SV', 1),
(65, 1, 'Equatorial Guinea', 								'GNQ', 'GQ', 1),
(66, 1, 'Eritrea', 											'ERI', 'ER', 1),
(67, 1, 'Estonia', 											'EST', 'EE', 1),
(68, 1, 'Ethiopia', 										'ETH', 'ET', 1),
(69, 1, 'Falkland Islands (Malvinas)', 						'FLK', 'FK', 1),
(70, 1, 'Faroe Islands', 									'FRO', 'FO', 1),
(71, 1, 'Fiji', 											'FJI', 'FJ', 1),
(72, 1, 'Finland', 											'FIN', 'FI', 1),
(73, 1, 'France', 											'FRA', 'FR', 1),
(74, 1, 'France, Metropolitan', 							'FXX', 'FX', 1),
(75, 1, 'French Guiana', 									'GUF', 'GF', 1),
(76, 1, 'French Polynesia', 								'PYF', 'PF', 1),
(77, 1, 'French Southern Territories', 						'ATF', 'TF', 1),
(78, 1, 'Gabon', 											'GAB', 'GA', 1),
(79, 1, 'Gambia', 											'GMB', 'GM', 1),
(80, 1, 'Georgia', 											'GEO', 'GE', 1),
(81, 1, 'Germany', 											'DEU', 'DE', 1),
(82, 1, 'Ghana', 											'GHA', 'GH', 1),
(83, 1, 'Gibraltar', 										'GIB', 'GI', 1),
(84, 1, 'Greece', 											'GRC', 'GR', 1),
(85, 1, 'Greenland', 										'GRL', 'GL', 1),
(86, 1, 'Grenada', 											'GRD', 'GD', 1),
(87, 1, 'Guadeloupe', 										'GLP', 'GP', 1),
(88, 1, 'Guam', 											'GUM', 'GU', 1),
(89, 1, 'Guatemala',										'GTM', 'GT', 1),
(90, 1, 'Guinea', 											'GIN', 'GN', 1),
(91, 1, 'Guinea-bissau', 									'GNB', 'GW', 1),
(92, 1, 'Guyana', 											'GUY', 'GY', 1),
(93, 1, 'Haiti',											'HTI', 'HT', 1),
(94, 1, 'Heard and Mc Donald Islands', 						'HMD', 'HM', 1),
(95, 1, 'Honduras', 										'HND', 'HN', 1),
(96, 1, 'Hong Kong', 										'HKG', 'HK', 1),
(97, 1, 'Hungary', 											'HUN', 'HU', 1),
(98, 1, 'Iceland', 											'ISL', 'IS', 1),
(99, 1, 'India', 											'IND', 'IN', 1),
(100, 1, 'Indonesia', 										'IDN', 'ID', 1),
(101, 1, 'Iran (Islamic Republic of)', 						'IRN', 'IR', 1),
(102, 1, 'Iraq', 											'IRQ', 'IQ', 1),
(103, 1, 'Ireland', 										'IRL', 'IE', 1),
(104, 1, 'Israel', 											'ISR', 'IL', 1),
(105, 1, 'Italy', 											'ITA', 'IT', 1),
(106, 1, 'Jamaica', 										'JAM', 'JM', 1),
(107, 1, 'Japan', 											'JPN', 'JP', 1),
(108, 1, 'Jordan', 											'JOR', 'JO', 1),
(109, 1, 'Kazakhstan', 										'KAZ', 'KZ', 1),
(110, 1, 'Kenya', 											'KEN', 'KE', 1),
(111, 1, 'Kiribati', 										'KIR', 'KI', 1),
(112, 1, 'Korea, Democratic People''s Republic of',			'PRK', 'KP', 1),
(113, 1, 'Korea, Republic of', 								'KOR', 'KR', 1),
(114, 1, 'Kuwait', 											'KWT', 'KW', 1),
(115, 1, 'Kyrgyzstan', 										'KGZ', 'KG', 1),
(116, 1, 'Lao People''s Democratic Republic', 				'LAO', 'LA', 1),
(117, 1, 'Latvia', 											'LVA', 'LV', 1),
(118, 1, 'Lebanon', 										'LBN', 'LB', 1),
(119, 1, 'Lesotho', 										'LSO', 'LS', 1),
(120, 1, 'Liberia', 										'LBR', 'LR', 1),
(121, 1, 'Libyan Arab Jamahiriya', 							'LBY', 'LY', 1),
(122, 1, 'Liechtenstein', 									'LIE', 'LI', 1),
(123, 1, 'Lithuania', 										'LTU', 'LT', 1),
(124, 1, 'Luxembourg', 										'LUX', 'LU', 1),
(125, 1, 'Macau', 											'MAC', 'MO', 1),
(126, 1, 'Macedonia, The Former Yugoslav Republic of',		'MKD', 'MK', 1),
(127, 1, 'Madagascar', 										'MDG', 'MG', 1),
(128, 1, 'Malawi', 											'MWI', 'MW', 1),
(129, 1, 'Malaysia', 										'MYS', 'MY', 1),
(130, 1, 'Maldives', 										'MDV', 'MV', 1),
(131, 1, 'Mali', 											'MLI', 'ML', 1),
(132, 1, 'Malta', 											'MLT', 'MT', 1),
(133, 1, 'Marshall Islands', 								'MHL', 'MH', 1),
(134, 1, 'Martinique', 										'MTQ', 'MQ', 1),
(135, 1, 'Mauritania', 										'MRT', 'MR', 1),
(136, 1, 'Mauritius', 										'MUS', 'MU', 1),
(137, 1, 'Mayotte', 										'MYT', 'YT', 1),
(138, 1, 'Mexico', 											'MEX', 'MX', 1),
(139, 1, 'Micronesia, Federated States of', 				'FSM', 'FM', 1),
(140, 1, 'Moldova, Republic of', 							'MDA', 'MD', 1),
(141, 1, 'Monaco', 											'MCO', 'MC', 1),
(142, 1, 'Mongolia', 										'MNG', 'MN', 1),
(143, 1, 'Montserrat', 										'MSR', 'MS', 1),
(144, 1, 'Morocco', 										'MAR', 'MA', 1),
(145, 1, 'Mozambique', 										'MOZ', 'MZ', 1),
(146, 1, 'Myanmar', 										'MMR', 'MM', 1),
(147, 1, 'Namibia', 										'NAM', 'NA', 1),
(148, 1, 'Nauru', 											'NRU', 'NR', 1),
(149, 1, 'Nepal', 											'NPL', 'NP', 1),
(150, 1, 'Netherlands', 									'NLD', 'NL', 1),
(151, 1, 'Netherlands Antilles', 							'ANT', 'AN', 1),
(152, 1, 'New Caledonia', 									'NCL', 'NC', 1),
(153, 1, 'New Zealand', 									'NZL', 'NZ', 1),
(154, 1, 'Nicaragua', 										'NIC', 'NI', 1),
(155, 1, 'Niger', 											'NER', 'NE', 1),
(156, 1, 'Nigeria', 										'NGA', 'NG', 1),
(157, 1, 'Niue', 											'NIU', 'NU', 1),
(158, 1, 'Norfolk Island', 									'NFK', 'NF', 1),
(159, 1, 'Northern Mariana Islands', 						'MNP', 'MP', 1),
(160, 1, 'Norway', 											'NOR', 'NO', 1),
(161, 1, 'Oman', 											'OMN', 'OM', 1),
(162, 1, 'Pakistan', 										'PAK', 'PK', 1),
(163, 1, 'Palau', 											'PLW', 'PW', 1),
(164, 1, 'Panama', 											'PAN', 'PA', 1),
(165, 1, 'Papua New Guinea', 								'PNG', 'PG', 1),
(166, 1, 'Paraguay', 										'PRY', 'PY', 1),
(167, 1, 'Peru', 											'PER', 'PE', 1),
(168, 1, 'Philippines', 									'PHL', 'PH', 1),
(169, 1, 'Pitcairn', 										'PCN', 'PN', 1),
(170, 1, 'Poland', 											'POL', 'PL', 1),
(171, 1, 'Portugal', 										'PRT', 'PT', 1),
(172, 1, 'Puerto Rico', 									'PRI', 'PR', 1),
(173, 1, 'Qatar', 											'QAT', 'QA', 1),
(174, 1, 'Reunion', 										'REU', 'RE', 1),
(175, 1, 'Romania', 										'ROM', 'RO', 1),
(176, 1, 'Russian Federation', 								'RUS', 'RU', 1),
(177, 1, 'Rwanda', 											'RWA', 'RW', 1),
(178, 1, 'Saint Kitts and Nevis', 							'KNA', 'KN', 1),
(179, 1, 'Saint Lucia', 									'LCA', 'LC', 1),
(180, 1, 'Saint Vincent and the Grenadines', 				'VCT', 'VC', 1),
(181, 1, 'Samoa', 											'WSM', 'WS', 1),
(182, 1, 'San Marino', 										'SMR', 'SM', 1),
(183, 1, 'Sao Tome and Principe', 							'STP', 'ST', 1),
(184, 1, 'Saudi Arabia', 									'SAU', 'SA', 1),
(185, 1, 'Senegal', 										'SEN', 'SN', 1),
(186, 1, 'Seychelles', 										'SYC', 'SC', 1),
(187, 1, 'Sierra Leone', 									'SLE', 'SL', 1),
(188, 1, 'Singapore', 										'SGP', 'SG', 1),
(189, 1, 'Slovakia (Slovak Republic)', 						'SVK', 'SK', 1),
(190, 1, 'Slovenia', 										'SVN', 'SI', 1),
(191, 1, 'Solomon Islands', 								'SLB', 'SB', 1),
(192, 1, 'Somalia', 										'SOM', 'SO', 1),
(193, 1, 'South Africa', 									'ZAF', 'ZA', 1),
(194, 1, 'South Georgia and the South Sandwich Islands',	'SGS', 'GS', 1),
(195, 1, 'Spain', 											'ESP', 'ES', 1),
(196, 1, 'Sri Lanka', 										'LKA', 'LK', 1),
(197, 1, 'St. Helena', 										'SHN', 'SH', 1),
(198, 1, 'St. Pierre and Miquelon', 						'SPM', 'PM', 1),
(199, 1, 'Sudan', 											'SDN', 'SD', 1),
(200, 1, 'Suriname', 										'SUR', 'SR', 1),
(201, 1, 'Svalbard and Jan Mayen Islands', 					'SJM', 'SJ', 1),
(202, 1, 'Swaziland', 										'SWZ', 'SZ', 1),
(203, 1, 'Sweden', 											'SWE', 'SE', 1),
(204, 1, 'Switzerland', 									'CHE', 'CH', 1),
(205, 1, 'Syrian Arab Republic', 							'SYR', 'SY', 1),
(206, 1, 'Taiwan', 											'TWN', 'TW', 1),
(207, 1, 'Tajikistan', 										'TJK', 'TJ', 1),
(208, 1, 'Tanzania, United Republic of', 					'TZA', 'TZ', 1),
(209, 1, 'Thailand', 										'THA', 'TH', 1),
(210, 1, 'Togo', 											'TGO', 'TG', 1),
(211, 1, 'Tokelau', 										'TKL', 'TK', 1),
(212, 1, 'Tonga', 											'TON', 'TO', 1),
(213, 1, 'Trinidad and Tobago', 							'TTO', 'TT', 1),
(214, 1, 'Tunisia', 										'TUN', 'TN', 1),
(215, 1, 'Turkey', 											'TUR', 'TR', 1),
(216, 1, 'Turkmenistan', 									'TKM', 'TM', 1),
(217, 1, 'Turks and Caicos Islands', 						'TCA', 'TC', 1),
(218, 1, 'Tuvalu', 											'TUV', 'TV', 1),
(219, 1, 'Uganda', 											'UGA', 'UG', 1),
(220, 1, 'Ukraine', 										'UKR', 'UA', 1),
(221, 1, 'United Arab Emirates', 							'ARE', 'AE', 1),
(222, 1, 'United Kingdom', 									'GBR', 'GB', 1),
(223, 1, 'United States', 									'USA', 'US', 1),
(224, 1, 'United States Minor Outlying Islands', 			'UMI', 'UM', 1),
(225, 1, 'Uruguay', 										'URY', 'UY', 1),
(226, 1, 'Uzbekistan', 										'UZB', 'UZ', 1),
(227, 1, 'Vanuatu', 										'VUT', 'VU', 1),
(228, 1, 'Vatican City State (Holy See)', 					'VAT', 'VA', 1),
(229, 1, 'Venezuela', 										'VEN', 'VE', 1),
(230, 1, 'Viet Nam', 										'VNM', 'VN', 1),
(231, 1, 'Virgin Islands (British)', 						'VGB', 'VG', 1),
(232, 1, 'Virgin Islands (U.S.)', 							'VIR', 'VI', 1),
(233, 1, 'Wallis and Futuna Islands', 						'WLF', 'WF', 1),
(234, 1, 'Western Sahara', 									'ESH', 'EH', 1),
(235, 1, 'Yemen', 											'YEM', 'YE', 1),
(236, 1, 'Serbia', 											'SRB', 'RS', 1),
(237, 1, 'The Democratic Republic of Congo', 				'DRC', 'DC', 1),
(238, 1, 'Zambia', 											'ZMB', 'ZM', 1),
(239, 1, 'Zimbabwe', 										'ZWE', 'ZW', 1),
(240, 1, 'East Timor', 										'XET', 'XE', 1),
(241, 1, 'Jersey', 											'XJE', 'XJ', 1),
(242, 1, 'St. Barthelemy', 									'XSB', 'XB', 1),
(243, 1, 'St. Eustatius', 									'XSE', 'XU', 1),
(244, 1, 'Canary Islands', 									'XCA', 'XC', 1),
(245, 1, 'Montenegro', 										'MNE', 'ME', 1);

CREATE TABLE IF NOT EXISTS `#__miwoevents_events` (
  `id` 									int(11) 			NOT NULL AUTO_INCREMENT,
  `parent_id` 							int(11) 			NOT NULL DEFAULT '0',
  `category_id` 						int(11) 			NOT NULL DEFAULT '0',
  `location_id` 						int(11) 			NOT NULL DEFAULT '0',
  `product_id` 							int(11) 			NOT NULL DEFAULT '0',
  `title` 								varchar(255) 		DEFAULT NULL,
  `alias` 								varchar(255) 		DEFAULT NULL,
  `event_type` 							tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_date` 							datetime 			DEFAULT NULL,
  `event_end_date` 						datetime 			DEFAULT NULL,
  `introtext` 							mediumtext			DEFAULT NULL,
  `fulltext` 							mediumtext			DEFAULT NULL,
  `article_id` 							int(11) 			NOT NULL DEFAULT '0',
  `access` 								tinyint(3) unsigned	NOT NULL DEFAULT '0',
  `registration_access`					tinyint(3) unsigned NOT NULL DEFAULT '0',
  `individual_price` 					decimal(10,2) 		DEFAULT NULL,
  `tax_class` 					      	varchar(5) 			DEFAULT '0',
  `event_capacity` 						int(11) 			DEFAULT NULL,
  `created_by` 							int(11)				NOT NULL DEFAULT '0',
  `cut_off_date` 						datetime 			DEFAULT NULL,
  `registration_type` 					tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_group_number` 					varchar(11) 		NOT NULL DEFAULT '',
  `early_bird_discount_type`			tinyint(3) unsigned NOT NULL DEFAULT '0',
  `early_bird_discount_date`			datetime 			DEFAULT NULL,
  `early_bird_discount_amount`			decimal(10,2) 		DEFAULT '0.00',
  `group_rates` 						text				DEFAULT NULL,
  `enable_cancel_registration`			tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cancel_before_date` 					datetime 			DEFAULT NULL,
  `enable_auto_reminder` 				tinyint(3) unsigned NOT NULL DEFAULT '0',
  `remind_before_x_days` 				tinyint(3) unsigned NOT NULL DEFAULT '0',
  `recurring_type` 						tinyint(3) unsigned NOT NULL DEFAULT '0',
  `recurring_frequency` 				int(11) 			NOT NULL DEFAULT '0',
  `weekdays` 							varchar(50) 		DEFAULT NULL,
  `monthdays` 							varchar(50) 		DEFAULT NULL,
  `recurring_end_date` 					datetime 			DEFAULT NULL,
  `recurring_occurrencies` 				int(11) 			DEFAULT '0',
  `attachment` 							varchar(255) 		DEFAULT NULL,
  `params` 								text				DEFAULT NULL,
  `ordering` 							int(11) 			DEFAULT '0',
  `published` 							int(11) 			DEFAULT '1',
  `fields`				 				text				DEFAULT NULL,
  `currency_symbol`						varchar(12) 		DEFAULT NULL,
  `thumb` 								varchar(60) 		DEFAULT NULL,
  `notification_emails` 				varchar(255) 		DEFAULT NULL,
  `registrant_email_body` 				text				DEFAULT NULL,
  `thanks_message` 						text				DEFAULT NULL,
  `registration_approved_email_body`	text				DEFAULT NULL,
  `meta_desc` 							varchar(1024) 		DEFAULT NULL,
  `meta_key` 							varchar(1024) 		DEFAULT NULL,
  `meta_author` 						varchar(255) 		DEFAULT NULL,
  `language` 							varchar(7) 			NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `location_id` (`location_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__miwoevents_event_categories` (
  `id` 			int(11) NOT NULL AUTO_INCREMENT,
  `event_id` 	int(11) NULL,
  `category_id`	int(11) NULL,
  PRIMARY KEY(`id`),
  INDEX `event_id` (`event_id`),
  INDEX `category_id` (`category_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__miwoevents_fields` (
  `id` 				int(11) 			NOT NULL AUTO_INCREMENT,
  `name` 			varchar(50) 		DEFAULT NULL,
  `title` 			varchar(255) 		DEFAULT NULL,
  `description` 	varchar(255) 		DEFAULT NULL,
  `field_type` 		varchar(50) 		DEFAULT NULL,
  `values` 			text,
  `default_values` 	text,
  `prices` 			text				DEFAULT NULL,
  `display_in` 		tinyint(3) unsigned DEFAULT NULL,
  `rows` 			tinyint(3) unsigned DEFAULT NULL,
  `cols`			tinyint(3) unsigned DEFAULT NULL,
  `size` 			int(11) 			DEFAULT NULL,
  `css_class` 		varchar(50) 		DEFAULT NULL,
  `field_mapping` 	varchar(100) 		DEFAULT NULL,
  `ordering` 		int(11) 			DEFAULT NULL,
  `access`			tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language` 		varchar(7) 			NULL DEFAULT '*',
  `published` 		tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_type` (`field_type`)
) DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__miwoevents_fields` (`id`, `name`, `title`, `description`, `field_type`, `values`, `default_values`, `display_in`, `rows`, `cols`, `size`, `css_class`, `field_mapping`, `ordering`, `access`, `language`, `published`) VALUES
(1, 'miwi_firstname',	'First Name', 	'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 1, 1, '*', 1),
(2, 'miwi_lastname', 	'Last Name', 	'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 2, 1, '*', 1),
(3, 'miwi_email', 		'Email', 		'Custom Field description', 'email', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 3, 1, '*', 1),
(4, 'miwi_address', 	'Address', 		'Custom Field description', 'textarea',				'', '', 1, 0, 0, 25, 'inputbox', '', 	 4, 1, '*', 1),
(5, 'miwi_organization','Organization',	'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '',	 5, 1, '*', 1),
(6, 'miwi_phone', 		'Phone', 		'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 6, 1, '*', 1),
(7, 'miwi_fax', 		'Fax', 			'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 7, 1, '*', 1),
(8, 'miwi_zip', 		'Zip', 			'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 8, 1, '*', 1),
(9, 'miwi_city', 		'City', 		'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	 9, 1, '*', 1),
(10, 'miwi_state', 		'State', 		'Custom Field description', 'text', 				'', '', 1, 0, 0, 25, 'inputbox', '', 	10, 1, '*', 1),
(11, 'miwi_country', 	'Country', 		'Custom Field description', 'miwoeventscountries', 	'', '', 1, 0, 0, 25, 'inputbox', '', 	11, 1, '*', 1),
(12, 'miwi_comment', 	'Comment', 		'Custom Field description', 'textarea', 			'', '', 1, 0, 0, 25, 'inputbox', '', 	12, 1, '*', 1);

CREATE TABLE IF NOT EXISTS `#__miwoevents_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` 	int(11) 			NOT NULL DEFAULT '0',
  `title` 		varchar(255) 		DEFAULT NULL,
  `alias` 		varchar(255) 		DEFAULT NULL,
  `description`	text 				DEFAULT NULL,
  `address` 	varchar(1024) 		DEFAULT NULL,
  `geo_city` 	varchar(100) 		DEFAULT NULL,
  `geo_state` 	varchar(50) 		DEFAULT NULL,
  `geo_country` varchar(100) 		DEFAULT NULL,
  `coordinates` varchar(30) 		DEFAULT '40.992954,29.042092',
  `language` 	varchar(7) 			DEFAULT '*',
  `meta_desc` 	varchar(1024) 		DEFAULT NULL,
  `meta_key` 	varchar(1024) 		DEFAULT NULL,
  `meta_author` varchar(255) 		DEFAULT NULL,
  `published` 	tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;