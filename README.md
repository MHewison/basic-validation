# Basic Validation
Basic validation for Laravel models. On creation and update events by default.

## Usage

- if basic validation is required
```php
class LaravelModel {
    use Hewison\BasicValidation;

    public $rules = [
        'column' => 'required'
    ];

    public $messages = [
        'column.required' => 'This is a custom message'
    ];
}
```

- if more advanced rules are required a method is provided
```php
use Illuminate\Validation\Rule;

class LaravelModel {
    use Hewison\BasicValidation;

    const AVAILABLE_OPTIONS = [
        'option_1',
        'option_2',
    ];

    public function validationRules()
    {
        return [
            'column' => [
                'required',
                Rule::In(self::AVAILABLE_OPTIONS)
            ],
        ];
    }
}
```

- validation will fire when using any of the following methods
```php
    LaravelModel::create();
    LaravelModel::update();
    LaravelModel::save();
```

- if you require that validation does not fire for specific events you can use ```php disableValidation() ```

```php
    LaravelModel::find(1)->disableValidation()->update();
    LaravelModel::find(1)->enableValidation()->update();
```

- You can also remove specific rules for an event, this will bypass validation for specific columns
```php
    LaravelModel::find(1)->removeRules('column')->update();
    LaravelModel::find(1)->removeRules(['column_1', 'column_2'])->update();
```