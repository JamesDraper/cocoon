# The Factory

The factory is the starting point for using Cocoon. It is used to define a list of directories that contain the view template files.
It is also used to define a template file extension, which by default is `tpl.php`.

## Setting the directories

There are 3 ways to set the view directories. The first is to pass them into the constructor as a string array:

    $factory = new \Cocoon\Factory([
        __DIR__ . '/path1/',
        __DIR__ . '/path2/'
    ]);

The second is to call `setDirs`, passing them in as a string array:

    $factory = new \Cocoon\Factory();
    $factory->setDirs([
        __DIR__ . '/path1/',
        __DIR__ . '/path2/'
    ]);

Finally, they can be passed individually using `addDir`:

    $factory = new \Cocoon\Factory();
    $factory->addDir(__DIR__ . '/path1/');
    $factory->addDir(__DIR__ . '/path2/');

## Setting the extension

To change the template file extension to something other than `tpl.php`, call `setExtension`:

    $factory = new \Cocoon\Factory();
    $factory->setExtension('phtml');

## Creating a view object

To create a view object, call the `createView` method passing it the view path,
The path must be relative to one of the view directories:

    $factory = new \Cocoon\Factory();
    $factory->createView('path/to/view');

The full path to the view file is determined by the view directory, the path specified by createView, and the extension.
The first view directory that produces a match is the one that will be used, so if several directories are registered in the factory
then the earlier ones can be used to override the later ones.

[back to contents](contents.md)
