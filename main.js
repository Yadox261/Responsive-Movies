/**
 * Word of the Movies - Lógica de Cliente y Reservaciones
 * =======================================================
 * Este archivo JavaScript maneja de forma asíncrona la carga de la cartelera de películas
 * desde el backend en Laravel, inicializa componentes de slider dinámicos (Swiper) y gestiona
 * el ciclo de vida del modal de reservaciones con AJAX (Fetch API) de manera fluida (SPA feeling).
 */

// URL del endpoint API en Laravel que retorna el catálogo completo de películas activas y sus horarios
const API_URL = "http://127.0.0.1:8000/api/movies";

// Variables globales para almacenar las instancias de Swiper.js y controlar su ciclo de vida
let homeSwiper, comingSwiper;

/**
 * Consulta el Catálogo de Películas del Servidor
 * ----------------------------------------------
 * Carga el JSON de películas, pobla dinámicamente el slider principal, la sección de
 * tendencias y próximos estrenos, y luego inicializa/reconstruye los carousels táctiles.
 */
async function fetchMovies() {
  try {
    // Realiza la petición GET asíncrona al backend
    const response = await fetch(API_URL);
    const movies = await response.json();
    
    // Almacena de forma global las películas recibidas para poder leer sus horarios rápidamente en el modal sin re-consultar
    window.activeMovies = movies;

    // Obtención de contenedores HTML del DOM
    const homeWrapper = document.getElementById("home-wrapper");
    const moviesList = document.getElementById("movies-list");
    const comingList = document.getElementById("coming-list");

    let homeHTML = "";
    let trendingHTML = "";
    let comingHTML = "";

    // Procesa cada película retornada por el servidor
    movies.forEach((movie) => {
      // 1. Inyectar en el Slider Principal (Solo si tiene imagen de Banner configurada)
      if (movie.banner_url) {
        homeHTML += `
            <div class="swiper-slide container">
                <img src="${movie.banner_url}" alt="${movie.title}">
                <div class="home-text">
                    <span> World of Movies </span>
                    <h1>${movie.title}</h1>
                    <a href="javascript:void(0)" class="btn" onclick="openReservationModal(${movie.id})">Book Now</a>
                    <a href="javascript:void(0)" class="play" style="cursor: not-allowed;"> <i class='bx bx-play'></i></a>
                </div>
            </div>`;
      }

      // 2. Compilar horarios/funciones en píldoras CSS estilizadas
      let schedulesHTML = "";
      if (movie.schedules && movie.schedules.length > 0) {
        schedulesHTML = `
          <div class="movie-schedules" style="margin-top: 10px; border-top: 1px dashed rgba(255, 255, 255, 0.15); padding-top: 8px; width: 100%; text-align: left;">
              <p style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #ff3333; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 4px;">
                  <i class='bx bx-time' style='font-size: 12px;'></i> Horarios
              </p>
              <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                  ${movie.schedules.map(s => `
                      <span class="schedule-pill" title="${s.day} | ${s.room}" style="background: rgba(255, 0, 0, 0.08); border: 1px solid rgba(255, 0, 0, 0.25); color: #ff4d4d; font-size: 10px; padding: 3px 7px; border-radius: 6px; font-weight: 700; cursor: help; transition: all 0.2s ease;">
                          ${s.time} <span style="font-size: 8px; opacity: 0.6; font-weight: 500; margin-left: 2px;">${s.format}</span>
                      </span>
                  `).join('')}
              </div>
          </div>`;
      }

      // Estructura común de la tarjeta de presentación de la película
      const movieBoxHTML = `
          <div class="swiper-slide box" style="cursor: default; display: flex; flex-direction: column; align-items: flex-start; text-align: left; height: auto;">
              <div class="box-img" style="width: 100%;">
                   <img src="${movie.poster_url || 'img/default.jpg'}" alt="${movie.title}">
              </div>
              <h3>${movie.title}</h3> 
              <span>${movie.duration || 'N/A'} | ${movie.genre}</span> 
              ${schedulesHTML}
              <button class="movie-reserve-btn" onclick="openReservationModal(${movie.id})">Reservar</button>
          </div>`;

      // Clasificación reactiva según año de estreno: Próximos estrenos (Coming Soon) vs Tendencias
      // Va a Coming Soon si el año de estreno es en el futuro (> 2026) O si se estrena este año (2026) y todavía no tiene una duración definida.
      const releaseYearInt = parseInt(movie.release_year);
      const hasNoDuration = !movie.duration || String(movie.duration).toUpperCase() === 'N/A' || String(movie.duration).trim() === '';
      
      if (releaseYearInt > 2026 || (releaseYearInt === 2026 && hasNoDuration)) {
        comingHTML += movieBoxHTML;
      } else {
        trendingHTML += `
          <div class="box" style="display: flex; flex-direction: column; align-items: flex-start; text-align: left; height: auto;">
              <div class="box-img" style="width: 100%;">
                  <img src="${movie.poster_url || 'img/default.jpg'}" alt="${movie.title}">
              </div>
              <h3>${movie.title}</h3> 
              <span>${movie.duration || 'N/A'} | ${movie.genre}</span> 
              ${schedulesHTML}
              <button class="movie-reserve-btn" onclick="openReservationModal(${movie.id})">Reservar</button>
          </div>`;
      }
    });

    // Inyecta el marcado HTML generado directamente en los contenedores del DOM
    homeWrapper.innerHTML = homeHTML;
    moviesList.innerHTML = trendingHTML;
    comingList.innerHTML = comingHTML;

    // Inicializa o destruye y reconstruye las instancias de Swiper.js para aplicar los sliders en el nuevo HTML
    initSwipers();

  } catch (error) {
    console.error("Error cargando películas:", error);
  }
}

/**
 * Inicializador de Componentes Swiper Carousel
 * ---------------------------------------------
 * Destruye instancias previas para evitar fugas de memoria y fugas de eventos,
 * y luego crea carousels dinámicos con autoplay, loops y paginadores interactivos.
 */
function initSwipers() {
  if (homeSwiper) homeSwiper.destroy();
  if (comingSwiper) comingSwiper.destroy();

  // Slider de Banner Principal
  homeSwiper = new Swiper(".home", {
    spaceBetween: 30,
    centeredSlides: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    loop: true,
    pagination: { el: ".swiper-pagination", clickable: true },
    observer: true,
    observeParents: true,
  });

  // Slider horizontal táctil de próximos estrenos (Coming Soon)
  comingSwiper = new Swiper(".coming-container", {
    spaceBetween: 20,
    loop: true,
    autoplay: { delay: 3000, disableOnInteraction: false },
    breakpoints: {
      0: { slidesPerView: 2 },
      568: { slidesPerView: 3 },
      768: { slidesPerView: 4 },
      1024: { slidesPerView: 5 },
    },
    observer: true,
    observeParents: true,
  });
}

// Escucha el evento de Scroll para añadir la clase 'sticky' al navbar principal
let header = document.querySelector('header');
window.addEventListener('scroll', () => {
    header.classList.toggle('sticky', window.scrollY > 0);
});

// ==========================================
// LÓGICA DEL MODAL DE RESERVACIONES
// ==========================================

/**
 * Abre el Modal y Pobla Horarios
 * ------------------------------
 * Carga dinámicamente la información de la película seleccionada y pobla su respectivo
 * control select con las funciones/horarios programados vigentes.
 * 
 * @param {number} movieId ID único de la película.
 */
window.openReservationModal = function(movieId) {
  const movie = window.activeMovies.find(m => m.id === movieId);
  if (!movie) return;

  // Asigna valores ocultos de referencia en el formulario
  document.getElementById("reserve-movie-id").value = movie.id;
  document.getElementById("modal-movie-title").innerHTML = `Película: <span style="color: #ff6600;">${movie.title}</span>`;

  // Pobla los horarios disponibles en el combo
  const scheduleSelect = document.getElementById("reserve-schedule");
  scheduleSelect.innerHTML = "";

  if (movie.schedules && movie.schedules.length > 0) {
    movie.schedules.forEach(s => {
      const option = document.createElement("option");
      option.value = s.id;
      option.text = `${s.day} a las ${s.time} (${s.format}) - ${s.room}`;
      scheduleSelect.appendChild(option);
    });
  } else {
    // Manejador en caso de que la película no posea horarios asignados aún
    const option = document.createElement("option");
    option.value = "";
    option.text = "Sin horarios disponibles";
    scheduleSelect.appendChild(option);
  }

  // Resetea campos de formularios para eliminar datos de reservaciones previas
  document.getElementById("reservation-form").reset();
  
  // Muestra el formulario principal y oculta la vista de confirmación exitosa
  document.getElementById("reservation-form-container").classList.remove("hidden");
  document.getElementById("reservation-success-card").classList.add("hidden");

  // Activa el modal aplicando transición suave CSS
  document.getElementById("reservation-modal").classList.add("active");
};

/**
 * Cierra el Modal
 */
window.closeReservationModal = function() {
  document.getElementById("reservation-modal").classList.remove("active");
};

// Cierra de forma automática el modal al realizar un click en el fondo desenfocado (backdrop)
window.addEventListener("click", (e) => {
  const modal = document.getElementById("reservation-modal");
  if (e.target === modal) {
    closeReservationModal();
  }
});

/**
 * Envío AJAX del Formulario de Reservación
 * -----------------------------------------
 * Captura el evento submit del formulario, activa animaciones de carga (spinners),
 * sanitiza el número telefónico anteponiendo el código internacional de México (+52)
 * solicitado por la API de UltraMsg, despacha la petición POST e interpreta la respuesta
 * mostrando la tarjeta de confirmación de éxito.
 * 
 * @param {Event} event Evento original submit.
 */
window.submitReservation = async function(event) {
  event.preventDefault(); // Previene la recarga clásica del navegador

  // Captura de valores de los inputs del DOM
  const movieId = document.getElementById("reserve-movie-id").value;
  const scheduleId = document.getElementById("reserve-schedule").value;
  const name = document.getElementById("reserve-name").value;
  const email = document.getElementById("reserve-email").value;
  const phoneInput = document.getElementById("reserve-phone").value;
  const seats = document.getElementById("reserve-seats").value;

  // Validación de seguridad en cliente
  if (!scheduleId) {
    alert("Por favor selecciona una función con horario válido.");
    return;
  }

  // Intercambio visual: Activa el spinner de carga y deshabilita el botón para evitar doble envío (double-submit protection)
  const submitBtn = document.getElementById("reserve-submit-btn");
  const btnText = submitBtn.querySelector(".btn-text");
  const btnSpinner = submitBtn.querySelector(".btn-spinner");

  btnText.classList.add("hidden");
  btnSpinner.classList.remove("hidden");
  submitBtn.disabled = true;

  try {
    // Despacho de la petición POST AJAX a la API del Servidor Laravel
    const response = await fetch("http://127.0.0.1:8000/api/reservations", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        movie_id: parseInt(movieId),
        schedule_id: parseInt(scheduleId),
        name: name,
        email: email,
        phone: "+52" + phoneInput, // Agrega el prefijo mexicano automáticamente para UltraMsg
        seats: parseInt(seats)
      })
    });

    const result = await response.json();

    if (response.ok && result.success) {
      // Inyecta dinámicamente los datos de la reservación guardada en la tarjeta de éxito
      document.getElementById("success-movie").innerText = result.data.movie;
      document.getElementById("success-datetime").innerText = `Día: ${result.data.day} | Hora: ${result.data.time}`;
      document.getElementById("success-seats").innerText = `${result.data.seats} boleto(s) reservado(s)`;

      // Oculta el formulario de llenado y revela la tarjeta animada de confirmación de boleto
      document.getElementById("reservation-form-container").classList.add("hidden");
      document.getElementById("reservation-success-card").classList.remove("hidden");
    } else {
      // Muestra los errores devueltos por el validador del backend de Laravel
      alert(result.message || "Ocurrió un error al procesar tu reservación.");
    }

  } catch (error) {
    console.error("Error de conexión:", error);
    alert("No se pudo conectar con el servidor. Verifica que el backend esté corriendo.");
  } finally {
    // Ciclo Finalizador: Restaura el botón de envío ocultando el spinner
    btnText.classList.remove("hidden");
    btnSpinner.classList.add("hidden");
    submitBtn.disabled = false;
  }
};

// Escucha la carga inicial completa del documento para detonar la llamada AJAX que pobla la cartelera
document.addEventListener("DOMContentLoaded", fetchMovies);
