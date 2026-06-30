(function($){
$(window).scroll(startCounter);
function startCounter() {
    if ($(window).scrollTop() > 50) {
        $(window).off("scroll", startCounter);
        $('.count').each(function () {
            var $this = $(this);
            jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                duration: 1000,
                easing: 'swing',
                step: function () {
                    $this.text(Math.ceil(this.Counter));
                }
            });
        });
    }
	};

Fancybox.bind('[data-fancybox="gallery"]', {
  Toolbar: false,
  animated: false,
  dragToClose: false,

  showClass: false,
  hideClass: false,

  closeButton: "top",

  Image: {
    click: "close",
    wheel: "slide",
    zoom: false,
    fit: "cover",
  },

  Thumbs: {
    minScreenHeight: 0,
  },
});
})(jQuery);