/*cartelera */
var swiper = new Swiper(".home", {
  spaceBetween: 600,
  centeredSlides: true,
  autoplay: {
      delay: 5000,
      disableOnInteraction: false,
  },
  loop: true, // Habilita el loop
  pagination: {
      el: ".swiper-pagination", // Asegúrate de que este selector apunte a un elemento existente en tu HTML
      clickable: true,
  },
});
/*Proximamente */
var swiper = new Swiper(".coming-container", {
  spaceBetween: 10,
  centeredSlides: true,
  autoplay: {
      delay: 5000,
      disableOnInteraction: false,
  },
  loop: true, // Habilita el loop
  breakpoints: {
      0: {
          slidesPerView: 1, // Cambia según tus necesidades
      },
      568: {
          slidesPerView: 2,
      },
      768: {
          slidesPerView: 3,
      },
      1024: {
          slidesPerView: 4,
      },
  },
});
