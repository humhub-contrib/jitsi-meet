Changelog
=========

1.2.1 (October 13, 2025)
------------------------
- Enh #45: Replace default meet.jit.si server domain (which has issues for the microphone and camera with the mobile app), with a list of popular ones

1.2.0 (August 27, 2025)
-----------------------
- Fix #40: Update module resources path
- Enh #41: Use PHP CS Fixer
- Enh #43: Migration to Bootstrap 5 for HumHub 1.18

1.1.9 (April 16, 2024)
----------------------
- Fix #38: Fix missing room prefix in JWT token

1.1.8 (January 30, 2024)
-------------------------
- Fix #37: Don't restrict an opening of a created room

1.1.7 (January 10, 2024)
-------------------------
- Fix #35: New group permission "Can access Jitsi Meet from main navigation"


1.1.6 (November 9, 2023)
-------------------------
- Fix #33: Fix visibility of the method `Controller::getAccessRules()`
- Fix #34: Fix JWT encoding function


1.1.5 (May 6, 2022)
-------------------
- Fix #30: Fix rooms loading


1.1.4 (April 18, 2022)
----------------------
- Fix #27: Fix assets on updating of disabled module


1.1.3 (April 14, 2022)
----------------------
- Enh: Disable Guest Access - Do not show menu item to users which are not logged in.


1.1.2 (September 12, 2020)
--------------------------
- Fix #10: Workaround for broken "open in app" link on Android devices
- Fix #11: Authentication failed with Jitsi Password


1.1.0 (April 05, 2020)
----------------------
- Enh #2: Add JWT authentication (thanks to @edmw)
- Fix #4: Added nonce to inline scripts


1.0.0 (March 22, 2020)
----------------------
Initial release
