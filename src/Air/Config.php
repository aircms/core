<?php

declare(strict_types=1);

namespace Air;

use Air\Crud\Nav;
use Air\Type\RichContent;

class Config
{
  public static function defaults(
    ?string $title = 'AirCms',
    ?array  $settings = null,
    ?array  $extensions = null,
    ?array  $nav = null,
    ?array  $routes = null,
    ?bool   $reportErrors = false,
  ): array
  {
    $appEntryPoint = realpath(dirname($_SERVER['SCRIPT_FILENAME'], 2));
    $routes = $routes ?: require_once $appEntryPoint . '/config/routes.php';
    $nav = $nav ?: require_once $appEntryPoint . '/config/nav.php';

    return array_replace_recursive(
      [
        'air' => [
          'modules' => '\\App\\Module',
          'exception' => $reportErrors,
          'phpIni' => [
            'display_errors' => $reportErrors ? '1' : '0',
          ],
          'startup' => [
            'error_reporting' => $reportErrors ? E_ALL : 0,
            'date_default_timezone_set' => 'Europe/Kyiv',
          ],
          'loader' => [
            'namespace' => 'App',
            'path' => $appEntryPoint . '/app',
          ],
          'db' => [
            'driver' => 'mongodb',
            'servers' => [[
              'host' => 'localhost',
              'port' => 27017,
            ]],
            'db' => getenv('AIR_DB_DB')
          ],
          'storage' => [
            'url' => getenv('AIR_FS_URL'),
            'key' => getenv('AIR_FS_KEY'),
          ],
          'logs' => [
            'enabled' => true,
            'exception' => true,
          ],
          'crypt' => [
            'secret' => getenv('AIR_CRYPT_SECRET'),
          ],
          'fontsUi' => 'fontsUi',
          'admin' => [
            'title' => $title,
            'logo' => '/assets/ui/images/favicon.png',
            'favicon' => '/assets/ui/images/favicon.png',
            'notAllowed' => '_notAllowed',
            'settings' => $settings ?: Nav::getAllSettings(),
            'rich-content' => RichContent::getAllTypes(),
            'auth' => [
              'route' => '_auth',
              'source' => 'database',
              'root' => [
                'login' => getenv('AIR_ADMIN_AUTH_ROOT_LOGIN'),
                'password' => getenv('AIR_ADMIN_AUTH_ROOT_PASSWORD'),
              ],
            ],
            'tiny' => getenv('AIR_ADMIN_TINY_KEY'),
            'menu' => $nav,
          ],
        ],
        'router' => [
          'cli' => [
            'module' => 'cli',
          ],
          'admin.*' => [
            'module' => 'admin',
            'air' => [
              'asset' => [
                'underscore' => false,
                'prefix' => '/assets/air',
              ],
            ]
          ],
          'api.*' => [
            'strict' => true,
            'module' => 'api',
            'routes' => $routes,
            'air' => [
              'strictInject' => true,
            ],
          ],
          '*' => [
            'strict' => true,
            'module' => 'ui',
            'routes' => $routes,
            'air' => [
              'strictInject' => true,
              'asset' => [
                'underscore' => false,
                'prefix' => '/assets/ui',
              ],
            ],
          ],
        ],
      ],
      $extensions ?: []
    );
  }
}
