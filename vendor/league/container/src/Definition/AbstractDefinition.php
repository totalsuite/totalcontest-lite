<?php

namespace TotalContestVendors\League\Container\Definition;

use TotalContestVendors\League\Container\Argument\ArgumentResolverInterface;
use TotalContestVendors\League\Container\Argument\ArgumentResolverTrait;
use TotalContestVendors\League\Container\ImmutableContainerAwareTrait;

abstract class AbstractDefinition implements ArgumentResolverInterface, DefinitionInterface
{
    use ArgumentResolverTrait;
    use ImmutableContainerAwareTrait;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var mixed
     */
    protected $concrete;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Constructor.
     *
     * @param string $alias
     * @param mixed  $concrete
     */
    public function __construct($alias, $concrete)
    {
        $this->alias     = $alias;
        $this->concrete  = $concrete;
    }

    /**
     * {@inheritdoc}
     */
    public function withArgument($arg)
    {
        $this->arguments[] = $arg;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(array $args)
    {
        foreach ($args as $arg) {
            $this->withArgument($arg);
        }

        return $this;
    }
}
