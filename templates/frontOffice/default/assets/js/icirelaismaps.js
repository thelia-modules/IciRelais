function initMaps(adr_geoloc, locations, url_site){

	var geocoder = new google.maps.Geocoder();
	
	var mapOptions = {
	  	zoom: 13,
	  	mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	
	// On va créer la map dans la div qui a l'id relaymap
	var map = new google.maps.Map(document.getElementById('relaymap'), mapOptions);
	
	/*
	// On récupère les coordonnées de l'adresse en cours
	geocoder.geocode({'address': adr_geoloc}, function(results, status){
	  	if(status == google.maps.GeocoderStatus.OK){
	  		// Et on centre la map sur cette position
	    	map.setCenter(results[0].geometry.location);
	    } 
	    else{
	    	// Sinon on met le centre de la map sur Clermont-Ferrand ;)
	        alert('L\'adresse en cours ne peut être geolocalisée');
	        var myLatLng = new google.maps.LatLng(45.7789, 3.0782);
	        map.setCenter(myLatLng);
	        map.setZoom(3);
	    }
	});
	var infowindow = new google.maps.InfoWindow();
	
	var marker, i;
	
	// Pour chaque point relais dans locations on crée un nouveau marker
	for(i = 0; i < locations.length; i++){  
		marker = new google.maps.Marker({
	    	position: new google.maps.LatLng(locations[i][1], locations[i][2]),
	    	// Icone d'un point relai
	    	icon: new google.maps.MarkerImage(url_site + "assets/frontOffice/default/IciRelais/img/logo_pr.png"),
	    	map: map
	    });
	    
	    // Lors du clic sur un point relai on affiche une bulle avec les informations
	    google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
	    		infowindow.setContent(locations[i][0]+'<br/>'+locations[i][4]+'<br/>'+locations[i][5]+' '+locations[i][6]+'<br/>'+locations[i][7]);
	    		infowindow.open(map, marker);
	    		$('#pr'+locations[i][3]).attr('checked','checked');
	    	}
	    })(marker, i));
	    
	     // Lors de la fermeture de la bulle d'information on déselectionne le bouton radio associé
	    google.maps.event.addListener(infowindow, 'closeclick', (function(marker, i) {
			return function() {
	    		$('#pr'+locations[i][3]).removeAttr('checked');
	    	}
	    })(marker, i));
	    
	}
	*/
	

}
