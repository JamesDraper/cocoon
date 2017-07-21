# Cocoon

A simple php view renderer.

# Requirements

* Composer
* PHP 7.1 or greater

# Installation

To install Cocoon via composer, run the following command:

    composer require james-draper/cocoon

# Getting started

Here is a simple example of how to render a view, for a more detailed explanation consult the [documentation](docs/contents.md).

    <?php

    $factory = new \Cocoon\Factory([__DIR__ . '/views'])
    return $factory
        ->create('path/to/template')
        ->setVar('some_var', 123)
        ->setVarRaw('some_other_var', 456)
        ->render();

The above code snippet first creates a factory, uses the factory to creates a view, assigns variables to that view, and then renders the view.
Below is what the template file might contain.

    <p>var1: <?php echo $this->some_var; ?></p>
    <p>var2: <?php echo $this->some_other_var; ?></p>