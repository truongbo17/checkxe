Build CMS base on Laravel...

* `cp .env.example .env`
* `composer install`
* `php artisan migrate`
* `php artisan bo:cms:install`

### If File Manager not working, run it :
* `php artisan bo:filemanager:install`
### Revision Operation
* Add in model :
```php
  namespace MyApp\Models;

  class Article extends Eloquent {
  use \Bo\CRUD\CrudTrait, \Venturecraft\Revisionable\RevisionableTrait;

    public function identifiableName()
    {
        return $this->name;
    }

    // If you are using another bootable trait
    // be sure to override the boot method in your model
    public static function boot()
    {
        parent::boot();
    }
  }
```
* Add in CrudController :
```php
namespace App\Http\Controllers\Admin;

use Bo\CRUD\app\Http\Controllers\CrudController;

class CategoryCrudController extends CrudController
{
    use \Bo\ReviseOperation\ReviseOperation;
```
