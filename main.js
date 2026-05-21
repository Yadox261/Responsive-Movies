const API_URL = "http://127.0.0.1:8000/api/movies";

let homeSwiper, comingSwiper;

async function fetchMovies() {
  try {
    const response = await fetch(API_URL);
    const movies = await response.json();
    window.activeMovies = movies; // Almacenar globalmente para usar en las reservaciones

    const homeWrapper = document.getElementById("home-wrapper");
    const moviesList = document.getElementById("movies-list");
    const comingList = document.getElementById("coming-list");

    let homeHTML = "";
    let trendingHTML = "";
    let comingHTML = "";

    movies.forEach((movie) => {
      // 1. Inyectar en el Slider Principal (Solo si tiene Banner)
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

      // 2. Clasificar entre Trending y Coming Soon
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

      if (parseInt(movie.release_year) > 2026) {
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

    homeWrapper.innerHTML = homeHTML;
    moviesList.innerHTML = trendingHTML;
    comingList.innerHTML = comingHTML;

    // Inicializar Swipers después de inyectar todo el HTML
    initSwipers();

  } catch (error) {
    console.error("Error cargando películas:", error);
  }
}

function initSwipers() {
  if (homeSwiper) homeSwiper.destroy();
  if (comingSwiper) comingSwiper.destroy();

  homeSwiper = new Swiper(".home", {
    spaceBetween: 30,
    centeredSlides: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    loop: true,
    pagination: { el: ".swiper-pagination", clickable: true },
    observer: true,
    observeParents: true,
  });

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

// Sticky Header
let header = document.querySelector('header');
window.addEventListener('scroll', () => {
    header.classList.toggle('sticky', window.scrollY > 0);
});

// ==========================================
// LÓGICA DEL MODAL DE RESERVACIONES
// ==========================================

window.openReservationModal = function(movieId) {
  const movie = window.activeMovies.find(m => m.id === movieId);
  if (!movie) return;

  // Llenar datos de la película
  document.getElementById("reserve-movie-id").value = movie.id;
  document.getElementById("modal-movie-title").innerHTML = `Película: <span style="color: #ff6600;">${movie.title}</span>`;

  // Poblar los horarios disponibles
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
    const option = document.createElement("option");
    option.value = "";
    option.text = "Sin horarios disponibles";
    scheduleSelect.appendChild(option);
  }

  // Resetear el formulario
  document.getElementById("reservation-form").reset();
  
  // Mostrar formulario y ocultar tarjeta de éxito
  document.getElementById("reservation-form-container").classList.remove("hidden");
  document.getElementById("reservation-success-card").classList.add("hidden");

  // Activar modal
  document.getElementById("reservation-modal").classList.add("active");
};

window.closeReservationModal = function() {
  document.getElementById("reservation-modal").classList.remove("active");
};

// Cerrar modal al hacer clic fuera del contenido
window.addEventListener("click", (e) => {
  const modal = document.getElementById("reservation-modal");
  if (e.target === modal) {
    closeReservationModal();
  }
});

window.submitReservation = async function(event) {
  event.preventDefault();

  const movieId = document.getElementById("reserve-movie-id").value;
  const scheduleId = document.getElementById("reserve-schedule").value;
  const name = document.getElementById("reserve-name").value;
  const email = document.getElementById("reserve-email").value;
  const phoneInput = document.getElementById("reserve-phone").value;
  const seats = document.getElementById("reserve-seats").value;

  if (!scheduleId) {
    alert("Por favor selecciona una función con horario válido.");
    return;
  }

  // Activar spinner en botón de envío
  const submitBtn = document.getElementById("reserve-submit-btn");
  const btnText = submitBtn.querySelector(".btn-text");
  const btnSpinner = submitBtn.querySelector(".btn-spinner");

  btnText.classList.add("hidden");
  btnSpinner.classList.remove("hidden");
  submitBtn.disabled = true;

  try {
    // Realizar POST a la API del backend
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
        phone: "+52" + phoneInput, // Añadir código internacional de México automáticamente
        seats: parseInt(seats)
      })
    });

    const result = await response.json();

    if (response.ok && result.success) {
      // Rellenar datos en la tarjeta de éxito
      document.getElementById("success-movie").innerText = result.data.movie;
      document.getElementById("success-datetime").innerText = `Día: ${result.data.day} | Hora: ${result.data.time}`;
      document.getElementById("success-seats").innerText = `${result.data.seats} boleto(s) reservado(s)`;

      // Cambiar vistas en el modal
      document.getElementById("reservation-form-container").classList.add("hidden");
      document.getElementById("reservation-success-card").classList.remove("hidden");
    } else {
      alert(result.message || "Ocurrió un error al procesar tu reservación.");
    }

  } catch (error) {
    console.error("Error de conexión:", error);
    alert("No se pudo conectar con el servidor. Verifica que el backend esté corriendo.");
  } finally {
    // Restaurar botón de envío
    btnText.classList.remove("hidden");
    btnSpinner.classList.add("hidden");
    submitBtn.disabled = false;
  }
};

document.addEventListener("DOMContentLoaded", fetchMovies);
