<?php
/**
 * Custom Url Routing that handles an infinitely nested parameter while leaving others alone.
 *
 * @author Wil Wade <wil@supplyhog.com>
 * @copyright (c) 2012, Wil Wade
 * @license MIT
 * @package
 * @category Components.Yii
 * @version 1.0
 *
 * This custom url rule allows an infinite nesting of a parameter.
 *
 * Features:
 *  -Friendly routes.
 *  -Friendly parameters before the nesting.
 *  -Custom nesting string separators.
 *  -Additional parameters placed after "?".
 *
 * Example:
 *  /friendlyBase/category/subcategory/.../nth-subcategory
 *  into
 *  /controller/action $_GET[$nested] = "category/subcategory/.../nth-subcategory"
 *
 *  /controller/action/key1/value1/nest1/nest2/.../nestN
 *  when key1 is in $preNestedParameters
 *  /controller/action $_GET[key1] = value1, $_GET[$nested] = "nest1/nest2/.../nestN"
 *
 *	$this->createUrl('controller/action', array('key1' => 'value1', $nested => "nest1/nest2/.../nestN"));
 *  when key1 is NOT in $preNestedParameters
 *  /controller/action/nest1/nest2/.../nestN?key1=value1
 *
 */
class NestedUrlRule extends CBaseUrlRule{

	/**
	 * The route to look for.
	 * REQUIRED to be set.
	 * @var string
	 */
	public $route = '';

	/**
	 * The parameter that is nested.
	 * REQUIRED to be set.
	 * All others are required to be after the "?".
	 * @var string
	 */
	public $nested = '';

	/**
	 * A friendly name to use instead of the route.
	 * @var string
	 */
	public $routeFriendly = '';

	/**
	 * Array of parameters that should go before the nested.
	 * Note that these MUST always exist.
	 * @var array
	 */
	public $preNestedParameters = array();

	/**
	 * The string that separates the different nested levels
	 * Defaults to "/"
	 * @var string
	 */
	public $nestedSeparator = '/';

	public function createUrl($manager, $route, $params, $ampersand){
		if($route === $this->route && isset($params[$this->nested])){
			$preNest = $this->routeFriendly === '' ? array($this->route) : array($this->routeFriendly);
			foreach($this->preNestedParameters as $key){
				if(!isset($params[$key])){
					return false;
				}
				$preNest[] = $key;
				$preNest[] = $params[$key];
				unset($params[$key]);
			}
			$nest = explode($this->nestedSeparator, $params[$this->nested]);
			$preAndNest = array_merge($preNest, $nest);
			unset($params[$this->nested]);

			$additional = $manager->createPathInfo($params, '=', $ampersand);
			$additional = $additional === '' ? '' : '?' . $additional;
			return implode('/', $preAndNest) . $additional;
		}
		return false;
	}

	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo){
		$route = $this->routeFriendly === '' ? $this->route : $this->routeFriendly;
		if(substr($pathInfo, 0, strlen($route) + 1) === $route.'/'){
			$path = preg_replace("/{$route}\//", '', $pathInfo, 1);
			$params = explode('/', $path);
			//remove friendly
			foreach($this->preNestedParameters as $pre){
				if(array_shift($params) !== $pre){
					//Missing a required preNest
					return false;
				}
				$_GET[$pre] = $params[0];
				array_shift($params);
			}
			$_GET[$this->nested] = implode($this->nestedSeparator, $params);
			return $this->route;
		}
		return false;  // this rule does not apply
	}

}
?>
