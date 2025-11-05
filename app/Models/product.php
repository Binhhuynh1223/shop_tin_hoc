<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = true;

    protected $fillable = [
        'product_name',
        'category',
        'brand',
        'description',
        'price',
        'stock',
        'image_url'
    ];

    /**
     * Validation rules
     */
    protected static $rules = [
        'product_name' => 'required|min:3',
        'category' => 'required',
        'brand' => 'required',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0'
    ];

    /**
     * Validate product data
     * @param array $data
     * @throws \Exception
     */
    public static function validate($data)
    {
        foreach (self::$rules as $field => $rules) {
            $rules = explode('|', $rules);
            
            foreach ($rules as $rule) {
                if ($rule === 'required' && (!isset($data[$field]) || empty($data[$field]))) {
                    throw new \Exception("Trường $field là bắt buộc");
                }
                
                if (strpos($rule, 'min:') === 0) {
                    $min = substr($rule, 4);
                    if ($field === 'product_name' && strlen($data[$field]) < $min) {
                        throw new \Exception("$field phải có ít nhất $min ký tự");
                    }
                    if (($field === 'price' || $field === 'stock') && $data[$field] < $min) {
                        throw new \Exception("$field không thể nhỏ hơn $min");
                    }
                }

                if ($rule === 'numeric' && !is_numeric($data[$field])) {
                    throw new \Exception("$field phải là số");
                }

                if ($rule === 'integer' && !filter_var($data[$field], FILTER_VALIDATE_INT)) {
                    throw new \Exception("$field phải là số nguyên");
                }
            }
        }
    }

    public function createRecord($data)
    {
        return self::create($data);
    }

    public function updateRecord($id, $data)
    {
        $item = self::find($id);
        if (!$item) return false;
        return $item->update($data);
    }

    public function getByCategory($category)
    {
        return self::where('category', $category)->get()->toArray();
    }

    public function getByBrand($brand)
    {
        return self::where('brand', $brand)->get()->toArray();
    }

    public function updateStock($id, $stock)
    {
        $item = self::find($id);
        if (!$item) return false;
        $item->stock = $stock;
        return $item->save();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }
}

