@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="contact-us container">
            <div class="mw-930">
                <h2 class="page-title">About US</h2>
            </div>

            <div class="about-us__content pb-5 mb-5">
                <p class="mb-5">
                    <img loading="lazy" class="w-100 h-auto d-block"
                        src="{{ asset('assets/images/home/demo3/background.jpeg') }}" width="1200" height="500" alt="" />
                </p>
                <div class="mw-930">
                    <h3 class="mb-4">OUR STORY</h3>
                    <p class="fs-6 fw-medium mb-4">We are HACHI & LOTA, a hamper purveyor since 2019, we have sold more than
                        15.000 hampers and worked with hundreds of brands and thousands of clients. Let us provide what you
                        need and let's collaborate!</p>
                    <p class="mb-4">Christmas Hampers • Eid Hampers • Custom Hampers
                        <br>Artisanal Gifting Studio -
                        Gifting more, connecting more
                        <br> 📍Jakarta & Karawang
                        <br> 🎁 PR Package | Custom Hampers
                        <br> 📦 Shipping nationwide since 2019
                    </p>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-3">Our Mission</h5>
                            <p class="mb-3">Offer the best, most reliable, and innovative products that meet the diverse
                                needs of our customers</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Our Vision</h5>
                            <p class="mb-3">To be the most trusted and customer-centric online shop, offering high-quality
                                products and exceptional service to enhance the shopping experience</p>
                        </div>
                    </div>
                </div>
                <div class="mw-930 d-lg-flex align-items-lg-center">
                    <div class="image-wrapper col-lg-6">
                        <img class="h-auto" loading="lazy" src="{{ asset('assets/images/logo.jpg') }}" width="450"
                            height="500" alt="">
                    </div>
                    <div class="content-wrapper col-lg-6 px-lg-4">
                        <h5 class="mb-3">What they said</h5>
                        <p>"SETIAP TAHUN GAK PERNAH KECEWA!! SELALU RAPI DAN AMAN. GAK AKAN PINDAH KE LAIN HATI. PELAYANAN
                            SUPERB. GAK MAU PESEN HAMPERS DI TEMPAT LAIN! HARUS DISINI AJA!!"</p>
                        <br>
                        <p>"INI BAGUS BANGET! wangi bgt, rapi dan setiap detailnyaa omg niat banget. WORTH IT TO BUY"</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection