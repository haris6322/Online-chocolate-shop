<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Add Product
                    </div>
                    <div class="card-body">
                      
                        <ul class="nav nav-tabs">
                            <li class="nav-item" v-for="l in langs" :key="l.id">
                                <a class="nav-link" @click="changeLange(l.id)" :class="{'active' : l.default}">{{ l.name }}</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane fade show" :class="{'active' : l.default}" v-for="l in langs" :key="l.id" >
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" v-model="product.translate[l.code].name" class="form-control" placeholder="Name" />
                                </div>
                                <div class="form-group">
                                    <label for="name">Description</label>
                                    <textarea type="text" v-model="product.translate[l.code].description" class="form-control" placeholder="Description" ></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="name">Hover text</label>
                                    <textarea type="text" v-model="product.translate[l.code].hover_text" class="form-control" placeholder="Hover text" ></textarea>
                                </div>
                            </div>
                        </div>

                        <form novalidate="novalidate">

                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" v-model="product.category_id" class="form-control">
                                    <option :disabled="c.code == 'ob'" :value="c.id" v-for="c in category" v-bind:key="c.id">{{ c.parent_id ? '-' : ''  }} {{ c.name }}</option>
                                </select>
                            </div>

                             <div class="form-group">
                                <label for="name">Picture</label>
                                <input type="file" name="form-control-file" @change="(e) => { product.image = e.target.files[0]; }" />
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" v-model="product.price" class="form-control" id="price" placeholder="Price" />
                            </div>
                 
                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" v-model="product.position" class="form-control" id="position" placeholder="Position" />
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" v-model="product.status">
                                    <option value="active">Active</option>
                                    <option value="deleted">Deleted</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="color">Active effect</label>
                                <div class="input-group">
                                    <!-- <div class="input-group-addon"></div> -->
                                    <!-- <input type="color" class="form-control" v-model="product.active_effect" /> -->
                                    <photoshop-picker v-model="product.active_effect" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="color">Hover effect</label>
                                <div class="input-group">
                                    <label class="switch switch-3d switch-primary">
                                        <input type="checkbox" v-model="product.hover_check" class="switch-input" value="true" />
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                                <div class="card" v-if=" ! product.hover_check">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="hover_text">Hover Color</label>
                                            <!-- <input type="color" class="form-control" v-model="product.hover_color" /> -->
                                            <photoshop-picker v-model="product.hover_color" />
                                        </div>
                                    </div>
                                </div>
                                 <div class="card" v-if="product.hover_check">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="hover_text">Hover Image</label>
                                            <input type="file" @change="(e) => { product.hover_img = e.target.files[0]; }" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <p v-if="errors.length">
                                <b>Please correct the following error(s):</b>
                                <div class="alert alert-danger" v-for="error in errors">{{ error }}</div>
                            </p>
                            <button type="button" @click="checkForm" class="btn btn-default">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { Photoshop } from 'vue-color';
import axios from 'axios';

export default {
    data() {
        return {
            errors : [],
            langs : [],
            category : [],
            product : {
                category_id : 0,
                hover_check : false,
                hover_img : "",
                hover_color : { hex : "#ffffff" },
                active_effect : { hex : "#ffffff" },
                hover_text : "",
                price : 0,
                description : "",
                short_description : "",
                position : 0,
                status : "active",
                color : { hex : "#ffffff" },
                image : false,
                tmp_image: false,
                hover_img : false,
                hover_text : "",
                translate : {}
            }
        }
    },
    name: 'AddProducts',
    mounted() {
        this.getCategory();
        this.getLangs();
    },
    components: {
        'photoshop-picker': Photoshop
    },
    methods : {
        checkForm:function(e) {
            var checkTranslate = true;
            for(var l in this.langs) {
                l = this.langs[l];
                if ( ! this.product.translate[l.code].name ||
                    ! this.product.translate[l.code].description) {
                       checkTranslate = false;
                    }
            }

            this.errors = [];
            

            for(var l in this.langs) {
                l = this.langs[l];
                console.log(l)
                if ( ! this.product.translate[l.code].name) {
                    this.errors.push("Name [" + l.code + "] required.");
                }

                if ( ! this.product.translate[l.code].description) {
                    this.errors.push("Description [" + l.code + "] required.");
                }
            }

            if( ! this.product.category_id) this.errors.push("Category required.");
            if( ! this.product.price) this.errors.push("Price required.");
            
             if(
                checkTranslate &&
                this.product.category_id &&
                this.product.price
                ) {
                this.addProduct();
                return true;
            }

            e.preventDefault();
        },
        changeLange(id) {
            this.langs = this.langs.map((l) => { 
                l.default = l.id == id ? true : false;
                return l; 
            });
        },
        getLangs() {
            let self = this;
            axios.get('/api/langs/get')
                .then((res) => {
                   self.langs = res.data;
                   self.langs.map(function(l) {
                       self.product.translate[l.code] = {};
                   });
                });
        },
        convert(obj) {
            for(var o in obj) {
                if (obj[o] * 1) {
                    obj[o] = obj[o] * 1;
                }
            }
            return obj;
        },
        addProduct() {
            this.product.hover_check = this.product.hover_check ? 1 : 0;
            
            this.product.active_effect = this.product.active_effect.hex;
            this.product.hover_color = this.product.hover_color.hex;
            this.product.color = this.product.color.hex;

            let fd = new FormData;
            for(let i in this.convert(this.product)) {
                fd.append(i, this.product[i] ? ( this.product[i] instanceof File ? this.product[i] : JSON.stringify(this.product[i]) ) : "" );
            }
            
            axios.post('/api/product/add', fd)
                .then((response) => {
                    Vue.router.push({ path: 'list' })
                });
        },
        getCategory() {
            let self = this;
            axios.get('/api/category/get')
                .then((response) => {
                    self.category = response.data;
                });
        }
    }
}
</script>
