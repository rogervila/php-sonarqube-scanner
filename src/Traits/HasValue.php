<?php

namespace Sonar\Traits;

trait HasValue
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
