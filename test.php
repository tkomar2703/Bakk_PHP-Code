<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de-de">
    <head>
	<title>Map | Testanwendung</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-language" content="de" />
<meta name="author" content="Thomas Heiles" />
<link rel="stylesheet" type="text/css" href="map.css"></link>

<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
<script type="text/javascript" src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
<script type="text/javascript" src="tom.js"></script>
    <!--    <link rel="stylesheet" href="../theme/default/style.css" type="text/css"> -->
       <!-- <link rel="stylesheet" href="style.css" type="text/css"> -->
        <style type="text/css">
        <!--    #controlToggle li {
                list-style: none;
            }
            p {
                width: 512px;
            }

            /* avoid pink tiles */
            .olImageLoadError {
                background-color: transparent !important;
            }
			input, select, textarea {
            font: 0.9em Verdana, Arial, sans-serif;
        } -->
        </style>

        <script type="text/javascript">
            var map, drawControls, geojson;
			var polygonLayer;
			var control;
		
		//function serialize() {
        //    var str = polygonLayer.features[0].geometry.getVertices();
        //    document.getElementById('features').value = str;
        //}
		
		function newPolygonAdded () {
			//alert('Polygon completed');
			//var coord = poly.features[0].geometry.getVertices();
			//alert("coord");
			poly.deactivate(); //stops the drawing
			var poly_coord = polygonLayer.features[0].geometry.getVertices();
			var anzahl = poly_coord.length;
			//alert(typeof(anzahl));
			var lon_punkt = new Array(anzahl);
			var lat_punkt = new Array(anzahl);
			for (var i = 0; i < anzahl; i++) {
				lon_punkt[i] = polygonLayer.features[0].geometry.getVertices()[i].x;
				lat_punkt[i] = polygonLayer.features[0].geometry.getVertices()[i].y;
			}
			
			//if (myPoint) {alert('test');}
			//else {alert('idiot');}
			//var myLatLonPoint = myPoint.transform( map.getProjectionObject(),
            //       new OpenLayers.Projection("EPSG:4326"));
			//var Ergebnis = poly_coord.match(/POINT/g);
			//if (Ergebnis) {
			//	for (var i = 0; i < Ergebnis.length; ++i) {
			//alert("Test 2: Fund " + i + " - " + Ergebnis[i]);} }
			//alert(typeof(poly_coord));
			//alert(typeof(myPoint));
			alert(anzahl);
			//alert(lat_punkt);
			
		}   		

		
	//	function endDrag() {
      	//var bounds = bbox.getBounds();
        //setBounds(bounds);
        //drawBox(bounds);
        //document.getElementById("type").value = "test";
		//alert;
	//	}	
            function init(){
			OpenLayers.Lang.setCode('de');
                    map = new OpenLayers.Map('map', {
    projection: new OpenLayers.Projection("EPSG:900913"),
    displayProjection: new OpenLayers.Projection("EPSG:4326"),
    controls: [
        new OpenLayers.Control.Navigation(),
        //  new OpenLayers.Control.LayerSwitcher(),
		//	new OpenLayers.Control.MouseDefaults(),
		//	new OpenLayers.Control.Attribution(),
        new OpenLayers.Control.PanZoomBar()],
        maxExtent:
            new OpenLayers.Bounds(-20037508.34,-20037508.34,
                                    20037508.34, 20037508.34),
        numZoomLevels: 18,
        maxResolution: 156543,
        units: 'meters'
    });
				//geojson = new OpenLayers.Format.GeoJSON();
                //var wmsLayer = new OpenLayers.Layer.WMS( "OpenLayers WMS",
                //    "http://vmap0.tiles.osgeo.org/wms/vmap0?", {layers: 'basic'});
				var wmsLayer = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
            //    var pointLayer = new OpenLayers.Layer.Vector("Point Layer");
            //    var lineLayer = new OpenLayers.Layer.Vector("Line Layer");
                polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer");
            //    var boxLayer = new OpenLayers.Layer.Vector("Box layer");

            //    map.addLayers([wmsLayer, pointLayer, lineLayer, polygonLayer, boxLayer]);
				map.addLayers([wmsLayer, polygonLayer]);
               // map.addControl(new OpenLayers.Control.LayerSwitcher());
            //    map.addControl(new OpenLayers.Control.MousePosition());

               drawControls = {
                    //point: new OpenLayers.Control.DrawFeature(pointLayer,
                    //    OpenLayers.Handler.Point),
                    //line: new OpenLayers.Control.DrawFeature(lineLayer,
                    //    OpenLayers.Handler.Path),	
					polygon: poly = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, 
                            {eventListeners:{"featureadded": newPolygonAdded}}),  
						
                    //polygon: poly = new OpenLayers.Control.DrawFeature(polygonLayer,
                    //    OpenLayers.Handler.Polygon),
                    box: boxt = new OpenLayers.Control.DrawFeature(boxLayer,
                        OpenLayers.Handler.RegularPolygon, {
                            handlerOptions: {
                                sides: 4,
                                irregular: true
                            }
                        }
                    )
                
				
				}; 
				
                for(var key in drawControls) {
                    map.addControl(drawControls[key]);
                } 

                //map.setCenter(new OpenLayers.LonLat(0, 0), 3);
						var lon = 15;
		var lat = 47;
		var zoom = 7;
		jumpTo(lon, lat, zoom);
            //    document.getElementById('noneToggle').checked = true;
				//control.deactivate();
				//poly.handler.callbacks.done = endDrag;
            }

            function toggleControl(element) {
                for(key in drawControls) {
                    control = drawControls[key];
                    if(element.value == key) {
                        control.activate();
                    } else {
                        control.deactivate();
                    }
                }
            }

        //    function allowPan(element) {
        //        var stop = !element.checked;
        //        for(var key in drawControls) {
        //            drawControls[key].handler.stopDown = stop;
        //            drawControls[key].handler.stopUp = stop;
        //        }
        //    }
			
	//		      function dragNewBox() {
    //    poly.activate();
 //       transform.deactivate(); //The remove the box with handles
     //   polygonLayer.destroyFeatures();
     // }
			
        </script>
    </head>
    <body onload="init()">
      <!--  <h1 id="title">OpenLayers Draw Feature Example</h1>

        <div id="tags">
            point, line, linestring, polygon, box, digitizing, geometry, draw, drag
        </div>

        <p id="shortdesc">
            Demonstrate on-screen digitizing tools for point, line, polygon and box creation.
        </p> -->

        <div id="map"></div>
    
    <!--    <ul id="controlToggle">
            <li>
                <input type="radio" name="type" value="none" id="noneToggle"
                       onclick="toggleControl(this);" checked="checked" />
                <label for="noneToggle">navigate</label>
            </li>
            <li>
                <input type="radio" name="type" value="point" id="pointToggle" onclick="toggleControl(this);" />
                <label for="pointToggle">draw point</label>
            </li>
            <li>
                <input type="radio" name="type" value="line" id="lineToggle" onclick="toggleControl(this);" />
                <label for="lineToggle">draw line</label>
            </li>
            <li> -->
                <input type="button" name="type" value="polygon" id="polygonToggle" onclick="toggleControl(this);" />
                <!--<label for="polygonToggle">draw polygon</label>
            </li>
            <li>
                <input type="radio" name="type" value="box" id="boxToggle" onclick="toggleControl(this);" />
                <label for="boxToggle">draw box</label>
            </li>
            <li>
                <input type="checkbox" name="allow-pan" value="allow-pan" id="allowPanCheckbox" checked=true onclick="allowPan(this);" />
                <label for="allowPanCheckbox">allow pan while drawing</label>
            </li>
        </ul> -->
	<!--	<div>Adjust the box ...or <input type="button" value="drag new box" onclick="dragNewBox();"></div>
        <div id="docs">
            <p>With the point drawing control active, click on the map to add a point.</p>
            <p>With the line drawing control active, click on the map to add the points that make up your line.
            Double-click to finish drawing.</p>
            <p>With the polygon drawing control active, click on the map to add the points that make up your
            polygon.  Double-click to finish drawing.</p>
            <p>With the box drawing control active, click in the map and drag the mouse to get a rectangle. Release
            the mouse to finish.</p>
            <p>With any drawing control active, paning the map can still be achieved.  Drag the map as
            usual for that.</p>
            <p>Hold down the shift key while drawing to activate freehand mode.  While drawing lines or polygons
            in freehand mode, hold the mouse down and a point will be added with every mouse movement.<p>
        </div>
		
		<input type="button" value="refresh" onclick="serialize();"><br>
        <textarea id="features"></textarea> -->
    </body>
</html>
