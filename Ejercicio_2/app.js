// app.js – JS for CRUD operations
const tabla=document.getElementById('tabla');
const busqueda=document.getElementById('busqueda');
const recargar=document.getElementById('recargar');
const loading=document.getElementById('loading');
const formNuevo=document.getElementById('form-nuevo');
const mensaje=document.getElementById('mensaje');
const modal=document.getElementById('modal');
const editId=document.getElementById('edit-id');
const editNombre=document.getElementById('edit-nombre');
const editEmail=document.getElementById('edit-email');
const editCiudad=document.getElementById('edit-ciudad');

async function cargar(q=''){
  loading.style.display='block';
  const resp=await fetch('/api/estudiantes.php?q='+encodeURIComponent(q));
  const data=await resp.json();
  tabla.innerHTML='';
  data.forEach(e=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`<td>${e.nombre}</td><td>${e.email}</td><td>${e.ciudad}</td>
      <td>
        <button class="eliminar" data-id="${e.id}">Eliminar</button>
        <button class="editar" data-id="${e.id}" data-nombre="${e.nombre}" data-email="${e.email}" data-ciudad="${e.ciudad}">Editar</button>
      </td>`;
    tabla.appendChild(tr);
  });
  loading.style.display='none';
}

recargar.onclick=()=>cargar();
busqueda.oninput=()=>cargar(busqueda.value);

formNuevo.addEventListener('submit', async e=>{
  e.preventDefault();
  const data={nombre: formNuevo.nombre.value, email: formNuevo.email.value, ciudad: formNuevo.ciudad.value};
  const resp=await fetch('/api/estudiantes.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
  if(resp.ok){
    formNuevo.reset();
    cargar(busqueda.value);
    mensaje.textContent='Estudiante agregado exitosamente';
  } else {
    mensaje.textContent='Error al agregar estudiante';
  }
});

tabla.addEventListener('click',async e=>{
  if(e.target.classList.contains('eliminar')){
    const id=e.target.dataset.id;
    const resp=await fetch('/api/estudiantes.php', {method:'DELETE', body:JSON.stringify({id})});
    if(resp.ok){
      cargar(busqueda.value);
      mensaje.textContent='Estudiante eliminado';
    }
  }
  if(e.target.classList.contains('editar')){
    const id=e.target.dataset.id;
    const nombre=e.target.dataset.nombre;
    const email=e.target.dataset.email;
    const ciudad=e.target.dataset.ciudad;
    editId.value=id;
    editNombre.value=nombre;
    editEmail.value=email;
    editCiudad.value=ciudad;
    modal.style.display='block';
  }
});

document.getElementById('edit-cancel').addEventListener('click',()=>{
  modal.style.display='none';
});

document.getElementById('edit-save').addEventListener('click', async ()=>{
  const data={id: editId.value, nombre: editNombre.value, email: editEmail.value, ciudad: editCiudad.value};
  const resp=await fetch('/api/estudiantes.php', {method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
  if(resp.ok){
    modal.style.display='none';
    cargar(busqueda.value);
    mensaje.textContent='Estudiante actualizado';
  } else {
    mensaje.textContent='Error al actualizar';
  }
});

cargar();