<?php

namespace Mnabialek\LaravelHandyRequest\Traits;

use Mnabialek\LaravelHandyRequest\Filters\Contracts\Filter;

trait HandyRequest
{
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
            $value = $this->filteredField($value, $key, $fullKey);
        }

        return $value;
    }

    /**
     * Apply filter to scalar value. If field has custom filter, use custom filter, otherwise apply
     * general filters
     *
     * @param mixed $value
     * @param mixed $key
     * @param mixed $fullKey
     *
     * @return mixed
     */
    protected function filteredField($value, $key, $fullKey)
    {
        if ($this->hasFieldFilter($key, $fullKey)) {
            $value = $this->{$this->fieldFilterName($key)}($value, $key);
        } else {
            $value = $this->applyFilters($value, $key, $this->normalizedFilters());
        }

        return $value;
    }

    /**
     * Verify whether exists custom field filter method for given key
     *
     * @param mixed $key
     * @param $fullKey
     *
     * @return bool
     */
    protected function hasFieldFilter($key, $fullKey)
    {
        return method_exists($this, $this->fieldFilterName($key));
    }

    /**
     * Get filter method name for single field
     *
     * @param string $fieldName
     *
     * @return string
     */
    protected function fieldFilterName($fieldName)
    {
        return 'apply' . ucfirst(mb_strtolower($fieldName)) . 'fieldFilter';
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
                $input = $filterClass->applyGlobal($input);
            }
        }

        return $input;
    }

    /**
     * Apply filters to given value
     *
     * @param mixed $value
     * @param mixed $key
     * @param array $filters
     *
     * @return mixed
     */
    protected function applyFilters($value, $key, array $filters)
    {
        foreach ($filters as $name => $options) {
            if (! $this->shouldApplyFilter($key, $options)) {
                continue;
            }
            /** @var Filter $class */
            $class = $this->getFilterClass($name, array_except($options, ['only', 'except']));
            $value = $class->apply($value, $key);
        }

        return $value;
    }

    /**
     * Verify whether filter should be applied to given key
     *
     * @param mixed $key
     * @param array $filterOptions
     *
     * @return bool
     */
    protected function shouldApplyFilter($key, $filterOptions)
    {
        if (array_key_exists('only', $filterOptions) &&
            ! in_array($key, $filterOptions['only'], true)
        ) {
            return false;
        }
        if (array_key_exists('except', $filterOptions) &&
            in_array($key, $filterOptions['except'], true)
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
        $className = '\\Mnabialek\\LaravelHandyRequest\\Filters\\' .
            ucfirst(studly_case($filterName)) . 'Filter';

        /** @var Filter $class */
        $class = $this->container->make($className);
        $class->setOptions($filterOptions);

        return $class;
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
}
