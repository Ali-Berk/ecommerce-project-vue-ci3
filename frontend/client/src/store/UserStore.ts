import {defineStore} from 'pinia';
import axios from "axios";

export const useUserStore = defineStore('User',{
    state: () => ({
        status:"undefined",
        user:{} as any,
        cart:[] as any[]
    }),
    actions:{
        authVerify() {
            axios.get("http://localhost:8080/api/checkLogin", { withCredentials: true })
            .then(res => {
            if (res.data.status === 'success') {
            this.user = res.data.user; 
            this.status = 'success';
            }
            else{
            this.user = {};
            this.status = 'error';
            }
            })
            .catch(err => console.error("Kullanıcı doğrulama hatası:", err));
        },
        sessionDestroy(){
            this.user = {};
        },
        addToCart(product:any){
            const item = this.cart.find(p => p.product_id == product.product_id);
            
            if(item){
                item.qty++;
            }
            else{

                this.cart.push({
                    product_id:product.product_id,
                    title:product.title,
                    price:product.price,
                    thumbnail:product.thumbnail,
                    qty:1
                });
            }
        },
        removeFromCart(product_id:number){
            this.cart.splice(product_id,1);
        }
    }

})