<template>
  <div class="about-page">
    
    <section class="hero-section position-relative d-flex align-items-center justify-content-center text-center text-white">
      <div class="overlay"></div>
      <div class="container position-relative z-1">
        <h1 class="display-3 fw-bold mb-3">Evinizin Hikayesi Burada Başlıyor</h1>
        <p class="lead fs-4 mb-4 opacity-75">Konfor, estetik ve ustalığın buluşma noktası.</p>
        <button class="btn btn-light rounded-pill px-5 py-3 fw-bold" @click="scrollToStory">Hikayemizi Keşfedin</button>
      </div>
    </section>

    <section id="our-story" class="container my-5 py-5">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="position-relative">
            <img 
              src="https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?q=80&w=2070&auto=format&fit=crop" 
              alt="Mobilya Atölyesi" 
              class="img-fluid rounded-4 shadow-lg main-story-img"
            >
            <img 
              src="https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?q=80&w=1965&auto=format&fit=crop" 
              class="img-fluid rounded-4 shadow border border-4 border-white position-absolute bottom-0 end-0 d-none d-md-block sub-story-img"
              alt="Detay"
            >
          </div>
        </div>
        <div class="col-lg-6">
          <h5 class="text-primary fw-bold text-uppercase ls-1">Hakkımızda</h5>
          <h2 class="display-6 fw-bold mb-4 text-dark">Ahşaba Duyulan Tutkuyla Başlayan Bir Yolculuk</h2>
          <p class="text-muted fs-5 mb-4">
            2010 yılında küçük bir atölyede, sadece ahşabın kokusunu ve işçiliğin sıcaklığını seven üç kişilik bir ekiple yola çıktık. Bugün, binlerce eve konuk olmanın gururunu yaşıyoruz.
          </p>
          <p class="text-muted mb-4">
            Amacımız sadece mobilya satmak değil; yaşam alanlarınıza ruh katmak. "Kendi evimizde kullanmayacağımız hiçbir ürünü müşterimize sunmayız" ilkesiyle, her bir parçayı özenle tasarlıyor ve üretiyoruz. Modern çizgileri geleneksel ustalıkla harmanlıyoruz.
          </p>
          
          <div class="d-flex gap-4 mt-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
              <span class="fw-bold text-dark">El İşçiliği</span>
            </div>
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
              <span class="fw-bold text-dark">Sürdürülebilir Malzeme</span>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section class="bg-dark text-white py-5">
      <div class="container">
        <div class="row text-center g-4">
          <div class="col-6 col-md-3">
            <h2 class="display-4 fw-bold mb-0 text-primary">15+</h2>
            <p class="opacity-75">Yıllık Tecrübe</p>
          </div>
          <div class="col-6 col-md-3">
            <h2 class="display-4 fw-bold mb-0 text-primary">5000+</h2>
            <p class="opacity-75">Mutlu Müşteri</p>
          </div>
          <div class="col-6 col-md-3">
            <h2 class="display-4 fw-bold mb-0 text-primary">350+</h2>
            <p class="opacity-75">Özgün Tasarım</p>
          </div>
          <div class="col-6 col-md-3">
            <h2 class="display-4 fw-bold mb-0 text-primary">2</h2>
            <p class="opacity-75">Şehirde Mağaza</p>
          </div>
        </div>
      </div>
    </section>

    <section class="container my-5 py-5">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Bizi Farklı Kılan Değerler</h2>
        <p class="text-muted">Mobilyadan fazlasını sunmak için çalışıyoruz.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="value-card p-4 rounded-4 bg-light h-100 text-center transition-up">
            <div class="icon-box bg-white text-primary shadow-sm rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
              <i class="bi bi-gem fs-2"></i>
            </div>
            <h4 class="fw-bold mb-3">Kalite & Estetik</h4>
            <p class="text-muted">
              Görünenin ötesine bakıyoruz. Kumaşın dokusundan iskeletin sağlamlığına kadar her detayda en iyisini hedefliyoruz.
            </p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="value-card p-4 rounded-4 bg-light h-100 text-center transition-up">
            <div class="icon-box bg-white text-primary shadow-sm rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
              <i class="bi bi-palette fs-2"></i>
            </div>
            <h4 class="fw-bold mb-3">Özgün Tasarım</h4>
            <p class="text-muted">
              Modası geçen trendler yerine, her döneme hitap eden, modern çizgileri klasik dokunuşlarla harmanlayan tasarımlar sunuyoruz.
            </p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="value-card p-4 rounded-4 bg-light h-100 text-center transition-up">
            <div class="icon-box bg-white text-primary shadow-sm rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
              <i class="bi bi-emoji-smile fs-2"></i>
            </div>
            <h4 class="fw-bold mb-3">Müşteri Mutluluğu</h4>
            <p class="text-muted">
              Satışla biten değil, satışla başlayan bir dostluk. Satış sonrası destek ekibimizle her zaman yanınızdayız.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="container my-5 pb-5">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Tasarımın Arkasındaki Yüzler</h2>
      </div>
      <div class="row g-4 justify-content-center">
        <div v-for="member in team" :key="member.id" class="col-md-6 col-lg-3">
          <div class="team-card text-center">
            <div class="mb-3 position-relative d-inline-block">
              <img :src="member.image" class="rounded-circle shadow team-img" alt="Team">
            </div>
            <h5 class="fw-bold mb-1">{{ member.name }}</h5>
            <small class="text-muted text-uppercase ls-1">{{ member.role }}</small>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section bg-primary-subtle py-5 text-center rounded-5 container mb-5">
      <div class="p-4">
        <h2 class="fw-bold mb-3">Evinizi Yenilemeye Hazır mısınız?</h2>
        <p class="text-muted mb-4 fs-5">En yeni koleksiyonlarımızı görmek için mağazamızı veya online kataloğumuzu ziyaret edin.</p>
        <div class="d-flex gap-3 justify-content-center">
          <router-link to="/" class="btn btn-dark rounded-pill px-5 py-3 fw-bold">Alışverişe Başla</router-link>
          <button class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold">İletişime Geç</button>
        </div>
      </div>
    </section>

  </div>
</template>

<script>
export default {
  name: 'AboutView',
  data() {
    return {
      team: [
        { 
          id: 1, 
          name: 'Ahmet Yılmaz', 
          role: 'Kurucu & Baş Tasarımcı', 
          image: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop' 
        },
        { 
          id: 2, 
          name: 'Elif Demir', 
          role: 'İç Mimar', 
          image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1976&auto=format&fit=crop' 
        },
        { 
          id: 3, 
          name: 'Caner Öztürk', 
          role: 'Üretim Müdürü', 
          image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=2070&auto=format&fit=crop' 
        },
        { 
          id: 4, 
          name: 'Zeynep Kaya', 
          role: 'Müşteri İlişkileri', 
          image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=1961&auto=format&fit=crop' 
        }
      ]
    }
  },
  methods: {
    scrollToStory() {
      const element = document.getElementById("our-story");
      if(element) element.scrollIntoView({ behavior: "smooth" });
    }
  }
}
</script>

<style scoped>
.hero-section {
  height: 60vh;
  background-image: url('https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=2000&auto=format&fit=crop');
  background-size: cover;
  background-position: center;
  margin-top: -20px; 
}

.hero-section .overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5); 
}

.main-story-img {
  width: 100%;
  height: 500px;
  object-fit: cover;
}

.sub-story-img {
  width: 250px;
  height: 250px;
  object-fit: cover;
  transform: translate(20%, 20%);
}

.ls-1 {
  letter-spacing: 1px;
}

.value-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.value-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  background-color: #fff !important;
}

.icon-box {
  width: 80px;
  height: 80px;
}
.team-img {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border: 5px solid #fff;
  transition: transform 0.3s ease;
}

.team-card:hover .team-img {
  transform: scale(1.05);
  border-color: #f8f9fa;
}

@media (max-width: 991px) {
  .sub-story-img {
    display: none;
  }
  .main-story-img {
    height: 350px;
  }
  .hero-section {
    height: 50vh;
  }
}
</style>