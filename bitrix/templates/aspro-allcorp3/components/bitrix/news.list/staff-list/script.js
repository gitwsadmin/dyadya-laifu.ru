(function ($) {
  $.fn.carouselWithoutHiding = function (options) {
    var settings = $.extend({}, options);

    var _resize = function ($container) {
      var $owlCarousel = $container.find(".owl-carousel"),
        $navigationContainer = $container.find('[data-carousel-without-hiding-role="navigation"]'),
        $maxwidthContainer = $container.find('[data-carousel-without-hiding-role="maxwidth"]'),
        maxwidthOffset = $maxwidthContainer.offset();

      var left = parseInt(($maxwidthContainer.outerWidth(true) - $maxwidthContainer.outerWidth(false)) / 2),
        padding = parseFloat(($maxwidthContainer.outerWidth() - $maxwidthContainer.width()) / 2);

      var offsetLeft = left + padding;

      $navigationContainer.css("left", offsetLeft);
      $navigationContainer.css("right", offsetLeft);
      $owlCarousel.css("padding-left", offsetLeft * 2);
      $owlCarousel.css("margin-left", "-" + offsetLeft + "px");
      $owlCarousel.css("margin-right", "-" + offsetLeft * 2 + "px");
      $owlCarousel.find(".owl-stage-outer").css("padding-left", offsetLeft);
      $owlCarousel.find(".owl-stage-outer").css("margin-left", "-" + offsetLeft + "px");
      $owlCarousel.find(".owl-stage-outer").css("margin-right", "-" + offsetLeft + "px");
    };

    return this.each(function () {
      var $container = $(this),
        $owlCarousel = $container.find(".owl-carousel");

      _resize($container);

      $owlCarousel.on("resized.owl.carousel", function (event) {
        _resize($container);
      });
    });
  };
})(jQuery);

BX.addCustomEvent(
  "onSliderInitialized",
  BX.delegate(function (eventdata) {
    $("[data-carousel-without-hiding]").carouselWithoutHiding();
  })
);

$(document).ready(function () {
  $(".staff-list__item--scroll-text-hover").on("mouseenter click touchstart", function () {
    var _this = $(this);
    var text = _this.find(".staff-list__item-text-wrapper");
    if (text.length) {
      text.mCustomScrollbar({
        mouseWheel: {
          scrollAmount: 150,
          preventDefault: true,
        },
      });
    }
  });

  // $(document).on("click", ".staff-list.staff-list--view1 .staff-list__item, .staff-list.staff-list--view4 .staff-list__item", function (e) {
  //   if (e && e.target && e.target.tagName !== "A" && !$(e.target).closest(".mCSB_scrollTools").length && !$(e.target).closest(".owl-grab").length) {
  //     var $link = $(this).find("a.staff-list__item-link");
  //     if ($link.length) {
  //       if (
  //         (window.getSelection && !window.getSelection().toString()) ||
  //         (document.selection && !document.selection.createRange().text)
  //       ) {
  //         location.href = $link.attr("href");
  //       }
  //     }
  //   }
  // });
});
