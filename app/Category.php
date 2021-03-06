<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Category extends Model
{
    public function product() {
        return $this->hasMany("App\Product", "category_id");
    }

    public function translate() {
        return $this->hasMany("App\Translate", "table_id")->where("table", "categories");
    }

    public function getNameAttribute($value) {
        $translate = $this->translate->filter(function($t){
            if ($t->lang->code == LaravelLocalization::getCurrentLocale()) {
                return true;
            }
        })->first();

        return ! empty($translate->translate->name) ? $translate->translate->name : "";
    }

    public function getShortDescriptionAttribute($value) {
        $translate = $this->translate->filter(function($t){
            if ($t->lang->code == LaravelLocalization::getCurrentLocale()) {
                return true;
            }
        })->first();
        
        return ! empty($translate->translate->short_description) ? $translate->translate->short_description : "";
    }

    public function getSubDescriptionAttribute($value) {
        $translate = $this->translate->filter(function($t){
            if ($t->lang->code == LaravelLocalization::getCurrentLocale()) {
                return true;
            }
        })->first();
        
        return ! empty($translate->translate->sub_description) ? $translate->translate->sub_description : "";
    }
}
