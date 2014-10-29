<?php

namespace BrownPaperBag;

/**
 * Class Flush
 * @package BrownPaperBag
 */
class Flush{

    /**
     * @var string
     */
    public $contentType = '';
    /**
     * @var bool
     */
    public $enableLimbo = false;
    /**
     * @var bool
     */
    public $isPrepared = false;

    /**
     * @param string $contentType
     * @param bool $enableLimbo
     */
    public function __construct($contentType = '', $enableLimbo = false){

        $this->contentType = $contentType;
        $this->enableLimbo = $enableLimbo;

    }

    /**
     * @param string $data
     * @param bool $prepare
     * @return $this
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
     * @param string $data
     * @param bool $prepare
     * @return Flush
     */
    public function json($data, $prepare = null){

        if(is_null($prepare) && $this->enableLimbo){

            $prepare = true;

        }

        return $this->data(json_encode($data), $prepare);

    }

    /**
     * @return Flush
     */
    public function prepare(){

        if($this->contentType){

            header('Content-Type: ' . $this->contentType);

        }

        if($this->enableLimbo){

            header('Content-Length: ' . ob_get_length());
            header('Connection: close');

        }

        ignore_user_abort($this->enableLimbo);
        set_time_limit(0);
        
        if(session_id()){

            session_write_close();

        }

        ini_set('output_buffering', false);
        ini_set('zlib.output_compression', false);

        $this->dump(true);

        ini_set('implicit_flush', true);
        ob_implicit_flush(true);

        $this->isPrepared = true;

        return $this;

    }

    /**
     * @return Flush
     */
    public function signal(){

        return $this->data(chr(32));

    }

}