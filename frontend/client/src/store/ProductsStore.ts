import {defineStore} from 'pinia';
import axios from "axios";

export const useProductStore = defineStore('Products', {
    state: () => ({
        status:'error',
        products:[{product_id:1,title:"err",thumbnail:"https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png",price:1}],
        categories:[{category_id:0,category_name:"placeholder"}]

    }),

    actions:{
        async loadCategory() {
            axios.get("http://localhost:8080/api/get_all_data")
            .then(res => (this.products = res.data))
            .catch(err => console.log("ürün yüklenemedi :" , err));
            axios.get("http://localhost:8080/api/get_categories")
            .then(res => this.categories = res.data.categories)
            .catch(err => console.log(err))
            .finally(() => this.status = 'success');
        },

        async updateProduct(data: { product_id: number; title: string; thumbnail: string; price: number; category:string; category_fk:any;}){
            try{
                data.category_fk = this.categories.find(c => c.category_name === data.category)?.category_id ?? null;
                delete (data as any).category;
                console.log(data);
                await axios.post(`http://localhost:8080/api/update_product/${data.product_id}`, data)
                .catch(err => console.log(err));

                const index = this.products.findIndex(p => p.product_id === data.product_id);
                if(index !== -1) this.products[index] = data;
            }
            catch(err)
            {
                console.log(err);
            }
        }
    }
})