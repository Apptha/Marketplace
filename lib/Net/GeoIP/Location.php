<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Net_GeoIP_Location implements Serializable
{
    protected $aData = array(
        'countryCode'  => null,
        'countryCode3' => null,
        'countryName'  => null,
        'region'       => null,
        'city'         => null,
        'postalCode'   => null,
        'latitude'     => null,
        'longitude'    => null,
        'areaCode'     => null,
        'dmaCode'      => null
    );


    /**
     * Calculate the distance in km between two points.
     *
     * @param Net_GeoIP_Location $loc The other point to which distance will be calculated.
     *
     * @return float The number of km between two points on the globe.
     */
    public function distance(Net_GeoIP_Location $loc)
    {
        // ideally these should be class constants, but class constants
        // can't be operations.
        $RAD_CONVERT = M_PI / 180;
        $EARTH_DIAMETER = 2 * 6378.2;

        $lat1 = $this->latitude;
        $lon1 = $this->longitude;
        $lat2 = $loc->latitude;
        $lon2 = $loc->longitude;

        // convert degrees to radians
        $lat1 *= $RAD_CONVERT;
        $lat2 *= $RAD_CONVERT;

        // find the deltas
        $delta_lat = $lat2 - $lat1;
        $delta_lon = ($lon2 - $lon1) * $RAD_CONVERT;

        // Find the great circle distance
        $temp = pow(sin($delta_lat/2), 2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2), 2);
        return $EARTH_DIAMETER * atan2(sqrt($temp), sqrt(1-$temp));
    }

    /**
     * magic method to make it possible
     * to store this object in cache when
     * automatic serialization is on
     * Specifically it makes it possible to store
     * this object in memcache
     *
     * @return array
     */
    public function serialize()
    {
        return serialize($this->aData);
    }

    /**
     * unserialize a representation of the object
     *
     * @param array $serialized The serialized representation of the location
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->aData = unserialize($serialized);
    }


    /**
     * Setter for elements of $this->aData array
     *
     * @param string $name The variable to set
     * @param string $val  The value
     *
     * @return object $this object
     */
    public function set($name, $val)
    {
        if (array_key_exists($name, $this->aData)) {
            $this->aData[$name] = $val;
        }

        return $this;
    }

    public function __set($name, $val)
    {
        return $this->set($name, $val);
    }

    /**
     * Getter for $this->aData array
     *
     * @return array
     */
    public function getData()
    {
         return $this->aData;
    }


    /**
     * Magic method to get value from $this->aData array
     *
     * @param string $name The var to get
     *
     * @return mixed string if value exists or null if it is empty of
     * just does not exist
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->aData)) {
            return $this->aData[$name];
        }

        return null;
    }


    /**
     * String representation of the object
     *
     * @return string text and result of print_r of $this->aData array
     */
    public function __toString()
    {
        return 'object of type '.__CLASS__.'. data: '.implode(',', $this->aData);
    }


    /**
     * Magic method
     * makes it possible to check if specific record exists
     * and also makes it possible to use empty() on any property
     *
     * @param strign $name The name of the var to check
     *
     * @return bool
     */
    public function __isset($name)
    {
        return (null !== $this->__get($name));
    }

}
