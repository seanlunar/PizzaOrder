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
        let currentLevel = 'district'; // 'district' or 'constituency'
        let selectedFeature = null;

        function loadGeoJSON(geojsonFile, sourceId, highlightFeature = null, zoomOut = false) {
            fetch(geojsonFile)
                .then(response => response.json())
                .then(data => {
                    if (highlightFeature) {
                        // Highlight selected feature
                        data.features = data.features.map(feature => {
                            if (
                                (currentLevel === 'district' && feature.properties.District ===
                                    highlightFeature) ||
                                (currentLevel === 'constituency' && feature.properties.Constituency ===
                                    highlightFeature)
                            ) {
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
                            id: `${sourceId}-layer`,
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
                            id: `${sourceId}-border`,
                            type: 'line',
                            source: sourceId,
                            paint: {
                                'line-color': '#000',
                                'line-width': 2
                            }
                        });
                    }

                    // 🚀 Remove any existing click event for this layer before adding a new one
                    map.off('click', `${sourceId}-layer`);
                    map.on('click', `${sourceId}-layer`, function(e) {
                        if (currentLevel === 'district') {
                            const districtName = e.features[0].properties.District;
                            selectedFeature = districtName;

                            map.flyTo({
                                center: e.lngLat,
                                zoom: 10,
                                essential: true
                            });

                            currentLevel = 'constituency';
                            loadGeoJSON('/last.geojson', 'constituencies', districtName);
                            backButton.style.display = "block";
                        } else if (currentLevel === 'constituency') {
                            // Pick the first valid feature and prevent duplicate clicks
                            const feature = e.features[0];
                            if (!feature || !feature.properties.Constituency) return;

                            const constituencyName = feature.properties.Constituency;

                            // 🚀 Check if the constituency is already selected (prevents duplicate logs)
                            if (selectedFeature === constituencyName) return;
                            selectedFeature = constituencyName;

                            console.log(`Clicked constituency: ${constituencyName}`);
                            alert(`You clicked on: ${constituencyName}`);

                            loadGeoJSON('/last.geojson', 'constituencies', constituencyName);
                        }
                    });



                    map.on('mouseenter', `${sourceId}-layer`, function() {
                        map.getCanvas().style.cursor = 'pointer';
                    });

                    map.on('mouseleave', `${sourceId}-layer`, function() {
                        map.getCanvas().style.cursor = '';
                    });

                    if (zoomOut) {
                        map.flyTo({
                            center: [34.8, -13.9],
                            zoom: 6,
                            essential: true
                        });
                        backButton.style.display = "none";
                        selectedFeature = null;
                        currentLevel = 'district';
                    }
                })
                .catch(error => console.error('Error loading GeoJSON:', error));
        }


        // Load district map initially
        map.on('load', function() {
            loadGeoJSON('/districts.geojson', 'districts');
        });

        // Back to Districts button event
        backButton.addEventListener("click", function() {
            loadGeoJSON('/districts.geojson', 'districts', null, true);
        });
    </script>
</body>

</html>
