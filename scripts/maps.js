$(document).ready(function(){

    var map;
    var markersArray = [];
    var bounds = new google.maps.LatLngBounds();
    var myLatlng = new google.maps.LatLng(45.519098,-122.672138);
    
    alert("hola");
    initialize();
    function initialize() {
        
        
        var myOptions = {
        zoom: 13,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.HYBRID
        };
        
    
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    if($('#ddlTypes').length) {
        getAllTypes();
    } else {
        getAllSightings();
    }
    }

    function getAllTypes() {

        $.getJSON("service.php?action=getSightingsTypes", function(json_types) {
            alert(JSON.stringify(json_types.creature_types));
            if (json_types.creature_types.length > 0) {
                $.each(json_types.creature_types, function() {
                    var info = this['type'];
                    var $li = $("<option />");
                    $li.html(info);
                    $li.appendTo("#ddlTypes");
                });
            }
        });
    }

    function getSightingsByType(type){
        
        $.getJSON("service.php?action=getSightingsByType&type="+type, function(json) {
            
			if (json.sightings.length > 0) {
				$('#sight_list').empty();	
				
				$.each(json.sightings, function() {
                    
                    /* add_sighting(this); */


                    var info = 'Distance: ' + this['distance'] + '<br>' + ' Height: ' + this['height'];
                    info += ', Weight: ' + this['weight'] + ', Color: ' + this['color'] + '<br>';
                    info += 'Latitude: ' + this['lat'] + ', Longitude: ' + this['long'];

                    var loc = new google.maps.LatLng(this['lat'], this['long']);
                    var opts = {
                        map: map,
                        position: loc,
                        clickable: true
                    };

                    var marker = new google.maps.Marker(opts);
                    markersArray.push(marker);
                    google.maps.event.addListener(marker, 'click', function(){
                        var info_window = new google.maps.InfoWindow( { content: info });
                         /* info_window.content = info; */
                         info_window.open(map, marker);
                    });
                    var $li = $("<li />");
                    $li.html('Date: ' + this['date'] + ', Type: ' + this['type']);
                    $li.addClass("sightings");
                    $li.click(function(){
                        var info_window = new google.maps.InfoWindow( { content: info });
                        /* info_window.content = info;  */
                        info_window.open(map, marker);
                    });
                    $li.appendTo("#sight_list");
                    bounds.extend(loc);
                });
                map.fitBounds(bounds);
			} 
		});
    }

    

    $('#ddlTypes').change(function() {
        alert("juliprofe");
        if($(this).val() != ""){
            clearOverlays();
            getSightingsByType($(this).val());
        }
    });

    function clearOverlays() {
        if (markersArray) {
            for (i in markersArray) {
                markersArray[i].setMap(null);
            }
            markersArray.length = 0;
            bounds = null;
            bounds = new google.maps.LatLngBounds();
        }
    }
    

    google.maps.event.addListener(map, 'zoom_changed', function() {
        setTimeout(moveToNewYork, 3000);
    });

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title:"Hello World!"
    });

    google.maps.event.addListener(marker, 'click', function() {
        map.setZoom(8);
    });
    

    function getAllSightings(){
        $.getJSON("service.php?action=getAllSightings", function(json) {
            alert("Mundo");
            if (json.sightings.length > 0) {
                $("#sight_list").empty();

                $.each(json.sightings, function() {
                    var info = 'Date: ' + this['date'] + ', Type: ' + this['type'];

                    var $li = $("<li />");
                    $li.html(info);
                    $li.addClass("sightings");
                    $li.attr('id', this['id']) ;
                    $li.click(function(){
                        getSingleSighting(this['id']);
                    });
                    $li.appendTo("#sight_list");
                });
            }
        });
    }

    function getSingleSighting(id){
        $.getJSON("service.php?action=getSingleSighting&id="+id, function(json){
            if(json.sightings.length > 0){
                $.each(json.sightings, function(){
                    var loc = new google.maps.LatLng(this['lat'], this['long']);
                    var my_marker = new google.maps.Marker({
                        position:loc,
                        map:map,
                        title:this['type']
                    });
                    map.setCenter(loc, 20);
                });
                
            }
        });
        
    }

    
    function moveToNewYork() {
        var NewYork = new google.maps.LatLng(45.526585, -122.642612);
        map.setCenter(NewYork);
    }
    
    /* initialize(); */
    




});//End ready function