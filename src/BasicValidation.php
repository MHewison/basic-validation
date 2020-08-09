<?php

namespace Hewison;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait BasicValidation
{
    /**
     * Laravel validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Determine validation status.
     *
     * @var boolean
     */
    protected $validation_enabled = true;

    /**
     * Trait boot method
     *
     * @return void
     */
    public static function bootBasicValidation() : void
    {
        self::saving(function (Model $model) {
            if ($model->isValid() !== true && $model->validation_enabled) {
                throw new ValidationException($model->getValidator());
            }
        });
    }

    /**
     * Check validity.
     *
     * @return bool
     * @throws Exception
     */
    public function isValid() : bool
    {
        $this->setRules();

        if (!$this->hasRules()) {
            return true;
        }

        $validator = $this->makeValidator();

        if ($validator->fails()) {
            $this->validator = $validator;
            return false;
        }

        return true;
    }

    /**
     * Get the validator instance.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function makeValidator() : \Illuminate\Validation\Validator
    {
        return Validator::make(
            static::toArray(),
            $this->getRules(),
            $this->hasCustomMessages() ? $this->messages : []
        );
    }

    /**
     * Determine if a model has rules.
     *
     * @return bool
     */
    protected function hasRules() : bool
    {
        return (bool) count($this->getRules());
    }

    /**
     * Determines if a rules method has been defined.
     *
     * @return bool
     */
    private function hasValidationRulesMethod(): bool
    {
        return method_exists($this, 'validationRules');
    }

    /**
     * Resolves the validation rules from the model.
     *
     * @return void
     */
    private function setRules(): void
    {
        if ($this->hasValidationRulesMethod()) {
            $this->rules = static::validationRules();
        }

        if ($this->rules === null) {
            $this->rules = [];
        }
    }

    /**
     * Gets the current list of rules required for validation.
     *
     * @return array
     */
    public function getRules(): array
    {
        return isset($this->rules) && is_array($this->rules) ? $this->rules : [];
    }

    /**
     * Informs if there are custom messages.
     *
     * @return bool
     */
    public function hasCustomMessages(): bool
    {
        return (bool) (is_array($this->messages) && count($this->messages));
    }

    /**
     * Returns an array of validation errors.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator(): \Illuminate\Validation\Validator
    {
        return $this->validator;
    }

    /**
     * Removes rules from the validator.
     *
     * @param array|string $rule
     * @return $this
     */
    public function removeRules($rule): self
    {
        if (is_array($rule)) {
            foreach($rule as $r) {
                $this->removeRule($rule);
            }
        } else {
            $this->removeRule($rule);
        }

        return $this;
    }

    /**
     * Remove a specific rule if in rules array.
     *
     * @param string $rule
     */
    private function removeRule(string $rule): void
    {
        if ($this->hasRule((string) $rule)) {
            Arr::forget($this->getRules(), $rule);
        }
    }

    /**
     * Determines if a rule exists.
     *
     * @param string $rule
     * @return bool
     */
    private function hasRule(string $rule): bool
    {
        return Arr::has($this->getRules(), $rule);
    }

    /**
     * Allows inline disabling of validation.
     *
     * @return $this
     */
    public function disableValidation(): self
    {
        $this->validation_enabled = false;

        return $this;
    }

    /**
     * Allows inline enabling of validation.
     *
     * @return $this
     */
    public function enableValidation(): self
    {
        $this->validation_enabled = true;

        return $this;
    }
}
