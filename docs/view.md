# The View

The view is a wrapper around a template file. It is used to set the template variables, and an optional helper object.

## Adding template variables

There are 2 methods for adding a template variable: `setVar` and `setVarRaw`.
The difference is that setVar automatically escapes html, setVarRaw does not.
Each takes 2 arguments: the template variable name, and the template variable value:

    $view
        ->setVar('name1', 'value1')
        ->setVarRaw('name2', 'value2');

There are some rules to naming template variables, they cannot contain any tabs or spaces, and cannot be the word "content",
which is a reserved word. See wrapping views further down for more details. Either of these cases will throw an exception when they are set.

## Referencing template variables in the template

Any variables registered are accessible in the template using `$this`:

    <?php echo $this->name1; ?>
    <p>Lorem ipsum</p>
    <?php echo $this->name2; ?>

## Setting a helper object

Helper objects can be registered to views to seperate more complex view logic from the templates themselves.
Of course there is nothing stopping that logic to be in the views, this is just an alternative if it would make the view logic more elegant.
Helper objects can be set by using the `setHelper` method:

    $view->setHelper($obj);

## Accessing helper object methods in the template

Helper object methods can be accessed from within the template by calling them using `$this`, similar to referencing template variables.
The only difference is that template variables are always properties, and helper object methods are always methods:

    <?php echo $this->someMethodOnHelperObject(); ?>

## Wrapping a template

A view can be used to wrap another view, which in turn can also be used to wrap another view.
There is no limit (at least in the source code) as to how many views can be wrapped in this way.
To wrap a view inside another view call `wrap`:

    $view1->wrap($view2);

If one view wraps another, then the inner view is assigned to the template variable `content` in the outer view
(which is why content is a reserved word when naming template variables).

[back to contents](contents.md)
