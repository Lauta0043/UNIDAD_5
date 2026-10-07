const tabla = document.getElementById("tablaUsuarios");
const loading = document.getElementById("loading");
const btnRecargar = document.getElementById("btnRecargar");

async function cargarUsuarios() {
    loading.style.display = "block"; // Mostrar el indicador de carga
    tabla.innerHTML = "";

    //Se hace una try/exept para manejar errores en la solicitud fetch
    try {
        const respuesta = await fetch("https://jsonplaceholder.typicode.com/users");

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const usuarios = await respuesta.json();

        if (!Array.isArray(usuarios) || usuarios.length === 0) {
            tabla.innerHTML = '<tr><td colspan="3">No hay usuarios disponibles</td></tr>';
            return;
        }

        tabla.innerHTML = usuarios.map(({ name, email, address }) => `
            <tr>
                <td>${name}</td>
                <td>${email}</td>
                <td>${address?.city ?? "Sin ciudad"}</td>
            </tr>
        `).join("");
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
        tabla.innerHTML = `
            <tr>
                <td colspan="3">No se pudieron cargar los usuarios</td>
            </tr>
        `;
    } finally {
        loading.style.display = "none";
    }
}

btnRecargar.addEventListener("click", cargarUsuarios);
cargarUsuarios();
