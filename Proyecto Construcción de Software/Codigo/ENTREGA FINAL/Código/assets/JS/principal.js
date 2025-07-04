// CAMBIAR MODO OSCURO O CLARO
const   body = document.querySelector('body'),
        sidebar = body.querySelector('nav'),
        toggle = body.querySelector(".toggle"),
        searchBtn = body.querySelector(".search-box"),
        modeSwitch = body.querySelector(".toggle-switch"),
        modeText = body.querySelector(".mode-text");

toggle.addEventListener("click", () =>{ 
    sidebar.classList.toggle("close");
});

searchBtn.addEventListener("click", () =>{ 
    sidebar.classList.remove("close");
});

modeSwitch.addEventListener("click", () =>{
    body.classList.toggle("dark");
    sendDarkModeToIframe();

    if(body.classList.contains("dark")){ 
        modeText.innerText = "Modo Oscuro";

    }else{
        modeText.innerText = "Modo Claro";
    }
});

// CAMBIAR CONTENIDO DEL SECTION
const iframe = document.getElementById('content-frame');

// Función para enviar el estado del modo oscuro al iframe
function sendDarkModeToIframe() {
    const isDarkMode = body.classList.contains('dark');
    iframe.contentWindow.postMessage({ darkMode: isDarkMode }, '*');
}

// Escuchar mensajes del iframe
window.addEventListener('message', function(event) {
    // Si el iframe solicita recargar los estilos
    if (event.data.reloadStyles === true) {
        // Enviar el estado actual del modo oscuro al iframe
        sendDarkModeToIframe();
    }
});


// BUSCAR
document.addEventListener('DOMContentLoaded', () => {
  const inputBuscar = document.getElementById('buscar-menu');
  const items = document.querySelectorAll('.menu-links .nav-link');

  inputBuscar.addEventListener('input', () => {
    const texto = inputBuscar.value.toLowerCase();

    items.forEach(item => {
      const textoItem = item.textContent.toLowerCase();
      item.style.display = textoItem.includes(texto) ? 'flex' : 'none';
    });
  });
});

// CAMBIAR SECTION

const frame = document.getElementById('content-frame');

	document.querySelectorAll('.menu-links a').forEach(link => {
		link.addEventListener('click', (e) => {
		    e.preventDefault();
			const sectionKey = e.currentTarget.getAttribute('data-section');
			frame.src = `${sectionKey}.php`;
		});
	});
