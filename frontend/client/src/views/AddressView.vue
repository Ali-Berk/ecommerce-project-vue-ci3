<template>
  <div class="address-page-container">
    <div class="address-grid">
      
      <div class="address-card new-address-card" role="button" @click="openModal()">
        <div class="icon-wrapper">
          <span class="plus-icon">+</span>
        </div>
        <span class="new-address-text">Yeni Adres Ekle</span>
      </div>

      <div class="address-card" @click="setDefault()">
        <div class="card-header">
          <div class="header-left">
            <h3 class="address-title">Ev Adresi</h3>
            <span class="badge default-badge">Varsayılan</span>
          </div>
          <button class="icon-btn">
            <span class="dots">•••</span>
          </button>
        </div>
        <div class="address-body">
          <div class="user-info">
            <span class="user-name">Ahmet Yılmaz</span>
            <span class="user-phone">+90 555 123 45 67</span>
          </div>
          <p class="address-text">
            Cumhuriyet Mah. Atatürk Cad. No:12 D:4
            <br>
            Çankaya / Ankara
          </p>
        </div>
        <div class="card-footer">
          <button class="btn btn-outline" @click.stop="openModal({title:'Ev Adresi', name:'Ahmet Yılmaz', tel:'+90 555 123 45 67', address:'Cumhuriyet Mah...', district:'Çankaya', city:'Ankara'})">Düzenle</button>
          <button class="btn btn-danger-ghost">Sil</button>
        </div>
      </div>

      <div class="address-card" v-for="item in userStore.address" :key="item.id" @click="setDefault(item)">
        <div class="card-header">
          <div class="header-left">
            <h3 class="address-title">{{ item.title }}</h3>
            <span class="badge default-badge" v-if="item.default == 1" >Varsayılan</span>
          </div>
          <button class="icon-btn">
            <span class="dots">•••</span>
          </button>
        </div>
        
        <div class="address-body">
          <div class="user-info">
            <span class="user-name">{{item.name}}</span>
            <span class="user-phone">{{ item.tel }}</span>
          </div>
          <p class="address-text">
            {{item.address}}
            <br>
            {{item.district}} / {{item.city}}
          </p>
        </div>

        <div class="card-footer">
          <button class="btn btn-outline" @click.stop="openModal(item)">Düzenle</button>
          <button class="btn btn-danger-ghost" @click="handleDelete(item.id,item.title)">Sil</button>
        </div>
      </div>
    </div>

    <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ isEditMode ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}</h3>
          <button class="close-btn" @click="closeModal">✕</button>
        </div>
        
        <form @submit.prevent="handleSave" class="edit-form">
          <div class="form-group">
            <label>Adres Başlığı</label>
            <input type="text" v-model="formData.title" placeholder="Örn: Ev, İş">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Ad Soyad</label>
              <input type="text" v-model="formData.name">
            </div>
            <div class="form-group">
              <label>Telefon</label>
              <input type="text" v-model="formData.tel">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>İl</label>
              <input type="text" v-model="formData.city">
            </div>
            <div class="form-group">
              <label>İlçe</label>
              <input type="text" v-model="formData.district">
            </div>
          </div>

          <div class="form-group">
            <label>Açık Adres</label>
            <textarea rows="3" v-model="formData.address"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn btn-outline" @click="closeModal">Vazgeç</button>
            <button type="submit" class="btn btn-primary-solid">
              {{ isEditMode ? 'Güncelle' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script>
import { useUserStore } from '@/store/UserStore';
import axios from 'axios';

export default {
  data() {
    return {
      isModalOpen: false,
      formData: {},
      isEditMode: false
    };
  },
  methods: {
    setDefault(data) {
      if(confirm('Varsayılan adres olarak güncellemek istediğinizden emin misiniz?')){
        console.log(data);
        axios.put('http://localhost:8080/api/set_initial_address', data, {withCredentials:true});
      }
    },
    
    openModal(item = null) {
      this.isModalOpen = true;

      if (item) {
        this.isEditMode = true;
        this.formData = { ...item };
      } else {
        this.isEditMode = false;
        this.formData = {
          title: '',
          name: '',
          tel: '',
          city: '',
          district: '',
          address: ''
        };
      }
    },

    closeModal() {
      this.isModalOpen = false;
      this.formData = {};
    },

    async handleSave() {
      if (this.isEditMode) {
        console.log("GÜNCELLENİYOR:", this.formData);
        await axios.post('http://localhost:8080/api/update_address',this.formData,{withCredentials:true});
        alert("Başarıyla Güncellendi!");
      } else {
        console.log("EKLENİYOR:", this.formData);
        axios.post('http://localhost:8080/api/add_address', this.formData,{withCredentials:true});
        alert("Yeni Adres Eklendi!");
      }

      
      this.closeModal();
    },

    handleDelete(id, title){
        if(confirm(`${title} isimli adresi silmek istediğinize emin misiniz?`)){
            axios.delete(`http://localhost:8080/api/delete_address/${id}`, {withCredentials:true});
        }
    }
  },
  computed: {
    userStore() {
      return useUserStore();
    }
  }
}
</script>

<style scoped>
.address-page-container {
  --primary-color: #2563eb;
  --primary-hover: #1d4ed8;
  --bg-surface: #ffffff;
  --bg-main: #f3f4f6;
  --text-main: #111827;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --danger-color: #ef4444;
  --radius-md: 12px;
  --radius-sm: 6px;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  margin-top: 87px;
  padding: 2rem;
  background-color: var(--bg-main);
  min-height: 100vh;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.address-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  max-width: 1280px;
  margin: 0 auto;
}

.address-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  transition: all 0.2s ease;
  position: relative;
  min-height: 240px;
}

.address-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: #d1d5db;
}

.new-address-card {
  border: 2px dashed var(--border-color);
  background-color: transparent;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-secondary);
  gap: 1rem;
}

.new-address-card:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
  background-color: #eff6ff;
}

.icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.new-address-card:hover .icon-wrapper {
  background-color: #dbeafe;
}

.plus-icon {
  font-size: 1.5rem;
  line-height: 1;
}

.new-address-text {
  font-weight: 600;
  font-size: 1rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.25rem;
}

.header-left {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.address-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-main);
  margin: 0;
  letter-spacing: -0.025em;
}

.badge {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  width: fit-content;
}

.default-badge {
  background-color: #eff6ff;
  color: var(--primary-color);
  border: 1px solid #dbeafe;
}

.icon-btn {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
}

.icon-btn:hover {
  background-color: #f3f4f6;
  color: var(--text-main);
}

.address-body {
  flex-grow: 1;
  color: var(--text-secondary);
  font-size: 0.925rem;
  line-height: 1.6;
}

.user-info {
  margin-bottom: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.user-name {
  font-weight: 600;
  color: var(--text-main);
}

.card-footer {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
  display: flex;
  gap: 0.75rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  flex: 1;
}

.btn-outline {
  background-color: transparent;
  border: 1px solid var(--border-color);
  color: var(--text-main);
}

.btn-outline:hover {
  border-color: var(--text-secondary);
  background-color: #f9fafb;
}

.btn-danger-ghost {
  background-color: transparent;
  border: 1px solid transparent;
  color: var(--danger-color);
}

.btn-danger-ghost:hover {
  background-color: #fef2f2;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  animation: fadeIn 0.2s ease-out;
}

.modal-content {
  background: white;
  width: 90%;
  max-width: 500px;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  animation: slideUp 0.3s ease-out;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: var(--text-main);
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-secondary);
}

.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-main);
}

.form-group input,
.form-group textarea {
  width: 100%;
  box-sizing: border-box;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-sm);
  font-size: 0.925rem;
  transition: border-color 0.2s;
  font-family: inherit
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-primary-solid {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  font-weight: 500;
  cursor: pointer;
  flex: 1;
}

.btn-primary-solid:hover {
  background-color: var(--primary-hover);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@media (max-width: 640px) {
  .address-grid {
    grid-template-columns: 1fr;
  }
  .address-page-container {
    padding: 1rem;
  }
  .form-row {
    grid-template-columns: 1fr; 
  }
}
</style>