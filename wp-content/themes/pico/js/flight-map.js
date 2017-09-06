
var map;
var polyline = new google.maps.Polyline({
    // set desired options for color width
    strokeColor:"black",  // blue (RRGGBB, R=red, G=green, B=blue)
    strokeOpacity: 0.5      // opacity of line
}); // create the polyline (global)


function initialize() {
    map = new google.maps.Map(
        document.getElementById("map_canvas"), {
            center: new google.maps.LatLng(53.4823323, -2.2329286),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    gotdata();
    setInterval(gotdata, 60000);

}
google.maps.event.addDomListener(window, "load", initialize);

function gotdata() {
    var path = [];
    var lastCoordinates = [];
    var visibility = true; //will reset after the first one
    var index;


    jQuery.getJSON("https://spacenear.us/tracker/datanew.php?mode=1day&type=positions&format=json&max_positions=0&position_id=0&vehicles="+CALLSIGN, function( data) {



            var positions = data.positions.position; //array
            for (index = 0; index < positions.length; ++index) {

                var position = new google.maps.LatLng(positions[index].gps_lat, positions[index].gps_lon);
                lastCoordinates[index] = new google.maps.Marker({
                    position: position,
                    map: map,
                    visible: visibility,
                    title: 'Altitude: ' + positions[index].gps_alt

                });

                map.panTo(position);
                path.push(position);

                if (path.length >= 2) {
                    polyline.setMap(map);
                    polyline.setPath(path);
                }
                visibility = false;

            }

        })
        .fail(function(jqxhr, textStatus, error ) {
            console.log( jqxhr + textStatus + error  );
        });


}