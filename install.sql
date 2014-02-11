
-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 11, 2014 at 02:00 AM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a3677379_weixin`
--

-- --------------------------------------------------------

--
-- Table structure for table `intermediate`
--

CREATE TABLE `intermediate` (
  `iid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `wechat_user` varchar(255) NOT NULL COMMENT '微信用户',
  `wid` int(11) NOT NULL COMMENT '公众平台账号',
  `protrol` int(11) NOT NULL COMMENT '步骤',
  `handle` varchar(255) NOT NULL COMMENT '处理的文件',
  PRIMARY KEY (`iid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `intermediate`
--


-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `lid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `link` varchar(255) NOT NULL COMMENT '链接地址',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `display` enum('yes','no') NOT NULL COMMENT '是否显示',
  `secquence` int(11) NOT NULL COMMENT '显示顺序',
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `links`
--

INSERT INTO `links` VALUES(1, 'http://mp.weixin.qq.com', '腾讯 - 微信公众平台', 'yes', 0);

-- --------------------------------------------------------

--
-- Table structure for table `msgindexes`
--

CREATE TABLE `msgindexes` (
  `mid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键,标识符',
  `wid` int(11) NOT NULL COMMENT '对应微信号',
  `keyword` varchar(255) NOT NULL COMMENT '接收关键字',
  `type` enum('text','pictext','music') NOT NULL COMMENT '回复类型',
  `rid` int(11) NOT NULL COMMENT '回复id号',
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `msgindexes`
--


-- --------------------------------------------------------

--
-- Table structure for table `musicreply`
--

CREATE TABLE `musicreply` (
  `rid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `musicurl` varchar(255) NOT NULL COMMENT '音乐链接',
  `hqurl` varchar(255) NOT NULL COMMENT '高清音乐链接',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `musicreply`
--


-- --------------------------------------------------------

--
-- Table structure for table `pictextreply`
--

CREATE TABLE `pictextreply` (
  `pid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `rid` int(11) NOT NULL COMMENT '回复rid',
  `title` varchar(255) NOT NULL COMMENT '图文信息标题',
  `description` varchar(255) NOT NULL COMMENT '图文信息描述',
  `picurl` varchar(255) NOT NULL COMMENT '图片地址',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `secquence` int(11) NOT NULL COMMENT '显示顺序',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pictextreply`
--


-- --------------------------------------------------------

--
-- Table structure for table `plugin`
--

CREATE TABLE `plugin` (
  `pid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `name` varchar(255) NOT NULL COMMENT '插件名称',
  `version` varchar(255) NOT NULL COMMENT '插件版本',
  `folder` varchar(255) NOT NULL COMMENT '所在文件夹',
  `protrol` enum('before','after') NOT NULL COMMENT '响应顺序',
  `keyword` varchar(255) NOT NULL COMMENT '关键词',
  `hasconfigpage` enum('yes','no') NOT NULL COMMENT '是否有配置页面',
  `wid` int(11) NOT NULL COMMENT '所在公众平台',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `plugin`
--


-- --------------------------------------------------------

--
-- Table structure for table `textreply`
--

CREATE TABLE `textreply` (
  `rid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `content` text NOT NULL COMMENT '回复内容',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `textreply`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(1) NOT NULL AUTO_INCREMENT COMMENT '用户数字ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `group` enum('admin','manager') NOT NULL COMMENT '组别',
  `admin` varchar(255) NOT NULL COMMENT '所管理的微信账号',
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES(1, 'admin', '7152ac398fae8c07540651ac48ba4a58', 'admin', '0');

-- --------------------------------------------------------

--
-- Table structure for table `weconfig`
--

CREATE TABLE `weconfig` (
  `wid` int(1) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统内标识符',
  `name` varchar(255) NOT NULL COMMENT '公众账号名',
  `wechat_id` varchar(255) NOT NULL COMMENT '公众账号',
  `welcome_msg` text NOT NULL COMMENT '欢迎语',
  `token` varchar(255) NOT NULL COMMENT 'token号',
  `fans_num` int(11) NOT NULL COMMENT '关注人数',
  `email` varchar(255) NOT NULL COMMENT '登录邮箱',
  `password` varchar(255) NOT NULL COMMENT '登录密码',
  PRIMARY KEY (`wid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `weconfig`
--


-- --------------------------------------------------------

--
-- Table structure for table `wxwall_msg`
--

CREATE TABLE `wxwall_msg` (
  `mid` int(1) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `wid` int(11) NOT NULL COMMENT '所属公众平台',
  `num` int(11) NOT NULL COMMENT '数量',
  `content` text NOT NULL COMMENT '内容',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `avatar` varchar(255) NOT NULL COMMENT '头像地址',
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `wxwall_msg`
--


-- --------------------------------------------------------

--
-- Table structure for table `wxwall_user`
--

CREATE TABLE `wxwall_user` (
  `uid` int(1) NOT NULL AUTO_INCREMENT COMMENT '主键标识符',
  `wid` int(11) NOT NULL COMMENT '所在微信号',
  `openid` varchar(255) NOT NULL COMMENT '开放ID',
  `fakeid` varchar(255) NOT NULL COMMENT '隐藏ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `wxwall_user`
--

