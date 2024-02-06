<style>
    .wrapper-img-banner {
        max-width: 100%;
        max-height: 400px;
    }

    .img-banner {
        width: 100%;
    }
</style>
<div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="wrapper-img-banner">
            <img src="/img/banner.jpg" class="img-banner" alt="Crousel">
        </div>

        <div class="container">
          <div class="carousel-caption text-start">
            <h1>Example headline.</h1>
            <p>Some representative placeholder content for the first slide of the carousel.</p>
            <p><a class="btn btn-lg btn-primary" href="#">Sign up today</a></p>
          </div>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container mt-3">
    <div class="text-center">
        <h4 class="">About</h4>
        <p>Anda Tidak Tahu Kami??, Akan Kami Beri Tahu Anda</p>
    </div>
    <div class="row">
        <div class="col-md-6">
            <img src="/img/banner.jpg" width="100%" class="" alt=""/>
        </div>
        <div class="col-md-6">
            <p> 
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Ratione modi dolor illum illo soluta, temporibus ad dignissimos obcaecati eaque perspiciatis nostrum sit rerum a ut ea voluptate ipsa perferendis sapiente? Porro quod cupiditate quibusdam repellat officiis adipisci beatae, nisi necessitatibus consequatur! Animi recusandae nostrum debitis obcaecati? Rem magnam consectetur tempora quo, aliquam maxime in sed sunt quaerat alias voluptas, ipsa culpa praesentium voluptatem aut hic explicabo temporibus inventore? A modi praesentium sit! Dolores sed quod soluta, deleniti ipsam eum doloribus, est sapiente illum voluptatum adipisci quae obcaecati quaerat nemo ab necessitatibus dolorem eveniet magni repellat ad optio officiis! Cum, quae. 
            </p>
            <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum maxime unde facere repudiandae fugiat voluptate officiis tempora, odit reprehenderit veniam nostrum vitae, debitis, obcaecati asperiores veritatis minus similique amet ea. Inventore in laboriosam soluta, explicabo accusantium magnam tenetur rerum ducimus dignissimos aperiam repudiandae sint assumenda quo fugiat ea neque ipsam?
            </p>
        </div>
    </div>
</div>

<div class="bg-success my-5">
  <div class="container py-5">
    <div class="text-white">
      <h5>Pelajari tentang kami</h5>
      <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eius quidem quam quos consequatur, nulla enim voluptates accusantium illum sapiente laborum magni obcaecati mollitia omnis ut.</p>
    </div>
  </div>
</div>

<div class="container my-3">
  <div class="text-center">
      <h4 class="">Sevices</h4>
      <p>Apa Yang Kami Lakukan?, Akan Kami Beri Tahu Anda</p>
  </div>

  <div class="row mt-3">
    @for ($i = 0; $i < 4; $i++)
    <div class="col-md-3">
      <div class="text-center">
        <i class="fas fa-home fa-3x text-success"></i>
        <h5><b>Penanaman Pohon</b></h5>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Error, fugiat.</p>
      </div>
    </div>
      @endfor
  </div>
  <div class="text-center mt-3">
    <a href="" class="btn btn-success px-5">Selengkapnya <i class="fas fa-arrow-right"></i></a>
  </div>
</div>

<div class="bg-light my-5">
  <div class="container py-5">
    <div class="text-dark text-center">
      <h5>Pelajari tentang kami</h5>
      <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eius quidem quam quos consequatur, nulla enim voluptates accusantium illum sapiente laborum magni obcaecati mollitia omnis ut.</p>
    </div>
  </div>
</div>

<div class="container my-2">
  <div class="text-center">
      <h4 class="">Blog</h4>
      <p>Apa Saja Kabar Hari Ini?, Akan Kami Beri Tahu Anda</p>
  </div>

  <div class="row">
    @for ($i = 0; $i < 4; $i++)
    <div class="col-md-3">
      <div class="card shadow">
        <div class="wrapper-card-block">
          <img src="/img/thumb.jpg" class="img-card-blog" alt="">
        </div>
        <div class="p-3">
          <a href="" class="text-decoration-none">
            <h5>Tanam Pohon Adam</h5>
          </a>
          <p>
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laudantium, rerum?. 
            <a href="">Selengkapnya &rightarrow;</a>
          </p>
        </div>
      </div>
    </div>
    @endfor

    <div class="text-center mt-3">
      <a href="" class="btn btn-success px-5">Selengkapnya <i class="fas fa-arrow-right"></i></a>
    </div>

  </div>
</div>

<div class="bg-success my-5">
  <div class="container py-5">
    <div class="text-white">
      <h5>Pelajari tentang kami</h5>
      <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eius quidem quam quos consequatur, nulla enim voluptates accusantium illum sapiente laborum magni obcaecati mollitia omnis ut.</p>
    </div>
  </div>
</div>

<div class="container my-3 mb-5">
  <div class="text-center">
      <h4 class="">Contact</h4>
      <p>Anda Dapat Bertanya Langsung Dengan Kami</p>
      <a href="" class="btn btn-success px-5" target="blank"> <i class="fas fa-phone"></i> Hubungi Kami Di Whatsapp</a>
  </div>
</div>