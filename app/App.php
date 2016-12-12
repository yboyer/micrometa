<?php

use Silex\Application;

class App extends Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
    use Application\FormTrait;
}
