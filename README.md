# AzuraForms

AzuraForms is a lightweight, self-contained forms engine, analogous to Zend Forms or Symfony Forms (but without framework dependencies), that allows you to build forms as PHP arrays, then automate both the rendering of the form on the client-side and the server-side processing and validation of form input.

Some features of AzuraForms include:

- Automatic CSRF session tokens generated each time a form is rendered and checked upon submission.
- Built-in escaping of user input when displayed in forms
- Support for fieldsets
- The ability to specify a sub-array for each field, from which data will be populated and input will be stored
- Support for file uploads and verification of uploaded file type
- Automatic detection of the necessary "enctype" parameter for the form, based on whether file elements are present

By default, AzuraForms' HTML output uses the standard Bootstrap 3 form template style.

The original purpose of this project was to provide a lightweight forms engine to power the [AzuraCast](https://github.com/AzuraCast/AzuraCast) application. You can find good examples of even [very complex forms](https://github.com/AzuraCast/AzuraCast/blob/master/app/config/forms/station.conf.php) being rendered with AzuraForms in that application.

### Installing

AzuraForms is a Composer package that you can include in your project by running:

```bash
composer require azuracast/azuraforms
```

### Quick Start

The configuration array AzuraForms uses looks like this:

```php
<?php
return [
    'method' => 'post',
    'elements' => [

        'username' => [
            'email',
            [
                'label' => 'E-mail Address',
                'class' => 'half-width',
                'spellcheck' => 'false',
                'required' => true,
            ]
        ],

        'password' => [
            'password',
            [
                'label' => 'Password',
                'class' => 'half-width',
                'required' => true,
            ]
        ],

        'submit' => [
            'submit',
            [
                'type' => 'submit',
                'label' => 'Log in',
                'class' => 'btn btn-lg btn-primary',
            ]
        ],
    ],
];
```

AzuraForms is built to support the PSR-7 `ServerRequestInterface` natively.

If you don't want to use a framework that supports PSR-7, you can convert from native PHP (i.e. `$_POST`) using one of
many [PSR-17 factory libraries](https://packagist.org/providers/psr/http-factory-implementation), many of which
implement a `fromGlobals` method to create a `ServerRequest` from global variables.

Your controller code should look something like this:

```php
<?php
/** @var Psr\Http\Message\ServerRequestInterface $request */

$form_config = include('form_config.php');
$defaults = [
    'email' => 'foo@bar.com',
];

$form = new \AzuraForms\Form($form_config, $defaults);

if ($form->isValid($request)) {
    $data = $form->getValues();

    // Process your submission here...
}

echo $form->render();
?>
```

### Configuration Format

The configuration format used by AzuraForms is meant to be flexible, and to be simple for uncomplicated forms, while flexible enough to allow more powerful forms. It loosely resembles the flatfile configuration style used by Zend Forms 1.0 many years ago.

```php
<?php
return [
    // Use 'groups' to denote fieldsets
    'groups' => [

        'fieldset_name' => [
            'legend' => 'My Fieldset',
            'description' => 'The description of the fieldset.',
            'elements' => [

                // The key is the name of the element
                'field_foo' => [
                    'text', // The first item is the field type
                    [
                        // The second item contains configuration options and attributes
                        'label' => 'Foo',
                        'required' => true,

                        // Any attributes that aren't configuration options will automatically
                        // be applied to the HTML element when rendered.
                        'class' => 'text-danger',
                        'autocomplete' => 'off',
                    ],
                ],

            ],
        ],

    ],

    // You can also list elements directly, without any fieldset
    'elements' => [

        'field_bar' => [
            'field_type',
            [
                'label' => 'Bar',
                'required' => true,
            ],
        ],

    ]
];
```

### Field Reference

#### Text-style Input Fields

- `text`: `<input type="text">`
- `email`: `<input type="email">`, with built-in e-mail address validation
- `url`: `<input type="url">`, with built-in URL validation
- `date`: `<input type="date">`
- `time`: `<input type="time">`
- `number`: `<input type="number">`
- `password`: `<input type="password">`
- `textarea`: `<textarea>`

#### Single-Option Fields

- `radio`: Radio buttons with individual labels
- `select`: A `<select>` dropdown field accepting a single response

#### Multiple-Option Fields

- `checkbox`: One or more checkboxes with individual labels, returned as an array
- `multiselect`: A `<select>` field accepting multiple responses and returning them as an array

#### Button Fields

- `button`: `<input type="button">`
- `submit`: `<input type="submit">`

#### Other Fields

- `markup`: Support for embedding raw HTML into the middle of a form
- `recaptcha`: Support for ReCAPTCHA integration
- `file`: `<input type="file">`
- `csrf`: CSRF protection token, automatically added to forms by default.

### License

AzuraForms is licensed under the MIT license.

The forms engine code has been completely overhauled since it was forked, but it originally was built on top of [Nibble Forms 2](https://github.com/LRotherfield/Nibble-Forms), which is itself a fork of [Nibble Forms](http://nibble-development.com/nibble-forms-php-form-class).
