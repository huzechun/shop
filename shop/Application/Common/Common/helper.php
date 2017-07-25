<?php
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}
/**
 * 验证码检查函数
 */
function check_verify($code, $id = '') {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 打印函数
 */
if (!function_exists('p')) {
    function p($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * 全局变量
 *
 * @param        $name  变量名
 * @param string $value 变量值
 *
 * @return mixed 返回值
 */
if (!function_exists('v')) {
    function v($name = null, $value = '[null]') {
        static $vars = [];
        if (is_null($name)) {
            return $vars;
        } else if ($value == '[null]') {
            //取变量
            $tmp = $vars;
            foreach (explode('.', $name) as $d) {
                if (isset($tmp[$d])) {
                    $tmp = $tmp[$d];
                } else {
                    return null;
                }
            }

            return $tmp;
        } else {
            //设置
            $tmp = &$vars;
            foreach (explode('.', $name) as $d) {
                if (!isset($tmp[$d])) {
                    $tmp[$d] = [];
                }
                $tmp = &$tmp[$d];
            }

            return $tmp = $value;
        }
    }
}
function site_url($url, $arg = []) {
    $info = explode('.', $url);
    return __APP__ . "?mo={$info[0]}&ac={$info[1]}&t=site&" . http_build_query($arg);
}

function web_url($url, $arg = []) {
    $info = explode('.', $url);
    return __APP__ . "?mo={$info[0]}&ac={$info[1]}&t=web&" . http_build_query($arg);
}




