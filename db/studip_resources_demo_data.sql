--
-- Dumping data for table `clipboards`
--

REPLACE INTO `clipboards` (`id`, `user_id`, `name`, `handler`, `allowed_item_class`, `mkdate`, `chdate`) VALUES(1, '76ed43ef286fb55cf9e41beadb484a9f', 'HS', 'Clipboard', 'StudipItem', 1591715351, 1591715351);
REPLACE INTO `clipboards` (`id`, `user_id`, `name`, `handler`, `allowed_item_class`, `mkdate`, `chdate`) VALUES(2, '76ed43ef286fb55cf9e41beadb484a9f', 'SR', 'Clipboard', 'StudipItem', 1591715364, 1591715364);

--
-- Dumping data for table `clipboard_items`
--

REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(1, 1, '728f1578de643fb08b32b4b8afb2db77', 'Room', 1591715354, 1591715354);
REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(2, 1, 'b17c4ea6e053f2fffba8a5517fc277b3', 'Room', 1591715356, 1591715356);
REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(3, 1, '2f98bf64830043fd98a39fbbe2068678', 'Room', 1591715357, 1591715357);
REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(4, 2, '51ad4b7100d3a8a1db61c7b099f052a6', 'Room', 1591715367, 1591715367);
REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(5, 2, 'a8c03520e8ad9dc90fb2d161ffca7d7b', 'Room', 1591715368, 1591715368);
REPLACE INTO `clipboard_items` (`id`, `clipboard_id`, `range_id`, `range_type`, `mkdate`, `chdate`) VALUES(6, 2, '5ead77812be3b601e2f08ed5da4c5630', 'Room', 1591715370, 1591715370);

--
-- Dumping data for table `resources`
--

REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('2760740189890f47537537ed7fa51a05', '', '05278c70d89ae99404727408ef111963', NULL, 'Stud.IP', '', 0, 1591713936, 1591713936, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('2f98bf64830043fd98a39fbbe2068678', '8a57860ca2be4cc3a77c06c1d346ea57', '85d62e2a8a87a2924db8fc4ed3fde09d', 2, 'Hörsaal 3', '', 1, 1084640542, 1084640555, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '6350c6ae2ec6fd8bd852d505789d0666', '5a72dfe3f0c0295a8fe4e12c86d4c8f4', 2, 'Seminarraum 1', '', 1, 1084640567, 1084640578, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '6350c6ae2ec6fd8bd852d505789d0666', '5a72dfe3f0c0295a8fe4e12c86d4c8f4', 2, 'Seminarraum 3', '', 1, 1084640611, 1084723704, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('6350c6ae2ec6fd8bd852d505789d0666', '2760740189890f47537537ed7fa51a05', '3cbcc99c39476b8e2c8eef5381687461', 1, 'Übungsgebäude', '', 1, 1084640386, 1591715302, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('728f1578de643fb08b32b4b8afb2db77', '8a57860ca2be4cc3a77c06c1d346ea57', '85d62e2a8a87a2924db8fc4ed3fde09d', 2, 'Hörsaal 1', '', 1, 1084640456, 1084640468, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('8a57860ca2be4cc3a77c06c1d346ea57', '2760740189890f47537537ed7fa51a05', '3cbcc99c39476b8e2c8eef5381687461', 1, 'Hörsaalgebäude', '', 1, 1084640042, 1591715222, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '6350c6ae2ec6fd8bd852d505789d0666', '5a72dfe3f0c0295a8fe4e12c86d4c8f4', 2, 'Seminarraum 2', '', 1, 1084640590, 1084640599, 0);
REPLACE INTO `resources` (`id`, `parent_id`, `category_id`, `level`, `name`, `description`, `requestable`, `mkdate`, `chdate`, `sort_position`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '8a57860ca2be4cc3a77c06c1d346ea57', '85d62e2a8a87a2924db8fc4ed3fde09d', 2, 'Hörsaal 2', '', 1, 1084640520, 1084640528, 0);

--
-- Dumping data for table `resource_bookings`
--

REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('06a133ec2958178551ee6b48957058b6', '728f1578de643fb08b32b4b8afb2db77', '221bb1927fcd93fab3ec7dde7c6b3cce', '', 1734336000, 1734343200, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('2a251bfb01a99e533b3bbc841fc02ca7', '728f1578de643fb08b32b4b8afb2db77', '5f87ebde55d5527ceb27ccd6dcd9f66e', '', 1733731200, 1733738400, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('413cca32dbe8c3499ae1b7dae46b6e77', '728f1578de643fb08b32b4b8afb2db77', '7ec82c654e1b41819cd476ec72e77a76', '', 1730102400, 1730109600, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('4effa9b74ecbe244bac766e6256a361c', '728f1578de643fb08b32b4b8afb2db77', '132eec0a28623f5afde092a6960e45f4', '', 1736150400, 1736157600, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('577b04b04575ce3a60328cf97ff801c8', '728f1578de643fb08b32b4b8afb2db77', '60b4659f960ef05807cbaea6368158aa', '', 1730707200, 1730714400, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('6cb4d34009cee46357a98006ef824930', '728f1578de643fb08b32b4b8afb2db77', '13bf7a5cd577bcba5bff88d46512baad', '', 1732521600, 1732528800, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('9e55f071dc674f92ef7c0032bdc623f4', '728f1578de643fb08b32b4b8afb2db77', '4f47c3d25eca9ab8fb2a1644209074ae', '', 1733126400, 1733133600, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('a01a39e448aa6dbab5dd5dffaae78926', '728f1578de643fb08b32b4b8afb2db77', 'be1ad3a4bc5c933d4bfbaa2b313d3ab5', '', 1738569600, 1738576800, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('a672b97ead875ef080706c6f6da33a44', '728f1578de643fb08b32b4b8afb2db77', 'c729ae36a1503f471a407802e8f72cec', '', 1731916800, 1731924000, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('aec837593cbd51d21d58b97e9af9ba19', '728f1578de643fb08b32b4b8afb2db77', '7aaa9681da31192e49eaa63a4cef3dfb', '', 1737360000, 1737367200, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('bd9806b968f39178225603cb6812f2b0', '728f1578de643fb08b32b4b8afb2db77', '749e52b43a4fe025442f355779489a9d', '', 1729494000, 1729501200, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('cde83098d29a75de33e76a71b8a71a21', '728f1578de643fb08b32b4b8afb2db77', '1199f2c43a6ddcd05fd61456e6ac1451', '', 1736755200, 1736762400, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('e70aaf4655d2de64852b2212b04a0e67', '728f1578de643fb08b32b4b8afb2db77', 'a8f402ada308f68d5f24374923d25580', '', 1737964800, 1737972000, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');
REPLACE INTO `resource_bookings` (`id`, `resource_id`, `range_id`, `description`, `begin`, `end`, `repeat_end`, `mkdate`, `chdate`, `internal_comment`, `preparation_time`, `booking_type`, `booking_user_id`, `repetition_interval`) VALUES('ed37afe9ff2d96c489d5cf6e2b4fba80', '728f1578de643fb08b32b4b8afb2db77', 'e8212b6e58109ae94ad2a31796c4a520', '', 1731312000, 1731319200, NULL, 1716456982, 1716456982, '', 0, 0, '76ed43ef286fb55cf9e41beadb484a9f', '');


--
-- Dumping data for table `resource_booking_intervals`
--

REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('103172da14b07526e9c6ceb224c11c23', '728f1578de643fb08b32b4b8afb2db77', 'ed37afe9ff2d96c489d5cf6e2b4fba80', 1731312000, 1731319200, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('107c4ff6a9c207cfda35a80260a3f8c2', '728f1578de643fb08b32b4b8afb2db77', 'a672b97ead875ef080706c6f6da33a44', 1731916800, 1731924000, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('1f3b18df65f42101408da9ca0f8b06ae', '728f1578de643fb08b32b4b8afb2db77', 'aec837593cbd51d21d58b97e9af9ba19', 1737360000, 1737367200, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('22589cca9cf15513c507d6658d267492', '728f1578de643fb08b32b4b8afb2db77', '6cb4d34009cee46357a98006ef824930', 1732521600, 1732528800, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('2273be6840eb462735458020165dc663', '728f1578de643fb08b32b4b8afb2db77', '577b04b04575ce3a60328cf97ff801c8', 1730707200, 1730714400, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('298b0bc9d1be4d072c93c7cbc2647be1', '728f1578de643fb08b32b4b8afb2db77', '4effa9b74ecbe244bac766e6256a361c', 1736150400, 1736157600, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('4bf38db5dff602e6bef5a3d281783bc7', '728f1578de643fb08b32b4b8afb2db77', 'cde83098d29a75de33e76a71b8a71a21', 1736755200, 1736762400, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('5a7c2cd2975447334f0b3575513a5e6e', '728f1578de643fb08b32b4b8afb2db77', '9e55f071dc674f92ef7c0032bdc623f4', 1733126400, 1733133600, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('5bff17112dccfc248aa354748afceb46', '728f1578de643fb08b32b4b8afb2db77', 'a01a39e448aa6dbab5dd5dffaae78926', 1738569600, 1738576800, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('783aa785a85845d7eae547983afc0091', '728f1578de643fb08b32b4b8afb2db77', 'bd9806b968f39178225603cb6812f2b0', 1729494000, 1729501200, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('7f7470f79dc5ca2cc9e2e73ac50b6287', '728f1578de643fb08b32b4b8afb2db77', '413cca32dbe8c3499ae1b7dae46b6e77', 1730102400, 1730109600, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('914b8a1d43e28ed42cccd4bdc52d0b5c', '728f1578de643fb08b32b4b8afb2db77', '2a251bfb01a99e533b3bbc841fc02ca7', 1733731200, 1733738400, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('d566a5583c94cb7bbdbb67596780852c', '728f1578de643fb08b32b4b8afb2db77', 'e70aaf4655d2de64852b2212b04a0e67', 1737964800, 1737972000, 1716456982, 1716456982, 1);
REPLACE INTO `resource_booking_intervals` (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `mkdate`, `chdate`, `takes_place`) VALUES('deaa9da8e6003da444d2ff4756bbab9a', '728f1578de643fb08b32b4b8afb2db77', '06a133ec2958178551ee6b48957058b6', 1734336000, 1734343200, 1716456982, 1716456982, 1);




--
-- Dumping data for table `resource_properties`
--

REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2760740189890f47537537ed7fa51a05', '674ea21ef56fd973bb30ee6f247c0723', '+0.0+0.0+0.0CRSWGS_84/', 1591714592, 1591714592);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '28addfe18e86cc3587205734c8bc2372', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640542, 1084640555);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '44fd30e8811d0d962582fa1a9c452bdd', '25', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '613cfdf6aa1072e21a1edfcfb0445c69', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', '72723662c924e785a6662f42c84b8bb4', '', 1591714586, 1591714586);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('2f98bf64830043fd98a39fbbe2068678', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '28addfe18e86cc3587205734c8bc2372', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640567, 1084640578);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '44fd30e8811d0d962582fa1a9c452bdd', '25', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '613cfdf6aa1072e21a1edfcfb0445c69', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', '72723662c924e785a6662f42c84b8bb4', '', 1591714586, 1591714586);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('51ad4b7100d3a8a1db61c7b099f052a6', 'afb8675e2257c03098aa34b2893ba686', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '1f8cef2b614382e36eaa4a29f6027edf', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '28addfe18e86cc3587205734c8bc2372', '0', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640611, 1084723704);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '44fd30e8811d0d962582fa1a9c452bdd', '15', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', '72723662c924e785a6662f42c84b8bb4', '', 1591714586, 1591714586);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('5ead77812be3b601e2f08ed5da4c5630', 'afb8675e2257c03098aa34b2893ba686', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('6350c6ae2ec6fd8bd852d505789d0666', '674ea21ef56fd973bb30ee6f247c0723', '+51.5398160+9.9367200+0.0000000CRSWGS_84/', 1591714594, 1591715302);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('6350c6ae2ec6fd8bd852d505789d0666', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('6350c6ae2ec6fd8bd852d505789d0666', 'c4f13691419a6c12d38ad83daa926c7c', 'Liebigstr. 1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('6350c6ae2ec6fd8bd852d505789d0666', 'e141f19ca6da2938d4c51cc59462884b', '', 1591714589, 1591714589);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '1f8cef2b614382e36eaa4a29f6027edf', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '28addfe18e86cc3587205734c8bc2372', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640456, 1084640468);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '44fd30e8811d0d962582fa1a9c452bdd', '500', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '613cfdf6aa1072e21a1edfcfb0445c69', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '72723662c924e785a6662f42c84b8bb4', '', 1591714470, 1591714470);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', '7c1a8f6001cfdcb9e9c33eeee0ef343d', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', 'afb8675e2257c03098aa34b2893ba686', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('728f1578de643fb08b32b4b8afb2db77', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('8a57860ca2be4cc3a77c06c1d346ea57', '674ea21ef56fd973bb30ee6f247c0723', '+51.5407270+9.9354050+0.0000000CRSWGS_84/', 1591714991, 1591715222);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('8a57860ca2be4cc3a77c06c1d346ea57', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('8a57860ca2be4cc3a77c06c1d346ea57', 'c4f13691419a6c12d38ad83daa926c7c', 'Universitätsstr. 1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('8a57860ca2be4cc3a77c06c1d346ea57', 'e141f19ca6da2938d4c51cc59462884b', '', 1591714589, 1591714589);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '28addfe18e86cc3587205734c8bc2372', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640590, 1084640599);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '44fd30e8811d0d962582fa1a9c452bdd', '30', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '613cfdf6aa1072e21a1edfcfb0445c69', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '72723662c924e785a6662f42c84b8bb4', '', 1591714586, 1591714586);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', '7c1a8f6001cfdcb9e9c33eeee0ef343d', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', 'afb8675e2257c03098aa34b2893ba686', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('a8c03520e8ad9dc90fb2d161ffca7d7b', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '2650f839a2a02d99f82d4a6c019da329', '1', 1591713936, 1591713936);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '28addfe18e86cc3587205734c8bc2372', '0', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '3089b4bf392b42e8d21218f29b24f799', '76ed43ef286fb55cf9e41beadb484a9f', 1084640520, 1084640528);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '44fd30e8811d0d962582fa1a9c452bdd', '150', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '72723662c924e785a6662f42c84b8bb4', '', 1591714586, 1591714586);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', '7c1a8f6001cfdcb9e9c33eeee0ef343d', '1', 0, 0);
REPLACE INTO `resource_properties` (`resource_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b17c4ea6e053f2fffba8a5517fc277b3', 'b79b77f40706ed598f5403f953c1f791', '1', 0, 0);

--
-- Dumping data for table `resource_requests`
--

REPLACE INTO `resource_requests` (`id`, `course_id`, `termin_id`, `metadate_id`, `user_id`, `last_modified_by`, `resource_id`, `category_id`, `comment`, `reply_comment`, `reply_recipients`, `closed`, `mkdate`, `chdate`, `begin`, `end`, `preparation_time`, `marked`) VALUES
    ('b73b58e393bea88e9938744a4843ab45', 'a07535cf2f8a72df33c12ddfa4b53dde', '86de155d92a8f2da7ed6cd8ed9c08d71', '', '76ed43ef286fb55cf9e41beadb484a9f', '76ed43ef286fb55cf9e41beadb484a9f', '728f1578de643fb08b32b4b8afb2db77', '85d62e2a8a87a2924db8fc4ed3fde09d', '', NULL, 'lecturer', 0, 1698857463, 1698857463, 0, 0, 900, 0);


--
-- Dumping data for table `resource_request_properties`
--

REPLACE INTO `resource_request_properties` (`request_id`, `property_id`, `state`, `mkdate`, `chdate`) VALUES('b73b58e393bea88e9938744a4843ab45', '44fd30e8811d0d962582fa1a9c452bdd', '20', 1591714392, 1591714392);
