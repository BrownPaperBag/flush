Flush
=====

Library to flush data friendly. To use in long-pooling and zombie requests.

Usage
-----

Usage in long-pooling requests:
```php
<?php

$flush = new Flush('application/json');

$flush->prepare();

do{

    if(!$flush->signal()){
    
        break;
    
    }
    else{
    
        // Your commands here or conditions to break in some point...
    
    }

}
while(sleep(1) || true);

$flush->json(array(

    'command' => 'location.reload();'

));
```

Usage in zombie requests:
```php
<?php

// You could use new Flush('application/json', true) instead of commands bellow.
// Just showing another way...

$flush = new Flush();

$flush->contentType = 'application/json'; // Optional
$flush->enableLimbo = true; // Required for zombie requests

// Doing whatever task that user must to wait...

$flush->json(array(

    'message' => 'An awesome return message here...'

));

// From this moment you can execute any task in background like: render a PDF, send emails or fire some slow script.
// Just avoid to create an infinite loop :)
```

License
----

MIT
