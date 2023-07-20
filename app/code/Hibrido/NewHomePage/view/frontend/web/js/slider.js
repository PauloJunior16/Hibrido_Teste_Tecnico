require(['jquery', 'slick'], function($) {
    $(document).ready(function() {
        $('.slick-carousel').slick({
            // Customize the slider options as needed
            dots: true,
            arrows: true,
            prevArrow: '<button class="slick-prev">Prev</button>',
            nextArrow: '<button class="slick-next">Next</button>',
        });
    });
});
