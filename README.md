Magento Classic Theme (F002)
============================

http://templates-master.com/free-classic-magento-theme.html

## Installation
#### Deploy module
Unpack archive to the Magento root directory, disable compilation and clear cache.

Or, if you are using [modman](https://github.com/colinmollenhour/modman) you can
deploy the module with command line:

```bash
cd /path/to/magento
modman clone git@github.com:tmhub/core.git
modman clone git@github.com:tmhub/catalog-configurable-swatches.git
modman clone git@github.com:tmhub/templatef002.git
```

#### Run installer
1. Logout from Magento backend and login again.
2. Navigate to `Templates Master > Modules` menu.
3. Find the `TM_Templatef002` theme in the list.
4. Click on `Manage` link.
5. Select the store(s) where you wish to install theme.
6. Press `Run` button.

## Configuration
#### Change color theme
1. Navigate to `System > Configuration > Design`.
2. Make sure that `Package > Current Package Name` is set to `f002`.
3. Set the color theme with `Themes > Skin (Images / CSS)` option:
    - default
    - green
    - grey
    - orange
    - pink
    - red
    - sea_green
    - silver
    - violet
    - yellow
