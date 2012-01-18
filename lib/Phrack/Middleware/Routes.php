<?php
require_once('Phrack/Middleware.php');
require_once('Phrack/Util.php');

class Phrack_Middleware_Routes extends Phrack_Middleware
{
    protected $map; // instance of Net_URL_Mapper

    public function call(&$environ)
    {
        try {
            $route = $this->map->match($environ['PATH_INFO']);
        }
        catch (Net_URL_Mapper_InvalidException $e) {
            $route = null;
        }

        if ($route) {
            $environ['routes.route'] = $route;
            $environ['routes.url'] = $this->map;
            return $this->callApp($environ);
        }

        $status = Phrack_Util::statusText(404);
        $headers = array(array('Content-Type', 'text/plain'),
                         array('Content-Length', mb_strlen($status)));
        return array($status, $headers, array($status));
    }

    static public function wrap()
    {
        $args =& func_get_args();
        return parent::wrap(__CLASS__, $args);
    }
}
