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