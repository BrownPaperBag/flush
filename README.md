# Presentation

Library to flush data friendly. To use in long-polling and zombie requests. Distributed as [composer](http://getcomposer.org/) package.

## Instalation

```bash
composer require brownpaperbag/flush
```

## Usage

This library was built to work in three scenarios basically:

* Long-polling requests;
* Long polling requests with socket style;
* Zombie requests;

### Usage in long-polling requests
```php
<?php

use BrownPaperBag\Flush;

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

### Usage in long-polling requests with socket style

```php
<?php

use BrownPaperBag\Flush;

$flush = new Flush('application/json');

$flush->prepare();

do{

    $message = rand(1, 10);

    if(!$flush->signal()){

        break;

    }
    else if($message % 2){ // Any condition you want...

        $flush->json(array(

            'success' => true,

            'message' => $message

        ));

    }

}
while(sleep(1) || true);
```

That's an example how to receive the response on client side:

```javascript
var request = new XMLHttpRequest();

request.onreadystatechange = function(){

    if(this.status == 200){

        switch(this.readyState){

            case this.LOADING:

                var data = this.responseText,
                    length = data.length;

                data = data.slice(this.lastLength - length);
                data = $.trim(data);
                
                this.lastLength = length;

                if(data.length){

                    data = $.parseJSON(data);
                    
                    // Whatever you want to do with your new object.
                    // You can use it to implement push notifications for example...
                    
                    console.log(data);

                }

                break;

        }

    }

};

request.open('GET', '/api/notifications', true);
request.send();
```

### Usage in zombie requests
```php
<?php

use BrownPaperBag\Flush;

// You could use new Flush('application/json', true) instead of commands bellow.
// Just showing another way...

$flush = new Flush();

$flush->setContentType('application/json'); // Optional
$flush->setLimboEnabled(true); // Required for zombie requests

// Doing whatever task that user must to wait...

$flush->json(array( // If limbo is enabled Flush will prepare() automatically.

    'message' => 'An awesome return message here...'

)); // To prevent prepare(), for any reason, just pass an explicit false as second argument in json/data().

// From this moment you can execute any task in background like: render a PDF, send emails or fire some slow script.
// Just avoid to create an infinite loop :)
```

# License

MIT
