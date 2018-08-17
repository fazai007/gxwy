<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

namespace Think;

class AjaxPage {

    // 分页栏每页显示的页数
    public $roll_page = 10;
    // 页数跳转时要带的参数
    public $parameter;
    // 默认列表每页显示行数
    public $list_rows = 20;
    // 起始行数
    public $first_row;
    // 分页总页面数
    protected $total_pages;
    // 总行数
    protected $total_rows;
    // 当前页数
    protected $now_page;
    // 分页的栏的总页数
    protected $cool_pages;
    // 分页显示定制
    protected $config = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev' => '<<',
        'next' => '>>',
        'first' => '1',
        'last' => '%TOTAL_PAGE%',
        'theme' => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %HEADER%',
    );
    // 默认分页变量名
    protected $var_page;

    public function __construct($total_rows, $list_rows = '', $ajax_func, $parameter = '') {
        $this->total_rows = $total_rows;
        $this->ajax_func = $ajax_func;
        $this->parameter = $parameter;
        $this->var_page = C('VAR_PAGE') ? C('VAR_PAGE') : 'page';
        if (!empty($list_rows)) {
            $this->list_rows = intval($list_rows);
        }
        $this->total_pages = ceil($this->total_rows / $this->list_rows);     //总页数
        $this->cool_pages = ceil($this->total_pages / $this->roll_page);
        $this->now_page = !empty($_GET[$this->var_page]) ? intval($_GET[$this->var_page]) : 1;
        if (!empty($this->total_pages) && $this->now_page > $this->total_pages) {
            $this->now_page = $this->total_pages;
        }
        $this->first_row = $this->list_rows * ($this->now_page - 1);
    }

    public function setConfig($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    public function show() {
        if (0 == $this->total_rows)
            return '';
        $p = $this->var_page;
        $now_cool_page = ceil($this->now_page / $this->roll_page);
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        //上一页
        $up_row = $this->now_page - 1;
        $up_page = $up_row > 0 ? '<li><a href="javascript:' . $this->ajax_func . '(' . $up_row . ')">' . $this->config['prev'] . '</a></li>' : '<li class="disabled"><span>' . $this->config['prev'] . '</span></li>';
        //下一页
        $down_row = $this->now_page + 1;
        $down_page = ($down_row <= $this->total_pages) ? '<li><a href="javascript:' . $this->ajax_func . '(' . $down_row . ')">' . $this->config['next'] . '</a></li>' : '<li class="disabled"><span>' . $this->config['next'] . '</span></li>';
        if ($this->total_rows <= $this->list_rows) {
            $up_page = $down_page = '';
        }
        //第一页
        if ($now_cool_page == 1) {
            $the_first = '';
        } else {
            $the_first = '<li><a href="javascript:' . $this->ajax_func . '(1)">' . $this->config['first'] . ' ...</a></li>';
        }
        //最后一页
        if ($now_cool_page == $this->cool_pages) {
            $the_end = '';
        } else {
            $the_end = '<li><a href="javascript:' . $this->ajax_func . '(' . $this->total_pages . ')">...' . $this->config['last'] . '</a></li>';
        }
        $link_page = '';
        for ($i = 1; $i <= $this->roll_page; $i++) {
            $page = ($now_cool_page - 1) * $this->roll_page + $i;
            if ($page != $this->now_page) {
                if ($page <= $this->total_pages) {
                    $link_page .= '<li><a href="javascript:' . $this->ajax_func . '(' . $page . ')">' . $page . '</a></li>';
                } else {
                    break;
                }
            } else {

                if ($this->total_pages != 1) {
                    $link_page .= '<li class="active"><span>' . $page . '</span></li>';
                }
            }
        }
        $page_str = str_replace(array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%DOWN_PAGE%', '%TOTAL_ROW%', '%TOTAL_PAGE%'), array($this->config['header'], $this->now_page, $up_page, $the_first, $link_page, $the_end, $down_page, $this->total_rows, $this->total_pages), $this->config['theme']);
        return '<ul class="pagination">' . $page_str . '</ul>';
    }

}
