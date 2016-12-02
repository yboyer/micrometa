<?php

namespace YF\Provider;

class JsonManager
{
    private static $INSTANCE = null;

    private $dataFilename = 'data.json';
    private $data = [];


    private function __construct()
    {
        if (!file_exists($this->dataFilename)) {
            file_put_contents($this->dataFilename, '{}');
        }
        $this->data = json_decode(file_get_contents($this->dataFilename), true);
    }

    /**
     * Singleton method to return an unique instance of the class
     * @return The unique instance of the class
     */
    public static function getInstance(): JsonManager
    {
        if (is_null(self::$INSTANCE)) {
            self::$INSTANCE = new JsonManager();
        }

        return self::$INSTANCE;
    }

    /**
     * Checks if the the key exists
     * @param $key The key
     * @param True if the key exists
     */
    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Retreive the value to the key index
     * @param $key The key
     * @return The content of the key index
     */
    public function get(string $key)
    {
        return $this->data[$key];
    }

    /**
     * Sets the value to the key index
     * @param $key The key
     * @param $key The Value
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
        file_put_contents($this->dataFilename, json_encode($this->data));
    }
}
