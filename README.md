# Versions

This bundle enhances the contao version entity with persistent tables options and helper methods.

## Features

- Add fromTable names to `$GLOBALS['PERSISTENT_VERSION_TABLES']` that should persist within tl_version forever
- Use the VersionModel to find tl_version models

## Register persistent tables

To make your entities from a given fromTable persist add the table name to the `$GLOBALS['PERSISTENT_VERSION_TABLES']` within your module config.php or project initconfig.php.
 
```
//config.php
$GLOBALS['PERSISTENT_VERSION_TABLES'][] = 'tl_news';

```