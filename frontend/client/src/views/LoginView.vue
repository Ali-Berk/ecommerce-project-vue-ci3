<template>
  <div class="login-page d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
      <h3 class="text-center mb-4">Giriş Yap</h3>
      
      <form @submit.prevent="handleLogin" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="mail"
            v-model="email"
            required
          />
          <div class="invalid-feedback1" v-if="submitted && !email">
            Email alanı zorunludur.
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Şifre</label>
          <input
            type="password"
            class="form-control"
            id="password"
            v-model="password"
            required
          />
          <div class="invalid-feedback1" v-if="submitted && !password">
            Şifre alanı zorunludur.
          </div>
        </div>
        <div class="mb-3 invalid-feedback1" v-if="err">
          Kullanıcı Adı veya Şifre hatalı!
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2">Giriş Yap</button>
      </form>

      <div class="text-center mt-3">
        <router-link to="/register">Hesap oluştur</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: "LoginView",
  emits:['sessionCreate'],
  data() {
    return {
      email: "",
      password: "",
      submitted: false,
      err: null
    };
  },
  methods: {
    async handleLogin() {
      this.submitted = true;

      if (!this.email || !this.password) {
        return; 
      }
      const {data} = await axios.post('http://localhost:8080/api/login',{
        mail: this.email,
        password: this.password
      }, {withCredentials:true} )
      console.log(data);
      if(data.status == "success") {
        this.$router.push('/');
        window.dispatchEvent(new CustomEvent('session-created'))
      }
      else{
        this.err = true;
      }
    }
  }
};
</script>

<style scoped>
.login-page {
  background: linear-gradient(135deg, #6c5ce7, #a29bfe);
}

.card {
  border-radius: 1rem;
}

.form-control:invalid {
  border-color: #dc3545;
}

.btn-primary {
  background-color: #6c5ce7;
  border-color: #6c5ce7;
}

.btn-primary:hover {
  background-color: #5a4ddf;
  border-color: #5a4ddf;
}

.invalid-feedback1{
  color: #dc3545;
}
</style>
