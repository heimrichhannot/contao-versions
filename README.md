# Versions

This bundle enhances the contao version entity with persistent tables options and helper methods.

## Features

- Add fromTable names to `huh_versions.persistent_tables` that should persist within tl_version forever or a custom time frame.
- Use the VersionModel to find tl_version models

## Register persistent tables

To make your entities from a given fromTable persist add the table name to the `huh_versions.persistent_tables` within your project configuration (typical `config/config.yml`).
 
```yaml
# config/config.yml
huh_versions:
  persistent_tables:
    - tl_my_custom_entity
    - tl_keep_forever
```

## Configuration reference

```yaml
# Default configuration for extension with alias: "huh_versions"
huh_versions:

  # Set table names that should be persist within tl_versions.
  persistent_tables:

    # Examples:
    - tl_content
    - tl_my_custom_entity

  # Set the time period persistent table versions should be kept in version table. Set to 0 for forever.
  persistent_version_period: ~ # Example: 7776000
```