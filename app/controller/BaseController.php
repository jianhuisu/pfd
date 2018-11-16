<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 18:16
 */
namespace app\controller;

use vendor\ActionEvent;
use vendor\base\Event;

class BaseController
{
    public function __construct()
    {
        Event::on(ActionEvent::EVENT_BEFORE_ACTION,[$this,ActionEvent::EVENT_BEFORE_ACTION]);
        Event::on(ActionEvent::EVENT_AFTER_ACTION,[$this,ActionEvent::EVENT_AFTER_ACTION]);
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

    protected function render($view,$params)
    {

        $callClass = get_called_class();
        $paths = explode("\\",$callClass);
        $viewPath = strtolower( str_replace("Controller",'',array_pop($paths)) );
        $file = WEB_ROOT.'/app/view/'.$viewPath.'/'.$view.'.php';


        // 1  输出压缩 gzip
        // 2  多层缓存

        if( file_exists($file) ){

            if(ob_get_level() == 0){
                ob_start();
            }

            ob_implicit_flush(false);
            extract($params, EXTR_OVERWRITE);
            require $file;
            return ob_get_clean();
        } else {
            trigger_error("view file {$file} is not exists",E_USER_ERROR);
        }


    }

}