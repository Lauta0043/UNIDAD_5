<?php
// Archivo de prueba para formularios
header('Content-Type: text/html; charset=utf-8');

// Simular datos en memoria
$estudiantes = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar estudiante
    if (isset($_POST['nombre'], $_POST['email'], $_POST['ciudad'])) {
        $estudiantes[] = [
            'id' => count($estudiantes) + 1,
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'ciudad' => $_POST['ciudad']
        ];
        echo "<script>alert('Estudiante agregado exitosamente');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar estudiante (simulado)
    if (isset($_GET['id'])) {
        $estudiantes = array_filter($estudiantes, function($e) use ($id) {
            return $e['id'] != $id;
        });
        echo "<script>alert('Estudiante eliminado');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Actualizar estudiante (simulado)
    if (isset($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['ciudad'])) {
        foreach ($estudiantes as &$e) {
            if ($e['id'] == $_POST['id']) {
                $e['nombre'] = $_POST['nombre'];
                $e['email'] = $_POST['email'];
                $e['ciudad'] = $_POST['ciudad'];
            }
        }
        echo "<script>alert('Estudiante actualizado');</script>";
    }
}

// Buscar
$q = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '';
$lista = $estudiantes;
if ($q) {
    $lista = array_filter($estudiantes, function($e) use ($q) {
        return strpos($e['nombre'], $q) !== false || strpos($e['email'], $q) !== false;
    });
}

// Mostrar formulario de agregar
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Prueba Formularios - Ejercicio 2</title>
<style>
  table{border-collapse:collapse;width:100%;}
  th,td{border:1px solid #ccc;padding:5px;}
  #loading{display:none;}
</style>
</head>
<body>
<h1>Prueba de Formularios - Ejercicio 2 (Versión Local)</h1>

<!-- Formulario de agregar -->
<form id="form-nuevo" style="margin-top: 1em;">
  <input type="text" id="nombre" placeholder="Nombre" required>
  <input type="email" id="email" placeholder="Email" required>
  <input type="text" id="ciudad" placeholder="Ciudad" required>
  <button type="submit">Agregar</button>
</form>

<!-- Modal de edición -->
<div id="modal" style="display:none; position:fixed; top:20%; left:30%; background:#fff; padding:1em; border:1px solid #000;">
  <h3>Editar Estudiante</h3>
  <input type="hidden" id="edit-id">
  <label>Nombre: <input type="text" id="edit-nombre" required></label><br>
  <label>Email: <input type="email" id="edit-email" required></label><br>
  <label>Ciudad: <input type="text" id="edit-ciudad" required></label><br>
  <button id="edit-save">Guardar</button>
  <button id="edit-cancel">Cancelar</button>
</div>

<div id="mensaje" style="margin-top:1em;"></div>

<!-- Tabla -->
<table>
  <thead><tr><th>Nombre</th><th>Email</th><th>Ciudad</th><th>Acciones</th></tr></thead>
  <tbody id="tabla"></tbody>
</table>

<script>
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

// Cargar datos iniciales (simulados)
function cargar(q=''){
  loading.style.display='block';
  // Simular carga con timeout
  setTimeout(() => {
    let data = <?php echo json_encode($lista); ?>;
    
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
  }, 300);
}

recargar.onclick=()=>cargar();
busqueda.oninput=()=>cargar(busqueda.value);

formNuevo.addEventListener('submit', async e=>{
  e.preventDefault();
  const data={nombre: formNuevo.nombre.value, email: formNuevo.email.value, ciudad: formNuevo.ciudad.value};
  
  // Simular POST exitoso
  const estudiantes = <?php echo json_encode($estudiantes); ?>;
  estudiantes.push({id: count(estudiantes) + 1, ...data});
  
  cargar(busqueda.value);
  mensaje.textContent='Estudiante agregado exitosamente';
});

tabla.addEventListener('click',async e=>{
  if(e.target.classList.contains('eliminar')){
    const id=e.target.dataset.id;
    
    // Simular DELETE exitoso
    <?php echo json_encode($estudiantes); ?> = <?php echo json_encode(array_filter(<?php echo json_encode($estudiantes); ?>, function($e) use ($id) { return $e['id'] != $id; })); ?>;
    
    cargar(busqueda.value);
    mensaje.textContent='Estudiante eliminado';
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
  
  // Simular PUT exitoso
  <?php echo json_encode($estudiantes); ?> = <?php echo json_encode(array_map(function($e) use ($data) { 
      if ($e['id'] == $data['id']) { return $data; } 
      return $e; 
    }, <?php echo json_encode($estudiantes); ?>)); ?>;
  
  modal.style.display='none';
  cargar(busqueda.value);
  mensaje.textContent='Estudiante actualizado';
});

cargar();
</script>
</body>
</html>
