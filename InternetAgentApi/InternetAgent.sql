/*
Navicat MySQL Data Transfer

Source Server         : 10.0.0.1
Source Server Version : 50552
Source Host           : 10.0.0.1:3306
Source Database       : InternetAgent

Target Server Type    : MYSQL
Target Server Version : 50552
File Encoding         : 65001

Date: 2019-09-05 12:21:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `InternetAgentMessage`
-- ----------------------------
DROP TABLE IF EXISTS `InternetAgentMessage`;
CREATE TABLE `InternetAgentMessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `read` int(1) DEFAULT '0',
  `direction` int(1) DEFAULT '0',
  `author` text,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `index01` (`user_id`,`date`,`direction`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of InternetAgentMessage
-- ----------------------------
INSERT INTO `InternetAgentMessage` VALUES ('1', '2', '2019-09-03 13:40:08', '1', '1', 'Система', 'Открыта входная дверь');
INSERT INTO `InternetAgentMessage` VALUES ('2', '0', '2019-09-03 13:41:12', '0', '1', 'Система', 'Закрыта входная дверь');
INSERT INTO `InternetAgentMessage` VALUES ('4', '2', '2019-09-04 18:24:22', '1', '0', '', 'Это тест');

-- ----------------------------
-- Table structure for `InternetAgentToken`
-- ----------------------------
DROP TABLE IF EXISTS `InternetAgentToken`;
CREATE TABLE `InternetAgentToken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `token` char(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_token` (`token`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of InternetAgentToken
-- ----------------------------
INSERT INTO `InternetAgentToken` VALUES ('1', 'Лена', '397156775c40ac94b63ff259620066');
INSERT INTO `InternetAgentToken` VALUES ('2', 'Евгений', '397156775c40ac94b63ff259620065');
