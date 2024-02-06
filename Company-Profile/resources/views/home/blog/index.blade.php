<div class="container my-5">
    <div class="text-center">
        <h4 class="">Blog</h4>
        <p>Apa Saja Kabar Hari Ini?, Akan Kami Beri Tahu Anda</p>
    </div>
  
    <div class="row">
      @for ($i = 0; $i < 12; $i++)
      <div class="col-md-3 my-5">
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
  
      {{-- <div class="text-center mt-3">
        <a href="" class="btn btn-success px-5">Selengkapnya <i class="fas fa-arrow-right"></i></a>
      </div> --}}
  
    </div>
  </div>