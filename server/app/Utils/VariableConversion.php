<?php

namespace App\Utils;

/**
 * Class VariableConversion.
 */
class VariableConversion
{
    /**
     * Convert an underscored table name to an uppercased class name.
     *
     * @param $table
     *
     * @return mixed
     */
    public static function convertTableNameToClassName($table)
    {
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));

        return $string;
    }

    /**
     * Convert a PHP array into a string version.
     *
     * @param $array
     *  @param $tmun
     *  @param $last_symbol
     *
     * @return string
     */
    public static function convertArrayToString($array,$tmun=3,$last_symbol="'")
    {
        $t = $t_1 = "";
        for($i=0;$i<$tmun;$i++){
            $t .="\t";
            if($i<$tmun-1){
                $t_1 .="\t";
            }
        }
        $string = 'array(';
        if (!empty($array)) {
            $string .= "\n".$t."'";
            $string .= implode($last_symbol.",\n".$t."'", $array);
            $string .= $last_symbol."\n".$t_1;
        }
        $string .= ")";

        return $string;
    }

    /**
     * Convert a boolean into a string.
     *
     * @param $boolean
     *
     * @return string true|false
     */
    public static function convertBooleanToString($boolean)
    {
        $string = $boolean ? 'true' : 'false';

        return $string;
    }

}
