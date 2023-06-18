## 1.4.13

Fixes:

- Legacy archive conversion incorrect floorplan device port format
- Legacy archive conversion replace empty category name with "PCM_Generated"
- Legacy archive conversion replace invalid location name characters with "_"
- Legacy archive conversion incorrectly converting cabinet_ru installed in top-down cabinets
- Archive import trunk assumes b_id is never floorplan device
- Legacy archive conversion assumes Trunk Peer B is never floorplan device

## 1.4.12

Enhancements:

- Added legacy migration procedure to documentation FAQ

## 1.4.11

Fixes:

- Object/template details card was not displaying partition media type

Enhancements:

- Added Documentation link to user dropdown

## 1.4.10

Enhancements:

- Moved documentation to Read the Docs

## 1.4.9

Fixes:

- Data sync preventing registration under certain conditions

## 1.4.8

Enhancements:

- Initial markdown documentation

## 1.4.7

Enhancements:

- Upgrade to Vue.js 2.7.14

## 1.4.6

Enhancements:

- Remove server management capabilities from Admin page
- Remove dark/light toggle
- Remove empty "Actions" dropdown in connection path card
- Moved version to "About" modal

## 1.4.5

Fixes:

- Multiple RU templates not filling entire space in Firefox

## 1.4.4

Features:

- Set template image during configuration
- Selecte template view (template/image)

Enhancements:

- Completed legacy archive conversion feature

Fixes:

- Inserts displaying in all enclosure partitions
- Template images broken

## 1.4.3

Fixes:

- Version 1.4.1 seeder missing CableConnectorModel

## 1.4.2

Fixes:

- Add timestamps to attributes_port_connector

## 1.4.1

Features:

- Archive conversion

Fixes:

- Not able to drop templates into cabinet

## 1.4.0

Features:

- Archive import/export

Fixes:

- Resolved "Invalid partition size" error when creating templates
- Not able to clone insert templates

## 1.3.1

Fixes:

- New tenant registration failing to seed database

## 1.3.0

Features:

- Managed patch cables

Fixes:

- Inserts failing to install in deep nested enclosure partitions
- Incorrect trunk peer port in cable path
- Fantom templates after creation
- Floorplan card size
- Location is deselected when object is deleted
- Validation error when renaming floorplan objects
- Incorrect floorplan object position when window is scrolled

Enhancements:

- Display port name on hover

## 1.2.4

Enhancements:

- Data synchronization

## 1.2.3

Features:

- Fix user registration error

## 1.2.2

Features:

- Fix tenant registration error

## 1.2.1

Features:

- Delete users

## 1.2.0

Features:

- Cabinet Details

Fixes:

- Children of pods not visible

## 1.1.0

Features:

- Template catalog

Fixes:

- Resolved error when marking port as populated

## 1.0.2

Fixes:

- Resolved inability to upload template image

## 1.0.1

Fixes:

- Resolved card width issue on mobile browsers

Enhancements:

- Display object ports in ascending order
- Scroll long environment trees
- Allow cabinets to be "sticky" when scrolling

## 1.0.0

This is the initial release of PatchCableMgr.
