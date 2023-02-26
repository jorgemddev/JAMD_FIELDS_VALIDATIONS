
/**
 * Description of validations
 *
 * @author jorge
 */
class FieldValidations {

    /**
     * guarda si el formulario paso la validacion o no 
     * @var boolean
     */
    private $validate;

    /**
     *
     * @var Array $_GET $_POST 
     */
    private $request = null;

    /**
     * campos no encontrados
     * @var array 
     */
    private $mistakes;

    /**
     * data validada a retornar
     * @var array
     */
    private $response = array();
    private $validations = array("md5" => "md5", "email" => "email", "number" => "number", "money" => "money", "rut" => "rut", "secure" => "secure", "owner" => "owner", "date" => "date", "array" => "array");
    private $formats = array("md5" => "md5", "rut" => "frut", "fdate" => "fdate", "uppercase" => "uppercase", "lowercase" => "lowercase");

    function __construct(Array $request = null) {
        $this->request = $this->normalizeData($request);
    }

    private function normalizeData($requests) {
        $data = array();
        foreach ($requests as $key => $val) {
            $value = trim($val);
            if (($value != null) && ($value != "null") && ($value != "undefined")) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Valida los datos a ingresar, recibe distintos parametros en el array
     * @param Array $param data: información a ingresar (obligatorio),
     * required: true | false (opcional),
     * msg: Texto a devolver en caso de error (opcional), 
     * default: data por default (optional)
     * @return boolean
     */
    public function validate($param) {
        $result = 0;
        foreach ($param as $key => $input) {
            $data = (($this->request != null) && (array_key_exists($key, $this->request)) && (!empty($this->request[$key]))) ? $this->request[$key] : ((isset($input["data"])) ? $input["data"] : null);
            if ((array_key_exists("required", $input)) && ($input["required"] == true)) {
                if (empty($data)) {
                    $msg = (array_key_exists("msg", $input)) ? $input["msg"] : $key . " is requerid";
                    $this->mistakes[] = $msg . "\n";
                    $result++;
                } else {


                    //format to value
                    if (array_key_exists("format", $input)) {
                        $type = ((is_array($input["format"])) && (array_key_exists("type", $input["format"]))) ? $input["format"]["type"] : $input["format"];
                        $witchFilter = ((array_key_exists("filter", $input["format"])) && (array_key_exists($input["format"]["filter"], $param))) ? true : false;
                        $data = $this->format(array(
                            "method" => $type, "value" => $this->request[$key]),
                                $witchFilter);
                        $this->response[$key] = $data;
                    }



                    if (array_key_exists("unique", $input)) {
                        $_field = $key;
                        $_value = $this->request[$key];
                        $_obj = ((is_array($input["unique"])) && (array_key_exists("model", $input["unique"]))) ? $input["unique"]["model"] : $input["unique"];
                        $witchFilter = ((array_key_exists("filter", $input["unique"])) && (array_key_exists($input["unique"]["filter"], $param))) ? true : false;
                        $exclude = false;
                        if (array_key_exists("exclude", $input["unique"])) {
                            $data_exclude = $input["unique"]["exclude"];
                            $value_exclude = $this->request[$key];
                            if ($data_exclude == $value_exclude) {
                                $exclude = true;
                            }
                        }
                        if (!$exclude) {
                            if ($witchFilter) {
                                $key_filter = $input["unique"]["filter"];
                                $value_filter = $this->normalizeString($param[$key_filter]["default"]);
                                if ((new $_obj)->exists("$_field='$_value' and $key_filter=$value_filter")) {
                                    $result++;
                                    $msg = ((is_array($input["unique"])) && (array_key_exists("msg", $input["unique"]))) ? $this->replaceString("%v%", $this->request[$key], $input["unique"]["msg"]) : $_value . ", ya existe en nuestros registros";
                                    $this->mistakes[] = $msg . "\n";
                                }
                            } else {
                                if ((new $_obj)->exists("$_field='$_value'")) {
                                    $result++;
                                    $msg = ((is_array($input["unique"])) && (array_key_exists("msg", $input["unique"]))) ? $this->replaceString("%v%", $this->request[$key], $input["unique"]["msg"]) : $_value . ", ya existe en nuestros registros";
                                    $this->mistakes[] = $msg . "\n";
                                }
                            }
                        }
                    }
                    if (array_key_exists("validations", $input)) {
                        $type = ((is_array($input["validations"])) && (array_key_exists("type", $input["validations"]))) ? $input["validations"]["type"] : $input["validations"];
                        $_obj = ((is_array($input["validations"])) && (array_key_exists("model", $input["validations"]))) ? $input["validations"]["model"] : false;
                        $format = ((is_array($input["validations"])) && (array_key_exists("format", $input["validations"]))) ? $input["validations"]["format"] : false;
                        $witchFilter = ((is_array($input["validations"])) && (array_key_exists("filter", $input["validations"])) && (array_key_exists($input["validations"]["filter"], $param))) ? true : false;

                        if ($witchFilter && $_obj) {
                            $key_filter = $input["validations"]["filter"];
                            $value_filter = $this->normalizeString($param[$key_filter]["default"]);

                            $data = $this->valid(array(
                                "method" => $type, "value" => $this->request[$key]),
                                    array("key_filter" => $key_filter, "value_filter" => $value_filter, "model" => $_obj, "needle" => $key));

                            if (($data)) {
                                $this->response[$key] = $data;
                            } else {
                                $result++;
                                $msg = ((is_array($input["validations"])) && (array_key_exists("msg", $input["validations"]))) ? $this->replaceString("%v%", $this->request[$key], $input["validations"]["msg"]) : $this->request[$key] . ", no valido";
                                $this->mistakes[] = $msg . "\n";
                            }
                        } else if ($format) {
                            $data = $this->valid(array(
                                "method" => $type, "value" => $this->request[$key]),
                                    array("format" => $format));

                            if ($data) {
                                $this->response[$key] = $data;
                            } else {
                                $result++;
                                $msg = ((is_array($input["validations"])) && (array_key_exists("msg", $input["validations"]))) ? $this->replaceString("%v%", $this->request[$key], $input["validations"]["msg"]) : $this->request[$key] . ", no valido";
                                $this->mistakes[] = $msg . "\n";
                            }
                        } else {
                            //valido el resultado
                            $data = $this->valid(array("method" => $type, "value" => $this->request[$key]));
                            if (($data)) {
                                $this->response[$key] = $data;
                            } else {
                                $result++;
                                $msg = ((is_array($input["validations"])) && (array_key_exists("msg", $input["validations"]))) ? $this->replaceString("%v%", $this->request[$key], $input["validations"]["msg"]) : $this->request[$key] . ", no valido";
                                $this->mistakes[] = $msg . "\n";
                            }
                        }
                    } else {
                        //No requiere valdiacion de datos
                        $this->response[$key] = $data;
                    }
                }
            } else {
                if (!empty($data)) {

                    //format to value
                    if (array_key_exists("format", $input)) {
                        $type = ((is_array($input["format"])) && (array_key_exists("type", $input["format"]))) ? $input["format"]["type"] : $input["format"];
                        $witchFilter = ((array_key_exists("filter", $input["format"])) && (array_key_exists($input["format"]["filter"], $param))) ? true : false;
                        $data = $this->format(array(
                            "method" => $type, "value" => $this->request[$key]),
                                $witchFilter);
                        $this->response[$key] = $data;
                    }

                    //verify unique value
                    if (array_key_exists("unique", $input)) {
                        $_field = $key;
                        $_value = $this->request[$key];
                        $_obj = ((is_array($input["unique"])) && (array_key_exists("model", $input["unique"]))) ? $input["unique"]["model"] : $input["unique"];
                        $witchFilter = ((array_key_exists("filter", $input["unique"])) && (array_key_exists($input["unique"]["filter"], $param))) ? true : false;
                        $exclude = false;
                        if (array_key_exists("exclude", $input["unique"])) {
                            $data_exclude = $input["unique"]["exclude"];
                            $value_exclude = $this->request[$key];
                            if ($data_exclude == $value_exclude) {
                                $exclude = true;
                            }
                        }
                        if (!$exclude) {
                            if ($witchFilter) {
                                $key_filter = $input["unique"]["filter"];
                                $value_filter = $this->normalizeString($param[$key_filter]["default"]);
                                if ((new $_obj)->exists("$_field='$_value' and $key_filter=$value_filter")) {
                                    $result++;
                                    $msg = ((is_array($input["unique"])) && (array_key_exists("msg", $input["unique"]))) ? $this->replaceString("%v%", $this->request[$key], $input["unique"]["msg"]) : $_value . ", ya existe en nuestros registros";
                                    $this->mistakes[] = $msg . "\n";
                                }
                            } else {
                                if ((new $_obj)->exists("$_field='$_value'")) {
                                    $result++;
                                    $msg = ((is_array($input["unique"])) && (array_key_exists("msg", $input["unique"]))) ? $this->replaceString("%v%", $this->request[$key], $input["unique"]["msg"]) : $_value . ", ya existe en nuestros registros";
                                    $this->mistakes[] = $msg . "\n";
                                }
                            }
                        }
                    }
                    //validations on required false witch value
                    if (array_key_exists("validations", $input)) {
                        $type = ((is_array($input["validations"])) && (array_key_exists("type", $input["validations"]))) ? $input["validations"]["type"] : $input["validations"];
                        $data = $this->valid(array("method" => $type, "value" => $this->request[$key]));
                        if (($data)) {
                            $this->response[$key] = $data;
                        } else {
                            $result++;
                            $msg = ((is_array($input["validations"])) && (array_key_exists("msg", $input["validations"]))) ? $this->replaceString("%v%", $this->request[$key], $input["validations"]["msg"]) : $this->request[$key] . ", no valido";
                            $this->mistakes[] = $msg . "\n";
                        }
                    } else {
                        //No requiere valdiacion de datos
                        $this->response[$key] = $data;
                    }
                } else if (array_key_exists("default", $input)) {
                    //format to value
                    if (array_key_exists("format", $input)) {
                        $type = ((is_array($input["format"])) && (array_key_exists("type", $input["format"]))) ? $input["format"]["type"] : $input["format"];
                        $witchFilter = ((array_key_exists("filter", $input["format"])) && (array_key_exists($input["format"]["filter"], $param))) ? true : false;
                        $data = $this->format(array(
                            "method" => $type, "value" => $this->request[$key]),
                                $witchFilter);
                        $this->response[$key] = $data;
                    } else {
                        $this->response[$key] = $input["default"];
                    }
                }
            }
        }
        $this->validate = ($result == 0) ? true : false;
        return $this->validate;
    }

    /**
     * retorna un array con el nombre de las variables recibidas en data
     * @return array
     */
    function getData() {
        return $this->response;
    }

    /**
     * Valida el dato a traves de una function prextablecida, la funcion debe estar creada e informada en el array validations
     * @param Array $param
     * @param Array $filters key,value,model,needle
     * @return val
     */
    private function valid($param, $filters = null) {
        if (array_key_exists($param["method"], $this->validations)) {
            $_function = $param["method"];
            return $this->$_function($param["value"], $filters);
        } else {
            return false;
        }
    }

    /**
     * Formatea un valor a traves de una function prextablecida, la funcion debe estar creada e informada en el array formats
     * @param Array $param
     * @param Array $filters 
     * @return val
     */
    private function format($param, $filters = null) {
        if (array_key_exists($param["method"], $this->formats)) {
            $_function = $param["method"];
            return $this->$_function($param["value"], $filters);
        } else {
            return false;
        }
    }

    /**
     * Remplaza el string con sus valores indicados
     * @param String $s busqueda
     * @param String $r replazar
     * @param String $msg Texto completo
     * @return String
     */
    private function replaceString($s, $r, $msg) {
        return str_replace($s, $r, $msg);
    }

    /*     * ******* validations********** */

    private function email($param, $filters = null) {
        return (filter_var($this->normalizeString($param), FILTER_VALIDATE_EMAIL)) ? $this->normalizeString($param) : false;
    }

    private function array($param, $filters = null) {
        return (is_array($param)) ? true : false;
    }

    private function number($param, $filters = null) {
        return (is_numeric($this->normalizeString($param))) ? $this->normalizeString($param) : false;
    }

    private function money($param, $filters = null) {
        return (($this->normalizeString($param)) >= 0) ? $this->normalizeString($param) : false;
    }

    private function rut($param, $filters = null) {
        return (Utilidad::isRut($this->normalizeString($param))) ? $this->normalizeString($param) : false;
    }

    private function date($param, $filters = null) {
        $format = $filters["format"];
        return (Utilidad::validateDate($this->normalizeString($param), $format)) ? $this->normalizeString($param) : false;
    }

    /**
     * valida que el usuario certifique mediante clave su aprovación para realziar la operación
     * @param type $param clave recibida
     * @param type $filters model,key_filer,value_filter,comparador en bd
     * @return boolean
     */
    private function secure($param, $filters = null) {
        $_model = $filters["model"];
        $_field = $filters["key_filter"];
        $_value = $filters["value_filter"];
        $_needle = $filters["needle"];
        $obj = (new $_model)->find_first("conditions: $_field=$_value");
        if ($obj->id) {
            if ($obj->$_needle == md5($param)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function owner($param, $filters = null) {
        $_model = $filters["model"];
        $_field = $filters["key_filter"];
        $_value = $filters["value_filter"];
        $_needle = $filters["needle"];
        $obj = (new $_model)->find_first("conditions: $_field=$_value and $_needle=$param");
        if (isset($obj->id)) {
            return $param;
        } else {
            return false;
        }
    }

    /*     * ******* validations********** */



    /*     * ********* formats********** */

    private function md5($param, $filters = null) {
        return md5($this->normalizeString($param));
    }

    private function fdate($param, $filters = null) {
        $format = $filters["format"];
        return (Utilidad::formatDate($this->normalizeString($param), $format)) ? $this->normalizeString($param) : false;
    }

    private function uppercase($param, $filters = null) {
        return strtoupper($this->normalizeString($param));
    }

    private function lowercase($param, $filters = null) {
        return strtolower($this->normalizeString($param));
    }

    /*     * ******** /formats ********* */

    public function getMistakes() {
        if (!$this->validate) {
            return $this->mistakes;
        }return null;
    }

    public function setMistakes($mistakes) {
        $this->mistakes = $mistakes;
    }

    function getRequest() {
        return $this->request;
    }

    private function normalizeString($str) {
        return ltrim(rtrim($str));
    }

}
