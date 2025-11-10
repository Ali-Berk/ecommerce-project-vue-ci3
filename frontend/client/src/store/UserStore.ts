import {defineStore} from 'pinia';
import axios from "axios";

export const useUserStore = defineStore('User',{
    state: () => ({
        status:"undefined",
        user:{}
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
    }

})