import {defineStore} from 'pinia';
import axios from "axios";

interface Product{
    product_id:number;
    title:string;
    category:string;
    thumbnail:string;
    price:symbol;
    
}
export const useProductStore = defineStore('Products', {
    state: () => ({
        status:'error',
        products:[{product_id:1,title:"err",category:"err",thumbnail:"https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png",price:1,active:1, stock:1,images:[{image_id:0,image_url:"err",alt_text:"err"}]}],
        categories:[{category_id:0,category_name:"placeholder",categorySlug:"placeholder"}]

    }),

    actions:{
        async loadCategory() {
            await axios.get("http://localhost:8080/api/get_all_data")
            .then(res => (this.products = res.data.data))
            .catch(err => console.log("ürün yüklenemedi :" , err));
            await axios.get("http://localhost:8080/api/get_categories")
            .then(res => this.categories = res.data.data)
            .catch(err => console.log(err))
            .finally(() => this.status = 'success');
        },

        async updateProduct(data: { product_id: number; title: string; thumbnail: string; price: number; active:number; stock:number; category:string; category_fk:any; images:[];newImage:{image_url:string,alt_text:string}}){
            try{
                data.category_fk = this.categories.find(c => c.category_name === data.category)?.category_id ?? null;
                delete (data as any).category;
                
                await axios.post(`http://localhost:8080/api/update_product/${data.product_id}`, data)
                .catch(err => console.log(err));

                const imageData = {newImage:data.newImage};
                if(imageData.newImage?.image_url != undefined && Object.keys(imageData.newImage?.image_url).length > 0){
                    await axios.post(`http://localhost:8080/api/AddProductImages/${data.product_id}`, imageData);
                }

                const currentProduct = this.products.find(p => p.product_id === data.product_id);
                const isChanged = JSON.stringify(data.images) === JSON.stringify(currentProduct?.images);
                const updateImageData = data.images;
                if(!isChanged){
                    console.log(updateImageData)
                    await axios.post(`http://localhost:8080/api/UpdateProductImages/`, updateImageData );
                }
                const index = this.products.findIndex(p => p.product_id === data.product_id);
                if(index !== -1) this.products[index] = data;
            }
            catch(err)
            {
                console.log(err);
            }
        },

        async deleteProductImage(product_id:number,image_id:number){
            const product_data = this.products.find(p => p.product_id == product_id);
            
            if(product_data?.images.find(i=>i.image_id == image_id)){
                console.log(product_data);
                console.log(image_id);
                await axios.post(`http://localhost:8080/api/DeleteProductImage/${image_id}`)
            }
            else{
                console.log("ürn yok");
            }
        }
    }
})