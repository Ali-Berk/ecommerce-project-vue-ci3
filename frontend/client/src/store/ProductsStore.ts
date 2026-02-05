import {defineStore} from 'pinia';
import axios from "axios";

export interface Product{
    product_id:number;
    title:string;
    category:string;
    thumbnail:string;
    price:number;
    
}
export const useProductStore = defineStore('Products', {
    state: () => ({
        status:'error',
 products: [
    {
        product_id: 1,
        title: "Modern Genç Çalışma Masası ve Ünitesi",
        category: "Genç Odaları",
        thumbnail: "https://www.fezamutfak.com/pages/dekorasyon/evdekorasyonu/Erkek_Genc_Odasi_Calisma_Masasi.jpg?w=&h=",
        price: 4500,
        active: 1,
        stock: 10,
        images: [{ image_id: 101, image_url: "err", alt_text: "Çalışma Masası" }]
    },
    {
        product_id: 2,
        title: "Metal Gövde Ranza Sistemi",
        category: "Genç Odaları",
        thumbnail: "https://www.argimo.com/cdn/shop/products/0a072247a42648119534e3b0bcbcd70c_dac6f595-1819-4fc3-a9c6-dfea36b40038.jpg?v=1755875320&width=1800",
        price: 8900,
        active: 1,
        stock: 4,
        images: [{ image_id: 102, image_url: "err", alt_text: "Ranza" }]
    },

    {
        product_id: 3,
        title: "Büyüyebilen Bebek Beşiği - Beyaz",
        category: "Bebek Odaları",
        thumbnail: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0W3Uf12_PeecGtK_kCE6JUmYXcToSeLoGSQ&s",
        price: 6200,
        active: 1,
        stock: 8,
        images: [{ image_id: 103, image_url: "err", alt_text: "Beşik" }]
    },
    {
        product_id: 4,
        title: "Bebek Odası Şifonyer",
        category: "Bebek Odaları",
        thumbnail: "https://cdn.dsmcdn.com/mnresize/420/620/ty1674/prod/QC/20250506/15/8b1dae47-bd8a-31a9-ab15-b17b2fd20211/1_org_zoom.jpg",
        price: 3100,
        active: 1,
        stock: 0,
        images: [{ image_id: 104, image_url: "err", alt_text: "Şifonyer" }]
    },

    {
        product_id: 5,
        title: "6 Kapaklı Aynalı Gardırop",
        category: "Yatak Odaları",
        thumbnail: "https://www.modalife.com/shop/bo/48/myassets/products/931/eva-6-kapakli-aynali-cekmeceli-gardirop.jpg?revision=1767703945",
        price: 18500,
        active: 1,
        stock: 5,
        images: [{ image_id: 105, image_url: "err", alt_text: "Gardırop" }]
    },
    {
        product_id: 6,
        title: "Bazalı Çift Kişilik Karyola",
        category: "Yatak Odaları",
        thumbnail: "https://platincdn.com/516/pictures/thumb/460X-270X-DRARQMOEUX1102023181115_kapadokya-bazali-karyola-ve-baslik.jpg",
        price: 9500,
        active: 1,
        stock: 12,
        images: [{ image_id: 106, image_url: "err", alt_text: "Karyola" }]
    },

    {
        product_id: 7,
        title: "L Tipi Köşe Koltuk Takımı - Gri",
        category: "Oturma Odaları",
        thumbnail: "https://platincdn.com/4164/pictures/thumb/1000X-666X-UZCBIPCEKB7112025154230_pier-kose-imh-6.jpg",
        price: 24000,
        active: 1,
        stock: 3,
        images: [{ image_id: 107, image_url: "err", alt_text: "Köşe Koltuk" }]
    },
    {
        product_id: 8,
        title: "Masif Ahşap TV Ünitesi",
        category: "Oturma Odaları",
        thumbnail: "https://www.viserdi.com/images/urunler/elit-masif-ahsap-tv-unitesi-775_2.webp",
        price: 5400,
        active: 1,
        stock: 15,
        images: [{ image_id: 108, image_url: "err", alt_text: "TV Ünitesi" }]
    },

    {
        product_id: 9,
        title: "Mermer Desenli Mutfak Masası (4 Kişilik)",
        category: "Mutfak Mobilyaları",
        thumbnail: "https://witcdn.medusahome.com.tr/alessa-yemek-masasi-sabit-yemek-masalari-254940-36-B.jpg",
        price: 7800,
        active: 1,
        stock: 6,
        images: [{ image_id: 109, image_url: "err", alt_text: "Mutfak Masası" }]
    },
    {
        product_id: 10,
        title: "Çok Amaçlı Mutfak Dolabı/Kiler",
        category: "Mutfak Mobilyaları",
        thumbnail: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVTtwQoPXI7D1kOWApEuk5sC4HUeUPPrYCQ&s",
        price: 3250,
        active: 1,
        stock: 20,
        images: [{ image_id: 110, image_url: "err", alt_text: "Mutfak Dolabı" }]
    },

    {
        product_id: 11,
        title: "Ergonomik Yönetici Koltuğu",
        category: "Çalışma Odaları",
        thumbnail: "https://forsit.com.tr/cdn/shop/files/omega-ahsap-yonetici-koltugu-02.jpg?v=1722425343",
        price: 4200,
        active: 1,
        stock: 9,
        images: [{ image_id: 111, image_url: "err", alt_text: "Ofis Koltuğu" }]
    },
    {
        product_id: 12,
        title: "5 Raflı Dekoratif Kitaplık",
        category: "Çalışma Odaları",
        thumbnail: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSK5DVahDYYbABdF2D1khCcIsfn6ZIgUrurKw&s",
        price: 2100,
        active: 1,
        stock: 0,
        images: [{ image_id: 112, image_url: "err", alt_text: "Kitaplık" }]
    }
],
        categories: [
    {
        category_id: 1,
        category_name: "Genç Odaları",
        categorySlug: "genc-odalari"
    },
    {
        category_id: 2,
        category_name: "Bebek Odaları",
        categorySlug: "bebek-odalari"
    },
    {
        category_id: 3,
        category_name: "Yatak Odaları",
        categorySlug: "yatak-odalari"
    },
    {
        category_id: 4,
        category_name: "Oturma Odaları",
        categorySlug: "oturma-odalari"
    },
    {
        category_id: 5,
        category_name: "Mutfak Mobilyaları",
        categorySlug: "mutfak-mobilyalari"
    },
    {
        category_id: 6,
        category_name: "Çalışma Odaları",
        categorySlug: "calisma-odalari"
    }
],

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