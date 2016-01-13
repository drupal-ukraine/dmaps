RoadMap
-------
1. Backend (Controller)
- Investigate list of backend features for Location and Gmap modules within latest D7 Versions
- Decide what features needs to be implemented into D8 version of dmaps
- Start to implement part of features as D8 module's services
- Make a ToDo as a result of features that are not going to be as services

2. Storage (Model)
- Investigate all parts of old modules that are going to be used as entities
- Implement respective entities as D8 Entities.


3. Frontend (View)
- Investigate all forms for gmap and location modules
- Decide what forms needs to be implemented into D8 version
- Implements all forms

Code features that are going to be services for dmaps
-----------------------------------------------------
### Location:
- [ ] earth.inc - Trigonometry for calculating geographical distances.

EarthGeographicalDistances()
include location_distance_between()

- [x] service for getting supported locations, now they are located in the folder /supported as separate files for each location.
LocationCountriesManager()

- [ ] Province service
location_get_provinces()
location_province_name()
location_province_code()

- [ ] Map link service
location_map_link()
location_map_link_providers()
location_map_link_default_providers()
location_map_link_google()

- [ ] Location geocoder - this is almost all functions from location.inc
other functions from location.inc not included to other services

- [ ] possibly some other that can be allocated from the Location module.

### Gmap:
- [ ] Gmap strings
- [ ] Gmap marker
- [ ] GmapPolylineToolbox
- [ ] possibly some other that can be allocated from the Gmap module.

### StaticMap
------------
- [ ] Request to google API
