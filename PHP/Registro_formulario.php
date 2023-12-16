<?php
include 'coneccion.php';

function validarRut($rut) {
    // Eliminar puntos y guiones del RUT
    $rut = str_replace(['.', '-'], '', $rut);

    // Separar el cuerpo del RUT del dígito verificador
    $cuerpoRut = substr($rut, 0, -1);
    $digitoVerificador = strtoupper(substr($rut, -1));

    // Calcular el dígito verificador esperado
    
    $suma = 0;
    $multiplo = 2;

    // Recorrer el cuerpo del RUT de derecha a izquierda
    for ($i = strlen($cuerpoRut) - 1; $i >= 0; $i--) {
        // Multiplicar el dígito por el factor multiplicador actual
        $suma += $multiplo * intval($cuerpoRut[$i]);

        // Incrementar el factor multiplicador o reiniciarlo si es necesario
        $multiplo = ($multiplo < 7) ? $multiplo + 1 : 2;
    }

    // Calcular el dígito verificador esperado según el algoritmo
    $digitoEsperado = 11 - ($suma % 11);

    // Ajustar casos especiales (11 se convierte en 0, 10 se convierte en 'K')
    $digitoEsperado = ($digitoEsperado == 11) ? 0 : (($digitoEsperado == 10) ? 'K' : $digitoEsperado);

    // Comparar el dígito verificador ingresado con el calculado
    return ($digitoVerificador == $digitoEsperado);
}



// Obtener datos del formulario
$rut = $_POST['rut'];
$nombres = $_POST['nombres'];
$apellido_paterno = $_POST['apellido_paterno'];
$apellido_materno = $_POST['apellido_materno'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
//$comuna = $_POST['comuna'];
$fono = $_POST['fono'];
$nacionalidad = $_POST['nacionalidad'];
$edad = $_POST['edad'];
$domicilio = $_POST['domicilio'];
$refugiado = $_POST['refugiado'];
$razon = $_POST['razon'];
$esterilizacion = $_POST['esterilizacion'];
$ingreso_economico = $_POST['ingreso_economico'];
$posee_mascotas = $_POST['posee_mascotas'];
$id_region = $_POST['id_region'];
$id_comuna = $_POST['id_comuna']; // error en signo peso $


// Validar campos obligatorios
if (empty(trim($rut)) || empty(trim($nombres)) || empty(trim($apellido_paterno)) || empty(trim($apellido_materno)) || empty(trim($email)) || empty(trim($direccion)) || empty(trim($fono)) || empty(trim($nacionalidad)) || empty(trim($edad)) || empty(trim($domicilio)) || empty(trim($refugiado)) || empty(trim($razon)) || empty(trim($esterilizacion)) || empty(trim($ingreso_economico))  || empty(trim($id_region)) || empty(trim($id_comuna))){
    
    echo '
        <script>
            alert("Todos los campos son obligatorios. Por favor, complete todos los campos e inténtelo de nuevo.");
            window.location = "../formulario.php"; 
        </script>
    ';
    exit();
}

//El siguiente código valida un rut con la estructura 11111111-1:
if (!validarRut($rut)) {
    echo '
        <script>
            alert("El rut '.$rut.' no es válido"); 
            window.location = "../formulario.php";  
        </script>
    ';
    exit();
}

// Validar formato de correo electrónico
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '
        <script>
            alert("Ingrese un correo válido");
            window.location = "../formulario.php";  
        </script>
    ';
    exit();
}

// Validar formato de número de teléfono (puedes ajustar según el formato que necesites)
if (!is_numeric($fono) || !strlen($fono) == 9) {
    echo '
        <script>
            alert("Ingrese un celular válido con 9 digitos");
            window.location = "../formulario.php";  
        </script>
    ';
    exit();
}

//Edad debe ser mayor o igual a 18 años
if (!is_numeric($edad) || $edad < 18) {
    echo '
        <script>
            alert("Debes ser mayor de 18 años para adoptar");
            window.location = "../formulario.php";  
        </script>
    ';
    exit();
}
// Puedes agregar más validaciones según tus necesidades



// Crear consulta preparada
$query = "INSERT INTO formulario (rut,nombres, apellido_paterno, apellido_materno, email, direccion, fono,nacionalidad,edad,domicilio,refugiado,razon,esterilizacion,ingreso_economico,posee_mascotas,id_region,id_comuna) 
        VALUES (?,? ,?, ? ,? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // 17

$insertar_formulario = mysqli_prepare($coneccion, $query);

// Verificar la preparación de la consulta
if (!$insertar_formulario) {
    die("Error en la preparación de la consulta: " . mysqli_error($coneccion));
}

// Vincular parámetros
mysqli_stmt_bind_param($insertar_formulario, "sssssssssssssssss", $rut, $nombres, $apellido_paterno, $apellido_materno, $email, $direccion, $fono, $nacionalidad, $edad, $domicilio, $refugiado, $razon, $esterilizacion, $ingreso_economico, $posee_mascotas, $id_region, $id_comuna);

// Ejecutar la consulta
$ejecutar = mysqli_stmt_execute($insertar_formulario);

// Verificar la ejecución de la consulta
if (!$ejecutar) {
    die("Error al ejecutar la consulta: " . mysqli_stmt_error($insertar_formulario));
} else {
    echo '<script>
            alert("Hemos recibido tu solicitud. Estamos redirigiéndote al inicio.");
            console.log("Redireccionando al inicio...");
            setTimeout(function() {
                window.location.href = "../index.php";
            }, 2000);
          </script>';
}

// Cerrar la consulta y la conexión
mysqli_stmt_close($insertar_formulario);
mysqli_close($coneccion);
?>
