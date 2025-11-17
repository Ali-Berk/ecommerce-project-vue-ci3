# Proje Hakkında
Bu proje, CodeIgniter backend ve Vue.js frontend kullanılarak geliştirilmiş bir e-ticaret uygulamasıdır. Ürün yönetimi ve kullanıcı etkileşimini dinamik bir şekilde sağlar. Projede ürün ekleme, düzenleme ve silme, kullanıcı yetkilendirmesi, dinamik frontend ve Pinia ile durum yönetimi gibi özellikler bulunur.

## Kurulum
Repo'yu klonlayın: `git clone https://github.com/Ali-Berk/ecommerce-project-vue-ci3.git`  

### Backend
Backend dizinine gidin: `cd backend/ci3`  
Bağımlılıkları yükleyin ve CodeIgniter yapılandırmasını tamamlayın.
**Bağımlılık Kurulumu:**  
- PHP’nin sisteminizde kurulu olduğundan emin olun (7.4+ önerilir).  
- Gerekli PHP eklentileri: `mysqli`, `curl`, `mbstring`, `json` ve `openssl` aktif olmalı.  
- Eğer Composer ile ek kütüphane kullanıyorsan:  
  `
  composer install`
  
Uygulamayı başlatın ve 8080 portunda çalışmasını sağlayın. Örnek: `php -S localhost:8080 -t public`  

### Frontend
Frontend dizinine gidin: `cd frontend/client`  
Bağımlılıkları yükleyin: `npm install`  
Uygulamayı başlatın: `npm run serve` (8081 portunda çalışacak şekilde ayarlayın)  
Tarayıcıda `http://localhost:8081` adresine gidin.

## Kullanım
Yönetici paneli üzerinden ürün ekleyebilir, düzenleyebilir veya silebilirsiniz. Kullanıcılar ürünleri görüntüleyebilir ve filtreleyebilir.

## Teknolojiler
Backend: PHP (CodeIgniter), Frontend: Vue.js, Durum Yönetimi: Pinia, Testler: E2E ve Unit Testler

## Katkıda Bulunma
Projeyi forklayın, yeni bir branch açın (`git checkout -b yeni-ozellik`), değişiklikleri commitleyin (`git commit -am 'Yeni özellik eklendi'`), branch’i pushlayın (`git push origin yeni-ozellik`) ve pull request oluşturun.

##
**Dipnot:** Frontend Vercel üzerinde hostlanmıştır: [https://ecommerce-project-vue-ci3-7rxv.vercel.app/](https://ecommerce-project-vue-ci3-7rxv.vercel.app/) — bu sürüm backend ile bağlı değildir.  
Backend API ise şu adreste yer almaktadır: [https://karauzum.page.gd/](https://karauzum.page.gd/)
