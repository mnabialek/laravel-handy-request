<?php

namespace Mnabialek\LaravelHandyRequest\Traits;

use Mnabialek\LaravelHandyRequest\ConstraintChecker;
use Mnabialek\LaravelHandyRequest\Filters\Contracts\FieldFilter;
use Mnabialek\LaravelHandyRequest\Filters\Contracts\Filter;
use Mnabialek\LaravelHandyRequest\Filters\Contracts\GlobalFilter;

trait HandyRequest
{
    /**
     * Custom registered filters
     *
     * @var array
     */
    protected static $registeredFilters = [];

    /**
     * Constraint checker
     *
     * @var ConstraintChecker
     */
    protected $constraintChecker = null;

    /**
     * {@inheritdoc}
     */
    public function input($key = null, $default = null)
    {
        return $this->filtered($key, $default);
    }

    /**
     * Retrieve an original input item from the request (without applying any filters).
     *
     * @param  string $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function original($key = null, $default = null)
    {
        return parent::input($key, $default);
    }

    /**
     * Retrieve filtered input item from the request (after applying filters).
     *
     * @param  string $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function filtered($key = null, $default = null)
    {
        // get original input
        $input = $this->getInputSource()->all() + $this->query->all();

        // initialize filtering requirements
        $this->initializeFilteringRequirements();

        // modify all the input in case custom method exists
        if (method_exists($this, 'modifyInput')) {
            $input = $this->modifyInput($input);
        }

        // apply any global filters
        $input = $this->applyGlobalFilters($input, $this->normalizedFilters());

        // now get value/values from input
        $value = data_get($input, $key, $default);

        // get filtered value
        return $this->filteredValue($value, $key);
    }

    /**
     * Get filtered value (for scalar or array value)
     *
     * @param mixed $value
     * @param mixed $key
     * @param mixed|null $fullKey
     *
     * @return mixed
     */
    protected function filteredValue($value, $key, $fullKey = null)
    {
        if (empty($fullKey)) {
            $fullKey = $key;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->filteredValue($item, $key,
                    empty($fullKey) ? $key : $fullKey . '.' . $key);
            }
        } else {
            $value = $this->filteredField($value, $fullKey);
        }

        return $value;
    }

    /**
     * Apply filter to scalar value. If field has custom filter, use custom filter, otherwise apply
     * general filters
     *
     * @param mixed $value
     * @param mixed $fullKey
     *
     * @return mixed
     */
    protected function filteredField($value, $fullKey)
    {
        if ($this->hasFieldFilter($fullKey)) {
            $value = $this->{$this->fieldFilterName($fullKey)}($value, $fullKey);
        } else {
            $value = $this->applyFilters($value, $fullKey, $this->normalizedFilters());
        }

        return $value;
    }

    /**
     * Verify whether exists custom field filter method for given key
     *
     * @param string $fullKey
     *
     * @return bool
     */
    protected function hasFieldFilter($fullKey)
    {
        return method_exists($this, $this->fieldFilterName($fullKey));
    }

    /**
     * Get filter method name for single field
     *
     * @param string $fullKey
     *
     * @return string
     */
    protected function fieldFilterName($fullKey)
    {
        if (property_exists($this, 'fieldFiltersMethods')) {
            foreach ($this->fieldFiltersMethods as $constraint => $methodName) {
                if ($this->constraintChecker->fieldMatchesConstraint($fullKey, $constraint)) {
                    return $methodName;
                }
            }
        }

        return 'apply' . ucfirst(mb_strtolower($fullKey)) . 'fieldFilter';
    }

    /**
     * Apply global filters
     *
     * @param array $input
     * @param array $filters
     *
     * @return array
     */
    protected function applyGlobalFilters(array $input, array $filters)
    {
        foreach ($filters as $filter => $options) {
            /** @var Filter $filterClass */
            $filterClass = $this->getFilterClass($filter, $options);
            if ($filterClass->isGlobal()) {
                /** @var GlobalFilter $filterClass */
                $input = $filterClass->applyGlobal($input);
            }
        }

        return $input;
    }

    /**
     * Apply filters to given value
     *
     * @param mixed $value
     * @param mixed $fullKey
     * @param array $filters
     *
     * @return mixed
     */
    protected function applyFilters($value, $fullKey, array $filters)
    {
        foreach ($filters as $name => $options) {
            if (! $this->shouldApplyFilter($fullKey, $options)) {
                continue;
            }
            /** @var Filter $filterClass */
            $filterClass = $this->getFilterClass($name, array_except($options, ['only', 'except']));
            if (! $filterClass->isGlobal()) {
                /** @var FieldFilter $filterClass */
                $value = $filterClass->apply($value, $fullKey);
            }
        }

        return $value;
    }

    /**
     * Verify whether filter should be applied to given key
     *
     * @param mixed $fullKey
     * @param array $filterOptions
     *
     * @return bool
     */
    protected function shouldApplyFilter($fullKey, $filterOptions)
    {
        if (array_key_exists('only', $filterOptions) &&
            ! $this->constraintChecker->canBeMatchedToFieldsConstraints($fullKey,
                $filterOptions['only'])
        ) {
            return false;
        }
        if (array_key_exists('except', $filterOptions) &&
            $this->constraintChecker->canBeMatchedToFieldsConstraints($fullKey,
                $filterOptions['except'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get filter class for given name
     *
     * @param string $filterName
     * @param array $filterOptions
     *
     * @return Filter
     */
    protected function getFilterClass($filterName, array $filterOptions)
    {
        /** @var Filter $class */
        $class = $this->container->make($this->getFilterClassName($filterName));
        $class->setOptions($filterOptions);

        return $class;
    }

    /**
     * Get class name for given filter name
     *
     * @param string $filterName
     *
     * @return string
     */
    protected function getFilterClassName($filterName)
    {
        return array_get(self::$registeredFilters, $filterName,
            '\\Mnabialek\\LaravelHandyRequest\\Filters\\' . ucfirst(studly_case($filterName)) .
            'Filter');
    }

    /**
     * Get filters
     *
     * @return array
     */
    protected function filters()
    {
        return property_exists($this, 'filters') ? $this->filters : [];
    }

    /**
     * Get normalized filters in [[filterName => filterOptions],...] format
     *
     * @return array
     */
    protected function normalizedFilters()
    {
        $filters = $this->filters();

        foreach ($filters as $filter => $options) {
            if (! is_array($options)) {
                $filters[$options] = [];
                unset($filters[$filter]);
            }
        }

        return $filters;
    }

    /**
     * Register custom filter with given name
     *
     * @param string $filterName Filter name
     * @param string $filterClass Filter class
     */
    public static function registerFilter($filterName, $filterClass)
    {
        self::$registeredFilters[$filterName] = $filterClass;
    }

    /**
     * Set any filtering requirements and run any methods needed to be run before filtering
     */
    protected function initializeFilteringRequirements()
    {
        // register any custom filters
        if (method_exists($this, 'registerFilters')) {
            $this->registerFilters();
        }

        // create constraint checker
        $this->constraintChecker = $this->container->make(ConstraintChecker::class);
    }
}
