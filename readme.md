Groovy Logs for WooCommerce
===========================

> 💡 This project is an early stage exploration of logging in WooCommerce. Right now, it definitely is **not ready for production use** (and, at this stage, lacks a lot of what most people would need to use it, even in a local dev environment). It's primarily an ongoing experiment and exploration, which may mature into something usable, but also may not.

- What it does "today":
  - Adds a UI to select the logging engine (makes choice of logging engine accessible to those who cannot update their `wp-config.php` file directly).
  - Adds a SQLite logging implementation.
- What it lacks/what still needs to be implemented:
  - A means of viewing logs when SQLite logging is enabled (one would need to use a SQlite GUI or similar to view the logs).
  - Clean-up of expired logs.
- Things that may be explored in future:
  - Additional logging implementations, not just SQLite.
- Some discoveries made so far:
  - Replacing the existing page used to view WooCommerce logs is cumbersome (as of WooCommerce 7.8.0), partly because of hard-coded assumptions (that only a file or database logger require a UI) and partly because of the use of anonymous objects which make it hard to unhook various things.

