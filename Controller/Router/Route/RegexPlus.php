<?php
/**
 * @package    Zend_Controller
 * @subpackage Router
 */

/** Zend_Controller_Router_Route_Regex */
require_once 'Zend/Controller/Router/Route/Regex.php';

/**
 * RegexPlus Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 */
class Zend_Controller_Router_Route_RegexPlus extends Zend_Controller_Router_Route_Regex
{

    /**
     * @const string URI delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path, $partial = false)
    {
        $path = trim(urldecode($path), '/');
        $this->_regex = '/'.$this->_regex.'/';
        $res = preg_match($this->_regex, $path, $values);

        if ($res === 0) return false;

        // array_filter_key()? Why isn't this in a standard PHP function set yet? :)
        foreach ($values as $i => $value) {
            if (!is_int($i) || $i === 0) {
                unset($values[$i]);
            }
        }

        $this->_values = $values;

        $values = $this->_getMappedValues($values);
        $params = array();
        if (isset($values['__params'])) {
            $params = $this->strToParams($values['__params']);
            unset($values['__params']);
        }
        $defaults = $this->_getMappedValues($this->_defaults, false, true);

        $return = $values + $defaults + $params;

        return $return;
    }

    /**
     * Use this method to take a string and split in into key value pairs for use in the framework
     *
     * @param string $str
     * @return array
     */
    protected function strToParams($str) {
        $params = array();
        $str = trim($str, self::URI_DELIMITER);

        if ($str != '') {

            $arr = explode(self::URI_DELIMITER, $str);

            if ($numSegs = count($arr)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($arr[$i]);
                    $val = isset($arr[$i + 1]) ? urldecode($arr[$i + 1]) : null;
                    $params[$key] = $val;
                }
            }
        }

        return $params;
    }

}
