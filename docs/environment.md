# Overview
> The **Environment** page allows you to create a hierarchical representation of your environment's locations, pods, cabinets, floorplans, and objects as well as their relationship to each other. The **Environment** page is separated into multiple cards depending on location tree selection:

> - **Locations and Cabinets** displays and manages environment tree.
> - **Cabinet** displays the selected cabinet where you can install objects from the **Templates** card.
> - **Cabinet Details** displays details about the selected cabinet.
> - **Object Details** displays details about the selected object and object partition.
> - **Templates** displays templates available to be installed into a cabinet.
> - **Floorplan** displays the selected floorplan.
> - **Floorplan Object Details** displays details about the selected floorplan object.
> - **Floorplan Objects** displays all objects deployed in the selected floorplan.

## Locations and Cabinets
> The **Locations and Cabinets** card contains an editable tree of locations and cabinets.  Right click on a location to rename, delete, or create a new location, pod, or cabinet nested within it.

> <u>Locations</u> represent a physical region, building, floor, or room. Locations can only be nested under other locations.

> <u>Pods</u> represent a group of cabinets within a location. Pods are assumed to have cable path between adjacent cabinets. Pods can only be nested under locations.

> <u>Cabinets</u> represent a physical rack or cabinet that can contain objects. Cabinets can be nested under locations or pods.

> <u>Floorplans</u> represent the floor of a building. Floorplans can be nested under locations.

## Cabinet
> The **Cabinet** card displays the selected cabinet and all of the objects it contains.

> The **Face** dropdown selects which cabinet face is displayed.  The **View** dropdown selects the mode in which the template is displayed.  The **Sticky** switch determines if the preview box scrolls with the page keeping the template visible even when working at the end of the long list of properties.  A yellow highlight appears around the selected partition and indicates that any partition specific configuration will be applied to it.

## Cabinet Details
> The **Cabinet Details** card displays information about the selected cabinet.

The "RU Size" and "RU Orientation" properties are editable.
The "Cable Paths" table lists other cabinets which the selected cabinet has cable path to. This information is used by path finder for calculating cable paths.
The "Cabinet Adjacencies" table lists cabinets which are adjacent to the selected cabinet. This information is used by path finder for calculating cable paths. Path finder assumes that there is cable path to adjacent cabinets.