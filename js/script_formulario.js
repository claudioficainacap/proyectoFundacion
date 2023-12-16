// Función para validar un RUT chileno
function validarRut(rut) {
    // Expresión regular para validar el formato del RUT
    var rutRegex = /^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/;
    
    // Verificar si el RUT tiene el formato correcto
    if (!rutRegex.test(rut)) {
        return false;
    }

    // Eliminar puntos y guiones para realizar la validación
    rut = rut.replace(/[.-]/g, '');

    // Separar el cuerpo y el dígito verificador
    var cuerpo = rut.slice(0, -1);
    var digitoVerificador = rut.slice(-1).toUpperCase();

    // Calcular el dígito verificador esperado
    var suma = 0;
    var multiplo = 2;

    for (var i = cuerpo.length - 1; i >= 0; i--) {
        suma += parseInt(cuerpo.charAt(i)) * multiplo;

        if (multiplo < 7) {
            multiplo += 1;
        } else {
            multiplo = 2;
        }
    }

    var resultado = 11 - (suma % 11);
    var dv = resultado === 11 ? 0 : resultado === 10 ? 'K' : resultado;

    // Comparar el dígito verificador calculado con el proporcionado
    return dv == digitoVerificador;
}

// Función para validar un formulario
function validarFormulario() {
    // Obtener referencia al formulario
    let formulario = document.getElementById('registro');
    // Obtener todos los campos del formulario
    var inputs = formulario.querySelectorAll('input, textarea');

    // Iterar sobre cada campo del formulario
    for (var i = 0; i < inputs.length; i++) {
        // Obtener mensajes de error, nombre del campo y valor del campo
        let mensaje_error = inputs[i].getAttribute('data-msgerror');
        let nombre_campo = inputs[i].getAttribute('name');
        let valor_campo = inputs[i].value;

        // Validar campo vacío
        if (valor_campo.trim() === "") {
            alert(`${nombre_campo}: ${mensaje_error}`);
            inputs[i].focus();
            return false;
        }

        // Validar formato del RUT
        if(nombre_campo == "rut" && validarRut(valor_campo) == false) {
            alert('EL RUT NO TIENE EL FORMATO CORRECTO (SIN PUNTOS Y CON GUION) ');
            inputs[i].focus();
            return false;
        }

        // Validar formato del teléfono
        if(nombre_campo == "fono") {
            let fono = parseInt(valor_campo);
            if(isNaN(fono) || valor_campo.length !== 9) {
                alert('El campo telefono no es valido.');
                inputs[i].focus();
                return false;
            }
        }

        // Validar edad
        if (nombre_campo == "edad") {
            let edad = parseInt(valor_campo);
            if(isNaN(edad) || edad < 18) {
                alert("La edad no es válida o es menor de 18 años.");
                inputs[i].focus();
                return false;
            }
        }

        // Ejecutar la validación del navegador ya que usamos "novalidate" en el formulario
        if(!inputs[i].checkValidity()) {
            alert(`${nombre_campo}: ${inputs[i].validationMessage}`);
            inputs[i].focus();
            return false;
        }
    }

    // Si pasa todas las validaciones, retorna true
    return true;
}
