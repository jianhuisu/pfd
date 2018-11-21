<?php
/**
 * User: sujianhui
 * Date: 2018/10/22
 * Time: 17:59
 */
namespace vendor;

use vendor\base\Event;

class Application extends \vendor\base\Application
{

    const EVENT_BEFORE_ACTION = 'beforeAction';
    const EVENT_AFTER_ACTION = 'afterAction';

    public $controllerNamespace = 'app\controller';

    public function __construct()
    {
        $this->init();
    }

    public function beforeAction($event)
    {

    }

    public function afterAction($event)
    {

    }

    private function init()
    {
        set_error_handler('\vendor\ErrorHandle::hand',E_ALL | E_STRICT );
        register_shutdown_function(['\vendor\Shutdown','shutdownFunc']);
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

        $this->on(self::EVENT_BEFORE_ACTION,[$this,'beforeAction']);
        $this->on(self::EVENT_AFTER_ACTION,[$this,'afterAction']);

        $controllerID = $this->controllerNamespace.'\\'.$controllerID;
        $controllerObj = new $controllerID();

        $this->trigger(self::EVENT_BEFORE_ACTION);
        call_user_func_array([$controllerObj,$action],[]);
        $this->trigger(self::EVENT_AFTER_ACTION);

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