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
      const movieBoxHTML = `
          <div class="swiper-slide box" style="cursor: default;">
              <div class="box-img">
                  <img src="${movie.poster_url || 'img/default.jpg'}" alt="${movie.title}">
              </div>
              <h3>${movie.title}</h3> 
              <span>${movie.duration || 'N/A'} | ${movie.genre}</span> 
          </div>`;

      if (parseInt(movie.release_year) > 2026) {
        comingHTML += movieBoxHTML;
      } else {
        trendingHTML += `
          <div class="box">
              <div class="box-img">
                  <img src="${movie.poster_url || 'img/default.jpg'}" alt="${movie.title}">
              </div>
              <h3>${movie.title}</h3> 
              <span>${movie.duration || 'N/A'} | ${movie.genre}</span> 
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
