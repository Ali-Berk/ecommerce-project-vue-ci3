<template>
  <div class="row g-0 vh-100 bg-white">
    
    <div class="toast-container" v-if="toast.show">
      <div class="custom-toast" :class="toast.type === 'error' ? 'toast-error' : 'toast-success'">
        <div class="d-flex align-items-center">
          <i class="bi fs-4 me-3" :class="toast.type === 'error' ? 'bi-x-circle-fill text-danger' : 'bi-check-circle-fill text-success'"></i>
          <div>
            <h6 class="fw-bold mb-0">{{ toast.type === 'error' ? 'Hata' : 'Başarılı' }}</h6>
            <small class="text-muted">{{ toast.message }}</small>
          </div>
          <button @click="toast.show = false" class="btn-close ms-auto"></button>
        </div>
      </div>
    </div>

    <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden">
      <img 
        src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=2000&auto=format&fit=crop" 
        alt="Register Cover" 
        class="w-100 h-100 object-fit-cover"
      >
      <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
      <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
        <h2 class="fw-bold">Aramıza Katılın.</h2>
        <p class="lead">Size özel fırsatlardan yararlanmak için hemen üye olun.</p>
      </div>
    </div>

    <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
      <div class="w-100" style="max-width: 420px;">
        
        <div class="text-center mb-4">
           <h3 class="fw-bold">Hesap Oluştur</h3>
           <p class="text-muted">Formu eksiksiz doldurarak kayıt olun.</p>
        </div>
        
        <form @submit.prevent="handleRegister" novalidate>
          
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="name" v-model="name" placeholder="Ad Soyad" required>
            <label for="name">Ad Soyad</label>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="registerMail" v-model="email" placeholder="Email" required>
            <label for="registerMail">E-posta Adresi</label>
          </div>

          <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="tel" v-model="tel" placeholder="Telefon" required>
            <label for="tel">Telefon</label>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="address" v-model="address" placeholder="Adres" style="height: 80px"></textarea>
            <label for="address">Adres (İsteğe Bağlı)</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="registerPassword" v-model="password" placeholder="Şifre" required>
            <label for="registerPassword">Şifre</label>
          </div>

          <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-pill shadow-sm login-btn" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
            {{ isLoading ? 'Kaydediliyor...' : 'Kayıt Ol' }}
          </button>
        </form>

        <div class="text-center mt-4">
          <span class="text-muted">Zaten hesabınız var mı?</span>
          <router-link to="/login" class="fw-bold text-decoration-none ms-1 text-primary">Giriş Yap</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "RegisterView",
  data() {
    return {
      name: "",
      email: "",
      password: "",
      tel: "",
      address: "",
      isLoading: false,
      toast: {
        show: false,
        message: "",
        type: "error" 
      }
    };
  },
  methods: {
    showNotification(message, type = 'error') {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      
      setTimeout(() => {
        this.toast.show = false;
      }, 4000);
    },

    validateForm() {
      if (!this.name || this.name.length < 3) {
        this.showNotification("Ad Soyad en az 3 karakter olmalıdır.");
        return false;
      }

      // const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const englishEmailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!this.email) {
        this.showNotification("Lütfen geçerli bir E-Posta adresi giriniz.");
        return false;
      }
      if (!englishEmailPattern.test(this.email)) {
        this.showNotification("Lütfen E-Posta adresinde Türkçe karakter (ö,ü,ş,ı,ğ,ç) kullanmayınız.");
        return false;
      }
      if (!this.password || this.password.length < 6) {
        this.showNotification("Şifre en az 6 karakter olmalıdır.");
        return false;
      }

      
      return true;
    },

    async handleRegister() {
      if (!this.validateForm()) return;

      this.isLoading = true;
      try {
        const response = await axios.post('http://localhost:8080/api/signin', {
          name: this.name,
          mail: this.email,
          password: this.password,
          tel: this.tel,
          address: this.address
        });

        if (response.data.status === 'success') {
          this.showNotification("Kayıt Başarılı! Yönlendiriliyorsunuz...", "success");
          
          setTimeout(() => {
             this.$router.push('/login');
          }, 1500);
        } else {
          this.showNotification(response.data.message || "Kayıt işlemi başarısız oldu.");
        }
      } catch (error) {
        console.error(error);
        this.showNotification("Sunucu ile bağlantı kurulamadı.");
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
  color: #000;
  opacity: 0.65;
}
.form-control:focus {
  border-color: #000;
  box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.1);
}
.login-btn {
  transition: transform 0.2s;
}
.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  background-color: #000;
}

.toast-container {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 1055;
}

.custom-toast {
  background: #fff;
  min-width: 300px;
  padding: 16px 20px;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
  animation: slideIn 0.4s ease-out forwards;
  border-left: 6px solid transparent;
}

.toast-success {
  border-left-color: #198754;
}

.toast-error {
  border-left-color: #dc3545;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>