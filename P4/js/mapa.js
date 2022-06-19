function iniciarMap(){
    var coord = {lat: 37.19702923434852 ,lng: -3.62456440010419};
    var map = new google.maps.Map(document.getElementById('map'),{
      zoom: 10,
      center: coord
    });
    var marker = new google.maps.Marker({
      position: coord,
      map: map
    });
}