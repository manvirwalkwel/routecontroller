<?php
namespace Manvir\RouteC;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class YourClass extends ServiceProvider {
  protected $namespace = 'App\Http\Controllers';
  public function boot()
  {
      Route::macro('controller', function ($url, $controller) {
          $controller = 'App\Http\Controllers\\' . $controller;
          $reflection = new \ReflectionClass($controller);
          $methods = collect($reflection->getMethods())
              ->where('class', $controller);
          foreach ($methods as $method) {
              $name = $method->name;
              $snake_case = snake_case($name);
              $temp = explode('_', $snake_case);
              $request = $temp[0];
              unset($temp[0]);
              $main = implode('-', $temp);
              $params = [];
              foreach ($method->getParameters() as $key => $parameter) {
                  $optional = $parameter->isOptional() ? '?' : null;
                  $params[] = "{param$key$optional}";
              }
              $params = implode('/', $params);
              $route = $url . '/' . $main . '/' . $params;
              $action = "\\$controller@$name";
              Route::$request($route, $action);
          }
      });
  }
}
