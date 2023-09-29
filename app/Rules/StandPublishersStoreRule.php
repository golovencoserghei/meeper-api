<?php

namespace App\Rules;

use App\Models\StandRecords;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;

class StandPublishersStoreRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $standPublisherId = request()->route('id');
        $standRecordsQuery = StandRecords::query()->where('id', $standPublisherId);
        $isPublisherAlreadyRegistered = $standRecordsQuery
            ->whereHas(
                'publishers',
                static function (Builder $query) use ($value, $standPublisherId): void {
                    $query->where('publisher_id', $value);
                })
            ->exists();

        if ($isPublisherAlreadyRegistered) {
            $fail('This publisher already registered for this hour.');
        }

        $countOfPublishersAllowedToStand = StandRecords::query()
            ->findOrFail($standPublisherId)
            ->standTemplate
            ->value('publishers_at_stand');

        $countOfPublishersToRegister = $standRecordsQuery->count() + $value;
        $isCountOfPublishersToRegisterExceedLimit = $countOfPublishersToRegister > $countOfPublishersAllowedToStand;

        if ($isCountOfPublishersToRegisterExceedLimit) {
            $fail("You are trying to register more than $countOfPublishersAllowedToStand publishers allowed to stand.");
        }
    }
}
