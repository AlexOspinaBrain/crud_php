document.addEventListener('DOMContentLoaded', function () {
    
    const nuevoEmpleado = document.getElementById('guarda');
    nuevoEmpleado.addEventListener('click',event => {
        
        if (!valida_formulario()){
            event.preventDefault();
        } else {
            //ajax
        }

    })

}, false);

const valida_formulario = () => {
    const nombre = document.formEmpleado.nombre;
    const email = document.formEmpleado.nombre;
    const sexo = document.formEmpleado.nombre;
    const area = document.formEmpleado.nombre;
    const descrip = document.formEmpleado.descrip;
    const boletin = document.formEmpleado.boletin;
}