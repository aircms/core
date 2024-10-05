<?php

namespace Air\Model;

use Air\Cache;
use Air\Crud\Model\Language;
use ArrayAccess;
use Air\Core\Exception\ClassWasNotFound;
use Air\Model\Driver\CursorAbstract;
use Air\Model\Driver\DocumentAbstract;
use Air\Model\Driver\DriverAbstract;
use Air\Model\Driver\Exception\PropertyHasDifferentType;
use Air\Model\Driver\Exception\PropertyWasNotFound;
use Air\Model\Driver\Mongodb\Document;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use ReflectionException;

/**
 * @method static int remove (array $cond = [], int $limit = null)
 */
abstract class ModelAbstract implements ModelInterface, ArrayAccess
{
  /**
   * @var DocumentAbstract|string
   */
  private static DocumentAbstract|string $driverClassName = '';

  /**
   * @var Meta|null
   */
  private ?Meta $meta = null;

  /**
   * @var DocumentAbstract|null
   */
  private ?DocumentAbstract $document = null;

  /**
   * @param array|null $data
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws Meta\Exception\CollectionCantBeWithoutPrimary
   * @throws Meta\Exception\CollectionCantBeWithoutProperties
   * @throws Meta\Exception\CollectionNameDoesNotExists
   * @throws Meta\Exception\PropertyIsSetIncorrectly
   * @throws ReflectionException
   */
  public function __construct(?array $data = null)
  {
    if (!Config::getConfig()) {
      throw new ConfigWasNotProvided();
    }

    $this->meta = new Meta($this);

    if ($data) {
      $this->populateWithoutQuerying($data);
    }
  }

  /**
   * @param DriverAbstract $class
   */
  public static function setDriver(DriverAbstract $class): void
  {
    self::$driverClassName = get_class($class);
  }

  /**
   * @param array $cond
   * @param array $sort
   * @return static
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public static function fetchObject(array $cond = [], array $sort = []): static
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param string $methodName
   * @param array $args
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public static function __callStatic(string $methodName, array $args): mixed
  {
    $driver = self::getDriver();

    $method = [$driver, $methodName];

    if (is_callable($method)) {
      $driver->setModel(new static());
      return call_user_func_array($method, $args);
    }

    throw new CallUndefinedMethod(static::class, $methodName);
  }

  /**
   * @return DriverAbstract
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public static function getDriver(): DriverAbstract
  {
    if (!Config::getConfig()) {
      throw new ConfigWasNotProvided();
    }

    $driverClassName = null;

    if (!empty(self::$driverClassName)) {
      $driverClassName = self::$driverClassName;

    } else {
      switch (Config::getConfig()['driver']) {
        case 'mongodb':
          $driverClassName = Driver\Mongodb\Driver::class;
          break;

        case 'flat':
          $driverClassName = Driver\Flat\Driver::class;
          break;
      }
    }

    if ($driverClassName) {
      if (!class_exists($driverClassName)) {
        throw new DriverClassDoesNotExists($driverClassName);
      }

      if (!is_subclass_of($driverClassName, DriverAbstract::class)) {
        throw new DriverClassDoesNotExtendsFromDriverAbstract($driverClassName);
      }
    }

    return new $driverClassName(Config::getConfig());
  }

  /**
   * @param array $data
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function batchInsert(array $data = []): int
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $data
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function insert(array $data = []): int
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $cond
   * @param array $data
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function update(array $cond = [], array $data = []): int
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param array $map
   * @return static|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function singleOne(array $cond = [], array $sort = [], array $map = []): static|null
  {
    $cond = static::addCond($cond);
    $sort = static::addPosition($sort);

    return Cache::single(['one', static::class, $cond, $sort, $map], function () use ($cond, $sort, $map) {
      return static::fetchOne($cond, $sort, $map);
    });
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param array $map
   * @return static|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function one(array $cond = [], array $sort = [], array $map = []): static|null
  {
    return static::fetchOne(static::addCond($cond), static::addPosition($sort), $map);
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param array $map
   * @return static|null
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function fetchOne(array $cond = [], array $sort = [], array $map = []): static|null
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $cond
   * @return array
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function addCond(array $cond = []): array
  {
    $model = new static();
    if ($model->getMeta()->hasProperty('enabled') && !isset($cond['enabled'])) {
      $cond['enabled'] = true;
    }

    if ($model->getMeta()->hasProperty('language') && !isset($cond['language'])) {
      $cond['language'] = Language::getLanguage();
    }

    return $cond;
  }

  /**
   * @param array $sort
   * @return array
   */
  public static function addPosition(array $sort = []): array
  {
    $model = new static();
    if ($model->getMeta()->hasProperty('position') && !isset($sort['position'])) {
      $sort['position'] = 1;
    }
    return $sort;
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param int|null $count
   * @param int|null $offset
   * @param array $map
   * @return CursorAbstract|array|static[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function singleAll(
    array $cond = [],
    array $sort = [],
    int   $count = null,
    int   $offset = null,
    array $map = []
  ): CursorAbstract|array
  {
    $cond = static::addCond($cond);
    $sort = static::addPosition($sort);

    return Cache::single(
      ['all', static::class, $cond, $sort, $count, $offset, $map],
      function () use ($cond, $sort, $count, $offset, $map) {
        return static::fetchAll($cond, $sort, $count, $offset, $map);
      });
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param int|null $count
   * @param int|null $offset
   * @param array $map
   * @return CursorAbstract|array|static[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function all(
    array $cond = [],
    array $sort = [],
    int   $count = null,
    int   $offset = null,
    array $map = []
  ): CursorAbstract|array
  {
    return static::fetchAll(static::addCond($cond), static::addPosition($sort), $count, $offset, $map);
  }

  /**
   * @param array $cond
   * @param array $sort
   * @param int|null $count
   * @param int|null $offset
   * @param array $map
   * @return CursorAbstract|array|ModelInterface[]|static[]
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function fetchAll(
    array $cond = [],
    array $sort = [],
    int   $count = null,
    int   $offset = null,
    array $map = []
  ): CursorAbstract|array
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $cond
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function quantity(array $cond = []): int
  {
    return static::count(static::addCond($cond));
  }

  /**
   * @param array $cond
   * @return int
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   */
  public static function count(array $cond = []): int
  {
    return self::__callStatic(__FUNCTION__, func_get_args());
  }

  /**
   * @return string
   */
  public function getModelClassName(): string
  {
    return get_class($this);
  }

  /**
   * @param string $name
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function __get(string $name): mixed
  {
    return $this->getDocument()->__get($name);
  }

  /**
   * @param string $name
   * @param mixed $value
   * @return void
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function __set(string $name, mixed $value): void
  {
    $this->getDocument()->__set($name, $value);
  }

  /**
   * @return DocumentAbstract
   * @throws ClassWasNotFound
   */
  public function getDocument(): DocumentAbstract
  {
    if (!$this->document) {

      $driver = Config::getConfig()['driver'];
      $documentClassName = null;

      switch ($driver) {
        case 'mongodb':
          $documentClassName = Document::class;
          break;

        case 'flat':
          $documentClassName = Driver\Flat\Document::class;
      }

      if ($documentClassName) {
        $this->document = new $documentClassName($this);
      }
    }
    return $this->document;
  }

  /**
   * @param string $name
   * @return bool
   * @throws ClassWasNotFound
   */
  public function __isset(string $name): bool
  {
    return $this->getDocument()->__isset($name);
  }

  /**
   * @param mixed $offset
   * @return bool
   * @throws ClassWasNotFound
   */
  public function offsetExists(mixed $offset): bool
  {
    return $this->getDocument()->offsetExists($offset);
  }

  /**
   * @param mixed $offset
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function offsetGet(mixed $offset): mixed
  {
    return $this->getDocument()->offsetGet($offset);
  }

  /**
   * @param mixed $offset
   * @param mixed $value
   * @return void
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function offsetSet(mixed $offset, mixed $value): void
  {
    $this->getDocument()->offsetSet($offset, $value);
  }

  /**
   * @param mixed $offset
   * @return void
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function offsetUnset(mixed $offset): void
  {
    $this->getDocument()->offsetUnset($offset);
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function getData(): array
  {
    return self::__call(__FUNCTION__, func_get_args());
  }

  /**
   * @param string $methodName
   * @param array $args
   * @return mixed
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function __call(string $methodName, array $args)
  {
    /**
     * Checking Document calls
     */
    $document = $this->getDocument();
    $method = [$document, $methodName];

    if (is_callable($method)) {
      return call_user_func_array($method, $args);
    }

    /**
     * Checking Driver calls
     */
    $driver = self::getDriver();
    $method = [$driver, $methodName];

    if (is_callable($method)) {
      $driver->setModel($this);
      return call_user_func_array($method, $args);
    }

    throw new CallUndefinedMethod($this->getMeta()->getCollection(), $methodName);
  }

  /**
   * @return Meta
   */
  public function getMeta(): Meta
  {
    return $this->meta;
  }

  /**
   * @param array $data
   * @param bool $fromSet
   * @return void
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function populate(array $data, bool $fromSet = true): void
  {
    self::__call(__FUNCTION__, func_get_args());
  }

  /**
   * @param array $data
   * @return void
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function populateWithoutQuerying(array $data): void
  {
    self::__call(__FUNCTION__, func_get_args());
  }

  /**
   * @return int
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function save(): int
  {
    return self::__call(__FUNCTION__, func_get_args());
  }

  /**
   * @return int
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws ClassWasNotFound
   */
  public function getTimestamp(): int
  {
    return self::__call(__FUNCTION__, func_get_args());
  }

  /**
   * @return array
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyHasDifferentType
   * @throws PropertyWasNotFound
   * @throws ClassWasNotFound
   */
  public function toArray(): array
  {
    return $this->getDocument()->toArray();
  }
}
