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

>> <u>Locations</u> represent a physical region, building, floor, or room. Locations can only be nested under other locations.

>> <u>Pods</u> represent a group of cabinets within a location. Pods are assumed to have cable path between adjacent cabinets. Pods can only be nested under locations.

>> <u>Cabinets</u> represent a physical rack or cabinet that can contain objects. Cabinets can be nested under locations or pods.

>> <u>Floorplans</u> represent the floor of a building. Floorplans can be nested under locations.

## Cabinet
> The **Cabinet** card displays the selected cabinet and all of the objects it contains.

> The **Face** dropdown selects which cabinet face is displayed.  The **View** dropdown selects the mode in which the template is displayed.  The **Sticky** switch determines if the preview box scrolls with the page keeping the template visible even when working at the end of the long list of properties.  A yellow highlight appears around the selected partition and indicates that any partition specific configuration will be applied to it.

## Cabinet Details
> The **Cabinet Details** card displays information about the selected cabinet.

>> <u>RU Size</u> adjusts the size or number of rack units a cabinet is.

>> <u>RU Orientation</u> adjusts whether the RU numbers of a cabinet increment top-down or bottom-up.

>> <u>Cable Paths</u> represent physical cable paths that exist between two cabinets.  This information is used by path finder for calculating cable paths.

>> <u>Cabinet Adjacencies</u> represent a left/right adjacent relationship between two cabinets.  This information is used by path finder for calculating cable paths.  Path finder assumes that there is cable path to adjacent cabinets.

## Object Details
> The **Object Details** card displays information about the selected object and object partition.

> The following object properties are able to be edited:

> - Object Name
> - Trunked To

> The following template actions can be performed:

> - Delete
> - Clear Trunk

>> <u>Object Name</u> Specifies the object name. A valid template name contains alphanumeric characters as well as underscores (_) and hyphens (-).

>> <u>Template Name</u> Reflects the object's template.

>> <u>Category</u> Reflects the object's template category. Template categories can be changed from the **Templates** page.

>> <u>Type</u> Reflects the object's template type.

>> <u>Function</u> Reflects the object's template function.

>> <u>RU Size</u> Reflects the object's template RU size.

>> <u>Mount Config</u> Reflects the object's template mounting configuration.

>> <u>Image</u> Reflects the object's template image for the selected face. Template image can be changed from the **Templates** page.

>> <u>Partition Type</u> Reflects the object's template partition type.

>> <u>Trunked To</u> Reflects the trunk peer of the selected partition.  Only connectable partitions can be trunked to other connectable partitions.  When trunking a passive partition to another passive partition, the number of ports must be equal and the media type must match.  When trunking a passive partition to an endpoint partition, the number of ports must be equal and the endpoint partition port type must be RJ45.  An endpoint partition cannot be trunked to another endpoint partition.

>> <u>Port Range</u> Reflects the port range of the selected partition.  Template port range can be changed from the **Templates** page.

>> <u>Port Orientation</u> Reflects the port orientation or direction of the selected partition.  Template port orientation can be changed from the **Templates** page.

>> <u>Port Type</u> Reflects the port type of the selected partition.

>> <u>Media Type</u> Reflects the port media of the selected partition.

## Templates
> The **Templates** card lists all templates grouped by category.

> Front and Back radio buttons toggle the face of all available templates. Clicking on an object will highlight the selected partition.  The filter field allows you to display only templates containing one or more strings of text.  Drag and drop templates to a cabinet to install them.