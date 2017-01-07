<?php

namespace Mnabialek\LaravelHandyRequest\Traits;

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
        $input = $this->applyGlobalFilters($input, $this->filters());

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
            $value = $this->applyFilters($value, $key, $this->filters());
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
        // @todo
        return $input;
    }

    protected function applyFilters($value, $key, array $filters)
    {
        // @todo
        return $value;
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
}
