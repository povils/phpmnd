#!/usr/bin/env bash

if ! command -v box 2>&1 > /dev/null; then
    echo "It doesn't look like you have the box CLI installed for creating PHARs..."
    read -p "Would you like to install it now? (y/N) " YES_NO
    if [ "$YES_NO" != "y" ]; then
        echo "Please install https://github.com/box-project/box to continue."
        exit 1
    fi

    wget -O box.phar "https://github.com/box-project/box/releases/latest/download/box.phar"
    wget -O box.phar.asc "https://github.com/box-project/box/releases/latest/download/box.phar.asc"

    # Check that the signature matches
    gpg --keyserver hkps://keys.openpgp.org --recv-keys 41539BBD4020945DB378F98B2DF45277AEF09A2F
    gpg --verify box.phar.asc box.phar
    
    rm box.phar.asc
    mv box.phar box
    chmod +x box

    echo sudo mv box /usr/local/bin
    sudo mv box /usr/local/bin
fi

# Run composer install to insure that everything is good.
echo "Running a fresh \`composer install --no-dev\` to insure the latest dependencies are installed..." 
echo "Removing vendor/ and composer.lock ..."
rm -v composer.lock
rm -rf vendor
git checkout composer.json
mv composer.json composer.release.json
cp composer.build.json composer.json
composer install --no-dev


# Build the phar
echo "Building phpmnd.phar..."
box compile

mv composer.release.json composer.json
rm -rf vendor
