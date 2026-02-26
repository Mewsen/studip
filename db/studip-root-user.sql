# Anlegen des Benutzers root

# Benutzer: root@studip ; Password: testing
INSERT IGNORE INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('76ed43ef286fb55cf9e41beadb484a9f', 'root@studip', '$2a$08$E2oDjsPcmCrnJT3GiSvHGugLhyUc1mlpcB3FstDzgMwgnIdzi8iSW', 'root', 'Root', 'Studip', 'root@localhost', '', 'standard', 0, NULL, NULL, 'unknown');
INSERT IGNORE INTO `user_info` (`user_id`, `hobby`, `lebenslauf`, `publi`, `schwerp`, `Home`, `privatnr`, `privatcell`, `privadr`, `score`, `geschlecht`, `mkdate`, `chdate`, `title_front`, `title_rear`, `preferred_language`, `smsforward_copy`, `smsforward_rec`, `email_forward`, `motto`, `lock_rule`) VALUES('76ed43ef286fb55cf9e41beadb484a9f', '', '', '', '', '', '', '', '', 0, 0, 0, 0, '', '', NULL, 1, '', 0, '', '');
