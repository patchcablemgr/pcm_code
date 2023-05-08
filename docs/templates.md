# Overview
> Use the Template page to create and edit custom templates which represent objects in your environment. The template page is separated into 4 cards:

## Properties
> The **Properties** card contains all the configuration properties needed to create a template.

>> <u>Name:</u> Specifies the template name.  A valid template name contains alphanumeric characters as well as underscores (_) and hyphens (-).

>> <u>Category:</u> Specifies the template category.  A category is user created and applies a color to the template as well as the ability to group similar templates together for easier access.

>> <u>Template Type:</u> A stanadard template is one that can be installed in a cabinet by itself. An insert is a template is installed in an existing object's enclosure partition.

>> <u>Template Size:</u> Defines the Rack Unit size of the template. Max is 25.

>> <u>Template Function:</u> Defines the template as an endpoint or passive.

>> - **Endpoint** templates will always terminate a cable path (switch, router, server, etc.).

>> - **Passive** templates is part of the physical cable infrastructure (patch panel, fiber insert, etc.).

>> <u>Mounting Configuration:</u> A 2-post template will only be visible on one side of the installed cabinet and can have another 2-post template installed behind it. A 4-post template will have a front and a back which occupy both sides of the cabinet it is installed in.

>> <u>Partition Type:</u> Generic partitions have no properties and can be used as spacers or containers for other partitions. Connectable partitions contain ports or interfaces. Enclosure partitions can contain insert templates.

>> <u>Add/Remove Partition</u>: Partitions allow for the template layout to accurately reflect the object it is modeling. A horizontal partition spans the entire width of the partition it is created in and can grow vertically. A vertical partition spance the full height of the partition it is created in and can grow horizontally.

>> <u>Partition Size:</u> Horizontal partitions grow vertically in 0.5 RU increments. Vertical partitions grow horizontally in increments equal to 1/24th of the entire template width.

>> <u>Port ID:</u> The format describing how the template ports will be identified. Clicking the edit button will open a window allowing you to add/change/delete fields that will be used to compile each port ID (ie "Port-1a"). You can configure up to 5 fields of 3 possible field types.

>> - A **Static** field will be compiled into the port ID as it is defined by the user.
>> - An **Incremental** field accepts a single alphanumeric character and will increment with the port numbers.
> - A **Series** field accepts a comma separated list of strings that will be cycled through when compiling the port ID.

>> <u>Port Layout:</u> Number of port columns and rows in the selected connectable partition.

>> <u>Port Orientation:</u> Determines the direction in which port numbers are incremented. Switches are typically ordered top to bottom while most RJ45 patch panels are ordered left to right.

>> <u>Port Type:</u> The type of port for the selected connectable partition. When connecting a cable end to an object port, the cable end type and port type must match. The exception to this rule are SFP ports, which can accept any cable end type.

>> <u>Media Type:</u> This configuration is exclusive to passive templates. This refers to the cabling behind the passive object. When trunking two passive objects, the media type must match.

>> <u>Enclosure Layout:</u> This configuration is exclusive to enclosure partition types. Enclosure layout columns and rows determine the slots available to install insert objects.

## Preview
> The **Preview** card displays the template as it is created.

> The **Face** dropdown selects which cabinet face is displayed.  The **View** dropdown selects the mode in which the template is displayed.  The **Sticky** switch determines if the preview box scrolls with the page keeping the template visible even when working at the end of the long list of properties.  A yellow highlight appears around the selected partition and indicates that any partition specific configuration will be applied to it.

## Template Details
> The **Template Details** card displays information about the template selected in the **Templates** card.

> The following template properties are able to be edited:

> - Template Name
> - Category
> - Image

> The following template actions can be performed:

> - Clone
> - Delete

## Templates
> The **Templates** card lists all templates grouped by category.

> Front and Back radio buttons toggle the face of all available templates. Clicking on an object will highlight the selected partition and display information in the **Template Details** card.  The filter field allows you to display only templates containing one or more strings of text.