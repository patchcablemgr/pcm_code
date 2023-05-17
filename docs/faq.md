# FAQ
> Have questions?  Email us at [support@patchcablemgr.com](mailto:support@patchcablemgr.com)

## How do I upgrade a Self-Hosted deployment?
> Login to your PatchCableMgr server and run the command below to upgrade a self-hosted PatchCableMgr deployment:

```
ansible-playbook pcm_selfhosted/playbook.yaml --ask-become-pass --tags upgrade
```

> **Note:** You must be in the parent directory of the pcm_selfhosted repository.

## How do I perform a clean reinstall of a Self-Hosted deployment?
> Login to your PatchCableMgr server and run the command below to perform a clean reinstall of a self-hosted PatchCableMgr deployment:

```
ansible-playbook pcm_selfhosted/playbook.yaml --ask-become-pass --tags install --skip-tags prevent-destruction
```

> **Warning:** This command will delete all data and reinstall the application from scratch.

> **Note:** You must be in the parent directory of the pcm_selfhosted repository.


## How do I upgrade from legacy?
> Starting with version 1.0.0, PatchCableMgr has been completely rebuilt using modern frameworks to help deliver features, enhancements, and fixes faster.  Unfortunately, this means there is no direct upgrade path from legacy (<1.0.0).  Follow these steps to upgrade from legacy to mainline code.

> 1. <u>**Verify Legacy Version:**</u>
>> Login to your legacy PatchCableMgr app and ensure that you are running version >=0.3.19 by clicking on the user icon in the top right corner of the page and click ***About***.  If your current version is <0.3.19, please contact us at [support@patchcablemgr.com](mailto:support@patchcablemgr.com) for assistance.<br>
>> ![Accessing About](https://pcm-documentation-images.s3.us-west-2.amazonaws.com/public/Beta_Accessing_About.PNG "Accessing About")
> 2. <u>**Install New Version:**</u>
>> Follow the installation instructions [here](https://patchcablemgr.readthedocs.io/en/main/installation/) to get started with PatchCableMgr mainline code.
> 3. <u>**Download Legacy Archive:**</u>
>> Login to your legacy PatchCableMgr app and navigate to the Admin->Integration page.  Click the ***Backup*** button to download a legacy archive.<br>
>> ![Download Legacy Archive](https://pcm-documentation-images.s3.us-west-2.amazonaws.com/public/Legacy_Download_Archive.PNG "Download Legacy Archive")
> 4. <u>**Convert Legacy Archive:**</u>
>> Follow the installation instructions [here](https://patchcablemgr.readthedocs.io/en/main/archive/#convert-legacy-archive) to convert the legacy archive into one that can be imported into your new PatchCableMgr installation.
> 4. <u>**Import Archive:**</u>
>> Login to your new PatchCableMgr app and navigate to the Archive page.  Click the ***Import*** button to upload your converted archive.<br>
>> ![Import Archive](https://pcm-documentation-images.s3.us-west-2.amazonaws.com/public/Archive_Import.PNG "Import Archive")

> At this point, you should have a new PatchCableMgr installation with your data migrated from legacy.  Your users will need re-register with the new installation.  If you have any issues, contact us at [support@patchcablemgr.com](mailto:support@patchcablemgr.com).