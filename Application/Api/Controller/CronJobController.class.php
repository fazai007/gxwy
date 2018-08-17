<?php

namespace Api\Controller;

use Think\Controller;

class CronJobController extends Controller {

    public function checkLocation() {
        if ('f.xilukeji.com' != strtolower($_SERVER['HTTP_HOST'])) {
            xilu_log('Runtime/Logs/Api/url.log', strtolower($_SERVER['HTTP_HOST']) . '--denied', true);
            exit();
        }
    }

    /**
     * test
     */
    public function test() {
        $this->checkLocation();
        xilu_log('Runtime/Logs/Api/test.log', 'test--start', true);
        
        //code...
        
        xilu_log('Runtime/Logs/Api/test.log', 'test--end', true);
        echo 'done';
    }

}
