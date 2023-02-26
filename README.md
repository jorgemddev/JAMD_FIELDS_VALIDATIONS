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
 * array(): Todos las valdiaciones se cargan en el metodo validate, pasando como clave el nombre del campo y las validaciones correspondienye
 * required: Returns FALSE if the form element is empty. 
 * __minlength__: Returns FALSE if the form element is shorter then the parameter value. minlength=>6
 * __maxlength__: Returns FALSE if the form element is longer then the parameter value. maxlength=>10  
 * __email__: Returns FALSE if the form element does not contain a valid email address.
 * __activeemail__: Returns FALSE if the form element does not contain a valid and active email address. 
 * __url__: Returns FALSE if the form element does not contain a valid url address.
 * __activeurl__: Returns FALSE if the form element does not contain a valid and active url address.
 * __ip__: Returns FALSE if the supplied IP is not valid.
 * __alpha__: Returns FALSE if the form element contains anything other than alphabetical characters.
 * __alphaupper__: Returns FALSE if the form element contains anything other than upper alphabetical characters.
 * __alphalower__: Returns FALSE if the form element contains anything other than lower alphabetical characters.
 * __alphadash__: Returns FALSE if the form element contains anything other than alpha-numeric characters, underscores or dashes.
 * __alphanum__: Returns FALSE if the form element contains anything other than alpha-numeric characters.
 * __hexadecimal__: Returns FALSE if the form element contains anything other than hexadecimal characters.
 * __numeric__: Returns FALSE if the form element contains anything other than numeric characters.
 * __matches__: Returns FALSE if the form element does not match the one in the parameter. matches[form_item] 
 * __unique__: Returns FALSE if the form element is not unique to the table and field name in the parameter. unique[field]

### License ###

Released under the [MIT](http://www.opensource.org/licenses/mit-license.php) license<br>
Copyright (c) 2023 Jorge Morales D
