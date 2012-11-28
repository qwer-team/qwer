<?php

namespace Itc\AdminBundle\Entity\Translation;

class Translation
{
    
    private $prop;
    
    private $value;

    /**
     * Set prop
     *
     * @param string $prop
     * @return Translation
     */
    public function setProp($prop)
    {
        $this->prop = $prop;
    
        return $this;
    }

    /**
     * Get prop
     *
     * @return string 
     */
    public function getProp()
    {
        return $this->prop;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Translation
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
