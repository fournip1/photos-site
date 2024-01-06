# photos-site
An html-javascript-php site which renders dynamically a gallery of photos stored in a given directory. No database.
## how it works?
It scans dynamically the photos stored in the gallery folder and makes thumbnails with a predefined format. The photos are displayed in a gallery and ordered from the newest to the oldest depending on the Date/Time field in the exif file header.
I used and improved a ![code box](https://code-boxx.com/) tutorial.
## additional scripts
One shell script checks whether a picture does not have a Date/Time and moves it to a predefined folder.
The other python script computes a distance between all the possible pairs of photos in order to detect doublons. This script creates a csv file.
