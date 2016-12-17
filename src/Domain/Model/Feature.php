<?php

namespace LaravelFeature\Domain\Model;

class Feature
{
    private $name;
    private $isEnabled;

    public static function fromNameAndStatus($name, $isEnabled)
    {
        $feature = new self($name, (bool) $isEnabled);
        return $feature;
    }

    private function __construct($name, $isEnabled)
    {
        $this->name = $name;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    public function setNewName($newName)
    {
        $this->name = $newName;
    }

    public function enable()
    {
        $this->isEnabled = true;
    }

    public function disable()
    {
        $this->isEnabled = false;
    }
}
