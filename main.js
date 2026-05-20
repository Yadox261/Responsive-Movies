const API_URL = "http://127.0.0.1:8000/api/movies";

let homeSwiper, comingSwiper;

async function fetchMovies() {
  try {
    const response = await fetch(API_URL);
    const movies = await response.json();

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
                    <a href="javascript:void(0)" class="btn" style="opacity: 0.7; cursor: not-allowed;">Book Now</a>
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

document.addEventListener("DOMContentLoaded", fetchMovies);
