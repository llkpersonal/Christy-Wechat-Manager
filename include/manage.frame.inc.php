<?php

/*
 * file: feature.frame.inc.php:: the management page frame
 * author: Lingkun Li
 * date: 26th.Jan.2014
 */

require_once('feature.frame.inc.php');

class ManageFrame extends FeatureFrame{
    public function __construct($title = '站点设置 - Chirsty微信公众平台管理系统'){
        parent::__construct($title);
        $featurelinks = "";
        $featurelinks .= "<li><a href=\"weconfig.php\">公众账号设置</a></li>";
        $featurelinks .= "<li><a href=\"users.php\">用户设置</a></li>";
        $featurelinks .= "<li><a href=\"links.php\">友情链接设置</a></li>";
        $this->header = str_replace("{template_featurelink}",$featurelinks,$this->header);
    }
}

?>