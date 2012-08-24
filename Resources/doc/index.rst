# JmEmailBundle

The JmEmailBundle add support for multi template emails in Symfony2. It provide a way to store all email applications in the DB, and easily test it.


# Installation

# Step1 : Download JmEmailBundle using composer
Add JmEmailBundle in your composer.json :

{
    "require": {
        "jeremymarc/jmemail-bundle": "*"
    }
}

Now tell composer to download the bundle by running the command :
php composer.phar update jeremymarc/jmemail-bundle

Composer will install the bundle to your project's vendor/jeremymarc directory.

Step 2: Enable the bundle

Enable the bundle in the kernel:

<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\UserBundle\FOSUserBundle(),
    );
}
