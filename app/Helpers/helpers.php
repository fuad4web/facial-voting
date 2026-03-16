<?php

if (!function_exists('activity')) {
    function activity($logName = null)
    {
        return new class($logName) {
            protected $logName;
            protected $description;
            protected $causer;
            protected $properties = [];

            public function __construct($logName)
            {
                $this->logName = $logName;
            }

            public function causedBy($causer)
            {
                $this->causer = $causer;
                return $this;
            }

            public function withProperties($properties)
            {
                $this->properties = $properties;
                return $this;
            }

            public function log($description)
            {
                \App\Helpers\ActivityHelper::log(
                    $this->logName, 
                    $description, 
                    $this->causer, 
                    $this->properties
                );
            }
        };
    }
}
