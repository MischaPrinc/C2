
--
-- Table structure for table `online`
--

DROP TABLE IF EXISTS `online`;

CREATE TABLE `online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hardwareId` varchar(255) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hardwareId` varchar(255) NOT NULL,
  `task` text NOT NULL,
  `status` enum('pending','in progress','completed') NOT NULL DEFAULT 'pending',
  `result` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

