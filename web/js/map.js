var myOptions = {
    center: {
        lat: 37.09024,
        lng: -95.712891
    },
    scrollwheel: true,
    zoom: 3,
    disableDoubleClickZoom: false,
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var map = new google.maps.Map(document.getElementById("map"), myOptions);
var geocoder = new google.maps.Geocoder();

function getgeocodeFromCity(description, name, path, dataLocation) {
    geocoder.geocode({
        'address': dataLocation
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            placeMarker(results[0].geometry.location, description, name, path)
        }
    });
}

function placeMarker(location, description, name, path) {
    var marker = new google.maps.Marker({
        position: location,
        map: map,
        title: name,
        draggable: false,
        animation: google.maps.Animation.DROP
    });
    iWindowContent ='<figure><img class="thumb" src="' + path + '" alt="' + name + '">'+
						'<figcaption><p>' + description + '</p></figcaption>'+
						'</figure>';
    var infowindow = new google.maps.InfoWindow({
      content: iWindowContent
    });
    google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map, marker);
    });
}
