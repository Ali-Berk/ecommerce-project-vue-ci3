<template>
  <section class="settings-page container my-5">
    <h2 class="mb-4 fw-semibold">Hesap Ayarları</h2>

    <div class="row g-4">
      <!-- SOL KISIM: Profil Bilgileri -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-semibold">
            Profil Bilgileri
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile">
              <div class="mb-3">
                <label class="form-label">Ad Soyad</label>
                <input type="text" v-model="form.name" class="form-control" placeholder="Adınızı girin" />
              </div>

              <div class="mb-3">
                <label class="form-label">E-posta</label>
                <input type="email" v-model="form.email" class="form-control" placeholder="E-posta adresiniz" />
              </div>

              <div class="text-end">
                <button class="btn btn-primary px-4" type="submit">Güncelle</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- SAĞ KISIM: Şifre Değiştir -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-semibold">
            Şifre Değiştir
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile">
              <div class="mb-3">
                <label class="form-label">Mevcut Şifre</label>
                <input type="password" v-model="passwords.current" class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" v-model="passwords.new" class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label">Yeni Şifre (Tekrar)</label>
                <input type="password" v-model="passwords.confirm" class="form-control" />
              </div>

              <div class="text-end">
                <button class="btn btn-warning px-4" type="submit">Şifreyi Güncelle</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
      <div class="card-header bg-white fw-semibold">
        Kullanıcı Tercihleri
      </div>
      <div class="card-body">
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" v-model="preferences.notifications" id="notificationsSwitch" />
          <label class="form-check-label" for="notificationsSwitch">
            Bildirimleri etkinleştir
          </label>
        </div>

        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" v-model="preferences.darkMode" id="darkModeSwitch" />
          <label class="form-check-label" for="darkModeSwitch">
            Karanlık tema
          </label>
        </div>

        <div class="text-end mt-3">
          <button class="btn btn-success px-4" @click="savePreferences">
            Tercihleri Kaydet
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const form = ref({
  name: '',
  email: '',
})

const passwords = ref({
  current: '',
  new: '',
  confirm: '',
})

const preferences = ref({
  notifications: true,
  darkMode: false,
})

const updateProfile = async () => {
  try {
    await axios.post("http://localhost:8080/api/update_profile", form.value, {headers: { "Content-Type": "application/json" }, withCredentials: true});

    alert('Profil bilgileri başarıyla güncellendi.');
    console.log(form.value);
  } catch (error) {
    console.error(error)
    alert('Profil güncellenirken bir hata oluştu.')
  }
}

// const updatePassword = async () => {
//   if (passwords.value.new !== passwords.value.confirm) {
//     alert('Yeni şifreler eşleşmiyor.')
//     return
//   }
//   try {
//     await axios.post('/api/update-password', passwords.value)
//     alert('Şifre başarıyla güncellendi.')
//   } catch (error) {
//     console.error(error)
//     alert('Şifre güncellenirken bir hata oluştu.')
//   }
// }

const savePreferences = async () => {
  try {
    await axios.post('/api/save-preferences', preferences.value)
    alert('Tercihler kaydedildi.')
  } catch (error) {
    console.error(error)
    alert('Tercihler kaydedilirken hata oluştu.')
  }
}
</script>

<style scoped>
.settings-page h2 {
  font-weight: 600;
}
.card {
  border-radius: 12px;
}
.form-label {
  font-weight: 500;
}
</style>
