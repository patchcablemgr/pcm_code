# Overview
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
