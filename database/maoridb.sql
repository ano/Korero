-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2015 at 07:05 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `maoridb`
--
CREATE DATABASE IF NOT EXISTS `maoridb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `maoridb`;

DELIMITER $$
--
-- Functions
--
DROP FUNCTION IF EXISTS `jaro_winkler_similarity`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `jaro_winkler_similarity`(
in1 varchar(255),
in2 varchar(255)
) RETURNS float
    DETERMINISTIC
BEGIN
#finestra:= search window, curString:= scanning cursor for the original string, curSub:= scanning cursor for the compared string
declare finestra, curString, curSub, maxSub, trasposizioni, prefixlen, maxPrefix int;
declare char1, char2 char(1);
declare common1, common2, old1, old2 varchar(255);
declare trovato boolean;
declare returnValue, jaro float;
set maxPrefix=6; #from the original jaro - winkler algorithm
set common1="";
set common2="";
set finestra=(length(in1)+length(in2)-abs(length(in1)-length(in2))) DIV 4
+ ((length(in1)+length(in2)-abs(length(in1)-length(in2)))/2) mod 2;
set old1=in1;
set old2=in2;

#calculating common letters vectors
set curString=1;
while curString<=length(in1) and (curString<=(length(in2)+finestra)) do
set curSub=curstring-finestra;
if (curSub)<1 then
set curSub=1;
end if;
set maxSub=curstring+finestra;
if (maxSub)>length(in2) then
set maxSub=length(in2);
end if;
set trovato = false;
while curSub<=maxSub and trovato=false do
if substr(in1,curString,1)=substr(in2,curSub,1) then
set common1 = concat(common1,substr(in1,curString,1));
set in2 = concat(substr(in2,1,curSub-1),concat("0",substr(in2,curSub+1,length(in2)-curSub+1)));
set trovato=true;
end if;
set curSub=curSub+1;
end while;
set curString=curString+1;
end while;
#back to the original string
set in2=old2;
set curString=1;
while curString<=length(in2) and (curString<=(length(in1)+finestra)) do
set curSub=curstring-finestra;
if (curSub)<1 then
set curSub=1;
end if;
set maxSub=curstring+finestra;
if (maxSub)>length(in1) then
set maxSub=length(in1);
end if;
set trovato = false;
while curSub<=maxSub and trovato=false do
if substr(in2,curString,1)=substr(in1,curSub,1) then
set common2 = concat(common2,substr(in2,curString,1));
set in1 = concat(substr(in1,1,curSub-1),concat("0",substr(in1,curSub+1,length(in1)-curSub+1)));
set trovato=true;
end if;
set curSub=curSub+1;
end while;
set curString=curString+1;
end while;
#back to the original string
set in1=old1;

#calculating jaro metric
if length(common1)<>length(common2)
then set jaro=0;
elseif length(common1)=0 or length(common2)=0
then set jaro=0;
else
#calcolo la distanza di winkler
#passo 1: calcolo le trasposizioni
set trasposizioni=0;
set curString=1;
while curString<=length(common1) do
if(substr(common1,curString,1)<>substr(common2,curString,1)) then
set trasposizioni=trasposizioni+1;
end if;
set curString=curString+1;
end while;
set jaro=
(
length(common1)/length(in1)+
length(common2)/length(in2)+
(length(common1)-trasposizioni/2)/length(common1)
)/3;

end if; #end if for jaro metric

#calculating common prefix for winkler metric
set prefixlen=0;
while (substring(in1,prefixlen+1,1)=substring(in2,prefixlen+1,1)) and (prefixlen<6) do
set prefixlen= prefixlen+1;
end while;


#calculate jaro-winkler metric
return jaro+(prefixlen*0.1*(1-jaro));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dialects`
--

DROP TABLE IF EXISTS `dialects`;
CREATE TABLE IF NOT EXISTS `dialects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dialects` varchar(45) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dialects_language_idx` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
CREATE TABLE IF NOT EXISTS `words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maori` varchar(95) NOT NULL,
  `english` text NOT NULL,
  `description` text,
  `dialects_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_words_dialects1_idx` (`dialects_id`),
  KEY `indx_maori` (`maori`) USING BTREE,
  FULLTEXT KEY `indx_english` (`english`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11295 ;

-- --------------------------------------------------------

--
-- Table structure for table `words_archive`
--

DROP TABLE IF EXISTS `words_archive`;
CREATE TABLE IF NOT EXISTS `words_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_id` int(11) NOT NULL,
  `maori` varchar(95) NOT NULL,
  `english` text NOT NULL,
  `description` text,
  `dialects_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_words_dialects1_idx` (`dialects_id`),
  KEY `indx_maori` (`maori`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dialects`
--
ALTER TABLE `dialects`
  ADD CONSTRAINT `fk_dialects_language` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `words`
--
ALTER TABLE `words`
  ADD CONSTRAINT `fk_words_dialects1` FOREIGN KEY (`dialects_id`) REFERENCES `dialects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `words_archive`
--
ALTER TABLE `words_archive`
  ADD CONSTRAINT `words_archive_ibfk_1` FOREIGN KEY (`dialects_id`) REFERENCES `dialects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
