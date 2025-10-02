// Funcionalidad para modo oscuro/claro con CSS separados
const themeToggle = document.getElementById('themeToggle');
const themeIcon = themeToggle.querySelector('i');

// Referencia al elemento link del CSS
let themeStyle = document.getElementById('theme-style');

// Si no existe el elemento link, lo creamos
if (!themeStyle) {
    themeStyle = document.createElement('link');
    themeStyle.id = 'theme-style';
    themeStyle.rel = 'stylesheet';
    document.head.appendChild(themeStyle);
}

// URLs de los archivos CSS
const lightThemeCSS = '/mvc_oficialiapartes/css/caseta/styleindex.css';
const darkThemeCSS = '/mvc_oficialiapartes/css/globals/dark/style-inicio.css';

// Detectar preferencia del sistema
function detectSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

// Cargar tema guardado o usar el del sistema
function loadTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemTheme = detectSystemTheme();
    const theme = savedTheme || systemTheme;
    
    applyTheme(theme);
    updateThemeIcon(theme);
    return theme;
}

// Aplicar el tema cambiando el archivo CSS
function applyTheme(theme) {
    if (theme === 'dark') {
        themeStyle.href = darkThemeCSS;
    } else {
        themeStyle.href = lightThemeCSS;
    }
    document.documentElement.setAttribute('data-theme', theme);
}

// Actualizar icono del tema
function updateThemeIcon(theme) {
    if (theme === 'dark') {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        themeToggle.title = 'Cambiar a modo claro';
        themeToggle.setAttribute('aria-label', 'Cambiar a modo claro');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        themeToggle.title = 'Cambiar a modo oscuro';
        themeToggle.setAttribute('aria-label', 'Cambiar a modo oscuro');
    }
}

// Cambiar tema
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    applyTheme(newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

// Event listeners
themeToggle.addEventListener('click', toggleTheme);

// Escuchar cambios en la preferencia del sistema
if (window.matchMedia) {
    const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
    colorSchemeQuery.addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            const newTheme = e.matches ? 'dark' : 'light';
            applyTheme(newTheme);
            updateThemeIcon(newTheme);
        }
    });
}

// Inicializar tema al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
});

// Prevenir destello de contenido sin estilo
(function() {
    const savedTheme = localStorage.getItem('theme');
    const systemTheme = detectSystemTheme();
    const initialTheme = savedTheme || systemTheme;
    
    // Aplicar el tema inmediatamente para evitar FOUC (Flash of Unstyled Content)
    if (initialTheme === 'dark') {
        themeStyle.href = darkThemeCSS;
    } else {
        themeStyle.href = lightThemeCSS;
    }
    document.documentElement.setAttribute('data-theme', initialTheme);
})();

function hideThemeLoading() {
    const loadingElement = document.getElementById('themeLoading');
    if (loadingElement) {
        setTimeout(() => {
            loadingElement.classList.add('hidden');
            setTimeout(() => {
                loadingElement.remove();
            }, 300);
        }, 100);
    }
}

// Llamar esta función cuando el tema esté aplicado
document.addEventListener('DOMContentLoaded', hideThemeLoading);