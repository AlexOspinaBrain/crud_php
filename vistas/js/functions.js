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
                

            fetch('../Controladores/Empleado.php', {
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
        errorMessage += `<p>Descripción requerida requerida</p>`;
        passValidation = false;
    }

    displayError.removeAttribute('style');
    displayError.innerHTML += errorMessage;

    return passValidation;

}

const editEmpleado = (id) =>{

    const editData = new FormData();
        editData.append('id', id);
            

    fetch(`../Controladores/Empleado.php?id=${id}`, {
        method: 'GET',
        
      }).then(res=>res.json())
      .then(res => {
        if (res.data.length > 0) {
            
            res.data.map((element)=>{

                const nombre = element.nombre;
                const email = element.email;
                const areaId = element.idarea;
                const areaText = element.area;
                const sexo = element.sexo;
                const descrip = element.descripcion;
                const boletin = element.boletin;
                const idEmpl = element.id;
                

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


            });
        } else {
            alert('Empleado no existe');
            location.reload();
        }
      })
      .catch(error => console.error('Error:', error));

}