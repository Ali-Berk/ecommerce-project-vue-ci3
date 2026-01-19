<template>
  <div class="row g-0 vh-100 bg-white">
    <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden">
      <img 
        src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=2158&auto=format&fit=crop" 
        alt="Login Cover" 
        class="w-100 h-100 object-fit-cover"
      >
      <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
      <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
        <h2 class="fw-bold">Evinize Hoş Geldiniz.</h2>
        <p class="lead">Tarzınızı yansıtan mobilyalarla yaşam alanınızı yeniden keşfedin.</p>
      </div>
    </div>

    <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
      <div class="w-100" style="max-width: 420px;">
        
        <div class="text-center mb-5">
          <i class="bi bi-box-seam fs-1 text-primary mb-3"></i> <h3 class="fw-bold">Giriş Yap</h3>
          <p class="text-muted">Devam etmek için hesabınıza giriş yapın.</p>
        </div>
        
        <form @submit.prevent="handleLogin" novalidate>
          <div class="form-floating mb-3">
            <input
              type="email"
              class="form-control"
              id="mail"
              v-model="email"
              placeholder="name@example.com"
              :class="{ 'is-invalid': submitted && !email }"
              required
            />
            <label for="mail">E-posta Adresi</label>
            <div class="invalid-feedback" v-if="submitted && !email">
              Email alanı zorunludur.
            </div>
          </div>

          <div class="form-floating mb-3">
            <input
              type="password"
              class="form-control"
              id="password"
              v-model="password"
              placeholder="Şifre"
              :class="{ 'is-invalid': submitted && !password }"
              required
            />
            <label for="password">Şifre</label>
            <div class="invalid-feedback" v-if="submitted && !password">
              Şifre alanı zorunludur.
            </div>
          </div>

          <div class="alert alert-danger d-flex align-items-center" v-if="err">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <div>Kullanıcı Adı veya Şifre hatalı!</div>
          </div>

          <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-pill shadow-sm login-btn" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
            {{ isLoading ? 'Giriş Yapılıyor...' : 'Giriş Yap' }}
          </button>
        </form>

        <div class="text-center mt-4">
          <span class="text-muted">Hesabınız yok mu?</span>
          <router-link to="/register" class="fw-bold text-decoration-none ms-1 text-primary">Hesap Oluştur</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "LoginView",
  emits: ['sessionCreate'],
  data() {
    return {
      email: "",
      password: "",
      submitted: false,
      err: null,
      isLoading: false 
    };
  },
  methods: {
    async handleLogin() {
      this.submitted = true;
      this.err = null; 

      if (!this.email || !this.password) {
        return; 
      }

      this.isLoading = true;
      let response;

      try {
        const { data } = await axios.post('http://localhost:8080/api/login', {
          mail: this.email,
          password: this.password
        }, { withCredentials: true });
        response = data;
      } catch (error) {
        response = { 'status': 'error', 'message': 'Çok Fazla İstek Atıldı.' };
        console.error(error);
      } finally {
        this.isLoading = false;
      }

      if (response?.status == "success") {
        this.$router.push('/');
        window.dispatchEvent(new CustomEvent('session-created'));
      } else {
        this.err = true;
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
</style>