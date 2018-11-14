<?php
/**
 * User: sujianhui
 * Date: 2018/10/11
 * Time: 13:27
 */
namespace vendor;

class FileSession extends \SessionHandler
{
    private $savePath;

    function __construct()
    {
        register_shutdown_function('session_write_close');
    }

    function open($savePath, $sessionName)
    {

        $this->savePath = WEB_ROOT.'/runtime/session';
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        @session_start();

        return true;
    }

    function close()
    {
        echo __FUNCTION__."<br/>";
        return true;
    }

    function read($id)
    {
        $file = "$this->savePath/sess_$id";
        if(file_exists($file)){
            return (string)@file_get_contents($file);
        }

        return '';
    }

    function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

}