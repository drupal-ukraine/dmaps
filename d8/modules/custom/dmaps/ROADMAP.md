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
1. Location:
- earth.inc - Trigonometry for calculating geographical distances.
- service for getting supported locations, now they are located in the folder /supported as separate files for each location.
- Location geocoder - this is almost all functions from location.inc
- possibly some other that can be allocated from the Location module.

2. Gmap:
- Gmap strings
- Gmap marker
- GmapPolylineToolbox
- possibly some other that can be allocated from the Gmap module.

3. StaticMap
- Request to google API
