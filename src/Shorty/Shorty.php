<?php
/**
 * Class for shortening urls.
 * Author: Brett Thomas (brett.thomas@gmail.com)
 *
 */

namespace Shorty;

class Shorty
{
    const DB_HOSTNAME   = 'localhost';
    const DB_PORT       = 3306;
    const DB_USERNAME   = 'shorty';
    const DB_PASSWORD   = 'shorty';
    const DB_DATABASE   = 'shorty';
    const DB_TABLE      = 'shorty';

    const SLUG_CHAR_COUNT = 6;
    const SLUG_CHAR_LIST = 'abcdefghijklmnopqrstuvwxyz0123456789';

    /** @var mysqli $_mysqli */
    private $_mysqli;

    /**
     * connect to the db and save the connection
     * @throws \Exception
     * @return bool|\mysqli
     */
    private function _getMysqli(){
        if(!($this->_mysqli instanceof \mysqli)){
            $this->_mysqli = @new \mysqli(static::DB_HOSTNAME, static::DB_USERNAME, static::DB_PASSWORD, static::DB_DATABASE, static::DB_PORT);
            if($this->_mysqli->connect_error){
                throw new \Exception("Error connecting to database: {$this->_mysqli->connect_error}");
            }
        } else {
            if(!$this->_mysqli->ping()){
                $this->_mysqli = null;
                $this->_getMysqli();
            }
        }
        return $this->_mysqli;
    }

    public function getUrlFromSlug($slug){
        $slug = $this->_getMysqli()->escape_string($slug);
        $result = $this->_getMysqli()->query("SELECT url FROM `".static::DB_DATABASE."`.`".static::DB_TABLE."` WHERE `slug`='{$slug}'")->fetch_assoc();
        if($result && array_key_exists('url', $result)){
            return $result['url'];
        }
        return false;
    }

    public function redirect($slug){
        if($url = $this->getUrlFromSlug($slug)){
            $slug = $this->_getMysqli()->escape_string($slug);
            $this->_getMysqli()->query("UPDATE `".static::DB_DATABASE."`.`".static::DB_TABLE."` SET `hits` = `hits` + 1  WHERE `slug` = '{$slug}'");
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: {$url}");
        } else {
            throw new \Exception('Invalid Code');
        }
    }

    /**
     * shorten a url and save to the database
     * @param $url
     * @param null|string $slug
     * @return null|string
     * @throws Exception
     */
    public function shorten($url, $slug = null){
        if(!$this->isValidURL($url)){
            throw new \Exception('Invalid url format!');
        }

        if(!$slug){
            $slug = $this->generateSlug();
        }

        $slug = $this->_getMysqli()->escape_string($slug);
        $url = $this->_getMysqli()->escape_string($url);
        if($this->_getMysqli()->query("INSERT INTO `".static::DB_DATABASE."`.`".static::DB_TABLE."` (slug, url) VALUES ('{$slug}', '{$url}')") !== true){
            throw new \Exception("Error inserting row: {$this->_getMysqli()->error}");
        }
        return $slug;
    }

    public function isValidURL($url){
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            return false;
        }
        return true;
    }

    /**
     * generate a unique slug
     * @return string
     */
    public function generateSlug(){
        $slug = '';
        $charList = static::SLUG_CHAR_LIST;
        for ($i = 0; $i < static::SLUG_CHAR_COUNT; $i++) {
            $slug .= $charList[rand(0, strlen($charList) - 1)];
        }

        if(!$this->slugIsUnique($slug)){
            return $this->generateSlug();
        }
        return $slug;
    }

    /**
     * check if a slug already exists
     * @param string $slug
     * @return bool
     */
    private function slugIsUnique($slug){
        $result = $this->_getMysqli()->query("SELECT COUNT(1) AS CNT FROM `".static::DB_DATABASE."`.`".static::DB_TABLE."` WHERE slug = '{$slug}'");
        $result = $result->fetch_assoc();
        if($result['CNT'] < 1) return true;
        return false;
    }
} 