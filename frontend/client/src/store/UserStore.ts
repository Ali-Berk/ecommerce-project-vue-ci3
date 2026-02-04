import { defineStore } from 'pinia';
import axios from "axios";
import { Product } from "@/store/ProductsStore";

interface Address {
    address: string;
    city: string;
    district: string;
}

interface CartItem {
    product_id: number;
    title: string;
    price: number;
    thumbnail: string;
    qty: number;
}

interface User {
    user_id: number;
    mail: string;
    name: string;
    password?: string;
    address: string;
    role_fk: number;
    is_verified: number;
    token?: string;
    guest: number;
    tel: string;
    birthday: Date;
    info: string;
    promotional: boolean;
}

export const useUserStore = defineStore('User', {
    state: () => ({
        status: "undefined",
        user: null as User | null,
        cart: [] as CartItem[], 
        address: null as Address | null
    }),
    actions: {
        authVerify() {
            axios.get("http://localhost:8080/api/checkLogin", { withCredentials: true })
                .then(res => {
                    if (res.data.status === 'success') {
                        localStorage.setItem('user', res.data.user);
                        this.address = res.data.address;
                        this.user = res.data.user;
                        this.status = res.data.status;
                        this.cart = res.data.cart ? res.data.cart : [] ;
                    } else {
                        this.user = null;
                        const rawData: string | null = localStorage.getItem('cart');
                        this.cart = rawData ? JSON.parse(rawData) : [];
                        this.status = res.data.status;
                    }
                })
                .catch(err => console.error("Kullanıcı doğrulama hatası:", err));
        },

        sessionDestroy() {
            this.user = null;
            this.cart = [];
            localStorage.removeItem('user');
        },

        addToCart(product: Product | CartItem) {
            const item = this.cart.find(p => p.product_id === product.product_id);
            const fetchData = {
                    product_id: product.product_id,
                    qty: 1  
                };
            if (item) {
                item.qty++;
                fetchData.qty = item.qty;
            }else {
                this.cart.push({
                    product_id: product.product_id,
                    title: product.title,
                    price: product.price,
                    thumbnail: product.thumbnail,
                    qty: 1
                });
            }
            if(this.status == "success"){            
                axios.post(`http://localhost:8080/api/add_to_cart/${this.user?.user_id}`, fetchData, {withCredentials:true});
            }
            localStorage.setItem('cart',JSON.stringify(this.cart));
        },

        removeFromCart(product: Product | CartItem) {
            const index = this.cart.findIndex(x => x.product_id === product.product_id);
            if (index > -1) {
                this.cart.splice(index, 1);
            }
            if(this.status == "success"){
                axios.post(`http://localhost:8080/api/remove_from_cart/${this.user?.user_id}`, product.product_id, {withCredentials:true} );
            }
            else{
                localStorage.setItem('cart',JSON.stringify(this.cart));
            }
        },

        decreaseCart(product: Product | CartItem){
            const item = this.cart.find(p => p.product_id === product.product_id);
            const fetchData = {
                    product_id: product.product_id,
                    qty: 1  
            };
            if (item) {
                if(item.qty > 1){
                    item.qty--;
                    fetchData.qty = item.qty;
                }else{
                    this.removeFromCart(product);
                    return;
                }
            }
            if(this.status == "success"){            
                axios.post(`http://localhost:8080/api/add_to_cart/${this.user?.user_id}`, fetchData, {withCredentials:true});
            }
            localStorage.setItem('cart',JSON.stringify(this.cart));
        },
        updateProfile(data: User) {
            axios.post("http://localhost:8080/api/update_profile", data, { withCredentials: true });
        }
    }
});