<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapbox Interactive Map</title>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        #map {
            width: 100%;
            height: 850px;
        }

        #backButton {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: #060606;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: none;
            /* Hidden by default */
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h2>Interactive Map with District Boundaries</h2>
                <div id="map"></div>
                <button id="backButton">Back to Districts</button>
            </div>
            <div class="col-lg-4"></div>
        </div>
    </div>

    <script>
        mapboxgl.accessToken =
            'pk.eyJ1Ijoic2Vhbmx1bmFyIiwiYSI6ImNqdjI5Y242cTB3dHQ0OXFjOXV3YWRlMm0ifQ.spFYWhYyy72rNxAT0Cg59A'; // Replace with your token

            const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/light-v11',
            center: [34.8, -13.9],
            zoom: 6
        });

        const backButton = document.getElementById("backButton");
        let selectedDistrict = null;

        function loadGeoJSON(geojsonFile, sourceId, highlightFeature = null, zoomOut = false) {
            fetch(geojsonFile)
                .then(response => response.json())
                .then(data => {
                    if (highlightFeature) {
                        // Find the selected feature and highlight it
                        data.features = data.features.map(feature => {
                            if (feature.properties.District === highlightFeature) {
                                feature.properties.selected = true;
                            } else {
                                feature.properties.selected = false;
                            }
                            return feature;
                        });
                    }

                    if (map.getSource(sourceId)) {
                        map.getSource(sourceId).setData(data);
                    } else {
                        map.addSource(sourceId, {
                            type: 'geojson',
                            data: data
                        });

                        map.addLayer({
                            id: 'districts-layer',
                            type: 'fill',
                            source: sourceId,
                            paint: {
                                'fill-color': [
                                    'case',
                                    ['boolean', ['get', 'selected'], false], '#ff0000', // Highlight in red
                                    '#088' // Default green
                                ],
                                'fill-opacity': 0.4
                            }
                        });

                        map.addLayer({
                            id: 'districts-border',
                            type: 'line',
                            source: sourceId,
                            paint: {
                                'line-color': '#000',
                                'line-width': 2
                            }
                        });

                        map.on('click', 'districts-layer', function (e) {
                            const clickedDistrict = e.features[0].properties.District;
                            selectedDistrict = clickedDistrict;

                            const coordinates = e.lngLat;

                            // Zoom to clicked district
                            map.flyTo({
                                center: [coordinates.lng, coordinates.lat],
                                zoom: 7.8,
                                essential: true
                            });

                            // Switch to const.geojson and highlight the district
                            loadGeoJSON('/const.geojson', 'districts', clickedDistrict);

                            // Show back button
                            backButton.style.display = "block";
                        });

                        map.on('mouseenter', 'districts-layer', function () {
                            map.getCanvas().style.cursor = 'pointer';
                        });

                        map.on('mouseleave', 'districts-layer', function () {
                            map.getCanvas().style.cursor = '';
                        });
                    }

                    if (zoomOut) {
                        map.flyTo({
                            center: [34.8, -13.9],
                            zoom: 6,
                            essential: true
                        });
                        backButton.style.display = "none";
                        selectedDistrict = null;
                    }
                })
                .catch(error => console.error('Error loading GeoJSON:', error));
        }

        // Load districts.geojson initially
        map.on('load', function () {
            loadGeoJSON('/districts.geojson', 'districts');
        });

        // Back to Districts button event
        backButton.addEventListener("click", function () {
            loadGeoJSON('/districts.geojson', 'districts', null, true);
        });
    </script>
</body>
</html>
