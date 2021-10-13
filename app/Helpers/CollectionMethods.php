<?php


namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;

class CollectionMethods extends Collection
{
    protected function useAsCallable($value)
    {
        return !is_string($value) && is_callable($value);
    }

    protected function valueRetriever($value)
    {
        if ($this->useAsCallable($value)) {
            return $value;
        }

        return function ($item) use ($value) {
            return data_get($item, $value);
        };
    }

    public function groupBy($groupBy, $preserveKeys = false)
    {
        $groupBy = $this->valueRetriever($groupBy);

        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (!is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                $groupKey = is_bool($groupKey) ? (int)$groupKey : $groupKey;

                if (!array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static;
                }

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }
        return new static($results);
    }
}
