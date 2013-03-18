Getting started with RjEmailBundle
==================================

The symfony2 RjEmailBundle provide an easy way to manage application email templates.


## Installation

### Step 1: Download RjEmailBundle using composer

Add JmABBundle in your composer.json:

```js
{
    "require": {
        "rj/email-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update rj/email-bundle
```

Composer will install the bundle to your project's `vendor/rj` directory.


### Step 2: Enable the bundle
```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Rj\EmailBundle\RjEmailBundle(),
    );
}
```

### Step 3: Configuration

Configure the bundle using the dic configuration file :
```php
rj_email:
    locales: [en, fr, de, es]
```

locales:
Array of available locales for your application.
Example :
locales: [en, fr, de, es]

default_locale:
By default, it's using the same default_locale as the application. If
you want to override by using your default locale, you can with this
parameter.


### Step 4: Working with FOSUserBundle (Optional)
If you are using FOSUserBundle, we can make it load email templates
directly from Email Templates using a custom Mailer :

```php
fos_user:
    service:
        mailer: rj_email.mailer.twig_swift
    from_email:
        address:        send@email.com
        sender_name:    Sender
    registration:
        confirmation:
            enabled:    true
            template: 'registration'
    resetting:
        email:
            template: 'resetting'
```

Our custom mailer (rj_email.mailer.twig_swift) is using the same
variables you used to configure fos_user. Just specify the template name
for the registration confirmation email (by default it's confirmation)
and for resetting email (by default it's resetting).

PS: The custom mailer will only works if you are using Swift to send emails.


By running the command rj:email:import-fosuserbundle, it will automatically imports
FOSUserBundle emails from FOSUserBUndle translation files for all
locales you have specified in rj_email.locales parameters.
```php
./app/console rj:email:import-fosuserbundle
```

You can use Twig to edit emails.
If you want to add in the mail template a link to see it directly online,
just add the {{email_url}} twig variable in the email template.


PS : If you are using Sonata, a new menu will appear from the dashboard
called Emails.


### Sending an email from a Controller
To retrieve an EmailTemplate from a controller you can do :
```php
$this->get('rj_email.email_template_manager')->renderEmail($templateName, $locale = null, $vars = array(), Message $message = null);
```

It will returns an array containing the body and subject according to the current locale (if you didn't specify a value for $locale).


If you want to get a \Swift_Message you can do :
```php
$manager = $this->get('rj_email.email_template_manager');
\Rj\EmailBundle\Swift\Message::fromArray($manager->renderEmail($templateName));
```
You can send the message as you used to do with Swift.
