# adobe-cloud-ece-tools-extend

To execute extended build:generate scenario run next command from your Magento Cloud project:
```
php vendor/bin/ece-tools run vendor/magento/ece-tools/scenario/build/generate.xml vendor/exequiellares/adobe-cloud-ece-tools-extend/scenario/build/extend-build-generate.xml
```

You can extend base scenario with different custom scenarios.
```
php vendor/bin/ece-tools run "path/to/base/scenarion" "path/to/extended/scenarion" "path/to/extended/scenarion2" "path/to/extended/scenarion3"
```
Keep in mind that scenarios will be merged in provided order. 