<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'last_name' => $this->lastNameRules(),
            'ci' => $this->ciRules($userId),
            'email' => $this->emailRules($userId),
            'phone' => $this->phoneRules(),
            'gender' => $this->genderRules(),
            'birthdate' => $this->birthdateRules(),
        ];
    }

    protected function nameRules(): array
    {
        return ['required', 'string', 'max:150'];
    }

    protected function lastNameRules(): array
    {
        return ['required', 'string', 'max:150'];
    }

    protected function ciRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'max:15',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }

    protected function phoneRules(): array
    {
        return ['required', 'string', 'max:10'];
    }

    protected function genderRules(): array
    {
        return ['required', 'in:M,F'];
    }

    protected function birthdateRules(): array
    {
        return ['required', 'date'];
    }

    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
