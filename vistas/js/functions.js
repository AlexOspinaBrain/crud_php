document.addEventListener('DOMContentLoaded', function () {
    
    const nuevoEmpleado = document.getElementById('guarda');
    nuevoEmpleado.addEventListener('click',event => {
        
        if (valida_formulario()){
            event.preventDefault();

            fetch('../Controladores/Empleado.php', {
                method: 'POST',
                body: new FormData(document.getElementById('formEmpleado')),
                
              }).then(res=>{
                    console.log('Empleado guardado con exito!');
              }).catch(error => console.error('Error:', error))


        }

    })

}, false);

const valida_formulario = () => {
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const area = document.getElementById('area');
    const descrip = document.getElementById('descrip');

    const displayError = document.getElementById('error');
    displayError.innerHTML = "";

    let errorMessage = "";
    let passValidation = true;

    if (nombre.value == ""){
        errorMessage += `<p>Nombre requerido</p>`;
        passValidation = false;
    } else {
        if (!/[A-Za-z ]/.test(nombre.value))
        {
            errorMessage += `<p>Nombre con caracteres invalidos</p>`;
            passValidation = false;
        }
    }
    
    if (email.value == ""){
        errorMessage += `<p>Email requerido</p>`;
        passValidation = false;
    } else {
        if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
        {
            errorMessage += `<p>Email invalido</p>`;
            passValidation = false;
        }
    }
    if (area.value == "NO"){
        errorMessage += `<p>Area requerida</p>`;
        passValidation = false;
    }
    
    if (descrip.value == "NO"){
        errorMessage += `<p>Descripción requerida requerida</p>`;
        passValidation = false;
    }

    displayError.removeAttribute('style');
    displayError.innerHTML += errorMessage;

    return passValidation;

}