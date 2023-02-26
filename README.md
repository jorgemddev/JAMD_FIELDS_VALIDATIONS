# JAMD_FIELDS_VALIDATIONS
Clase que procesa los valores recibidos por peticiones POST y GET
### Usage ###

~~~
   $_data = Input::post();
        $validations = new FieldValidations($_data);
        if ($validations->validate(array(
                    "name" => array("required" => true, "msg" => "El nombre de fantasia es necesario", "format" => array("type" => "uppercase")),
                    "domain" => array("required" => true, "msg" => "El nombre de dominio es necesario", "format" => array("type" => "uppercase")),
                    "business" => array("required" => false, "msg" => "La razón social es necesaria", "format" => array("type" => "uppercase")),
                    "rut" => array("required" => false, "msg" => "El RUT, es obligatorio", "validations" => array("type" => "rut", "msg" => "El RUT ingresado no es valido"), "format" => array("type" => "rut")),
                    "location" => array("required" => false, "msg" => "La dirección es obligatoria"),
                    "email" => array("required" => true, "msg" => "El email es requerido", "format" => array("type" => "lowercase"), "validations" => array("type" => "email", "msg" => "el correo %v% no es valido.")),
                    "phone" => array("required" => false, "msg" => "Telefono es obligatorio"),
                    "twitter" => array("required" => false),
                    "instagram" => array("required" => false),
                    "youtube" => array("required" => false),
                    "whatsapp" => array("required" => false),
                    "company_id" => array("required" => true, "msg" => "Seleccione la sucursal"),
                    "users_id" => array("default" => $this->objUser->id),
                ))) {
            $company = (new Company);
            if ($company->save($validations->getData())) {
                $this->data = array("status" => "ok", "msg" => "Usuario creado correctamente", "data" => $company);
            } else {
                $this->data = array("status" => "fail", "msg" => "No fue posible crear este usuario");
            }
        } else {
            $this->data = array("status" => "fail", "msg" => "Tenemos un error", "mistakes" => $validations->getMistakes());
        }
~~~

### Rules ###

 * __required__: Returns FALSE if the form element is empty. 
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

Based on [Alex Garrett](https://twitter.com/alexjgarrett) work http://bit.ly/1oO8Yxn

### License ###

Released under the [MIT](http://www.opensource.org/licenses/mit-license.php) license<br>
Copyright (c) 2014 Ravi Kumar
