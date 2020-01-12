<?php
/**
 * Created by PhpStorm.
 * User: Ammar
 * Date: 12/29/2018
 * Time: 1:08 PM
 */

namespace App\Classes;


class Property
{
    private $title;
    private $view;
    private $callback;

    public function __construct($title, $view, callable $callback) {
        $this->title = $title;
        $this->view = $view;
        $this->callback = $callback;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    public function get($item) {
        return $this->callback->__invoke($item);
    }
}