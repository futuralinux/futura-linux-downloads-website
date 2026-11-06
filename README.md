# Futura Linux ISO downloads

A PHP download page styled to match the main Futura Linux website. It scans `/opt/iso` on every page request and lists regular `.iso` files newest-first. No database or manual release metadata is needed.

The app is intended to run at **https://downloads.futuralinux.org/** as a separate subdomain.

## Publish a release

Copy an ISO into `/opt/iso` on the VPS:

```bash
sudo cp Futura-Linux-x86_64.iso /opt/iso/
```

The page discovers it automatically on the next refresh. No container rebuild or restart is required. The ISO directory is mounted read-only in the container, and Apache serves downloads directly rather than loading the ISO into PHP memory.

## Notes

- The host path `/opt/iso` is the source of truth for release files.
- Only regular `.iso` files directly inside `/opt/iso` are listed. Subdirectories and symlinks are ignored.
- Directory indexing is disabled. The PHP page lists files, while Apache serves downloads as attachments.
- The container joins the shared `proxy` network only; it does not publish host ports.
- The theme preference is shared with the main website through a cookie scoped to `.futuralinux.org`.
