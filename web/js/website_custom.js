$('.animsition').animsition({
    timeout: true
});

$(document).ready(function () {
    $(document).find('.owl-carousel-img').owlCarousel({
        autoplay: true,
        autoplayHoverPause: true,
        loop: true,
        margin: 50,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 1
            },
            575: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            }
        }
    });

    var video = $(document).find('.owl-carousel-video');
    video.owlCarousel({
        videoWidth: 200,
        videoHeight: 150,
        video: true,
        center: true,
        autoplay: true,
        autoplayHoverPause: true,
        loop: true,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 1
            },
            575: {
                items: 2
            },
            768: {
                items: 3
            }
        }
    });

    $(document).find('.notice-link').click(function (e) {
        e.preventDefault();

        $('#modal-notice').remove();
        $('body').append('<div id="modal-notice" class="notice-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>');

        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (data) {
                $('#modal-notice').empty().append(data).modal('show');
            }
        });
    });

    $('#modal-notice').on('hidden.bs.modal', function () {
        $(this).remove();
    });

    $('.sticky').stick_in_parent();

    $('.lang-select').click(function (event) {
        event.preventDefault();

        $(document).find('.goog-te-combo').val($(this).data('lang'));

        window.location = $(this).attr('href');
        location.reload();
    });
});

function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'es'}, 'google_translate_element');
}

var mapStyle = [
    {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#e9e9e9"
            },
            {
                "lightness": 17
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f5f5f5"
            },
            {
                "lightness": 20
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 17
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 29
            },
            {
                "weight": 0.2
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 18
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "lightness": 16
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f5f5f5"
            },
            {
                "lightness": 21
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#dedede"
            },
            {
                "lightness": 21
            }
        ]
    },
    {
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#ffffff"
            },
            {
                "lightness": 16
            }
        ]
    },
    {
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "saturation": 36
            },
            {
                "color": "#333333"
            },
            {
                "lightness": 40
            }
        ]
    },
    {
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#f2f2f2"
            },
            {
                "lightness": 19
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#fefefe"
            },
            {
                "lightness": 20
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#fefefe"
            },
            {
                "lightness": 17
            },
            {
                "weight": 1.2
            }
        ]
    }
];
