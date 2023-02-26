# JAMD_FIELDS_VALIDATIONS
Clase que procesa los valores recibidos por peticiones POST y GET
### Usage ###

~~~
//recibimos los datos mediante POST o GET
 $_data       =  $_POST["form"];
 //instanciamos la clase de valdiación pasando en su constructor el array con los valores clave:valor
 $validations =  new FieldValidations($_data);
      
         if ($validations->validate(array(
                    "name" => array("required" => true, "msg" => "El nombre  es necesario", "format" => array("type" => "uppercase")),
                    "rut" => array("required" => true, "msg" => "El RUT, es obligatorio", "validations" => array("type" => "rut", "msg" => "El RUT ingresado no es valido"), "format" => array("type" => "rut")),
                    "email" => array("required" => true, "msg" => "El email es obligatorio", "format" => array("type" => "lowercase"), "validations" => array("type" => "email", "msg" => "el correo %v% no es valido.")),
                    "company_id" => array("required" => false, "default" => 1),
                ))) {
               if ($this->save($validations->getData())) {
                  echo "Datos almacenados correctamente";
               }
        } else {
            //retorna un array con los valores no validados, segun condiciones
            return  $validations->getMistakes();
        }
    
 /**
 * Metodo que se encarga de guardar los valores ya validados
 **/
 private function save($data){
 }
~~~

### Rules ###
* __valdiate()__: Returns FALSE en caso de que alguno de los campos no cumple su condicion establecida, TRUE en caso de ser valido
 * __array():__ Todos las valdiaciones se cargan en el metodo validate, pasando como clave el nombre del campo y las validaciones correspondienye
 * __required:__ Se establece si el valor el obligatorio o no 
 * __msg__: Establece el mensaje a devolver en caso de no efectuarse la vaidación correspondiente
 * __validations__: Array donde se establece el tipo de validación  
 * __Validations => array("type"=>"rut","msg"=>"el valor %v% ingresado no corresponde")__: Se debe establecer un array que tenga como parametros el type de validación y el mensaje 
 *  * __format__: Array donde se establece el tipo de formato a utilizar sobre un campo  
 * __getData()__: Returns un array asociativo con los campos antes ingresados y con sus valroes validados y formateados
 * __getMistakes()__: Returns un array con los errores cometidos en la validación
### License ###

Released under the [MIT](http://www.opensource.org/licenses/mit-license.php) license<br>
Copyright (c) 2023 Jorge Morales D
