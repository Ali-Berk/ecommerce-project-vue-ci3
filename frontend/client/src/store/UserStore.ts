import { defineStore } from 'pinia';
import axios from "axios";
import { Product } from "@/store/ProductsStore";

interface Address {
    address: string;
    city: string;
    district: string;
}

// 1. DÜZELTME: İsimlendirmeyi 'CartItem' (Sepet Öğesi) yaptık ve [] işaretini kaldırdık.
// Çünkü bu interface sadece TEK BİR ürünü temsil ediyor.
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
                        this.address = res.data.address;
                        this.user = res.data.user;
                        this.status = res.data.status;
                    } else {
                        this.user = null;
                        this.status = res.data.status;
                    }
                })
                .catch(err => console.error("Kullanıcı doğrulama hatası:", err));
        },

        sessionDestroy() {
            this.user = null;
            this.cart = [];
        },

        addToCart(product: Product | CartItem) {
            const item = this.cart.find(p => p.product_id === product.product_id);

            if (item) {
                item.qty++;
            } else {
                this.cart.push({
                    product_id: product.product_id,
                    title: product.title,
                    price: product.price,
                    thumbnail: product.thumbnail,
                    qty: 1
                });
            }
        },

        removeFromCart(product_id: number) {
            const index = this.cart.findIndex(x => x.product_id === product_id);
            if (index !== -1) {
                this.cart.splice(index, 1);
            }
        },

        updateProfile(data: User) {
            axios.post("http://localhost:8080/api/update_profile", data, { withCredentials: true });
        }
    }
});