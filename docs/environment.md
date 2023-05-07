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
> The **Location Tree** card contains an editable tree of locations and cabinets.  Right click on a location to rename, delete, or create a new location, pod, or cabinet nested within it.

<u>A location can represent a physical region, building, floor, or room. Locations can only be nested under other locations.
<u>A pod represents a group of cabinets within a location. Pods are assumed to have cable path between adjacent cabinets. Pods can only be nested under locations.
<u>A cabinet represents a physical rack or cabinet that can contain objects. Cabinets can be nested under locations or pods.
<u>A floorplan represents the floor of a building. Floor plans can be nested under locations.