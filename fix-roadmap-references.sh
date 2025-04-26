#!/bin/bash

# Define colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Fixing roadmap references in scripts...${NC}"

# Update VERSION-1-SETUP.sh
if [ -f "VERSION-1-SETUP.sh" ]; then
    sed -i 's/VERSION-1-ROADMAP.md/VERSION-1-ROADMAP.md/g' VERSION-1-SETUP.sh
    echo -e "${GREEN}Updated VERSION-1-SETUP.sh${NC}"
fi

# Update VERSION-1-1-SETUP.sh
if [ -f "VERSION-1-1-SETUP.sh" ]; then
    sed -i 's/VERSION-1-ROADMAP.md/VERSION-1-ROADMAP.md/g' VERSION-1-1-SETUP.sh
    echo -e "${GREEN}Updated VERSION-1-1-SETUP.sh${NC}"
fi

# Update README.md
if [ -f "README.md" ]; then
    sed -i 's/VERSION-1-ROADMAP.md/VERSION-1-ROADMAP.md/g' README.md
    echo -e "${GREEN}Updated README.md${NC}"
fi

echo -e "${GREEN}All roadmap references have been fixed!${NC}"
echo -e "${YELLOW}Please make sure to run this script with proper permissions.${NC}"
