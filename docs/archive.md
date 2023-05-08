# Archive
> The **Archive** page allows administrators and operators to export and import archives for purposes of backup/restore or mass edits.  The **Archive** page is separated into multiple cards:

> - **Archive** export or import archives
> - **Convert Legacy Archive** convert legacy archives to be accepted by current version

## Archive
> The **Archive** card contains two buttons to manage archives:

>> <u>Export:</u> When you export an archive from PatchCableMgr, a .zip file will be downloaded containing a number of .csv and image files.  The files should not be edited unless under the guidance of PatchCableMgr support.  It is important to note that only operational data is archived.  Archives do not contain any user data, settings, history, or licensing information.

>> <u>Import:</u> When you import an archive to PatchCableMgr, you are uploading a previously exported archive.  It is important to note that all data in your PatchCableMgr deployment will be replaced with the imported archive.

## Convert Legacy Archive
> The **Convert Legacy Archive** card contains a single button to upload a legacy archive so that it can be converted to be accepted by the current version.

>> <u>Upload:</u> When you upload a legacy archive to PatchCableMgr, you are uploading a previously exported archive obtained from a legacy version of PatchCableMgr.  This feature is helpful when migrating from legacy (<= 0.3.19) to current PatchCableMgr (>= 1.0.0).  The legacy archive version must be equal to 0.3.19 for the conversion to be successful.