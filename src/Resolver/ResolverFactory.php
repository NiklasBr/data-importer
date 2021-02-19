<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Enterprise License (PEL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PEL
 */

namespace Pimcore\Bundle\DataHubBatchImportBundle\Resolver;

use Pimcore\Bundle\DataHubBatchImportBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataHubBatchImportBundle\Resolver\Factory\FactoryInterface;
use Pimcore\Bundle\DataHubBatchImportBundle\Resolver\Load\LoadStrategyInterface;
use Pimcore\Bundle\DataHubBatchImportBundle\Resolver\Location\LocationStrategyInterface;
use Pimcore\Bundle\DataHubBatchImportBundle\Resolver\Publish\PublishStrategyInterface;

class ResolverFactory
{
    /**
     * @var Resolver
     */
    protected $resolverBlueprint;

    /**
     * @var LoadStrategyInterface[]
     */
    protected $loadingStrategyBlueprints;

    /**
     * @var LocationStrategyInterface[]
     */
    protected $locationStrategyBlueprints;

    /**
     * @var PublishStrategyInterface[]
     */
    protected $publishingStrategyBlueprints;

    /**
     * @var FactoryInterface[]
     */
    protected $factoryBlueprints;

    /**
     * ResolverFactory constructor.
     *
     * @param Resolver $resolverBlueprint
     * @param LoadStrategyInterface[] $loadingStrategyBlueprints
     * @param LocationStrategyInterface[] $locationStrategyBlueprints
     * @param PublishStrategyInterface[] $publishingStrategyBlueprints
     * @param FactoryInterface[] $factoryBlueprints
     */
    public function __construct(Resolver $resolverBlueprint, array $loadingStrategyBlueprints, array $locationStrategyBlueprints, array $publishingStrategyBlueprints, array $factoryBlueprints)
    {
        $this->resolverBlueprint = $resolverBlueprint;
        $this->loadingStrategyBlueprints = $loadingStrategyBlueprints;
        $this->locationStrategyBlueprints = $locationStrategyBlueprints;
        $this->publishingStrategyBlueprints = $publishingStrategyBlueprints;
        $this->factoryBlueprints = $factoryBlueprints;
    }

    /**
     * @param array $config
     * @param $classId
     *
     * @return LoadStrategyInterface
     *
     * @throws InvalidConfigurationException
     */
    protected function buildLoadingStrategy(array $config, $classId): LoadStrategyInterface
    {
        if (empty($config['type']) || !array_key_exists($config['type'], $this->loadingStrategyBlueprints)) {
            throw new InvalidConfigurationException('Unknown loading strategy type `' . ($config['type'] ?? '') . '`');
        }

        $loadingStrategy = clone $this->loadingStrategyBlueprints[$config['type']];
        $loadingStrategy->setSettings($config['settings'] ?? []);
        $loadingStrategy->setDataObjectClassId($classId);

        return $loadingStrategy;
    }

    protected function buildLocationStrategy(array $config): LocationStrategyInterface
    {
        if (empty($config['type']) || !array_key_exists($config['type'], $this->locationStrategyBlueprints)) {
            throw new InvalidConfigurationException('Unknown location strategy type `' . ($config['type'] ?? '') . '`');
        }

        $locationStrategy = clone $this->locationStrategyBlueprints[$config['type']];
        $locationStrategy->setSettings($config['settings'] ?? []);

        return $locationStrategy;
    }

    protected function buildPublishingStrategy(array $config): PublishStrategyInterface
    {
        if (empty($config['type']) || !array_key_exists($config['type'], $this->publishingStrategyBlueprints)) {
            throw new InvalidConfigurationException('Unknown publishing strategy type `' . ($config['type'] ?? '') . '`');
        }

        $publishStrategy = clone $this->publishingStrategyBlueprints[$config['type']];
        $publishStrategy->setSettings($config['settings'] ?? []);

        return $publishStrategy;
    }

    protected function buildElementFactory(string $type, string $subType = null): FactoryInterface
    {
        if (empty($type) || !array_key_exists($type, $this->factoryBlueprints)) {
            throw new InvalidConfigurationException('Unknown publishing strategy type `' . ($type) . '`');
        }

        $factory = clone $this->factoryBlueprints[$type];
        $factory->setSubType($subType);

        return $factory;
    }

    public function loadResolver(array $configuration): Resolver
    {
        $resolver = clone $this->resolverBlueprint;

        $resolver->setDataObjectClassId($configuration['dataObjectClassId'] ?? null);
        $resolver->setLoadingStrategy($this->buildLoadingStrategy($configuration['loadingStrategy'] ?? [], $resolver->getDataObjectClassId()));
        $resolver->setCreateLocationStrategy($this->buildLocationStrategy($configuration['createLocationStrategy'] ?? []));
        $resolver->setLocationUpdateStrategy($this->buildLocationStrategy($configuration['locationUpdateStrategy'] ?? []));
        $resolver->setPublishingStrategy($this->buildPublishingStrategy($configuration['publishingStrategy']));
        $resolver->setElementFactory($this->buildElementFactory($configuration['elementType' ?? ''], $resolver->getDataObjectClassId()));

        return $resolver;
    }
}
