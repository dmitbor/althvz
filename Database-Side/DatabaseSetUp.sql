-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2016 at 02:57 PM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hvzalt`
-- 

-- --------------------------------------------------------

--
-- Table structure for table `hvzarsenalclaims`
--

CREATE TABLE IF NOT EXISTS `hvzarsenalclaims` (
  `claimid` int(11) NOT NULL AUTO_INCREMENT,
  `wpnid` int(11) NOT NULL,
  `claimerid` int(11) NOT NULL,
  `claimstate` int(11) NOT NULL,
  `claimdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`claimid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzarsenalitems`
--

CREATE TABLE IF NOT EXISTS `hvzarsenalitems` (
  `wpnid` int(11) NOT NULL AUTO_INCREMENT,
  `wpnname` text NOT NULL,
  `wpnpic` text,
  `wpnnum` int(11) NOT NULL,
  `wpncost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`wpnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzbackground`
--

CREATE TABLE IF NOT EXISTS `hvzbackground` (
  `storyid` int(11) NOT NULL AUTO_INCREMENT,
  `storygame` int(11) NOT NULL,
  `storytitle` text NOT NULL,
  `storydescription` text NOT NULL,
  `storystate` int(11) NOT NULL DEFAULT '0',
  `storylock` text,
  PRIMARY KEY (`storyid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzgame`
--

CREATE TABLE IF NOT EXISTS `hvzgame` (
  `gameId` int(11) NOT NULL AUTO_INCREMENT,
  `gameName` text NOT NULL,
  `gameAcsCode` text NOT NULL,
  `gameState` int(11) NOT NULL DEFAULT '0',
  `gameIcon` text,
  `gameIsPrimary` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzgamemissions`
--

CREATE TABLE IF NOT EXISTS `hvzgamemissions` (
  `missionId` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL,
  `ismisprimary` int(11) NOT NULL DEFAULT '1',
  `missionState` int(11) NOT NULL DEFAULT '0',
  `missionHumanTitle` text,
  `missionZombieTitle` text,
  `missionHumanText` text,
  `missionZombieText` text,
  `missionPostHumanText` text,
  `missionPostZombieText` text,
  `missionSpecificPlayers` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`missionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzglobalnews`
--

CREATE TABLE IF NOT EXISTS `hvzglobalnews` (
  `newsId` int(11) NOT NULL AUTO_INCREMENT,
  `newsTitle` text NOT NULL,
  `newsText` text NOT NULL,
  `newsTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `newsEmailSent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzgroups`
--

CREATE TABLE IF NOT EXISTS `hvzgroups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `leaderId` int(11) NOT NULL,
  `grouptype` int(11) NOT NULL,
  `groupname` text NOT NULL,
  `groupsubtitle` text NOT NULL,
  `grouptext` text NOT NULL,
  `grouppic` text NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzmissionplayerassoc`
--

CREATE TABLE IF NOT EXISTS `hvzmissionplayerassoc` (
  `missionID` int(11) NOT NULL,
  `playerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzmissionstagassoc`
--

CREATE TABLE IF NOT EXISTS `hvzmissionstagassoc` (
  `associationid` int(11) NOT NULL AUTO_INCREMENT,
  `tagid` int(11) NOT NULL,
  `missionid` int(11) NOT NULL,
  PRIMARY KEY (`associationid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzsmallevents`
--

CREATE TABLE IF NOT EXISTS `hvzsmallevents` (
  `evntId` int(11) NOT NULL AUTO_INCREMENT,
  `evntType` int(11) NOT NULL,
  `evtDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usrSubjctId` int(11) NOT NULL,
  `relevantId` int(11) DEFAULT NULL,
  PRIMARY KEY (`evntId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvztagnums`
--

CREATE TABLE IF NOT EXISTS `hvztagnums` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `gameId` int(11) NOT NULL,
  `tagcode` text NOT NULL,
  `faketagused` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvztags`
--

CREATE TABLE IF NOT EXISTS `hvztags` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `tagerid` int(11) NOT NULL,
  `taggedid` int(11) NOT NULL,
  `tagdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `taggameid` int(11) NOT NULL,
  PRIMARY KEY (`tagid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzusers`
--

CREATE TABLE IF NOT EXISTS `hvzusers` (
  `usrID` int(11) NOT NULL AUTO_INCREMENT,
  `usrLogin` text NOT NULL,
  `usrSaltyPass` text NOT NULL,
  `Salt` text NOT NULL,
  `usrEmail` text NOT NULL,
  `ChatState` int(11) NOT NULL DEFAULT '0',
  `usrEmailState` int(11) NOT NULL DEFAULT '1',
  `usrForgotPass` int(11) NOT NULL DEFAULT '0',
  `usrForgotSetDate` datetime DEFAULT NULL,
  `usrForgotConfirm` text,
  PRIMARY KEY (`usrID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzuserstate`
--

CREATE TABLE IF NOT EXISTS `hvzuserstate` (
  `userid` int(11) NOT NULL,
  `userteam` int(11) NOT NULL,
  `userlastfed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usergame` int(11) NOT NULL DEFAULT '0',
  `checknews` int(11) NOT NULL DEFAULT '1',
  `checkmissions` int(11) NOT NULL DEFAULT '1',
  `missedmissions` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvzusrinfo`
--

CREATE TABLE IF NOT EXISTS `hvzusrinfo` (
  `usrid` int(11) NOT NULL,
  `usrname` text NOT NULL,
  `usrdesc` text,
  `usravy` text,
  `usrgroupliveId` int(11) DEFAULT NULL,
  `usrgrouplivetitle` text,
  `usrgroupdeadId` int(11) DEFAULT NULL,
  `usrgroupdeadtitle` text,
  UNIQUE KEY `usrid` (`usrid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
