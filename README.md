yii-nested-url-rule
===================

Custom Url Routing Rule for the Yii PHP Framework that handles an infinitely nested parameter.

NestedUrlRule allows for the following type urls in the [Yii Framework](http://www.yiiframework.com/).
With the most likely use by infinitely nesting categories. 
<pre>/route/param1/value1/category-1/category-2/.../category-n?page=2</pre>


Features
-------------------
* Friendly routes.
* Friendly parameters before the nesting.
* Custom nesting string separators.
* Additional parameters placed after "?".

Examples
===================
<pre>
/friendlyBase/category/subcategory/.../nth-subcategory
into
/controller/action $_GET[$nested] = "category/subcategory/.../nth-subcategory"
</pre>
<pre>
/controller/action/key1/value1/nest1/nest2/.../nestN
when key1 is in $preNestedParameters
/controller/action $_GET[key1] = value1, $_GET[$nested] = "nest1/nest2/.../nestN"
</pre>
<pre>
$this->createUrl('controller/action', array('key1' => 'value1', $nested => "nest1/nest2/.../nestN"));
when key1 is NOT in $preNestedParameters
/controller/action/nest1/nest2/.../nestN?key1=value1
</pre>

Installation
===================
Drop NestedUrlRule.php into the application.components directory.

In your config file:

```php
'components' => array(
  ...
  'urlManager' => array(
    ...
    'rules' => array(
      ...
      array(
        'class' => 'application.components.NestedUrlRule',
        'route' => '',
        [Other Custom Parameters]
      ),
```

Parameters
===================
Required
-------------------
* route - This is the base route to look for.
* nested - The name of the parameter that will hold the nested data

Optional
-------------------
* routeFriendly - This will convert your ugly route into a beautiful one.
* preNestedParameters - All parameters for the action that come before the nested parameter must be listed in an array here.
* nestedSeparator - (Default: "/") The separator that will be the separator for the parameter value passed to the action.
