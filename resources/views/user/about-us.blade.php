@extends('user.layouts.front')

@section('styles')
<style>
    .about-section {
        background-color: #f9f9f9;
        padding: 60px 0;
    }
    .about-title {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 20px;
        position: relative;
        padding-left: 40px;
        display: flex;
        align-items: center;
    }
    .about-title i {
        font-size: 30px;
        color: #f39c12;
        position: absolute;
        left: 0;
    }
    .about-description {
        font-size: 16px;
        line-height: 1.8;
        color: #555;
    }
    .about-section .col-lg-5 {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .search-bg-pooja {
        background-color: #f4f4f4;
        text-align: center;
    }
    .description {
        font-size: 42px;
        font-weight: bold;
        margin-top: 20px;
        color: #fbf5f5;
    }
</style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="description">About Us</h1>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="row y-gap-30 justify-content-center">

            <div class="col-lg-5">
                <h2 class="about-title"><i class="fas fa-bullseye"></i> What is 33 Crores</h2>
                <p class="about-description">
                    33 Crores is a platform dedicated to connecting devotees with temples, rituals, and spiritual experiences. We aim to preserve and promote the rich cultural and religious heritage of India by making temple services accessible online.
                </p>
            </div>

            <div class="col-lg-5">
                <h2 class="about-title"><i class="fas fa-seedling"></i> The Birth of 33 Crores</h2>
                <p class="about-description">
                  Our journey began with a simple yet profound realization: amidst our care for earthly comforts, have we overlooked the very creators of the Earth and the universe? Brahma, Vishnu, and Maheswar—supreme powers and parental figures in the vast cosmos—deserve our unwavering devotion
                </p>
            </div>

            <div class="col-lg-5">
                <h2 class="about-title"><i class="fas fa-hands"></i> Our Vision</h2>
                <p class="about-description">
                    Our vision is to become the largest spiritual platform that connects every temple in India with devotees across the globe, preserving the divine connection between faith, tradition, and modern technology.
                </p>
            </div>

            <div class="col-lg-5">
                <h2 class="about-title"><i class="fas fa-cogs"></i> How It Works</h2>
                <p class="about-description">
                    Through 33 Crores, devotees can book temple services, participate in virtual poojas, donate to temples, and receive blessings — all through a seamless online experience.
                </p>
            </div>

            <div class="col-lg-5">
                <h2 class="about-title"><i class="fas fa-users"></i> Join Us</h2>
                <p class="about-description">
                    We invite every temple, devotee, and spiritual seeker to join hands with 33 Crores. Together, let’s bridge the gap between faith and technology.
                </p>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
