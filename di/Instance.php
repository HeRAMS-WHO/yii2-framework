<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\di;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Instance is a reference to a named component in a container.
 *
 * You may use [[get()]] to obtain the actual component.
 *
 * Instance is mainly used in two places:
 *
 * - When configuring a dependency injection container, you use Instance to reference a component
 * - In classes which use external dependent objects.
 *
 * For example, the following configuration specifies that the "db" property should be
 * a component referenced by the "db" component:
 *
 * ```php
 * [
 *     'class' => 'app\components\UserFinder',
 *     'db' => Instance::of('db'),
 * ]
 * ```
 *
 * And in `UserFinder`, you may use `Instance` to make sure the "db" property is properly configured:
 *
 * ```php
 * namespace app\components;
 *
 * use yii\base\Object;
 * use yii\di\Instance;
 *
 * class UserFinder extends \yii\db\Object
 * {
 *     public $db;
 *
 *     public function init()
 *     {
 *         $this->db = Instance::ensure($this->db, 'yii\db\Connection');
 *     }
 * }
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Instance
{
    /**
     * @var string the component ID
     */
    public $id;

    /**
     * Constructor.
     * @param string $id the component ID
     */
    protected function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Creates a new Instance object.
     * @param string $id the component ID
     * @return Instance the new Instance object.
     */
    public static function of($id)
    {
        return new static($id);
    }

    /**
     * Ensures that `$value` is an object or a reference to the object of the specified type.
     *
     * An exception will be thrown if the type is not matched.
     *
     * Upon success, the method will return the object itself or the object referenced by `$value`.
     *
     * For example,
     *
     * ```php
     * use yii\db\Connection;
     *
     * // returns Yii::$app->db
     * $db = Instance::ensure('db', Connection::className());
     * // or
     * $instance = Instance::of('db');
     * $db = Instance::ensure($instance, Connection::className());
     * ```
     *
     * @param object|string|static $value an object or a reference to the desired object.
     * You may specify a reference in terms of a component ID or an Instance object.
     * @param string $type the class name to be checked
     * @param ContainerInterface $container the container. If null, the application instance will be used.
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public static function ensure($value, $type = null, $container = null)
    {
        if ($value instanceof $type) {
            return $value;
        } elseif (empty($value)) {
            throw new InvalidConfigException('The required component is not specified.');
        }

        if (is_string($value)) {
            $value = new static($value);
        }

        if ($value instanceof self) {
            $component = $value->get($container);
            if ($component instanceof $type || $type === null) {
                return $component;
            } else {
                throw new InvalidConfigException('"' . $value->id . '" refers to a ' . get_class($component) . " component. $type is expected.");
            }
        }

        $valueType = is_object($value) ? get_class($value) : gettype($value);
        throw new InvalidConfigException("Invalid data type: $valueType. $type is expected.");
    }

    /**
     * Returns the actual component referenced by this Instance object.
     * @return object the actual component referenced by this Instance object.
     */
    public function get($container = null)
    {
        /** @var ContainerInterface $container */
        if ($container) {
            return $container->get($this->id);
        }
        if (Yii::$app && Yii::$app->has($this->id)) {
            return Yii::$app->get($this->id);
        } else {
            return Yii::$container->get($this->id);
        }
    }
}
