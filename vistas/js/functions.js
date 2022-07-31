document.addEventListener('DOMContentLoaded', function () {

    fetch('../Controladores/Area.php', {
        method: 'GET',
      }).then(res=>res.json())
      .then(res => {
        let selectElement = document.getElementById('area');
        res.data.map((element)=>{
            selectElement.add(new Option(element.nombre,element.id));
        });
      })
      .catch(error => console.error('Error:', error))


    fetch('../Controladores/Empleado.php', {
        method: 'GET',
      }).then(res=>res.json())
      .then(res => {
        let tablaElement = document.getElementById('tablaBody');
        let optionButtons;
        res.data.map((element)=>{

            optionButtons = `<td><button type="button" class="btn btn-sm btn-primary"
              onclick="editEmpleado('${element.id}')">
            Modificar
            </button></td>
            <td><button type="button" class="btn btn-sm btn-danger"
              onclick="deleteEmpleado('${element.id}')">
            Eliminar
            </button></td>`;

            tablaElement.innerHTML =`<tr>
                <td>${element.nombre}</td>
                <td>${element.email}</td>
                <td>${element.sexo}</td>
                <td>${element.area}</td>
                <td>${element.boletin === '0' ? 'No' : 'Si'}</td>
                ${optionButtons}
            </tr>`;
        });
      })
      .catch(error => console.error('Error:', error))


    const nuevoEmpleado = document.getElementById('guarda');
    nuevoEmpleado.addEventListener('click',event => {
        
        if (valida_formulario()){
            event.preventDefault();

            const data = new FormData(document.getElementById('formEmpleado'));
                data.set('sexo', document.querySelector('input[name="sexo"]:checked').value);
                data.set('boletin', document.getElementById('boletin').checked ? 1 : 0);
                

            fetch('../Controladores/Empleado.php', {
                method: 'POST',
                body: data,
              }).then(res=>{

                    var myModal = new bootstrap.Modal(document.getElementById('modalEmpleado'));
                    myModal.hide();

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
        if (!/^[a-zA-Z\u00C0-\u017F\s]+$/.test(nombre.value))
        {
            errorMessage += `<p>Nombre con caracteres invalidos</p>`;
            passValidation = false;
        }
    }
    
    if (email.value == ""){
        errorMessage += `<p>Email requerido</p>`;
        passValidation = false;
    } else {
        /*if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
        {
            errorMessage += `<p>Email invalido</p>`;
            passValidation = false;
        }*/
    }
    if (area.value === "NO"){
        errorMessage += `<p>Area requerida</p>`;
        passValidation = false;
    }
    
    if (descrip.value === ""){
        errorMessage += `<p>Descripción requerida requerida</p>`;
        passValidation = false;
    }

    displayError.removeAttribute('style');
    displayError.innerHTML += errorMessage;

    return passValidation;

}

