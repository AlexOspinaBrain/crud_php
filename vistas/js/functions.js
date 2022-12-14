document.addEventListener('DOMContentLoaded', function () {

    fetch('../Controladores/AreaController.php', {
        method: 'GET',
      }).then(res=>res.json())
      .then(res => {
        let selectElement = document.getElementById('area');
        res.data.map((element)=>{
            selectElement.add(new Option(element.nombre,element.id));
        });
      })
      .catch(error => console.error('Error:', error))

    fetch('../Controladores/RolController.php', {
        method: 'GET',
      }).then(res=>res.json())
      .then(res => {
        let selectElement = document.getElementById('roles');
        res.data.map((element)=>{

            selectElement.innerHTML +=`<div class="form-check">
                <input class="form-check-input" type="checkbox" 
                    value="${element.id}" id="rol_${element.id}">
                <label class="form-check-label" for="rol_${element.id}">
                    ${element.nombre}
                </label>
                </div>`;
                
        });
      })
      .catch(error => console.error('Error:', error))

    fetch('../Controladores/EmpleadoController.php', {
        method: 'GET',
      }).then(res=>res.json())
      .then(res => {
        let tablaElement = document.getElementById('tablaBody');
        let optionButtons;
        res.data[0].map((element)=>{

            optionButtons = `<td><button type="button" class="btn btn-sm btn-primary"
              onclick="editEmpleado('${element.id}')">
            Modificar
            </button></td>
            <td><button type="button" class="btn btn-sm btn-danger"
              onclick="deleteEmpleado('${element.id}')">
            Eliminar
            </button></td>`;

            tablaElement.innerHTML +=`<tr>
                <td>${element.nombre}</td>
                <td>${element.email}</td>
                <td>${element.sexo}</td>
                <td>${element.area}</td>
                <td>${element.boletin === '0' ? 'No' : 'Si'}</td>
                ${optionButtons}
            </tr>`;
        });
      })
      .catch(error => console.error('Error:', error));


    const nuevoEmpleado = document.getElementById('guarda');
    nuevoEmpleado.addEventListener('click',event => {
        
        if (valida_formulario()){
            event.preventDefault();

            const data = new FormData(document.getElementById('formEmpleado'));
            
            data.set('sexo', document.querySelector('input[name="sexo"]:checked').value);
            data.set('boletin', document.getElementById('boletin').checked ? 1 : 0);

            let roles = [];
            const checks = document.querySelectorAll('input[type="checkbox"]:checked');
            
            checks.forEach(elem=>{
                if (elem.name !== 'boletin'){
                    roles.push(elem.value);
                }
            })
            
            data.set('roles', roles);

            fetch('../Controladores/EmpleadoController.php', {
                method: 'POST',
                body: data,
              }).then(res=>{

                    alert('Empleado guardado con exito!');
                    location.reload();
                    
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
        if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
        {
            errorMessage += `<p>Email invalido</p>`;
            passValidation = false;
        }
    }
    if (area.value === "NO"){
        errorMessage += `<p>Area requerida</p>`;
        passValidation = false;
    }
    
    if (descrip.value === ""){
        errorMessage += `<p>Descripci??n requerida requerida</p>`;
        passValidation = false;
    }

    displayError.removeAttribute('style');
    displayError.innerHTML += errorMessage;

    return passValidation;

}

const editEmpleado = (id) =>{

    const editData = new FormData();
        editData.append('id', id);
            

    fetch(`../Controladores/EmpleadoController.php?id=${id}`, {
        method: 'GET',
        
      }).then(res=>res.json())
      .then(res => {
        if (res.data.length > 0) {
            limpiar();
            res.data[0].map((element)=>{

                const nombre = element.nombre;
                const email = element.email;
                const areaId = element.idarea;
                const areaText = element.area;
                const sexo = element.sexo;
                const descrip = element.descripcion;
                const boletin = element.boletin;
                const idEmpl = element.id;
                
                const roles = res.data[1];

                const elemNombre = document.getElementById('nombre');
                const elemEmail = document.getElementById('email');
                const elemArea = document.getElementById('area');
                const elemSexo = document.getElementsByName('sexo');
                const elemDescrip = document.getElementById('descrip');
                const elemBoletin = document.getElementById('boletin');
                const elemidEmpleado = document.getElementById('idEmpleado');

                elemidEmpleado.value = idEmpl;
                elemNombre.value = nombre;
                elemEmail.value = email;
                elemArea.value = areaId;

                const options = elemArea.getElementsByTagName('option');
                for(var j = 0; j < options.length; j++) {
                    if(options[j].value === areaId) {
                        options[j].selected = true;
                    }
                }

                [areaId].selected = 'selected'

                for(var i = 0; i < elemSexo.length; i++) {
                    if(elemSexo[i].value === sexo.toLowerCase()) {
                        elemSexo[i].checked = true;
                    }
                }
                
                elemDescrip.value = descrip;
                if (boletin !== '0') {
                    elemBoletin.checked = true;
                } 


                const checks = document.querySelectorAll('input[type="checkbox"]');
                
                checks.forEach(elem=>{
                    if (elem.name !== 'boletin'){
                        roles.forEach(rol => {
                            if (rol.rol_id === elem.value){
                                elem.checked = true;
                            }
                        })
                    }
                })

                var modal = new bootstrap.Modal(document.getElementById('modalEmpleado'));
                modal.show();
            });
        } else {
            alert('Empleado no existe');
            location.reload();
        }
      })
      .catch(error => console.error('Error:', error));

}

const deleteEmpleado = (id) =>{

    const deleteData = new FormData();
    deleteData.append('id', id);

    fetch(`../Controladores/EmpleadoController.php`, {
        method: 'DELETE',
        body: deleteData,
      }).then(res=>res.json())
      .then(res => {
        alert('Empleado eliminado');
        location.reload();
      })
      .catch(error => console.error('Error:', error));

}

function limpiar(){

    document.querySelectorAll('input[type=text]').forEach(elem => {
        elem.value = '';
    });

    const checks = document.querySelectorAll('input[type="checkbox"]');
                
    checks.forEach(elem=>{
        elem.checked = false;
    })

    document.querySelector('select[name=area]').value = '';
    document.querySelector("select[name=area] option[value=NO]").setAttribute("selected",true);

};