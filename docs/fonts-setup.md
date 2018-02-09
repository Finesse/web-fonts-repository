## How to setup webfonts list

All settings go to the file `config/settings-local.php`.
If you don't have it, copy it from the file `config/settings-local.php.example`.

Simple example:

```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'styles' => [
                '300' => 'OpenSans/opensans-light.*',
                '300i' => 'OpenSans/opensans-light-italic.*',
                '400' => 'OpenSans/opensans-regular.*',
                '400i' => 'OpenSans/opensans-regular-italic.*',
            ]
        ],
        'Roboto' => [
            'styles' => [
                '300' => 'Roboto/roboto-light.*',
                '400' => 'Roboto/roboto-regular.*',
                '500' => 'Roboto/roboto-medium.*',
                '700' => 'Roboto/roboto-bold.*',
            ]
        ]
    ]
];
```

The `fonts` array keys are the font families names. The `styles` arrays keys are the styles names.
The numbers in the style names are the font weights, `i` stands for italic.

The font file paths are given relative to the `public/fonts` directory. 
The file paths are the [glob](https://en.wikipedia.org/wiki/Glob_(programming)) search patterns.
It means that the repository should consider all files matching the pattern as font files.

You can specify font files explicitly. It is faster on runtime because the repository doesn't have to scan the directories.

```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'styles' => [
                '300' => [
                    'files' => [
                        'OpenSans/opensans-light.woff2',
                        'OpenSans/opensans-light.woff',
                        'OpenSans/opensans-light.ttf'
                    ]
                ]
            ]
        ]
    ]
];
```
 
You can specify a common directory for font families and font styles:
 
```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'directory' => 'OpenSans',
            'styles' => [
                '300' => [
                    'directory' => 'light',
                    'files' => 'font.*'
                ]
                '400' => [
                    'directory' => 'regular',
                    'files' => 'font.*'
                ]
            ]
        ]
    ]
];
```

You can change the font name of the `local` source of the `src` value of a `@font-face` CSS rule by using the `name`
parameter of a style:

```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'styles' => [
                '500' => [
                    'name' => 'DemiBold',	// The `local` names are `Open Sans Demibold` and `OpenSans-DemiBold`
                    'files' => 'opensans_demibold.*'
                ]
            ]
        ]
    ]
];
```

You can forbid using a `local` source by using the `forbidLocal` parameter:

```php
return [
    // ...
    'fonts' => [
        'Open Sans' => [
            'forbidLocal' => true,
            'styles' => [
                '300' => 'OpenSans/opensans-light.*',
                '300i' => 'OpenSans/opensans-light-italic.*',
                '400' => [
                    'forbidLocal' => false,	// Overrides the family rule
                    'files' => 'OpenSans/opensans-regular.*'
                ],
                '400i' => 'OpenSans/opensans-regular-italic.*',
            ]
        ],
        'Roboto' => [
            'styles' => [
                '300' => [
                    'forbidLocal' => true,
                    'files' => 'Roboto/roboto-light.*'
                ],
                '400' => 'Roboto/roboto-regular.*',
                '500' => 'Roboto/roboto-medium.*',
                '700' => 'Roboto/roboto-bold.*',
            ]
        ]
    ]
];
```
