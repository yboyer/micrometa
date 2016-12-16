var map = new google.maps.Map(document.querySelector('#map'), {
  center: {
    lat: 0,
    lng: 0
  },
  zoom: 3,
  disableDoubleClickZoom: false
});
var geocoder = new google.maps.Geocoder();
var bounds = new google.maps.LatLngBounds();
var currentInfoWindow = null;

function getgeocodeFromCity(description, name, path, dataLocation, filename) {
  geocoder.geocode({
    'address': dataLocation
  }, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      placeMarker(results[0].geometry.location, description, name, path, filename);
    }
  });
}

function placeMarker(location, description, name, path, filename) {
  var marker = new google.maps.Marker({
    position: location,
    map: map,
    title: name,
    draggable: false,
    animation: google.maps.Animation.DROP
  });

  bounds.extend(location);
  map.fitBounds(bounds);

  marker.infowindow = new google.maps.InfoWindow({
    content: '<figure>' +
      '<img class="thumb" src="' + path + '" alt="' + name + '">' +
      '<figcaption><a href="detail/' + filename + '"><h5>' + name + '</h5></a></figcaption>' +
      '</figure>' +
      '<p>' + description + '</p>'
  });
  google.maps.event.addListener(marker, 'click', function() {
    // Close the current infowindow
    if (currentInfoWindow !== null) {
      currentInfoWindow.close();
    }
    currentInfoWindow = this.infowindow;

    this.infowindow.open(map, marker);
  });
}
