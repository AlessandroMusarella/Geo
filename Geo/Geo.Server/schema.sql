CREATE TABLE `data` (
                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                        `CLIENT_ID` int(11) NOT NULL,
                        `TEMPERATURE` float NOT NULL,
                        `LIGHT` float NOT NULL,
                        `HUMIDITY` float NOT NULL,
                        `UPLOAD_DATE` datetime NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4