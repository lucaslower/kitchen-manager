<?php
// KitchenManager class, extending from Sequel PDO class
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/sequel.php";

class KitchenManager extends Sequel{

    public $app_session_prefix;

    public function __construct(){

        // initialize .env helper
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $db_user = $_ENV['DB_USER'];
        $db_pass = $_ENV['DB_PASS'];
        $db_name = $_ENV['DB_NAME'];
        $this->app_session_prefix = $db_name;

        parent::__construct(new PDO("mysql:host=localhost;dbname=$db_name;charset=utf8mb4;", $db_user, $db_pass));
    }

    // MANAGE ITEMS

    public function item_add($brand, $name, $stock, $restock, $category, $location){
        // insert the item
        $item_id = $this->insert(
            'items',
            array(
                "item_brand" => $brand,
                "item_name" => $name,
                "item_stock" => $stock,
                "item_restock" => $restock,
                "category_id" => $category,
                "location_id" => $location
            )
        );
        return $item_id;
    }

    public function item_edit($brand, $name, $stock, $restock, $category, $location, $item_id){
        $curinfo = $this->selectOne('items', array('item_id' => $item_id));
        if(!$curinfo['item_restock'] && $stock == 0){
            $this->delete('items', array('item_id' => $item_id));
        }
        else $this->update('items',
            array(
                "item_brand" => $brand,
                "item_name" => $name,
                "item_stock" => $stock,
                "item_restock" => $restock,
                "category_id" => $category,
                "location_id" => $location
            ),
            array('item_id' => $item_id)
        );
    }

    public function category_add($name){
        $cat_id = $this->insert(
            'categories',
            array('category_name' => $name)
        );
        return $cat_id;
    }

    public function location_add($code,$desc){
        $loc_id = $this->insert(
            'locations',
            array('location_code' => $code, 'location_desc' => $desc)
        );
        return $loc_id;
    }
}