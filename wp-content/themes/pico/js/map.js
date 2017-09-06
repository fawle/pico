function initMap() {

    var a = {
        info: '<strong>Home</strong><br>\
            ',
        lat: 53.4823323,
        long: -2.2329286
    };

    var b = {
        info: '<strong>Home Sweet Home</strong>',
        lat: 53.483658,
        long: -2.2373902
    };

    var c = {
        info: '<strong>Common</strong>',
        lat: 53.4839072,
        long: -2.2359522
    };

    var locations = [
        [a.info, a.lat, a.long, 0],
        [b.info, b.lat, b.long, 1],
        [c.info, c.lat, c.long, 2],
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: new google.maps.LatLng(c.lat, c.long),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({});

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        /*
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
        */
    }
}