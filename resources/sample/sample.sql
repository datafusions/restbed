--
-- Database: `restbed`
--

-- --------------------------------------------------------

--
-- Table structure for table `sample`
--

CREATE TABLE IF NOT EXISTS `sample` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(32) NOT NULL,
  `number` int(10) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sample`
--

INSERT INTO `sample` (`uid`, `last_modified`, `name`, `number`) VALUES
(1, '2010-05-05 17:47:33', 'Sample 1', 1),
(2, '2010-05-05 17:47:33', 'Sample 2', 2);
