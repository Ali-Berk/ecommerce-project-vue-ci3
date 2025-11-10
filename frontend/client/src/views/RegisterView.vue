<template>
  <div class="register-page d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
      <h3 class="text-center mb-4">Hesap Oluştur</h3>
      
      <form @submit.prevent="handleRegister" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label">Ad Soyad</label>
          <input
            type="text"
            class="form-control"
            id="name"
            v-model="name"
            required
          />
          <div class="invalid-feedback1" v-if="submitted && !name">
            Ad Soyad zorunludur.
          </div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="email"
            v-model="email"
            required
          />
          <div class="invalid-feedback1" v-if="submitted && !email">
            Email zorunludur.
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
            Şifre zorunludur.
          </div>
        </div>

        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Şifreyi Onayla</label>
          <input
            type="password"
            class="form-control"
            id="confirmPassword"
            v-model="confirmPassword"
            required
          />
          <div class="invalid-feedback1" v-if="submitted && password !== confirmPassword">
            Şifreler eşleşmiyor.
          </div>
        </div>
        <div class="terms form-check">
            <input type="checkbox" class="form-check-input" v-model="term1" required>
            <label class="form-check-label mx-2" >*İnternet Aydınlatma Metnini Okudum.</label>
        </div>
        <div class="form-check terms">
            <input type="checkbox" class="form-check-input" v-model="term2" required>
            <label class="form-check-label mx-2" >*Yurt Dışı Veri Aktarım Onay Metni'ni okudum.</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Kayıt Ol</button>
      </form>

      <div class="text-center mt-3">
        <router-link to="/login">Zaten hesabınız var mı? Giriş Yap</router-link>
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
      confirmPassword: "",
      submitted: false,
      term1:false,
      term2:false,
    };
  },
  methods: {
    handleRegister() {
      this.submitted = true;

      if (!this.name || !this.email || !this.password || this.password !== this.confirmPassword || !this.term1 || !this.term2) {
        return;
      }
      axios.post('http://localhost:8080/api/signin',{
        name: this.name,
        mail: this.email,
        password: this.password,

      }).catch(err => console.log(err))
      // console.log("Kayıt bilgileri:", {
      //   name: this.name,
      //   email: this.email,
      //   password: this.password,
      // });

      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
.register-page {
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

.terms{
    font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
}

.invalid-feedback1{
  color: #dc3545;
}
</style>
