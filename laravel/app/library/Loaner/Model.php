<?php

namespace Loaner;

class Model extends \LaravelBook\Ardent\Ardent
{
    const PERPAGE      = 100;

    protected $perPage = self::PERPAGE;

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return 'U';
    }

	/**
	 * Convert the model's attributes to an array.
	 *
	 * @return array
	 */
	public function rawAttributesToArray()
	{
		$attributes = $this->getArrayableAttributes();

		// Here we will grab all of the appended, calculated attributes to this model
		// as these attributes are not really in the attributes array, but are run
		// when we need to array or JSON the model for convenience to the coder.
		foreach ($this->appends as $key)
		{
			$attributes[$key] = $this->mutateAttributeForArray($key, null);
		}

		return $attributes;
	}

}