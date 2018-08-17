<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Think;

class Page {

    public $first_row; // 起始行数
    public $list_rows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $total_rows; // 总行数
    public $total_pages; // 分页总页面数
    public $roll_page = 11; // 分页栏每页显示的页数
    public $last_suffix = true; // 最后一页是否显示总页数
    private $p = 'p'; //分页参数名
    private $url = ''; //当前链接URL
    private $now_page = 1;
    // 分页显示定制
    private $config = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev' => '<<',
        'next' => '>>',
        'first' => '1',
        'last' => '%TOTAL_PAGE%',
        'theme' => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %HEADER%',
    );

    /**
     * 架构函数
     * @param array $total_rows  总的记录数
     * @param array $list_rows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($total_rows, $list_rows = 20, $parameter = array()) {
        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->total_rows = $total_rows; //设置总记录数
        $this->list_rows = $list_rows;  //设置每页显示行数
        $this->parameter = empty($parameter) ? $_GET : $parameter;
        $this->now_page = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->now_page = $this->now_page > 0 ? $this->now_page : 1;
        $this->first_row = $this->list_rows * ($this->now_page - 1);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page) {
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if (0 == $this->total_rows)
            return '';
        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->total_pages = ceil($this->total_rows / $this->list_rows); //总页数
        if (!empty($this->total_pages) && $this->now_page > $this->total_pages) {
            $this->now_page = $this->total_pages;
        }
        /* 计算分页临时变量 */
        $now_cool_page = $this->roll_page / 2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->last_suffix && $this->config['last'] = $this->total_pages;
        //上一页
        $up_row = $this->now_page - 1;
        $up_page = $up_row > 0 ? '<li><a href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a></li>' : '<li class="disabled"><span>' . $this->config['prev'] . '</span></li>';
        //下一页
        $down_row = $this->now_page + 1;
        $down_page = ($down_row <= $this->total_pages) ? '<li><a href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a></li>' : '<li class="disabled"><span>' . $this->config['next'] . '</span></li>';
        if ($this->total_rows <= $this->list_rows) {
            $up_page = $down_page = '';
        }
        //第一页
        $the_first = '';
        if ($this->total_pages > $this->roll_page && ($this->now_page - $now_cool_page) >= 1) {
            $the_first = '<li><a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '...</a></li>';
        }
        //最后一页
        $the_end = '';
        if ($this->total_pages > $this->roll_page && ($this->now_page + $now_cool_page) < $this->total_pages) {
            $the_end = '<li><a class="end" href="' . $this->url($this->total_pages) . '">...' . $this->config['last'] . '</a></li>';
        }
        //数字连接
        $link_page = '';
        for ($i = 1; $i <= $this->roll_page; $i++) {
            if (($this->now_page - $now_cool_page) <= 0) {
                $page = $i;
            } elseif (($this->now_page + $now_cool_page - 1) >= $this->total_pages) {
                $page = $this->total_pages - $this->roll_page + $i;
            } else {
                $page = $this->now_page - $now_cool_page_ceil + $i;
            }
            if ($page > 0 && $page != $this->now_page) {
                if ($page <= $this->total_pages) {
                    $link_page .= '<li><a href="' . $this->url($page) . '">' . $page . '</a></li>';
                } else {
                    break;
                }
            } else {
                if ($page > 0 && $this->total_pages != 1) {
                    $link_page .= '<li class="active"><span>' . $page . '</span></li>';
                }
            }
        }
        //替换分页内容
        $page_str = str_replace(array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%DOWN_PAGE%', '%TOTAL_ROW%', '%TOTAL_PAGE%'), array($this->config['header'], $this->now_page, $up_page, $the_first, $link_page, $the_end, $down_page, $this->total_rows, $this->total_pages), $this->config['theme']);
        return '<ul class="pagination">' . $page_str . '</ul>';
    }

}
