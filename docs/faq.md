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


## How do I upgrade from beta?
> Starting with version 1.0.0, PatchCableMgr has been completely rebuilt using modern frameworks to help deliver features, enhancements, and fixes faster.  Unfortunately, this means there is no direct upgrade path from beta (<1.0.0).  Follow these steps to upgrade from beta to mainline code.

> 1. <u>**Verify Version:**</u> Login to your beta PatchCableMgr app and ensure that you are running version >= 0.3.19 by clicking on the user icon in the top right corner of the page and click ***About***.

>> ![Accessing About](https://pcm-documentation-images.s3.us-west-2.amazonaws.com/public/Beta_Accessing_About.PNG "Accessing About")