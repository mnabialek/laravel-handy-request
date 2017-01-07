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
     * Get filtered value
     *
     * @param mixed $value
     * @param mixed $key
     *
     * @return mixed
     */
    protected function filteredValue($value, $key)
    {
        // @todo
        return $value;
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
