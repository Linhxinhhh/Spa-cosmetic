<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;

class CategoryApiController extends Controller
{
    protected function getModel($type)
    {
        return $type === 'service' ? new ServiceCategory() : new ProductCategory();
    }

    public function parents($type)
    {
        $model = $this->getModel($type);
        return $model->whereNull('parent_id')
            ->where('status',1)
            ->orderBy('category_name')
            ->get(['id','category_name']);
    }

    public function children($type, $parentId)
    {
        $model = $this->getModel($type);
        return $model->where('parent_id',$parentId)
            ->where('status',1)
            ->orderBy('category_name')
            ->get(['id','category_name']);
    }
    public function header()
{
    $parents = ProductCategory::whereNull('parent_id')
        ->where('status',1)
        ->with(['children' => function($q){
            $q->where('status',1)->orderBy('category_name');
        }])
        ->orderBy('category_name')
        ->get();

    return view('Users.layout.header', compact('parents'));
}
}
