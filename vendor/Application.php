<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 17:59
 */
namespace vendor;

use app\controller\BaseController;
use vendor\base\Event;
use vendor\ActionEvent;

class Application
{

    public $controllerNamespace = 'app\controller';

    public function __construct()
    {

    }


    public function run()
    {

        list($controllerID,$action) = $this->getRoute();

        if(empty($controllerID) || empty($action)){

            //throw new \Exception("invalid route!");   最外层没有catch
            trigger_error("invalid route!",E_USER_ERROR);

        }else{
            $controllerID = ucfirst($controllerID)."Controller";
            $action       = 'action'.ucfirst($action);
        }

        $controllerID = $this->controllerNamespace.'\\'.$controllerID;
        $controllerObj = new $controllerID();

        Event::trigger(ActionEvent::EVENT_BEFORE_ACTION);
        call_user_func_array([$controllerObj,$action],[]);
        Event::trigger(ActionEvent::EVENT_AFTER_ACTION);

    }

    public function resolveRequest()
    {

    }

    protected function getRoute()
    {
        $params = $_GET;
        $controllerID = isset($params['c']) ? $params['c'] : 'site';
        $action       = isset($params['a']) ? $params['a'] : 'index';
        return [$controllerID,$action];
    }

}