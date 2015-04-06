<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Base extends Model 
{
    /**
     * Whether attribute has changed
     *
     * Returns true if the attribute is new.
     *
     * @param  string  $attribute
     * @return boolean
     */
    public function hasChanged($attribute)
    {
        if ( ! array_key_exists($attribute, $this->attributes)) return true;

        $key   = $attribute;
        $value = $this->attributes[$key];

        if ( ! array_key_exists($key, $this->original) or $value != $this->original[$key])
        {
            return true;
        }

        return false;
    }
}