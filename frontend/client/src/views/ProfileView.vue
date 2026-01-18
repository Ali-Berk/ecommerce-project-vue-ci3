<template>
  <div class="profile-page-wrapper">
    <div class="container pb-5"> <div class="row g-4">
        
        <div class="col-lg-3">
          <div class="sidebar-card bg-white shadow-sm rounded-4 p-4 sticky-menu">
            
            <div class="text-center mb-4 pb-4 border-bottom">
              <div class="position-relative d-inline-block mb-3">
                <img :src="user.avatar || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
                     class="avatar-img rounded-circle shadow-sm">
                <button class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 border-2 border-white edit-avatar-btn">
                  <i class="bi bi-camera-fill"></i>
                </button>
              </div>
              <h5 class="fw-bold mb-1">{{ user.name }}</h5>
              <p class="text-muted small mb-0">{{ user.mail }}</p>
            </div>

            <nav class="nav flex-column gap-2 profile-nav">
              <router-link to="/profile" class="nav-link">
                <span class="icon-wrapper"><i class="bi bi-person"></i></span>
                Profil Bilgileri
              </router-link>
              <router-link to="/orders" class="nav-link">
                <span class="icon-wrapper"><i class="bi bi-box-seam"></i></span>
                Siparişlerim
              </router-link>
              <router-link to="/address" class="nav-link">
                <span class="icon-wrapper"><i class="bi bi-geo-alt"></i></span>
                Adreslerim
              </router-link>
              <router-link to="/favorites" class="nav-link">
                <span class="icon-wrapper"><i class="bi bi-heart"></i></span>
                Favorilerim
              </router-link>
              <hr class="my-2 opacity-10">
              <button @click="logout" class="nav-link text-danger w-100 text-start">
                <span class="icon-wrapper bg-danger-subtle text-danger"><i class="bi bi-box-arrow-right"></i></span>
                Çıkış Yap
              </button>
            </nav>
          </div>
        </div>

        <div class="col-lg-9">
          
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="stat-card bg-primary text-white p-3 rounded-4 d-flex align-items-center justify-content-between">
                <div>
                  <h3 class="fw-bold mb-0">{{ OrdersStore.activeOrderCount}}</h3>
                  <small class="opacity-75">Aktif Sipariş</small>
                </div>
                <i class="bi bi-box-seam fs-1 opacity-50"></i>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card bg-white shadow-sm p-3 rounded-4 d-flex align-items-center justify-content-between border">
                <div>
                  <h3 class="fw-bold mb-0 text-dark">5</h3>
                  <small class="text-muted">Favori Ürün</small>
                </div>
                <i class="bi bi-heart fs-1 text-danger opacity-50"></i>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card bg-white shadow-sm p-3 rounded-4 d-flex align-items-center justify-content-between border">
                <div>
                  <h3 class="fw-bold mb-0 text-dark">150₺</h3>
                  <small class="text-muted">İndirim Kuponu</small>
                </div>
                <i class="bi bi-ticket-perforated fs-1 text-warning opacity-50"></i>
              </div>
            </div>
          </div>

          <div class="bg-white shadow-sm rounded-4 p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="fw-bold mb-0">Kişisel Bilgiler</h4>
              <button class="btn btn-light rounded-pill px-4 text-primary fw-bold" v-if="!isEditing" @click="isEditing = true">
                <i class="bi bi-pencil me-2"></i>Düzenle
              </button>
            </div>

            <form @submit.prevent="saveProfile">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-floating custom-form-floating">
                    <input type="text" class="form-control" id="nameInput" placeholder="Adınız" v-model="form.name" :disabled="!isEditing">
                    <label for="nameInput">Ad Soyad</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating custom-form-floating">
                    <input type="email" class="form-control" id="emailInput" placeholder="Email" v-model="form.mail" disabled>
                    <label for="emailInput">E-Posta Adresi</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating custom-form-floating">
                    <input type="tel" class="form-control" id="phoneInput" placeholder="Telefon" v-model="form.phone" :disabled="!isEditing">
                    <label for="phoneInput">Telefon Numarası</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating custom-form-floating">
                    <input type="date" class="form-control" id="birthInput" v-model="form.birthDate" :disabled="!isEditing">
                    <label for="birthInput">Doğum Tarihi</label>
                  </div>
                </div>
                 <div class="col-12">
                   <label class="d-block text-muted small fw-bold mb-2">Cinsiyet</label>
                   <div class="d-flex gap-3">
                     <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="gender" id="genderM" value="male" v-model="form.gender" :disabled="!isEditing">
                        <label class="form-check-label" for="genderM">Erkek</label>
                     </div>
                     <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="gender" id="genderF" value="female" v-model="form.gender" :disabled="!isEditing">
                        <label class="form-check-label" for="genderF">Kadın</label>
                     </div>
                   </div>
                 </div>
              </div>

              <div class="d-flex gap-3 mt-5" v-if="isEditing">
                <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold">Değişiklikleri Kaydet</button>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2" @click="cancelEdit">İptal</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useOrdersStore } from '@/store/OrdersStore';
export default {
  name: "ProfileView",
  props: ['user'],
  data() {
    return {
      isEditing: false,
      form: {
        name: this.user?.name || '',
        mail: this.user?.mail || '',
        phone: this.user?.phone || '',
        birthDate: this.user?.birthDate || '',
        gender: this.user?.gender || 'male'
      }
    };
  },
  watch: {
    user: {
      handler(newVal) {
        if(newVal) {
          this.form = { ...this.form, ...newVal };
        }
      },
      deep: true
    }
  },
  computed:{
    OrdersStore() {
      return useOrdersStore();
    },
  },
  methods: {
    logout() {
       axios.get('http://localhost:8080/api/logout')
        .then(() => location.reload());
    },
    saveProfile() {
      console.log("Kaydedilen veriler:", this.form);
      this.isEditing = false;
    },
    cancelEdit() {
      this.form = { ...this.form, ...this.user };
      this.isEditing = false;
    }
  }
}
</script>

<style scoped>
.profile-page-wrapper {
  background-color: #f3f4f6;
  min-height: 100vh;
  padding-top: 120px; 
}

/* Sidebar */
.avatar-img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border: 4px solid #fff;
}

.edit-avatar-btn {
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;
}

.profile-nav .nav-link {
  color: #64748b; font-weight: 500; padding: 12px 15px; border-radius: 12px; transition: all 0.2s; display: flex; align-items: center;
}

.profile-nav .icon-wrapper {
  width: 36px; height: 36px; border-radius: 10px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-right: 12px; color: #475569; transition: all 0.2s;
}

.profile-nav .nav-link:hover, .profile-nav .router-link-exact-active {
  background-color: #fff; color: #000; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.profile-nav .nav-link:hover .icon-wrapper, .profile-nav .router-link-exact-active .icon-wrapper {
  background-color: #000; color: #fff;
}

.stat-card { transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }

.custom-form-floating .form-control { border: 1px solid #e2e8f0; border-radius: 12px; background-color: #f8fafc; }
.custom-form-floating .form-control:focus { background-color: #fff; border-color: #000; box-shadow: 0 0 0 4px rgba(0,0,0,0.05); }
.custom-form-floating .form-control:disabled { background-color: #f1f5f9; opacity: 0.7; border: none; }
.custom-form-floating label { color: #94a3b8; }
.custom-radio .form-check-input:checked { background-color: #000; border-color: #000; }

.sticky-menu {
  position: sticky;
  top: 120px; 
  z-index: 1;
}
</style>