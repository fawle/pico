
//todo don't redraw path
//todo position ID? is this the one to rely on add-fetching?
//todo only fetch last few points if marker is already set

var map;
var polyline = new google.maps.Polyline({
    // set desired options for color width
    strokeColor:"black",  // blue (RRGGBB, R=red, G=green, B=blue)
    strokeOpacity: 0.5      // opacity of line
}); // create the polyline (global)

var marker;


function initialize() {
    statusDiv = document.getElementById("status_div");
    if (CALLSIGN == '') {
        jQuery(statusDiv).html('No call sign received. No maps for you!');
        return false;
    }

    map = new google.maps.Map(
        document.getElementById("map_canvas"), {
            center: new google.maps.LatLng(53.4823323, -2.2329286),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    getData();
    setInterval(getData, 60000);

}
google.maps.event.addDomListener(window, "load", initialize);

function getData() {
    var path = [];
    var lastCoordinates = [];
    var markerVisibility = true; 
    var index;
    var max_positions = 2000;
    var altText;

    jQuery(statusDiv).html('Loading...');

    if (marker) {
        marker.setMap(null);
        //max_positions = 3;
    }


    jQuery.getJSON("https://spacenear.us/tracker/datanew.php?type=positions&format=json&max_positions="+max_positions+"&position_id=0&vehicles="+CALLSIGN, function( data) {



            var positions = data.positions.position; //array
            if (positions.length == 0) {
                jQuery(statusDiv).html('No route data available for this call sign!');
                return false;
            }


            for (index = 0; index < positions.length; ++index) {

                var position = new google.maps.LatLng(positions[index].gps_lat, positions[index].gps_lon);

                if (index == 0) {
                    map.panTo(position);

                    if (positions[0].gps_alt) {
                        altText = 'Altitude: '+positions[0].gps_alt + '\n';
                    }
                    if (positions[0].gps_heading) {
                        altText += 'Heading: '+positions[0].gps_heading + '\n';
                    }
                    if (positions[0].gps_speed) {
                        altText += 'Speed: '+positions[0].gps_speed + '\n';
                    }
                    if (positions[0].temp_inside) {
                        altText += 'Temperature: '+positions[0].temp_inside + '\n';
                    }
         


                    marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        visible: markerVisibility,
                        title: altText

                    });
                }


                path.push(position);

                if (path.length >= 2) {
                    polyline.setMap(map);
                    polyline.setPath(path);
                }


            }
            jQuery(statusDiv).html("Done!");

        })
        .fail(function(jqxhr, textStatus, error ) {
            jQuery(statusDiv).html("Couldn't load data");
            console.log( jqxhr + textStatus + error  );
        });


}