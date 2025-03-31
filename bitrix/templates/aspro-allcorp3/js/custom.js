$(document).ready(function () {
  //     $('.tariffs-list__wrapper').on('click', function (){
  //         var el = $(this);
  //         var link = $('a', el).eq(0).attr('href');
  //         location.href = link;
  //     });
  //
  //     $('.services-list__wrapper').on('click', function (){
  //         var el = $(this);
  //         var link = $('a.services-list__item-link', el).eq(0).attr('href');
  //         location.href = link;
  //     });
  //
  //     $('.blog-list__wrapper').on('click', function (){
  //         var el = $(this);
  //         var link = $('a', el).eq(0).attr('href');
  //         location.href = link;
  //     });
});

/* виджет соцсетей */
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.querySelector(".social-toggle");
    const menu = document.querySelector(".social-menu");

    toggleBtn.addEventListener("click", () => {
        menu.classList.toggle("active");
    });

    document.addEventListener("click", (e) => {
        if (!menu.contains(e.target) && !toggleBtn.contains(e.target)) {
            menu.classList.remove("active");
        }
    });
    setInterval(() => {
        toggleBtn.classList.add("pulsing");
        setTimeout(() => toggleBtn.classList.remove("pulsing"), 1000);
    }, 5000);
});
