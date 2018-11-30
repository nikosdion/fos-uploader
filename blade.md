# Setting up phpStorm for AWF's Blade templates

phpStorm comes with a default mapping for Blade templates suitable for Laravel. When working with an AWF project you will need to tell phpStorm how our Blade templates work to let it provide correct type hints. Go to Preferences, Languages & Frameworks, PHP, Blade and click on the Directives tab. You need to set up the following directives:

| Name | Has Parameter | Prefix | Suffix
| ---- |:-------------:| ------ | ------
| append | No | | |
| css | Yes | <?php echo \Awf\Utils\Template::addCss( | ); ?> |
| each | Yes | <?php echo $this->renderEach( | ); ?> |
| else | No | | |
| elseif | Yes | <?php elseif( | ); ?> |
| empty | Yes | <?php if(empty( | )): ?> |
| endfor | No | | |
| endforeach | No | | |
| endforelse | No | | |
| endif | No | | |
| endpush | No | | |
| endrepeatable | No | | |
| endsection | No | | |
| endunless | No | | |
| endwhile | No | | |
| extends | Yes | <?php echo $this->loadAnytemplate( | ); ?> |
| for | Yes | <?php for( | ); ?>> |
| foreach | Yes | <?php foreach( | ); ?> |
| forelse | Yes | <?php foreach( | ); ?> |
| html | Yes | <?php echo \Awf\Html\Html::_( | ); ?> |
| if | Yes | <?php foreach( | ); ?> |
| include | Yes | <?php echo $this->loadAnytemplate( | ); ?> |
| inlineCss | Yes | <?php $this->container->application->getDocument()->addStyleDeclaration( | ); ?> |
| inlineJs | Yes | <?php $this->container->application->getDocument()->addScriptDeclaration( | ); ?> |
| jhtml | Yes | <?php echo \Awf\Html\Html::_( | ); ?> |
| js | Yes | <?php echo \Awf\Utils\Template::addJs( | ); ?> |
| lang | Yes | <?php echo \Awf\Text\Text::_( | ); ?> |
| media | Yes | <?php echo \Awf\Utils\Template::parsePath( | ); ?> |
| overwrite | No | | |
| plural | Yes | <?php echo \Awf\Text\Text::plural( | ); ?> |
| push | Yes | <?php \$this->startSection( | ); ?> |
| repeatable | Yes | <?php // | ?> |
| route | Yes | <?php echo \$this->container->router->route( | ); ?> |
| section | Yes | <?php $this->startSection( | ); ?> |
| show | No | | |
| sprintf | Yes | <?php echo \Awf\Text\Text::sprintf( | ); ?> |
| stack | Yes | <?php echo \$this->yieldContent( | ); ?> |
| stop | No | | |
| token | Yes | <?php $this->container->session->getCsrfToken()->getValue( | ); ?> |
| unless | Yes | <?php if ( ! ( | )): ?> |
| while | Yes | <?php while ( | ): ?> |
| yield | Yes | <?php $this->yieldContent( | ); ?> |
| yieldRepeatable | Yes | <?php _fof_blade_repeatable_DUMMY( | ); ?> |

Please note that you should not keep any of the other default directives. They do not apply to AWF.