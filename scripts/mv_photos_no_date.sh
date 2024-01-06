#/bin/bash
find . -type f -iname "*.jpg" -exec sh -c 'jhead "$1" | grep Date/Time  > /dev/null || mv "$1" no_date_tags' _ {} \;
