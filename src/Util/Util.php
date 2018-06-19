<?php

namespace Drupal\dmpa_dashboard_filter\Util;


class Util {
    public static function processUrl($url){
        $parameters = explode('/', $url);

        if($url == '/'){
            return NULL;
        }

        if (count($parameters) == 2){ //checks for pattern /drc
            return $parameters[1];
        }
        else { //checks for patterns /countries/drc and /resources/indicatores/drc
            if ($parameters[1] == 'countries'){

                return $parameters[2];
            }
            else if ($parameters[1] == 'resources'){

                return $parameters[3];
            }
            else
                return NULL;
        }
    }
}