# Installation
> PatchCableMgr offers two different deployment methods; hosted and self-hosted.

> - Hosted deployment method is the easiest and fastest way to get to get started.  Your PatchCableMgr instance runs on our AWS servers.  Server, OS, Software, and SSL maintenance are all managed by PatchCableMgr.

> - Self-Hosted deployment method takes more effort to get started, but gives you more control over security

## Hosted
> A hosted account is hosted on PCM servers. Everything related to server maintenance and support (server configuration, OS updates, app upgrades, SSL certificates, etc.) is handled by PCM. Upon registration, a tenant is created with a unique domain name (ie. acme.patchcablemgr.com) and database creating an isolated environment.

> Click [here](https://register.patchcablemgr.com/register-tenant) to register.

## Self-Hosted
> A self-hosted account runs on a virtual machine hosted on your server and is maintained by you.

> Recommended requirements:

> - CPU: 2 cores
> - Memory: 4 GB
> - Disk: 100 GB
> - OS: Ubuntu 22.04 LTS

> Use the commands below to download, install, and run PatchCableMgr:

```
apt install ansible
git clone https://github.com/patchcablemgr/pcm_selfhosted.git
ansible-playbook pcm_selfhosted/playbook.yaml --ask-become-pass --tags install
```

> **Note:** You must be in the parent directory of the pcm_selfhosted repository.

> The installation could take 5 to 10 minutes.  Once the installation completes, the application can be accessed by navigating to https://SERVER_IP