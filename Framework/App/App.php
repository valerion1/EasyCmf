<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Traits\Singleton;
use Framework\Http\Routing\Router;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Database\DB;
use Framework\Database\Table;

class App {

    use Singleton;

    private $router;

    private $request;

    private $response;

    private $db;

    private $table;

    private $configApp = [];

    private $configDb = [];


    protected function __construct() {

        $this->router = Router::getInstance();

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

        $this->db = DB::getInstance();

        $this->table = Table::getInstance();


        $this->setConfig();

        $this->makeDbConnections();

    }

    public function run() {

        $this->routing();

    }

    protected function routing() {

        $route = new Route($this->router);

        require __DIR__ . '/../../route.php';

        $this->router->run();
    }

    protected function setConfig() {
        $this->configApp =  include __DIR__ . '/../config/config.php';
        $this->configDb =  include __DIR__ . '/../../config/db.php';
    }

    protected function makeDbConnections() {

        $connections = $this->configDb;

        foreach ($connections as $key => $value){
            DB::pushConnection($key, $value['driver'], $value['host'], $value['port'], $value['database'], $value['username'] . ':' . $value['password'], stristr($value['mode'], 'write'), stristr($value['mode'], 'read'));
        }
        
    }

    public function __toString() : string {

//        $result = (string)$this->response;

        return '';
    }

    public function debug() {
        return $this->configApp['debug'];
    }


}