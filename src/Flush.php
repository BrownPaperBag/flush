<?php

namespace BrownPaperBag;

/**
 * Class Flush
 * @package BrownPaperBag
 */
class Flush{

    /**
     * @const string
     */
    const HEADER_KEY_CONNECTION = 'Connection';
    /**
     * @const string
     */
    const HEADER_KEY_CONTENT_LENGTH = 'Content-Length';
    /**
     * @const string
     */
    const HEADER_KEY_CONTENT_TYPE = 'Content-Type';

    /**
     * @var bool
     */
    var $enableLimbo = false;
    /**
     * @var array
     */
    var $headers = array();
    /**
     * @var bool
     */
    var $isPrepared = false;

    /**
     * @param string $contentType
     * @param bool $enableLimbo
     */
    public function __construct($contentType = '', $enableLimbo = false){

        if($contentType){

            $this->setContentType($contentType);

        }

        $this->setLimboEnabled($enableLimbo);

    }

    /**
     * @param string $key 
     * @param string $value 
     * @return Flush
     */
    public function addHeader($key, $value){

        $this->headers[$key] = $value;

        return $this;

    }

    /**
     * @param string $key
     * @return Flush
     */
    private function applyHeader($key){

        $value = $this->getHeaderAndRemove($key);

        header(implode(': ', array($key, $value)));

        return $this;

    }

    /**
     * @return Flush
     */
    private function applyHeaders(){

        foreach(array_keys($this->getHeaders()) as $key){

            $this->applyHeader($key);

        }

        return $this;

    }

    /**
     * @param string $data
     * @param bool $prepare
     * @return Flush
     */
    public function data($data, $prepare = false){

        echo $data;

        if($this->isPrepared){

            $this->dump();

        }
        else if($prepare){

            $this->prepare();

        }

        return $this;

    }

    /**
     * @param bool $end_flush
     * @return Flush
     */
    public function dump($end_flush = false){

        if($end_flush){

            while(@ob_end_flush());

        }

        @ob_flush();
        @flush();

        return $this;

    }

    /**
     * @return string
     */
    public function getContentType(){

        return $this->getHeader(self::HEADER_KEY_CONTENT_TYPE);

    }

    /**
     * @param string $key 
     * @return string
     */
    public function getHeader($key){

        $headers = $this->getHeaders();
        $value = '';

        if(isset($headers[$key])){

            $value = $headers[$key];

        }

        return $value;

    }

    /**
     * @return array
     */
    public function getHeaders(){

        return $this->headers;

    }

    /**
     * @param string $key 
     * @return string
     */
    private function getHeaderAndRemove($key){

        $header = $this->getHeader($key);

        $this->removeHeader($key);

        return $header;

    }

    /**
     * @return bool
     */
    public function isLimboEnabled(){

        return $this->enableLimbo;

    }

    /**
     * @param string $data
     * @param bool $prepare
     * @return Flush
     */
    public function json($data, $prepare = null){

        if(is_null($prepare) && $this->isLimboEnabled()){

            $prepare = true;

        }

        return $this->data(json_encode($data), $prepare);

    }

    /**
     * @return Flush
     */
    public function prepare(){

        if($this->isLimboEnabled()){

            $this->addHeader(self::HEADER_KEY_CONTENT_LENGTH, ob_get_length());
            $this->addHeader(self::HEADER_KEY_CONNECTION, 'close');

        }

        $this->applyHeaders();

        ignore_user_abort($this->isLimboEnabled());
        set_time_limit(0);
        
        if(session_id()){

            session_write_close();

        }

        ini_set('implicit_flush', false);
        ini_set('output_buffering', false);
        ini_set('zlib.output_compression', false);

        ob_implicit_flush(false);

        $this->isPrepared = (bool) $this->dump(true);

        return $this;

    }

    /**
     * @param string $key 
     * @return Flush
     */
    public function removeHeader($key){

        unset($this->headers[$key]);

        return $this;

    }

    /**
     * @param string $value
     * @return Flush
     */
    public function setContentType($value){

        return $this->addHeader(self::HEADER_KEY_CONTENT_TYPE, $value);

    }

    /**
     * @param bool $enabled
     * @return Flush
     */
    public function setLimboEnabled($enabled){

        $this->enableLimbo = $enabled;

        return $this;

    }

    /**
     * @param bool $send
     * @return bool
     */
    public function signal($send = true){

        if($send){

            $this->data(chr(32));

        }

        return $send && !connection_aborted();

    }

}